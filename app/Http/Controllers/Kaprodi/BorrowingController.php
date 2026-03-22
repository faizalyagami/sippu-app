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

        // Filter by status
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

        // Statistik
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
        $hasPendingRequest = Borrowing::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingRequest) {
            return redirect()->route('kaprodi.borrowings.index')
                ->with('error', 'Anda masih memiliki permintaan yang belum diproses. Harap tunggu hingga permintaan sebelumnya selesai.');
        }

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
        $hasPendingRequest = Borrowing::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingRequest) {
            return redirect()->route('kaprodi.borrowings.index')
                ->with('error', 'Anda masih memiliki permintaan yang belum diproses. Harap tunggu hingga permintaan sebelumnya selesai.');
        }

        Log::info('Checkout request data:', $request->all());

        $validator = Validator::make($request->all(), [
            'books' => 'required|array|min:1',
            'books.*.id' => 'required|exists:books,id',
            'books.*.quantity' => 'required|integer|min:1',
            'purpose' => 'nullable|string|max:500',
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
            // Create borrowing
            $borrowing = Borrowing::create([
                'user_id' => auth()->id(),
                'borrowing_date' => now(),
                'expected_return_date' => null,
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
                    'status' => 'pending',
                    'returned_quantity' => 0,
                    'damage_quantity' => 0,
                    'lost_quantity' => 0,
                ]);
            }

            DB::commit();

            // Redirect ke halaman show dengan instruksi clear cart
            return redirect()->route('kaprodi.borrowings.show', $borrowing->id)
                ->with('success', 'Permintaan berhasil diajukan dan menunggu persetujuan admin.')
                ->with('clear_cart_now', true);
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

        // Log untuk debugging
        Log::info('Showing borrowing:', [
            'id' => $borrowing->id,
            'status' => $borrowing->status,
            'items_count' => $borrowing->items->count(),
            'items_status' => $borrowing->items->pluck('status')
        ]);

        return view('kaprodi.borrowings.show', compact('borrowing'));
    }

    /**
     * Cancel a pending borrowing.
     */
    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $borrowing = Borrowing::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->findOrFail($id);

            Log::info('Cancelling borrowing:', [
                'id' => $borrowing->id,
                'current_status' => $borrowing->status,
                'user_id' => auth()->id()
            ]);

            // Update status borrowing
            $borrowing->status = 'cancelled';
            $borrowing->save();

            // Update semua items menjadi cancelled - PASTIKAN INI BERJALAN
            $updatedItems = BorrowingItem::where('borrowing_id', $borrowing->id)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now()
                ]);

            Log::info('Updated items:', [
                'borrowing_id' => $borrowing->id,
                'items_updated' => $updatedItems,
                'query' => BorrowingItem::where('borrowing_id', $borrowing->id)->toSql()
            ]);

            // Verifikasi update
            $items = BorrowingItem::where('borrowing_id', $borrowing->id)->get();
            foreach ($items as $item) {
                Log::info('Item status after update:', [
                    'item_id' => $item->id,
                    'status' => $item->status
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan berhasil dibatalkan.',
                'data' => [
                    'borrowing_status' => $borrowing->status,
                    'items_updated' => $updatedItems
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error cancelling borrowing: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan permintaan: ' . $e->getMessage()
            ], 500);
        }
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
