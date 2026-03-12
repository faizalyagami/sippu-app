{{-- resources/views/admin/suppliers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Supplier Buku')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-truck text-primary me-2"></i>
            Supplier Buku
        </h4>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Supplier
        </a>
    </div>

    <!-- Statistik Cards Sederhana -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small">Total Supplier</span>
                            <h3 class="mt-2 mb-0">{{ $suppliers->total() }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-truck fs-3 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small">Supplier Aktif</span>
                            <h3 class="mt-2 mb-0">{{ $suppliers->where('is_active', true)->count() }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-check-circle fs-3 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small">Total Pengadaan</span>
                            <h3 class="mt-2 mb-0">{{ $totalProcurements ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-cart-plus fs-3 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small">Nilai Pengadaan</span>
                            <h6 class="mt-2 mb-0">Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</h6>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-cash-stack fs-3 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="Cari nama supplier, perusahaan, email..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Supplier -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-3 py-3">#</th>
                            <th class="px-3 py-3">Nama Supplier</th>
                            <th class="px-3 py-3">Perusahaan</th>
                            <th class="px-3 py-3">Email</th>
                            <th class="px-3 py-3">No. Telepon</th>
                            <th class="px-3 py-3">Kontak Person</th>
                            <th class="px-3 py-3 text-center">Total Pengadaan</th>
                            <th class="px-3 py-3 text-center">Status</th>
                            <th class="px-3 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td class="px-3">{{ $loop->iteration }}</td>
                            <td class="px-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-2">
                                        <i class="bi bi-building text-primary"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $supplier->name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3">{{ $supplier->company_name }}</td>
                            <td class="px-3">
                                <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                    {{ $supplier->email }}
                                </a>
                            </td>
                            <td class="px-3">{{ $supplier->phone_number }}</td>
                            <td class="px-3">
                                <div>{{ $supplier->contact_person }}</div>
                                <small class="text-muted">{{ $supplier->cp_phone }}</small>
                            </td>
                            <td class="px-3 text-center">
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                    {{ $supplier->procurements_count }} Pengadaan
                                </span>
                            </td>
                            <td class="px-3 text-center">
                                @if($supplier->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 text-center">
                                <div class="btn-group">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $supplier->id }}"
                                            title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-{{ $supplier->is_active ? 'danger' : 'success' }}" 
                                            onclick="toggleStatus({{ $supplier->id }})"
                                            title="{{ $supplier->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi bi-{{ $supplier->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-truck fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted">Tidak ada data supplier</h5>
                                @if(request('search') || request('status'))
                                    <p class="text-muted mb-3">Coba reset filter Anda</p>
                                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                    </a>
                                @else
                                    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle me-1"></i> Tambah Supplier
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-top">
                <small class="text-muted">
                    Menampilkan {{ $suppliers->firstItem() ?? 0 }} - {{ $suppliers->lastItem() ?? 0 }} 
                    dari {{ $suppliers->total() }} data
                </small>
                <div>
                    {{ $suppliers->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
@foreach($suppliers as $supplier)
<div class="modal fade" id="detailModal{{ $supplier->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-building me-2"></i>
                    Detail Supplier
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Informasi Perusahaan -->
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Perusahaan</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="40%"><strong>Nama Supplier</strong></td>
                                <td>: {{ $supplier->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Perusahaan</strong></td>
                                <td>: {{ $supplier->company_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>NPWP</strong></td>
                                <td>: {{ $supplier->npwp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td>: <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a></td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>: {{ $supplier->phone_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>: {{ $supplier->address }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Informasi Kontak -->
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Kontak</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="40%"><strong>Kontak Person</strong></td>
                                <td>: {{ $supplier->contact_person }}</td>
                            </tr>
                            <tr>
                                <td><strong>CP Telepon</strong></td>
                                <td>: {{ $supplier->cp_phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: 
                                    @if($supplier->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Terdaftar</strong></td>
                                <td>: {{ $supplier->created_at->format('d/m/Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Deskripsi -->
                    @if($supplier->description)
                    <div class="col-12">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Deskripsi</h6>
                        <p class="mb-0">{{ $supplier->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
function toggleStatus(supplierId) {
    if (confirm('Yakin ingin mengubah status supplier ini?')) {
        fetch(`/admin/suppliers/${supplierId}/toggle-status`, {
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
                alert(data.message || 'Gagal mengubah status');
            }
        })
        .catch(() => {
            alert('Terjadi kesalahan jaringan');
        });
    }
}
</script>

<style>
.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.badge {
    font-weight: 500;
}

/* Hapus efek glass-card yang terlalu rumit */
.card {
    border-radius: 12px;
    transition: all 0.2s;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .table {
        font-size: 0.9rem;
    }
    
    .btn-group .btn {
        padding: 0.2rem 0.4rem;
    }
}
</style>
@endsection