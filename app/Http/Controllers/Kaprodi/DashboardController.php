<?php
// app/Http/Controllers/Kaprodi/DashboardController.php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Book;
use App\Models\BookRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:kaprodi');
    }

    public function index()
    {
        try {
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
            $overdueBorrowings = Borrowing::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->where('expected_return_date', '<', now())
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

            // Buku yang sering dipinjam - VERSI AMAN
            try {
                $popularBooks = DB::table('books')
                    ->select('books.*', DB::raw('COUNT(borrowing_items.id) as total_borrowed'))
                    ->leftJoin('borrowing_items', 'books.id', '=', 'borrowing_items.book_id')
                    ->leftJoin('borrowings', 'borrowing_items.borrowing_id', '=', 'borrowings.id')
                    ->where('borrowings.user_id', $user->id)
                    ->groupBy('books.id')
                    ->orderByDesc('total_borrowed')
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                Log::error('Error in popular books query: ' . $e->getMessage());
                $popularBooks = collect([]);
            }

            // Jika tidak ada data populer, ambil buku random
            if ($popularBooks->isEmpty()) {
                $popularBooks = Book::inRandomOrder()->limit(5)->get();
            }

            // Peringatan buku yang akan jatuh tempo
            $upcomingDueDates = Borrowing::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->where('expected_return_date', '>=', now())
                ->where('expected_return_date', '<=', now()->addDays(3))
                ->get();

            // Aktivitas terbaru
            $recentActivities = Borrowing::with(['items.book'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $data = [
                'totalBorrowings' => $totalBorrowings,
                'activeBorrowings' => $activeBorrowings,
                'returnedBorrowings' => $returnedBorrowings,
                'pendingBorrowings' => $pendingBorrowings,
                'overdueBorrowings' => $overdueBorrowings,
                'currentBorrowings' => $currentBorrowings,
                'recentBorrowings' => $recentBorrowings,
                'totalBooks' => $totalBooks,
                'availableBooks' => $availableBooks,
                'newBooks' => $newBooks,
                'totalRequests' => $totalRequests,
                'pendingRequests' => $pendingRequests,
                'approvedRequests' => $approvedRequests,
                'popularBooks' => $popularBooks,
                'upcomingDueDates' => $upcomingDueDates,
                'recentActivities' => $recentActivities
            ];

            return view('kaprodi.dashboard.index', $data);

        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return view dengan data kosong
            return view('kaprodi.dashboard.index', [
                'totalBorrowings' => 0,
                'activeBorrowings' => 0,
                'returnedBorrowings' => 0,
                'pendingBorrowings' => 0,
                'overdueBorrowings' => 0,
                'currentBorrowings' => collect([]),
                'recentBorrowings' => collect([]),
                'totalBooks' => 0,
                'availableBooks' => 0,
                'newBooks' => 0,
                'totalRequests' => 0,
                'pendingRequests' => 0,
                'approvedRequests' => 0,
                'popularBooks' => collect([]),
                'upcomingDueDates' => collect([]),
                'recentActivities' => collect([])
            ]);
        }
    }
}