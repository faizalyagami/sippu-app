<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Book;
use App\Models\User;
use App\Models\BookCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BorrowingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = Borrowing::with(['user', 'approvedBy', 'items.book']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('borrowing_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('borrowing_date', '<=', $request->end_date);
        }

        // Search by borrowing number or user name
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('borrowing_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function($user) use ($request) {
                      $user->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        $borrowings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Data untuk filter
        $users = User::where('role_id', 2)->get(); // Kaprodi only
        $statuses = ['pending', 'approved', 'borrowed', 'returned', 'overdue', 'cancelled'];

        // Statistik
        $statistics = [
            'pending' => Borrowing::where('status', 'pending')->count(),
            'active' => Borrowing::whereIn('status', ['approved', 'borrowed'])->count(),
            'overdue' => Borrowing::where('status', 'borrowed')
                ->where('expected_return_date', '<', now())
                ->count(),
            'returned_today' => Borrowing::where('status', 'returned')
                ->whereDate('actual_return_date', today())
                ->count()
        ];

        return view('admin.borrowings.index', compact('borrowings', 'users', 'statuses', 'statistics'));
    }

    public function show($id)
    {
        $borrowing = Borrowing::with([
            'user', 
            'approvedBy', 
            'items.book.category',
            'items.book.bookConditions' => function($q) {
                $q->where('is_available', true);
            }
        ])->findOrFail($id);

        return view('admin.borrowings.show', compact('borrowing'));
    }

    public function approve(Request $request, $id)
    {
        $borrowing = Borrowing::with('items')->findOrFail($id);

        if ($borrowing->status !== 'pending') {
            return redirect()->route('admin.borrowings.show', $id)
                ->with('error', 'Peminjaman ini tidak dapat disetujui.');
        }

        // Check stock availability
        foreach ($borrowing->items as $item) {
            $book = Book::find($item->book_id);
            if ($book->available_stock < $item->quantity) {
                return redirect()->route('admin.borrowings.show', $id)
                    ->with('error', "Stok buku '{$book->title}' tidak mencukupi. Tersedia: {$book->available_stock}");
            }
        }

        DB::beginTransaction();
        try {
            // Update borrowing status
            $borrowing->status = 'approved';
            $borrowing->approved_by = auth()->id();
            $borrowing->save();

            // Decrease stock for each book
            foreach ($borrowing->items as $item) {
                $book = Book::find($item->book_id);
                $book->decreaseStock($item->quantity);
            }

            DB::commit();

            return redirect()->route('admin.borrowings.show', $id)
                ->with('success', 'Peminjaman berhasil disetujui.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.borrowings.show', $id)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $borrowing = Borrowing::findOrFail($id);

        if ($borrowing->status !== 'pending') {
            return redirect()->route('admin.borrowings.show', $id)
                ->with('error', 'Peminjaman ini tidak dapat ditolak.');
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $borrowing->status = 'cancelled';
        $borrowing->rejection_reason = $request->rejection_reason;
        $borrowing->approved_by = auth()->id();
        $borrowing->save();

        return redirect()->route('admin.borrowings.show', $id)
            ->with('success', 'Peminjaman berhasil ditolak.');
    }

    public function processReturn(Request $request, $id)
    {
        $borrowing = Borrowing::with('items')->findOrFail($id);

        if (!in_array($borrowing->status, ['approved', 'borrowed'])) {
            return redirect()->route('admin.borrowings.show', $id)
                ->with('error', 'Peminjaman ini tidak dapat diproses pengembalian.');
        }

        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.condition' => 'required|in:good,damaged,lost',
            'items.*.notes' => 'nullable|string',
            'penalty_amount' => 'nullable|numeric|min:0',
            'penalty_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $allReturned = true;
            $totalPenalty = 0;

            foreach ($borrowing->items as $item) {
                $returnData = $request->items[$item->id];
                
                // Tentukan quantity berdasarkan kondisi
                if ($returnData['condition'] == 'good') {
                    $returnedQty = $item->quantity;
                    $damagedQty = 0;
                    $lostQty = 0;
                } elseif ($returnData['condition'] == 'damaged') {
                    $returnedQty = 0;
                    $damagedQty = $item->quantity;
                    $lostQty = 0;
                    
                    // Hitung denda untuk buku rusak (contoh: 50% dari harga)
                    $penalty = ($item->book->price ?? 0) * 0.5;
                    $totalPenalty += $penalty;
                } else { // lost
                    $returnedQty = 0;
                    $damagedQty = 0;
                    $lostQty = $item->quantity;
                    
                    // Hitung denda untuk buku hilang (100% dari harga)
                    $penalty = $item->book->price ?? 0;
                    $totalPenalty += $penalty;
                }

                // Update borrowing item
                $item->returned_quantity = $returnedQty;
                $item->damaged_quantity = $damagedQty;
                $item->lost_quantity = $lostQty;
                $item->condition_notes = $returnData['notes'] ?? null;
                $item->return_date = now();
                $item->status = 'returned';
                $item->save();

                // Update book stock
                $book = Book::find($item->book_id);
                
                if ($returnedQty > 0) {
                    $book->returnBook($returnedQty);
                }
                
                if ($damagedQty > 0 || $lostQty > 0) {
                    // Update book stock (borrowed stock berkurang, available stock tidak bertambah)
                    $book->borrowed_stock -= ($damagedQty + $lostQty);
                    
                    // Update book conditions
                    $conditions = BookCondition::where('book_id', $book->id)
                        ->where('is_available', false)
                        ->where('current_borrowing_id', $borrowing->id)
                        ->limit($damagedQty + $lostQty)
                        ->get();

                    foreach ($conditions as $condition) {
                        $condition->condition = $damagedQty > 0 ? 'damaged' : 'lost';
                        $condition->is_available = false;
                        $condition->save();
                    }
                    
                    $book->save();
                }
            }

            // Update borrowing
            $borrowing->actual_return_date = now();
            $borrowing->status = 'returned';
            
            // Simpan penalty jika ada
            if ($totalPenalty > 0 || $request->filled('penalty_amount')) {
                $penaltyAmount = $request->penalty_amount ?? $totalPenalty;
                $borrowing->penalty_amount = $penaltyAmount;
                $borrowing->penalty_notes = $request->penalty_notes ?? 'Denda kerusakan/kehilangan buku';
                $borrowing->penalty_status = 'unpaid';
            }
            
            $borrowing->save();

            DB::commit();

            return redirect()->route('admin.borrowings.show', $id)
                ->with('success', 'Pengembalian buku berhasil diproses.' . 
                    ($totalPenalty > 0 ? " Total denda: Rp " . number_format($totalPenalty, 0, ',', '.') : ''));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function markAsBorrowed($id)
    {
        $borrowing = Borrowing::findOrFail($id);

        if ($borrowing->status !== 'approved') {
            return redirect()->route('admin.borrowings.show', $id)
                ->with('error', 'Peminjaman ini tidak dapat ditandai sebagai dipinjam.');
        }

        $borrowing->status = 'borrowed';
        $borrowing->save();

        return redirect()->route('admin.borrowings.show', $id)
            ->with('success', 'Status peminjaman berhasil diperbarui.');
    }

    public function payPenalty(Request $request, $id)
    {
        $borrowing = Borrowing::findOrFail($id);

        if ($borrowing->penalty_status !== 'unpaid') {
            return redirect()->route('admin.borrowings.show', $id)
                ->with('error', 'Denda sudah dibayar atau tidak ada.');
        }

        $validator = Validator::make($request->all(), [
            'payment_amount' => 'required|numeric|min:' . $borrowing->penalty_amount,
            'payment_method' => 'required|string|in:cash,transfer,credit_card',
            'payment_notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $borrowing->penalty_status = 'paid';
        $borrowing->payment_date = now();
        $borrowing->payment_method = $request->payment_method;
        $borrowing->payment_notes = $request->payment_notes;
        $borrowing->save();

        return redirect()->route('admin.borrowings.show', $id)
            ->with('success', 'Pembayaran denda berhasil diproses.');
    }
}