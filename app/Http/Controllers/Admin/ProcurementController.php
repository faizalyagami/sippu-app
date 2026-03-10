<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Procurement;
use App\Models\ProcurementItem;
use App\Models\Supplier;
use App\Models\Book;
use App\Models\BookCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProcurementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = Procurement::with(['vendor', 'createdBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by vendor
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('procurement_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('procurement_date', '<=', $request->end_date);
        }

        // Search by procurement number
        if ($request->filled('search')) {
            $query->where('procurement_number', 'like', "%{$request->search}%");
        }

        $procurements = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Data untuk filter
        $vendors = Supplier::active()->get();
        $statuses = ['pending', 'ordered', 'partial', 'completed', 'cancelled'];

        // Statistik
        $statistics = [
            'total' => Procurement::count(),
            'pending' => Procurement::where('status', 'pending')->count(),
            'completed' => Procurement::where('status', 'completed')->count(),
            'total_value' => Procurement::where('status', 'completed')->sum('total_amount'),
            'monthly_value' => Procurement::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount')
        ];

        return view('admin.procurements.index', compact(
            'procurements', 
            'vendors', 
            'statuses',
            'statistics'
        ));
    }

    public function create()
    {
        $suppliers = Supplier::active()->get();
        $books = Book::active()->orderBy('title')->get();
        
        return view('admin.procurements.create', compact('suppliers', 'books'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'procurement_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:procurement_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Hitung total amount
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            // Buat procurement
            $procurement = Procurement::create([
                'vendor_id' => $request->vendor_id,
                'created_by' => auth()->id(),
                'procurement_date' => $request->procurement_date,
                'expected_date' => $request->expected_date,
                'notes' => $request->notes,
                'total_amount' => $totalAmount,
                'status' => 'pending'
            ]);

            // Buat procurement items
            foreach ($request->items as $item) {
                ProcurementItem::create([
                    'procurement_id' => $procurement->id,
                    'book_id' => $item['book_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'notes' => $item['notes'] ?? null,
                    'status' => 'pending'
                ]);
            }

            DB::commit();

            return redirect()->route('admin.procurements.show', $procurement->id)
                ->with('success', 'Pengadaan berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $procurement = Procurement::with([
            'vendor', 
            'createdBy', 
            'items.book.category'
        ])->findOrFail($id);

        return view('admin.procurements.show', compact('procurement'));
    }

    public function receive($id)
    {
        $procurement = Procurement::with('items.book')->findOrFail($id);
        
        if (!in_array($procurement->status, ['ordered', 'partial'])) {
            return redirect()->route('admin.procurements.show', $id)
                ->with('error', 'Pengadaan ini tidak dapat diterima.');
        }

        return view('admin.procurements.receive', compact('procurement'));
    }

    public function processReceive(Request $request, $id)
    {
        $procurement = Procurement::with('items')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'received_date' => 'required|date',
            'receipt_number' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'items' => 'required|array',
            'items.*.received_quantity' => 'required|integer|min:0',
            'items.*.damaged_quantity' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $allCompleted = true;
            $anyPartial = false;

            foreach ($procurement->items as $item) {
                $receivedData = $request->items[$item->id];
                
                // Validasi quantity tidak melebihi pesanan
                if (($receivedData['received_quantity'] + $receivedData['damaged_quantity']) > $item->quantity) {
                    throw new \Exception("Jumlah diterima melebihi quantity pesanan untuk buku {$item->book->title}");
                }

                // Update item
                $item->received_quantity = $receivedData['received_quantity'];
                $item->damaged_quantity = $receivedData['damaged_quantity'];
                $item->notes = $receivedData['notes'] ?? $item->notes;

                // Tentukan status item
                if ($item->received_quantity == $item->quantity) {
                    $item->status = 'completed';
                } elseif ($item->received_quantity > 0) {
                    $item->status = 'partial';
                    $anyPartial = true;
                    $allCompleted = false;
                } else {
                    $item->status = 'pending';
                    $allCompleted = false;
                }

                $item->save();

                // Update stok buku untuk quantity yang diterima dalam kondisi baik
                if ($item->received_quantity > 0) {
                    $book = Book::find($item->book_id);
                    $book->increaseStock($item->received_quantity);

                    // Buat book conditions untuk setiap buku baru
                    for ($i = 0; $i < $item->received_quantity; $i++) {
                        BookCondition::create([
                            'book_id' => $book->id,
                            'book_code' => $book->id . '-' . str_pad(BookCondition::where('book_id', $book->id)->count() + 1, 3, '0', STR_PAD_LEFT),
                            'condition' => 'new',
                            'last_check_date' => now(),
                            'checked_by' => auth()->id(),
                            'is_available' => true
                        ]);
                    }
                }
            }

            // Update procurement
            $procurement->received_date = $request->received_date;
            $procurement->receipt_number = $request->receipt_number;
            $procurement->invoice_number = $request->invoice_number;

            if ($allCompleted) {
                $procurement->status = 'completed';
            } elseif ($anyPartial) {
                $procurement->status = 'partial';
            }

            $procurement->save();

            DB::commit();

            return redirect()->route('admin.procurements.show', $id)
                ->with('success', 'Penerimaan barang berhasil diproses.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function print($id)
    {
        $procurement = Procurement::with([
            'vendor', 
            'createdBy', 
            'items.book'
        ])->findOrFail($id);

        return view('admin.procurements.print', compact('procurement'));
    }

    public function cancel(Request $request, $id)
    {
        $procurement = Procurement::findOrFail($id);

        if (!in_array($procurement->status, ['pending', 'ordered'])) {
            return redirect()->route('admin.procurements.show', $id)
                ->with('error', 'Pengadaan ini tidak dapat dibatalkan.');
        }

        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $procurement->status = 'cancelled';
        $procurement->notes = $procurement->notes . "\n\nPembatalan: " . $request->cancellation_reason;
        $procurement->save();

        return redirect()->route('admin.procurements.show', $id)
            ->with('success', 'Pengadaan berhasil dibatalkan.');
    }
}