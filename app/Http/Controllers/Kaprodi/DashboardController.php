<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Book;
use App\Models\BookRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:kaprodi');
    }

    public function index()
    {
        $user = Auth::user();

        // Statistik Peminjaman
        $totalBorrowings = Borrowing::where('user_id', $user->id)->count();
        $activeBorrowings = Borrowing::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'borrowed'])
            ->count();
        $returnedBorrowings = Borrowing::where('user_id', $user->id)
            ->where('status', 'returned')
            ->count();
        $pendingBorrowings = Borrowing::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Peminjaman yang sedang berlangsung
        $currentBorrowings = Borrowing::with(['items.book'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['approved', 'borrowed'])
            ->orderBy('expected_return_date')
            ->limit(5)
            ->get();

        // Riwayat peminjaman terbaru
        $recentBorrowings = Borrowing::with(['items.book'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Statistik Buku
        $totalBooks = Book::count();
        $availableBooks = Book::where('available_stock', '>', 0)->count();
        $newBooks = Book::where('created_at', '>=', now()->subMonth())->count();

        // Request Buku
        $totalRequests = BookRequest::where('user_id', $user->id)->count();
        $pendingRequests = BookRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $approvedRequests = BookRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        // Buku yang sering dipinjam (top 5)
        $popularBooks = Book::withCount('borrowingItems')
            ->orderByDesc('borrowing_items_count')
            ->limit(5)
            ->get();

        // Peringatan buku yang akan jatuh tempo
        $upcomingDueDates = Borrowing::where('user_id', $user->id)
            ->where('status', 'borrowed')
            ->where('expected_return_date', '>=', now())
            ->where('expected_return_date', '<=', now()->addDays(3))
            ->get();

        return view('kaprodi.dashboard.index', compact(
            'totalBorrowings',
            'activeBorrowings',
            'returnedBorrowings',
            'pendingBorrowings',
            'currentBorrowings',
            'recentBorrowings',
            'totalBooks',
            'availableBooks',
            'newBooks',
            'totalRequests',
            'pendingRequests',
            'approvedRequests',
            'popularBooks',
            'upcomingDueDates'
        ));
    }
}