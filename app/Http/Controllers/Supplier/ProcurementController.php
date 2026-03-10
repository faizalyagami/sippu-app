<?php
// app/Http/Controllers/Supplier/ProcurementController.php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Procurement;
use App\Models\ProcurementItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProcurementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:supplier');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.dashboard')
                ->with('error', 'Data supplier tidak ditemukan');
        }

        $query = Procurement::with(['createdBy', 'items'])
            ->where('vendor_id', $supplier->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('procurement_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('procurement_date', '<=', $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('procurement_number', 'like', "%{$request->search}%");
        }

        $procurements = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistik
        $statistics = [
            'total' => Procurement::where('vendor_id', $supplier->id)->count(),
            'pending' => Procurement::where('vendor_id', $supplier->id)->where('status', 'pending')->count(),
            'ordered' => Procurement::where('vendor_id', $supplier->id)->where('status', 'ordered')->count(),
            'completed' => Procurement::where('vendor_id', $supplier->id)->where('status', 'completed')->count(),
            'total_value' => Procurement::where('vendor_id', $supplier->id)
                ->where('status', 'completed')
                ->sum('total_amount')
        ];

        return view('supplier.procurements.index', compact('procurements', 'statistics'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        $procurement = Procurement::with(['createdBy', 'items.book.category'])
            ->where('vendor_id', $supplier->id)
            ->findOrFail($id);

        return view('supplier.procurements.show', compact('procurement'));
    }

    public function confirm($id)
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        $procurement = Procurement::where('vendor_id', $supplier->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            $procurement->status = 'ordered';
            $procurement->save();

            // Update semua item status
            $procurement->items()->update(['status' => 'ordered']);

            DB::commit();

            return redirect()->route('supplier.procurements.show', $id)
                ->with('success', 'Pengadaan telah dikonfirmasi dan siap diproses.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function ship(Request $request, $id)
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        $procurement = Procurement::with('items')
            ->where('vendor_id', $supplier->id)
            ->where('status', 'ordered')
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'shipping_date' => 'required|date',
            'tracking_number' => 'nullable|string|max:100',
            'shipping_notes' => 'nullable|string|max:500',
            'items' => 'required|array',
            'items.*.shipped_quantity' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $allShipped = true;
            $anyPartial = false;

            foreach ($procurement->items as $item) {
                $shippedData = $request->items[$item->id];
                
                if ($shippedData['shipped_quantity'] > $item->quantity) {
                    throw new \Exception("Jumlah dikirim melebihi quantity pesanan untuk buku {$item->book->title}");
                }

                // Update item shipping status
                $item->shipped_quantity = $shippedData['shipped_quantity'];
                $item->shipping_notes = $shippedData['notes'] ?? null;

                if ($item->shipped_quantity == $item->quantity) {
                    $item->status = 'shipped';
                } elseif ($item->shipped_quantity > 0) {
                    $item->status = 'partial_shipped';
                    $anyPartial = true;
                    $allShipped = false;
                } else {
                    $item->status = 'ordered';
                    $allShipped = false;
                }

                $item->save();
            }

            // Update procurement status
            if ($allShipped) {
                $procurement->status = 'shipped';
            } elseif ($anyPartial) {
                $procurement->status = 'partial_shipped';
            }

            $procurement->shipping_date = $request->shipping_date;
            $procurement->tracking_number = $request->tracking_number;
            $procurement->shipping_notes = $request->shipping_notes;
            $procurement->save();

            DB::commit();

            return redirect()->route('supplier.procurements.show', $id)
                ->with('success', 'Informasi pengiriman berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function invoice($id)
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        $procurement = Procurement::with(['createdBy', 'items.book', 'vendor'])
            ->where('vendor_id', $supplier->id)
            ->where('status', 'completed')
            ->findOrFail($id);

        return view('supplier.procurements.invoice', compact('procurement'));
    }

    public function getShippingForm($id)
    {
        $procurement = Procurement::with('items.book')
            ->where('vendor_id', Auth::user()->supplier->id)
            ->findOrFail($id);

        return view('supplier.procurements.shipping-form', compact('procurement'));
    }
}