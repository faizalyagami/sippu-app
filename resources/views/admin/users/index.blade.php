@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-people text-primary"></i> 
            Manajemen User
        </h4>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Tambah User
        </a>
    </div>

    <!-- Filter Card -->
    <div class="glass-card p-4 mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control bg-light border-0" 
                           placeholder="Cari nama, email, username, NIP..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select bg-light border-0">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-light border-0">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="glass-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Kontak</th>
                        <th>NIP</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->role->name == 'admin' ? 'danger' : ($user->role->name == 'kaprodi' ? 'success' : 'info') }}">
                                {{ $user->role->display_name }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $user->phone_number ?? '-' }}</small>
                        </td>
                        <td>
                            <small>{{ $user->nip ?? '-' }}</small>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $user->created_at->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="btn btn-sm btn-outline-warning" 
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#resetPasswordModal{{ $user->id }}"
                                        title="Reset Password">
                                    <i class="bi bi-key"></i>
                                </button>
                                @if($user->id !== Auth::id())
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-{{ $user->is_active ? 'danger' : 'success' }}" 
                                            onclick="toggleStatus({{ $user->id }})"
                                            title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Yakin ingin menghapus user ini?')"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Reset Password Modal -->
                            <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Reset Password</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Reset password untuk user: <strong>{{ $user->name }}</strong></p>
                                                <div class="mb-3">
                                                    <label class="form-label">Password Baru</label>
                                                    <input type="password" name="password" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Konfirmasi Password</label>
                                                    <input type="password" name="password_confirmation" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Reset Password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-people fs-1 d-block mb-3 text-muted"></i>
                            <h5 class="text-muted">Tidak ada data user</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PERBAIKAN PAGINATION -->
        @if($users->hasPages())
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <div class="text-muted small">
                Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} 
                dari {{ $users->total() }} data
            </div>
            
            <!-- Custom Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if($users->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" aria-hidden="true">&laquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $users->previousPageUrl() }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                        @if($page == $users->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if($users->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $users->nextPageUrl() }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link" aria-hidden="true">&raquo;</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        @else
        <!-- Jika hanya 1 halaman, tampilkan info saja -->
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <div class="text-muted small">
                Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} 
                dari {{ $users->total() }} data
            </div>
        </div>
        @endif
    </div>
</div>

<style>
/* Custom Pagination Styling */
.pagination {
    gap: 5px;
}

.pagination .page-item .page-link {
    border-radius: 8px;
    border: none;
    padding: 0.5rem 0.9rem;
    color: #495057;
    font-weight: 500;
    transition: all 0.2s;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.pagination .page-item:not(.active):not(.disabled) .page-link:hover {
    background: #f8f9fa;
    color: #667eea;
    transform: translateY(-2px);
}

.pagination .page-item.disabled .page-link {
    background: #f8f9fa;
    color: #adb5bd;
    border: none;
}

/* Info text styling */
.text-muted.small {
    font-size: 0.875rem;
    color: #6c757d;
}

/* Responsive */
@media (max-width: 768px) {
    .pagination .page-link {
        padding: 0.4rem 0.7rem;
        font-size: 0.875rem;
    }
}
</style>

<script>
function toggleStatus(userId) {
    if (confirm('Yakin ingin mengubah status user ini?')) {
        fetch(`/admin/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan jaringan');
        });
    }
}

// Auto submit filter
document.querySelector('select[name="role"]').addEventListener('change', function() {
    this.form.submit();
});

document.querySelector('select[name="status"]').addEventListener('change', function() {
    this.form.submit();
});

// Search with debounce
let searchTimeout;
document.querySelector('input[name="search"]').addEventListener('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        this.form.submit();
    }, 500);
});
</script>
@endsection