<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Procurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:supplier');
    }

    public function index(Request $request)
    {
        $supplier = Auth::user()->supplier;

        $query = Procurement::with(['createdBy'])
            ->where('vendor_id', $supplier->id)
            ->where('status', 'completed');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('received_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('received_date', '<=', $request->end_date);
        }

        // Search by procurement number
        if ($request->filled('search')) {
            $query->where('procurement_number', 'like', "%{$request->search}%");
        }

        $invoices = $query->orderBy('received_date', 'desc')->paginate(15);

        // Statistik
        $statistics = [
            'total_invoices' => $query->count(),
            'total_amount' => $query->sum('total_amount'),
            'this_month' => $query->clone()
                ->whereMonth('received_date', now()->month)
                ->whereYear('received_date', now()->year)
                ->sum('total_amount'),
            'last_month' => $query->clone()
                ->whereMonth('received_date', now()->subMonth()->month)
                ->whereYear('received_date', now()->subMonth()->year)
                ->sum('total_amount')
        ];

        return view('supplier.invoices.index', compact('invoices', 'statistics'));
    }

    public function show($id)
    {
        $supplier = Auth::user()->supplier;

        $procurement = Procurement::with([
            'createdBy', 
            'items.book',
            'vendor'
        ])
        ->where('vendor_id', $supplier->id)
        ->where('status', 'completed')
        ->findOrFail($id);

        return view('supplier.invoices.show', compact('procurement'));
    }

    public function download($id)
    {
        $supplier = Auth::user()->supplier;

        $procurement = Procurement::with(['createdBy', 'items.book', 'vendor'])
            ->where('vendor_id', $supplier->id)
            ->where('status', 'completed')
            ->findOrFail($id);

        // Generate PDF invoice
        $pdf = \PDF::loadView('supplier.invoices.pdf', compact('procurement'));
        
        return $pdf->download('invoice-' . $procurement->procurement_number . '.pdf');
    }

    public function print($id)
    {
        $supplier = Auth::user()->supplier;

        $procurement = Procurement::with(['createdBy', 'items.book', 'vendor'])
            ->where('vendor_id', $supplier->id)
            ->where('status', 'completed')
            ->findOrFail($id);

        return view('supplier.invoices.print', compact('procurement'));
    }

    public function summary(Request $request)
    {
        $supplier = Auth::user()->supplier;

        $year = $request->get('year', now()->year);
        
        // Monthly summary
        $monthlySummary = Procurement::where('vendor_id', $supplier->id)
            ->where('status', 'completed')
            ->whereYear('received_date', $year)
            ->selectRaw('MONTH(received_date) as month, count(*) as total_invoices, sum(total_amount) as total_amount')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top procurements
        $topProcurements = Procurement::where('vendor_id', $supplier->id)
            ->where('status', 'completed')
            ->whereYear('received_date', $year)
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get(['procurement_number', 'total_amount', 'received_date']);

        // Yearly comparison
        $yearlyComparison = [];
        for ($i = 0; $i < 3; $i++) {
            $y = $year - $i;
            $yearlyComparison[$y] = Procurement::where('vendor_id', $supplier->id)
                ->where('status', 'completed')
                ->whereYear('received_date', $y)
                ->sum('total_amount');
        }

        return view('supplier.invoices.summary', compact(
            'monthlySummary',
            'topProcurements',
            'yearlyComparison',
            'year'
        ));
    }
}