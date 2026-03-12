<?php
// app/Http/Controllers/Admin/ReportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Procurement;
use App\Models\User;
use App\Models\Category;
use App\Models\Role;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        return view('admin.reports.index');
    }

    public function books(Request $request)
    {
        $query = Book::with('category');

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status == 'available') {
                $query->where('available_stock', '>', 0);
            } elseif ($request->stock_status == 'unavailable') {
                $query->where('available_stock', '<=', 0);
            } elseif ($request->stock_status == 'low') {
                $query->where('available_stock', '<=', 5)
                    ->where('available_stock', '>', 0);
            }
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == 'active');
        }

        // Get data
        $books = $query->orderBy('title')->get();

        // Statistics
        $statistics = [
            'total' => $books->count(),
            'total_stock' => $books->sum('total_stock'),
            'available' => $books->sum('available_stock'),
            'borrowed' => $books->sum('borrowed_stock'),
            'total_value' => $books->sum('price'),
            'active_books' => $books->where('is_active', true)->count(),
            'low_stock' => $books->where('available_stock', '<=', 5)
                ->where('available_stock', '>', 0)
                ->count()
        ];

        // Data for charts
        $categoryStats = Category::withCount('books')
            ->having('books_count', '>', 0)
            ->get()
            ->map(function($cat) {
                return [
                    'name' => $cat->name,
                    'total' => $cat->books_count
                ];
            });

        if ($request->has('export')) {
            return $this->exportBooks($books);
        }

        $categories = Category::all();

        return view('admin.reports.books', compact('books', 'statistics', 'categoryStats', 'categories'));
    }

    public function borrowings(Request $request)
    {
        $query = Borrowing::with(['user', 'items.book']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('borrowing_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('borrowing_date', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $borrowings = $query->orderBy('borrowing_date', 'desc')->get();

        // Statistics
        $statistics = [
            'total' => $borrowings->count(),
            'pending' => $borrowings->where('status', 'pending')->count(),
            'approved' => $borrowings->where('status', 'approved')->count(),
            'borrowed' => $borrowings->where('status', 'borrowed')->count(),
            'returned' => $borrowings->where('status', 'returned')->count(),
            'overdue' => $borrowings->where('status', 'borrowed')
                ->filter(function($b) {
                    return $b->expected_return_date < now();
                })->count(),
            'total_books_borrowed' => $borrowings->sum('total_items'),
            'total_penalty' => $borrowings->sum('penalty_amount')
        ];

        // Popular books
        $popularBooks = DB::table('borrowing_items')
            ->join('books', 'borrowing_items.book_id', '=', 'books.id')
            ->select('books.id', 'books.title', 'books.author', DB::raw('count(*) as total_borrowed'))
            ->groupBy('books.id', 'books.title', 'books.author')
            ->orderByDesc('total_borrowed')
            ->limit(10)
            ->get();

        // Borrowings by day chart
        $borrowingsByDay = Borrowing::select(
            DB::raw('DATE(borrowing_date) as date'),
            DB::raw('count(*) as total')
        )
        ->when($request->filled('start_date'), function($q) use ($request) {
            $q->whereDate('borrowing_date', '>=', $request->start_date);
        })
        ->when($request->filled('end_date'), function($q) use ($request) {
            $q->whereDate('borrowing_date', '<=', $request->end_date);
        })
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        if ($request->has('export')) {
            return $this->exportBorrowings($borrowings);
        }

        $users = User::where('role_id', 2)->get(); // Kaprodi only

        return view('admin.reports.borrowings', compact(
            'borrowings', 
            'statistics', 
            'popularBooks',
            'borrowingsByDay',
            'users'
        ));
    }

    public function procurements(Request $request)
    {
        $query = Procurement::with(['vendor', 'createdBy', 'items']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('procurement_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('procurement_date', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by vendor
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Gunakan pagination, bukan get()
        $procurements = $query->orderBy('procurement_date', 'desc')->paginate(15);
        
        // Statistik - hitung dari hasil query (bisa dari collection atau query terpisah)
        $allProcurements = $query->get(); // Untuk statistik
        
        $statistics = [
            'total' => $allProcurements->count(),
            'pending' => $allProcurements->where('status', 'pending')->count(),
            'ordered' => $allProcurements->where('status', 'ordered')->count(),
            'partial' => $allProcurements->where('status', 'partial')->count(),
            'completed' => $allProcurements->where('status', 'completed')->count(),
            'cancelled' => $allProcurements->where('status', 'cancelled')->count(),
            'total_value' => $allProcurements->where('status', 'completed')->sum('total_amount'),
            'total_books' => $allProcurements->sum(function($p) {
                return $p->items->sum('quantity');
            }),
            'avg_lead_time' => $allProcurements->whereNotNull('received_date')
                ->avg(function($p) {
                    return $p->received_date->diffInDays($p->procurement_date);
                })
        ];

        // Vendor performance
        $vendorPerformance = Supplier::with(['procurements' => function($q) use ($request) {
            $q->when($request->filled('start_date'), function($query) use ($request) {
                $query->whereDate('procurement_date', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function($query) use ($request) {
                $query->whereDate('procurement_date', '<=', $request->end_date);
            });
        }])->get()->map(function($vendor) {
            $procurements = $vendor->procurements;
            $completed = $procurements->where('status', 'completed');
            
            return [
                'name' => $vendor->name,
                'total_procurements' => $procurements->count(),
                'completed' => $completed->count(),
                'total_value' => $completed->sum('total_amount'),
                'avg_lead_time' => $completed->whereNotNull('received_date')
                    ->avg(function($p) {
                        return $p->received_date->diffInDays($p->procurement_date);
                    })
            ];
        })->sortByDesc('total_value')->values();

        // Data untuk chart (6 bulan terakhir)
        $procurementChart = Procurement::select(
            DB::raw('DATE_FORMAT(procurement_date, "%Y-%m") as month'),
            DB::raw('count(*) as total'),
            DB::raw('sum(total_amount) as value')
        )
        ->when($request->filled('start_date'), function($q) use ($request) {
            $q->whereDate('procurement_date', '>=', $request->start_date);
        })
        ->when($request->filled('end_date'), function($q) use ($request) {
            $q->whereDate('procurement_date', '<=', $request->end_date);
        })
        ->groupBy('month')
        ->orderBy('month')
        ->limit(6)
        ->get();

        $vendors = Supplier::all();
        $statuses = ['pending', 'ordered', 'partial', 'completed', 'cancelled'];

        return view('admin.reports.procurements', compact(
            'procurements',      // Ini harus paginator
            'statistics',
            'vendorPerformance',
            'procurementChart',
            'vendors',
            'statuses'
        ));
    }

    public function users(Request $request)
{
    $query = User::with('role');

    if ($request->filled('role_id')) {
        $query->where('role_id', $request->role_id);
    }

    if ($request->filled('is_active')) {
        $query->where('is_active', $request->is_active === 'active');
    }

    $users = $query->orderBy('created_at', 'desc')->paginate(15);

    $statistics = [
        'total' => User::count(),
        'active' => User::where('is_active', true)->count(),
        'inactive' => User::where('is_active', false)->count(),
        'kaprodi' => User::whereHas('role', fn($q) => $q->where('name', 'kaprodi'))->count(),
    ];

    $roleStats = collect([
        ['role' => 'Admin', 'total' => User::whereHas('role', fn($q) => $q->where('name', 'admin'))->count()],
        ['role' => 'Kaprodi', 'total' => User::whereHas('role', fn($q) => $q->where('name', 'kaprodi'))->count()],
        ['role' => 'Supplier', 'total' => User::whereHas('role', fn($q) => $q->where('name', 'supplier'))->count()],
    ]);

    $facultyStats = User::whereNotNull('faculty')
        ->select('faculty', DB::raw('count(*) as total'))
        ->groupBy('faculty')
        ->orderByDesc('total')
        ->limit(10)
        ->get();

    $roles = Role::all();

    return view('admin.reports.users', compact('users', 'statistics', 'roleStats', 'facultyStats', 'roles'));
    }

    public function categories(Request $request)
    {
        $categories = Category::withCount('books')->with('parent')->paginate(15);

        $statistics = [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'total_books' => Book::count(),
        ];

        $categoryStats = Category::withCount('books')
            ->having('books_count', '>', 0)
            ->orderByDesc('books_count')
            ->get();

        return view('admin.reports.categories', compact('categories', 'statistics', 'categoryStats'));
    }

    public function monthly(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $monthlyStats = [
            'books_added' => Book::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_borrowings' => Borrowing::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_returns' => Borrowing::whereBetween('actual_return_date', [$startDate, $endDate])->count(),
            'total_procurements' => Procurement::whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        $recentBorrowings = Borrowing::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentProcurements = Procurement::with('vendor')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $weeklyStats = collect();
        for ($week = 1; $week <= 5; $week++) {
            $weekStart = $startDate->copy()->addWeeks($week - 1);
            $weekEnd = $weekStart->copy()->endOfWeek();

            $weeklyStats->push([
                'week' => $week,
                'borrowings' => Borrowing::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                'returns' => Borrowing::whereBetween('actual_return_date', [$weekStart, $weekEnd])->count(),
                'procurements' => Procurement::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                'books_added' => Book::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
            ]);
        }

        return view('admin.reports.monthly', compact('monthlyStats', 'recentBorrowings', 'recentProcurements', 'weeklyStats'));
    }

    private function exportBooks($books)
    {
        $filename = 'books-report-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Headers
        fputcsv($handle, [
            'Judul', 'ISBN', 'Penulis', 'Penerbit', 'Tahun', 'Kategori',
            'Stok Total', 'Stok Tersedia', 'Stok Dipinjam', 'Lokasi Rak',
            'Harga', 'Status'
        ]);

        // Data
        foreach ($books as $book) {
            fputcsv($handle, [
                $book->title,
                $book->isbn ?? '-',
                $book->author,
                $book->publisher,
                $book->publication_year,
                $book->category->name,
                $book->total_stock,
                $book->available_stock,
                $book->borrowed_stock,
                $book->location_rack ?? '-',
                $book->price ? 'Rp ' . number_format($book->price, 0, ',', '.') : '-',
                $book->is_active ? 'Aktif' : 'Nonaktif'
            ]);
        }

        fclose($handle);
        exit;
    }

    private function exportBorrowings($borrowings)
    {
        $filename = 'borrowings-report-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, [
            'No. Peminjaman', 'Peminjam', 'Tanggal Pinjam', 'Tenggat', 
            'Tanggal Kembali', 'Jumlah Buku', 'Status', 'Denda', 'Keterangan'
        ]);

        foreach ($borrowings as $b) {
            fputcsv($handle, [
                $b->borrowing_number,
                $b->user->name,
                $b->borrowing_date->format('d/m/Y'),
                $b->expected_return_date->format('d/m/Y'),
                $b->actual_return_date ? $b->actual_return_date->format('d/m/Y') : '-',
                $b->total_items,
                $b->status_text,
                $b->penalty_amount ? 'Rp ' . number_format($b->penalty_amount, 0, ',', '.') : '-',
                $b->notes ?? '-'
            ]);
        }

        fclose($handle);
        exit;
    }

    private function exportProcurements($procurements)
    {
        $filename = 'procurements-report-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, [
            'No. Pengadaan', 'Vendor', 'Tanggal', 'Tgl Terima',
            'Total Item', 'Total Nilai', 'Status', 'Keterangan'
        ]);

        foreach ($procurements as $p) {
            fputcsv($handle, [
                $p->procurement_number,
                $p->vendor->name,
                $p->procurement_date->format('d/m/Y'),
                $p->received_date ? $p->received_date->format('d/m/Y') : '-',
                $p->items->count(),
                'Rp ' . number_format($p->total_amount, 0, ',', '.'),
                $p->status_text,
                $p->notes ?? '-'
            ]);
        }

        fclose($handle);
        exit;
    }

    private function exportUsers($users)
    {
        $filename = 'users-report-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, [
            'Nama', 'Email', 'Username', 'Role', 'NIP/NIM',
            'No. Telepon', 'Status'
        ]);

        foreach ($users as $user) {
            fputcsv($handle, [
                $user->name,
                $user->email,
                $user->username,
                $user->role->display_name,
                $user->nip ?? '-',
                $user->phone_number ?? '-',
                $user->is_active ? 'Aktif' : 'Nonaktif'
            ]);
        }

        fclose($handle);
        exit;
    }
}