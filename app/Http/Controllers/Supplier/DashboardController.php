<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Procurement;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:supplier');
    }

    public function index()
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return view('supplier.dashboard.index', ['error' => 'Data supplier tidak ditemukan']);
        }

        // Statistik Pengadaan
        $totalProcurements = Procurement::where('vendor_id', $supplier->id)->count();
        $pendingProcurements = Procurement::where('vendor_id', $supplier->id)
            ->where('status', 'pending')
            ->count();
        $orderedProcurements = Procurement::where('vendor_id', $supplier->id)
            ->where('status', 'ordered')
            ->count();
        $completedProcurements = Procurement::where('vendor_id', $supplier->id)
            ->where('status', 'completed')
            ->count();

        // Nilai Pengadaan
        $totalValue = Procurement::where('vendor_id', $supplier->id)
            ->where('status', 'completed')
            ->sum('total_amount');
        
        $monthlyValue = Procurement::where('vendor_id', $supplier->id)
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Pengadaan Terbaru
        $recentProcurements = Procurement::with(['createdBy'])
            ->where('vendor_id', $supplier->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Pengadaan yang perlu diproses
        $needAction = Procurement::where('vendor_id', $supplier->id)
            ->whereIn('status', ['pending', 'ordered'])
            ->count();

        // Grafik pengadaan per bulan (6 bulan terakhir)
        $monthlyStats = Procurement::where('vendor_id', $supplier->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as total, sum(total_amount) as value')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('supplier.dashboard.index', compact(
            'supplier',
            'totalProcurements',
            'pendingProcurements',
            'orderedProcurements',
            'completedProcurements',
            'totalValue',
            'monthlyValue',
            'recentProcurements',
            'needAction',
            'monthlyStats'
        ));
    }
}