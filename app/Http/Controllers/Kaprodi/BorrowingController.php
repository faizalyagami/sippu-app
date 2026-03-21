<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BorrowingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:kaprodi');
    }

    /**
     * Display a listing of user's requests.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Borrowing::with(['items.book', 'approvedBy'])
            ->where('user_id', $user->id);

        // Filter by status (hanya 3 status)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by borrowing number
        if ($request->filled('search')) {
            $query->where('borrowing_number', 'like', "%{$request->search}%");
        }

        $borrowings = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistik hanya untuk 3 status
        $totalBorrowings = Borrowing::where('user_id', $user->id)->count();
        $pendingBorrowings = Borrowing::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $approvedBorrowings = Borrowing::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
        $cancelledBorrowings = Borrowing::where('user_id', $user->id)
            ->where('status', 'cancelled')
            ->count();

        return view('kaprodi.borrowings.index', compact(
            'borrowings',
            'totalBorrowings',
            'pendingBorrowings',
            'approvedBorrowings',
            'cancelledBorrowings'
        ));
    }

    /**
     * Show checkout page.
     */
    public function checkout()
    {
        $books = Book::with('category')
            ->where('is_active', true)
            ->where('available_stock', '>', 0)
            ->orderBy('title')
            ->paginate(12);

        $categories = \App\Models\Category::where('is_active', true)->get();

        return view('kaprodi.borrowings.checkout', compact('books', 'categories'));
    }

    /**
     * Process checkout (ajukan permintaan).
     */
    public function processCheckout(Request $request)
    {
        Log::info('Checkout request data:', $request->all());

        // HAPUS validasi expected_return_date
        $validator = Validator::make($request->all(), [
            'books' => 'required|array|min:1',
            'books.*.id' => 'required|exists:books,id',
            'books.*.quantity' => 'required|integer|min:1',
            'purpose' => 'nullable|string|max:500',
            // 'expected_return_date' => 'required|date|after:today|before:' . now()->addMonths(3) // HAPUS BARIS INI
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate stock availability
        foreach ($request->books as $item) {
            $book = Book::find($item['id']);
            if ($book->available_stock < $item['quantity']) {
                return redirect()->back()
                    ->with('error', "Stok buku '{$book->title}' tidak mencukupi. Tersedia: {$book->available_stock}")
                    ->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Create borrowing WITHOUT expected_return_date
            $borrowing = Borrowing::create([
                'user_id' => auth()->id(),
                'borrowing_date' => now(),
                'expected_return_date' => null, // Set null karena tidak digunakan
                'purpose' => $request->purpose,
                'status' => 'pending',
                'total_items' => collect($request->books)->sum('quantity')
            ]);

            // Create borrowing items
            foreach ($request->books as $item) {
                BorrowingItem::create([
                    'borrowing_id' => $borrowing->id,
                    'book_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'status' => 'pending' // Ubah dari 'borrowed' ke 'pending'
                ]);
            }

            DB::commit();

            return redirect()->route('kaprodi.borrowings.show', $borrowing->id)
                ->with('success', 'Permintaan berhasil diajukan dan menunggu persetujuan admin.')
                ->with('clear_cart', true);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified borrowing.
     */
    public function show($id)
    {
        $borrowing = Borrowing::with(['items.book', 'approvedBy'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('kaprodi.borrowings.show', compact('borrowing'));
    }

    /**
     * Cancel a pending borrowing.
     */
    public function cancel($id)
    {
        $borrowing = Borrowing::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $borrowing->status = 'cancelled';
        $borrowing->save();

        return response()->json([
            'success' => true,
            'message' => 'Permintaan berhasil dibatalkan.'
        ]);
    }

    /**
     * Get cart data for AJAX.
     */
    public function getCartData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:books,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $books = Book::whereIn('id', $request->book_ids)
            ->get(['id', 'title', 'author', 'available_stock']);

        return response()->json($books);
    }
}