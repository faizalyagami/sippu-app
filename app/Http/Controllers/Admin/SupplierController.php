<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $suppliers = $query->orderBy('name')->paginate(15);
        
        // Statistik
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', true)->count();
        $totalProcurements = \App\Models\Procurement::count();
        $totalSpent = \App\Models\Procurement::where('status', 'completed')->sum('total_amount');

        return view('admin.suppliers.index', compact(
            'suppliers', 
            'totalSuppliers', 
            'activeSuppliers',
            'totalProcurements',
            'totalSpent'
        ));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'npwp' => 'nullable|string|max:50',
            'contact_person' => 'required|string|max:255',
            'cp_phone' => 'required|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $supplier = Supplier::create($data);

        // Optional: Create user account for supplier
        if ($request->has('create_user')) {
            $userValidator = Validator::make($request->all(), [
                'username' => 'required|string|unique:users',
                'password' => 'required|string|min:8'
            ]);

            if ($userValidator->fails()) {
                return redirect()->back()
                    ->withErrors($userValidator)
                    ->withInput();
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role_id' => 3, // Supplier role
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'is_active' => true,
                'supplier_id' => $supplier->id
            ]);
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show($id)
    {
        $supplier = Supplier::with(['procurements' => function($q) {
            $q->orderBy('created_at', 'desc')->limit(10);
        }])->findOrFail($id);

        $statistics = [
            'total_procurements' => $supplier->procurements()->count(),
            'completed_procurements' => $supplier->procurements()->where('status', 'completed')->count(),
            'total_spent' => $supplier->procurements()->where('status', 'completed')->sum('total_amount'),
            'pending_procurements' => $supplier->procurements()->where('status', 'pending')->count(),
            'average_lead_time' => $supplier->procurements()
                ->whereNotNull('received_date')
                ->whereNotNull('procurement_date')
                ->selectRaw('AVG(DATEDIFF(received_date, procurement_date)) as avg_days')
                ->value('avg_days')
        ];

        return view('admin.suppliers.show', compact('supplier', 'statistics'));
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'npwp' => 'nullable|string|max:50',
            'contact_person' => 'required|string|max:255',
            'cp_phone' => 'required|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $supplier->update($data);

        // Update associated user if exists
        if ($supplier->user) {
            $supplier->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
            ]);
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        // Check if supplier has procurements
        if ($supplier->procurements()->exists()) {
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'Supplier tidak dapat dihapus karena masih memiliki data pengadaan.');
        }

        // Delete associated user if exists
        if ($supplier->user) {
            $supplier->user->delete();
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->is_active = !$supplier->is_active;
        $supplier->save();

        // Toggle user status if exists
        if ($supplier->user) {
            $supplier->user->is_active = $supplier->is_active;
            $supplier->user->save();
        }

        $status = $supplier->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('admin.suppliers.index')
            ->with('success', "Supplier berhasil {$status}.");
    }
}