@extends('layouts.app')

@section('title', 'Daftar Kaprodi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-mortarboard text-primary me-2"></i>
            Daftar Ketua Program Studi (Kaprodi)
        </h4>
        <a href="{{ route('admin.users.create') }}?role=kaprodi" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah Kaprodi
        </a>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-people fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Kaprodi</h6>
                            <h3 class="mb-0 fw-bold">{{ $kaprodis->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-check-circle fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Aktif</h6>
                            <h3 class="mb-0 fw-bold">{{ $kaprodis->where('is_active', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-building fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Fakultas</h6>
                            <h3 class="mb-0 fw-bold">{{ $faculties->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-book fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Program Studi</h6>
                            <h3 class="mb-0 fw-bold">{{ $kaprodis->groupBy('department')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="Cari nama, email, NIP, program studi..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="faculty" class="form-select">
                        <option value="">Semua Fakultas</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty }}" {{ request('faculty') == $faculty ? 'selected' : '' }}>
                                {{ $faculty }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
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
            </form>
            @if(request('search') || request('faculty') || request('status'))
            <div class="mt-3 text-end">
                <a href="{{ route('admin.users.kaprodi') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Kaprodi Cards Grid -->
    <div class="row g-4">
        @forelse($kaprodis as $kaprodi)
        <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <!-- Header Card dengan Avatar -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; font-size: 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                {{ strtoupper(substr($kaprodi->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1 fw-semibold">{{ $kaprodi->name }}</h5>
                            <p class="mb-0 text-muted small">
                                <i class="bi bi-envelope me-1"></i>{{ $kaprodi->email }}
                            </p>
                        </div>
                    </div>

                    <!-- Informasi Detail -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-building me-2"></i>Fakultas
                            </span>
                            <span class="fw-medium">{{ $kaprodi->faculty ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-diagram-3 me-2"></i>Program Studi
                            </span>
                            <span class="fw-medium">{{ $kaprodi->department ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-person-badge me-2"></i>NIP
                            </span>
                            <span class="fw-medium">{{ $kaprodi->nip ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted">
                                <i class="bi bi-telephone me-2"></i>Telepon
                            </span>
                            <span class="fw-medium">{{ $kaprodi->phone_number ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">
                                <i class="bi bi-calendar me-2"></i>Terdaftar
                            </span>
                            <span class="fw-medium">{{ $kaprodi->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <!-- Footer Card -->
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <div>
                            @if($kaprodi->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i> Aktif
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                    <i class="bi bi-x-circle me-1"></i> Nonaktif
                                </span>
                            @endif
                        </div>
                        <div class="btn-group">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-info" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailModal{{ $kaprodi->id }}"
                                    title="Detail">
                                <i class="bi bi-eye"></i>
                            </button>
                            <a href="{{ route('admin.users.edit', $kaprodi->id) }}" 
                               class="btn btn-sm btn-outline-warning" 
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-{{ $kaprodi->is_active ? 'danger' : 'success' }}" 
                                    onclick="toggleStatus({{ $kaprodi->id }})"
                                    title="{{ $kaprodi->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i class="bi bi-{{ $kaprodi->is_active ? 'pause' : 'play' }}"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <div class="modal fade" id="detailModal{{ $kaprodi->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-semibold">
                            <i class="bi bi-person-circle text-primary me-2"></i>
                            Detail Kaprodi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4 mb-md-0">
                                <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width: 120px; height: 120px; font-size: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    {{ strtoupper(substr($kaprodi->name, 0, 1)) }}
                                </div>
                                <h5 class="fw-semibold mb-1">{{ $kaprodi->name }}</h5>
                                <p class="text-muted small mb-2">{{ $kaprodi->email }}</p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-person me-1"></i>{{ $kaprodi->username }}
                                </p>
                            </div>
                            <div class="col-md-8">
                                <h6 class="fw-semibold mb-3 pb-2 border-bottom">Informasi Pribadi</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="35%" class="text-muted">NIP</td>
                                        <td class="fw-medium">{{ $kaprodi->nip ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Fakultas</td>
                                        <td class="fw-medium">{{ $kaprodi->faculty ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Program Studi</td>
                                        <td class="fw-medium">{{ $kaprodi->department ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">No. Telepon</td>
                                        <td class="fw-medium">{{ $kaprodi->phone_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Alamat</td>
                                        <td class="fw-medium">{{ $kaprodi->address ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Status</td>
                                        <td>
                                            @if($kaprodi->is_active)
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                                    <i class="bi bi-check-circle me-1"></i> Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                                    <i class="bi bi-x-circle me-1"></i> Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Terdaftar</td>
                                        <td><small>{{ $kaprodi->created_at->format('d/m/Y H:i') }}</small></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Terakhir Update</td>
                                        <td><small>{{ $kaprodi->updated_at->format('d/m/Y H:i') }}</small></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <a href="{{ route('admin.users.edit', $kaprodi->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm p-5 text-center">
                <i class="bi bi-people fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted mb-3">Tidak ada data kaprodi</h5>
                @if(request('search') || request('faculty') || request('status'))
                    <p class="text-muted mb-3">Coba atur ulang filter Anda</p>
                    <a href="{{ route('admin.users.kaprodi') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                    </a>
                @else
                    <a href="{{ route('admin.users.create') }}?role=kaprodi" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i> Tambah Kaprodi
                    </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($kaprodis->hasPages())
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
        <div class="text-muted small mb-2 mb-md-0">
            Menampilkan {{ $kaprodis->firstItem() ?? 0 }} - {{ $kaprodis->lastItem() ?? 0 }} 
            dari {{ $kaprodis->total() }} data
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page Link --}}
                @if($kaprodis->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $kaprodis->previousPageUrl() }}" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach($kaprodis->getUrlRange(max(1, $kaprodis->currentPage() - 2), min($kaprodis->lastPage(), $kaprodis->currentPage() + 2)) as $page => $url)
                    @if($page == $kaprodis->currentPage())
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
                @if($kaprodis->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $kaprodis->nextPageUrl() }}" aria-label="Next">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
</div>

<style>
.card {
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08) !important;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.pagination {
    gap: 2px;
}

.pagination .page-link {
    border: none;
    color: #6c757d;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    color: white;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    color: #0d6efd;
}

.pagination .page-item.disabled .page-link {
    background-color: transparent;
    color: #adb5bd;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
}

.modal-content {
    border-radius: 16px;
}

.modal-header {
    border-bottom: 1px solid rgba(0,0,0,0.05);
    border-radius: 16px 16px 0 0;
}

.modal-footer {
    border-top: 1px solid rgba(0,0,0,0.05);
    border-radius: 0 0 16px 16px;
}

/* Gradient Avatar */
.bg-primary.text-white.rounded-3 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Responsive */
@media (max-width: 768px) {
    .card-body {
        padding: 1.25rem;
    }
    
    .pagination .page-link {
        padding: 0.3rem 0.6rem;
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
                'Accept': 'application/json',
                'Content-Type': 'application/json'
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

// Auto submit filter
document.querySelector('select[name="faculty"]')?.addEventListener('change', function() {
    this.form.submit();
});

document.querySelector('select[name="status"]')?.addEventListener('change', function() {
    this.form.submit();
});

// Search with debounce
let searchTimeout;
document.querySelector('input[name="search"]')?.addEventListener('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        this.form.submit();
    }, 500);
});
</script>
@endsection