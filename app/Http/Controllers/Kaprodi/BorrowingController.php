<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BorrowingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:kaprodi');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Borrowing::with(['items.book', 'approvedBy'])
            ->where('user_id', $user->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('borrowing_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('borrowing_date', '<=', $request->end_date);
        }

        $borrowings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistik
        $statistics = [
            'total' => Borrowing::where('user_id', $user->id)->count(),
            'pending' => Borrowing::where('user_id', $user->id)->where('status', 'pending')->count(),
            'active' => Borrowing::where('user_id', $user->id)->whereIn('status', ['approved', 'borrowed'])->count(),
            'returned' => Borrowing::where('user_id', $user->id)->where('status', 'returned')->count(),
            'overdue' => Borrowing::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->where('expected_return_date', '<', now())
                ->count()
        ];

        return view('kaprodi.borrowings.index', compact('borrowings', 'statistics'));
    }

    public function checkout()
    {
        // Get available books for borrowing
        $books = Book::with('category')
            ->where('is_active', true)
            ->where('available_stock', '>', 0)
            ->orderBy('title')
            ->paginate(12);

        // Get categories for filter
        $categories = \App\Models\Category::where('is_active', true)->get();

        return view('kaprodi.borrowings.checkout', compact('books', 'categories'));
    }

    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'books' => 'required|array|min:1',
            'books.*.id' => 'required|exists:books,id',
            'books.*.quantity' => 'required|integer|min:1',
            'purpose' => 'nullable|string|max:500',
            'expected_return_date' => 'required|date|after:today|before:' . now()->addMonths(3)
        ]);

        if ($validator->fails()) {
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
            // Create borrowing
            $borrowing = Borrowing::create([
                'user_id' => auth()->id(),
                'borrowing_date' => now(),
                'expected_return_date' => $request->expected_return_date,
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
                    'status' => 'borrowed'
                ]);
            }

            DB::commit();

            return redirect()->route('kaprodi.borrowings.show', $borrowing->id)
                ->with('success', 'Permintaan peminjaman berhasil diajukan dan menunggu persetujuan admin.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $borrowing = Borrowing::with(['items.book', 'approvedBy'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('kaprodi.borrowings.show', compact('borrowing'));
    }

    public function cancel($id)
    {
        $borrowing = Borrowing::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $borrowing->status = 'cancelled';
        $borrowing->save();

        return redirect()->route('kaprodi.borrowings.index')
            ->with('success', 'Peminjaman berhasil dibatalkan.');
    }

    public function getCartData(Request $request)
    {
        $bookIds = $request->get('book_ids', []);
        
        $books = Book::whereIn('id', $bookIds)
            ->get(['id', 'title', 'author', 'available_stock']);

        return response()->json($books);
    }
}