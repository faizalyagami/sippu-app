<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Constructor dengan middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of all users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::with('role');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by faculty
        if ($request->filled('faculty')) {
            $query->where('faculty', 'like', "%{$request->faculty}%");
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        $roles = Role::all();
        
        // Get unique faculties for filter
        $faculties = User::whereNotNull('faculty')
            ->distinct()
            ->pluck('faculty')
            ->filter()
            ->values();

        return view('admin.users.index', compact('users', 'roles', 'faculties'));
    }

    /**
     * Show form for creating new user.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $roles = Role::where('name', '!=', 'admin')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        
        // Jika ada parameter role, set selected role
        $selectedRole = null;
        if ($request->has('role')) {
            if ($request->role == 'kaprodi') {
                $selectedRole = Role::where('name', 'kaprodi')->first()->id;
            } elseif ($request->role == 'supplier') {
                $selectedRole = Role::where('name', 'supplier')->first()->id;
            }
        }
        
        return view('admin.users.create', compact('roles', 'suppliers', 'selectedRole'));
    }

    /**
     * Store a newly created user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|min:3|max:50',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'supplier_id' => 'nullable|exists:vendors,id',
            'phone_number' => 'nullable|string|max:20',
            'nip' => 'nullable|string|unique:users,nip|max:50',
            'department' => 'nullable|string|max:255',
            'faculty' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate supplier role requirement
        $role = Role::find($request->role_id);
        if ($role && $role->name === 'supplier' && !$request->supplier_id) {
            return redirect()->back()
                ->with('error', 'Supplier harus dipilih untuk user dengan role supplier')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['password'] = Hash::make($request->password);
            $data['is_active'] = $request->has('is_active');

            $user = User::create($data);

            // Create notification for new user
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => 'Selamat Datang di SIPPU',
                'message' => 'Akun Anda telah berhasil dibuat. Silakan login dengan username dan password yang telah didaftarkan.',
                'type' => 'success',
                'is_read' => false
            ]);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form for editing user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::where('name', '!=', 'admin')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.users.edit', compact('user', 'roles', 'suppliers'));
    }

    /**
     * Update user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => 'required|string|unique:users,username,' . $id . '|min:3|max:50',
            'role_id' => 'required|exists:roles,id',
            'supplier_id' => 'nullable|exists:vendors,id',
            'phone_number' => 'nullable|string|max:20',
            'nip' => 'nullable|string|unique:users,nip,' . $id . '|max:50',
            'department' => 'nullable|string|max:255',
            'faculty' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate supplier role requirement
        $role = Role::find($request->role_id);
        if ($role && $role->name === 'supplier' && !$request->supplier_id) {
            return redirect()->back()
                ->with('error', 'Supplier harus dipilih untuk user dengan role supplier')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active');

            $user->update($data);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Reset user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $user->password = Hash::make($request->password);
            $user->save();

            // Create notification for password reset
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => 'Password Diubah',
                'message' => 'Password akun Anda telah diubah oleh administrator.',
                'type' => 'warning',
                'is_read' => false
            ]);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Password user berhasil direset.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status (active/inactive).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Prevent deactivating own account
        if ($user->id === auth()->id()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.'
                ], 400);
            }
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        DB::beginTransaction();
        try {
            $user->is_active = !$user->is_active;
            $user->save();

            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

            // Create notification for status change
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => 'Status Akun ' . ucfirst($status),
                'message' => 'Status akun Anda telah ' . $status . ' oleh administrator.',
                'type' => $user->is_active ? 'success' : 'warning',
                'is_read' => false
            ]);

            DB::commit();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "User berhasil {$status}.",
                    'is_active' => $user->is_active
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', "User berhasil {$status}.");

        } catch (\Exception $e) {
            DB::rollback();
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Check if user has related data
        if ($user->borrowings()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak dapat dihapus karena masih memiliki data peminjaman.');
        }

        if ($user->bookRequests()->count() > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User tidak dapat dihapus karena masih memiliki data request buku.');
        }

        DB::beginTransaction();
        try {
            // Delete notifications
            $user->notifications()->delete();
            
            // Delete user
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display list of kaprodi users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function kaprodi(Request $request)
    {
        $query = User::whereHas('role', function($q) {
            $q->where('name', 'kaprodi');
        })->with('role');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('faculty', 'like', "%{$search}%");
            });
        }

        // Filter by faculty
        if ($request->filled('faculty')) {
            $query->where('faculty', $request->faculty);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $kaprodis = $query->orderBy('faculty')->orderBy('name')->paginate(15)->withQueryString();
        
        // Get unique faculties for filter
        $faculties = User::whereHas('role', function($q) {
            $q->where('name', 'kaprodi');
        })->whereNotNull('faculty')
          ->distinct()
          ->pluck('faculty')
          ->filter()
          ->values();

        return view('admin.users.kaprodi', compact('kaprodis', 'faculties'));
    }

    /**
     * Get user details for AJAX.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::with(['role', 'supplier'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $user->role->display_name,
                'supplier' => $user->supplier ? $user->supplier->name : null,
                'phone_number' => $user->phone_number,
                'nip' => $user->nip,
                'faculty' => $user->faculty,
                'department' => $user->department,
                'address' => $user->address,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at->format('d/m/Y H:i'),
                'updated_at' => $user->updated_at->format('d/m/Y H:i')
            ]
        ]);
    }

    /**
     * Bulk action on users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Filter out current user
        $userIds = array_filter($request->user_ids, function($id) {
            return $id != auth()->id();
        });

        if (empty($userIds)) {
            return redirect()->back()
                ->with('error', 'Tidak ada user yang dapat diproses.');
        }

        DB::beginTransaction();
        try {
            switch ($request->action) {
                case 'activate':
                    User::whereIn('id', $userIds)->update(['is_active' => true]);
                    $message = count($userIds) . ' user berhasil diaktifkan.';
                    break;
                    
                case 'deactivate':
                    User::whereIn('id', $userIds)->update(['is_active' => false]);
                    $message = count($userIds) . ' user berhasil dinonaktifkan.';
                    break;
                    
                case 'delete':
                    // Check if users have related data
                    $usersWithData = User::whereIn('id', $userIds)
                        ->where(function($q) {
                            $q->whereHas('borrowings')
                              ->orWhereHas('bookRequests');
                        })
                        ->count();

                    if ($usersWithData > 0) {
                        DB::rollback();
                        return redirect()->back()
                            ->with('error', "{$usersWithData} user tidak dapat dihapus karena masih memiliki data terkait.");
                    }

                    User::whereIn('id', $userIds)->delete();
                    $message = count($userIds) . ' user berhasil dihapus.';
                    break;
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export users data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $query = User::with('role');

        // Apply filters
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderBy('name')->get();

        $filename = 'users-' . date('Y-m-d-His') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Headers
        fputcsv($handle, [
            'ID',
            'Nama',
            'Email',
            'Username',
            'Role',
            'NIP/NIM',
            'No. Telepon',
            'Fakultas',
            'Departemen',
            'Alamat',
            'Status',
            'Tanggal Daftar'
        ]);

        // Data
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->name,
                $user->email,
                $user->username,
                $user->role->display_name,
                $user->nip ?? '-',
                $user->phone_number ?? '-',
                $user->faculty ?? '-',
                $user->department ?? '-',
                $user->address ?? '-',
                $user->is_active ? 'Aktif' : 'Nonaktif',
                $user->created_at->format('d/m/Y')
            ]);
        }

        fclose($handle);
        exit;
    }
}