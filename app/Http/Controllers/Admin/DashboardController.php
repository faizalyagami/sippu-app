<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Procurement;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\BookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        // Statistik Buku
        $totalBooks = Book::count();
        $totalAvailable = Book::sum('available_stock') ?? 0;
        $totalBorrowed = Book::sum('borrowed_stock') ?? 0;
        
        // Buku dengan stok menipis
        $lowStockBooks = Book::where('available_stock', '<=', 5)
            ->where('available_stock', '>', 0)
            ->orderBy('available_stock')
            ->limit(5)
            ->get();

        // Statistik User
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();

        // Statistik Supplier
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', true)->count();

        // Statistik Kategori
        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();

        // Statistik Peminjaman
        $totalBorrowings = Borrowing::count();
        $pendingBorrowings = Borrowing::where('status', 'pending')->count();
        $activeBorrowings = Borrowing::whereIn('status', ['approved', 'borrowed'])->count();
        $overdueBorrowings = Borrowing::where('status', 'borrowed')
            ->where('expected_return_date', '<', now())
            ->count();

        // Statistik Pengadaan
        $totalProcurements = Procurement::count();
        $completedProcurements = Procurement::where('status', 'completed')->count();
        $totalProcurementValue = Procurement::where('status', 'completed')->sum('total_amount') ?? 0;

        // Statistik Request Buku
        $totalRequests = BookRequest::count();
        $pendingRequests = BookRequest::where('status', 'pending')->count();

        // Data Terbaru
        $recentBorrowings = Borrowing::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentProcurements = Procurement::with(['vendor'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentRequests = BookRequest::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalBooks',
            'totalAvailable',
            'totalBorrowed',
            'lowStockBooks',
            'totalUsers',
            'activeUsers',
            'totalSuppliers',
            'activeSuppliers',
            'totalCategories',
            'activeCategories',
            'totalBorrowings',
            'pendingBorrowings',
            'activeBorrowings',
            'overdueBorrowings',
            'totalProcurements',
            'completedProcurements',
            'totalProcurementValue',
            'totalRequests',
            'pendingRequests',
            'recentBorrowings',
            'recentProcurements',
            'recentRequests'
        ));
    }
}