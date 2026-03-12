<?php
// app/Http/Controllers/Kaprodi/BorrowingController.php

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

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Borrowing::with(['items.book', 'approvedBy'])
            ->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('borrowing_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('borrowing_date', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $query->where('borrowing_number', 'like', "%{$request->search}%");
        }

        $borrowings = $query->orderBy('created_at', 'desc')->paginate(10);

        $totalBorrowings = Borrowing::where('user_id', $user->id)->count();
        $activeBorrowings = Borrowing::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'borrowed'])
            ->count();
        $pendingBorrowings = Borrowing::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $overdueBorrowings = Borrowing::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->where('expected_return_date', '<', now())
            ->count();

        return view('kaprodi.borrowings.index', compact(
            'borrowings',
            'totalBorrowings',
            'activeBorrowings',
            'pendingBorrowings',
            'overdueBorrowings'
        ));
    }

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

    public function processCheckout(Request $request)
    {
        Log::info('Checkout request data:', $request->all());

        $validator = Validator::make($request->all(), [
            'books' => 'required|array|min:1',
            'books.*.id' => 'required|exists:books,id',
            'books.*.quantity' => 'required|integer|min:1',
            'purpose' => 'nullable|string|max:500',
            'expected_return_date' => 'required|date|after:today|before:' . now()->addMonths(3)
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

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
            $borrowing = Borrowing::create([
                'user_id' => auth()->id(),
                'borrowing_date' => now(),
                'expected_return_date' => $request->expected_return_date,
                'purpose' => $request->purpose,
                'status' => 'pending',
                'total_items' => collect($request->books)->sum('quantity')
            ]);

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
                ->with('success', 'Permintaan peminjaman berhasil diajukan dan menunggu persetujuan admin.')
                ->with('clear_cart', true);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Checkout error: ' . $e->getMessage());
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

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil dibatalkan.'
        ]);
    }

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