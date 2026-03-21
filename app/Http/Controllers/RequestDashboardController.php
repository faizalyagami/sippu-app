<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RequestDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        // Statistik Permintaan
        $statistics = [
            'total' => Borrowing::count(),
            'pending' => Borrowing::where('status', 'pending')->count(),
            'approved' => Borrowing::where('status', 'approved')->count(),
            'cancelled' => Borrowing::where('status', 'cancelled')->count(),
        ];

        // Permintaan terbaru (5 data terakhir)
        $recentRequests = Borrowing::with(['user', 'items.book'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Data untuk chart (7 hari terakhir)
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('D');
            
            $count = Borrowing::whereDate('created_at', $date)->count();
            $chartData[] = $count;
        }

        return view('admin.dashboard.requests', compact(
            'statistics',
            'recentRequests',
            'chartLabels',
            'chartData'
        ));
    }

    /**
     * API untuk mendapatkan statistik (AJAX)
     */
    public function getStatistics()
    {
        $statistics = [
            'total' => Borrowing::count(),
            'pending' => Borrowing::where('status', 'pending')->count(),
            'approved' => Borrowing::where('status', 'approved')->count(),
            'cancelled' => Borrowing::where('status', 'cancelled')->count(),
        ];

        return response()->json($statistics);
    }

    /**
     * Detail statistik per fakultas
     */
    public function facultyStats()
    {
        $facultyStats = Borrowing::join('users', 'borrowings.user_id', '=', 'users.id')
            ->select('users.faculty', DB::raw('count(*) as total'))
            ->whereNotNull('users.faculty')
            ->groupBy('users.faculty')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return response()->json($facultyStats);
    }
}