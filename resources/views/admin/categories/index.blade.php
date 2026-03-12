@extends('layouts.app')

@section('title', 'Kategori Buku')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-tags text-primary me-2"></i>
            Kategori Buku
        </h4>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
        </a>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-tags fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Kategori</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalCategories }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-check-circle fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Kategori Aktif</h6>
                            <h3 class="mb-0 fw-bold">{{ $activeCategories }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-book fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Buku</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalBooksInCategories }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="Cari nama kategori, kode, atau deskripsi..."
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
            </form>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="5%">#</th>
                            <th class="px-4 py-3" width="25%">Nama Kategori</th>
                            <th class="px-4 py-3" width="10%">Kode</th>
                            <th class="px-4 py-3" width="15%">Slug</th>
                            <th class="px-4 py-3" width="15%">Parent</th>
                            <th class="px-4 py-3 text-center" width="10%">Jumlah Buku</th>
                            <th class="px-4 py-3 text-center" width="10%">Status</th>
                            <th class="px-4 py-3 text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td class="px-4">{{ $loop->iteration }}</td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <i class="bi bi-folder2 text-warning"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium">{{ $category->name }}</span>
                                        @if($category->parent)
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-arrow-return-right me-1"></i>
                                                Sub dari: {{ $category->parent->name }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                    {{ $category->code ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4">
                                <code class="text-primary">{{ $category->slug }}</code>
                            </td>
                            <td class="px-4">
                                @if($category->parent)
                                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                        {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 text-center">
                                <span class="badge bg-{{ $category->books_count > 0 ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $category->books_count > 0 ? 'success' : 'secondary' }} px-3 py-2">
                                    {{ $category->books_count }} Buku
                                </span>
                            </td>
                            <td class="px-4 text-center">
                                @if($category->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 text-center">
                                <div class="btn-group">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $category->id }}"
                                            title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($category->books_count == 0 && $category->children_count == 0)
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Yakin ingin menghapus kategori {{ $category->name }}?')"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-secondary" 
                                            disabled
                                            title="Tidak dapat dihapus (masih memiliki relasi)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-tags fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted">Tidak ada data kategori</h5>
                                @if(request('search') || request('status'))
                                    <p class="text-muted mb-3">Coba atur ulang filter Anda</p>
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                    </a>
                                @else
                                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle me-1"></i> Tambah Kategori Baru
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top">
                <div class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} 
                    dari {{ $categories->total() }} data
                </div>
                
                @if($categories->hasPages())
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if($categories->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link" aria-hidden="true">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $categories->previousPageUrl() }}" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach($categories->getUrlRange(max(1, $categories->currentPage() - 2), min($categories->lastPage(), $categories->currentPage() + 2)) as $page => $url)
                            @if($page == $categories->currentPage())
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
                        @if($categories->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $categories->nextPageUrl() }}" aria-label="Next">
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
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Detail Modals -->
@foreach($categories as $category)
<div class="modal fade" id="detailModal{{ $category->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    Detail Kategori
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <!-- Informasi Kiri -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 pb-2 border-bottom">Informasi Kategori</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted">Nama Kategori</td>
                                    <td class="fw-medium">{{ $category->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kode</td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">{{ $category->code ?? '-' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Slug</td>
                                    <td><code class="text-primary">{{ $category->slug }}</code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kategori Induk</td>
                                    <td>
                                        @if($category->parent)
                                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                                {{ $category->parent->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Informasi Kanan -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 pb-2 border-bottom">Statistik</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted">Jumlah Buku</td>
                                    <td>
                                        <span class="badge bg-{{ $category->books_count > 0 ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $category->books_count > 0 ? 'success' : 'secondary' }} px-3 py-2">
                                            {{ $category->books_count }} Buku
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jumlah Sub-Kategori</td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                            {{ $category->children_count }} Sub
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td>
                                        @if($category->is_active)
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
                                    <td class="text-muted">Dibuat</td>
                                    <td><small>{{ $category->created_at->format('d/m/Y H:i') }}</small></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Deskripsi -->
                @if($category->description)
                <div class="row mt-2">
                    <div class="col-12">
                        <h6 class="fw-semibold mb-3 pb-2 border-bottom">Deskripsi</h6>
                        <p class="bg-light p-3 rounded-3">{{ $category->description }}</p>
                    </div>
                </div>
                @endif

                <!-- Sub Kategori -->
                @if($category->children->count() > 0)
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="fw-semibold mb-3 pb-2 border-bottom">Sub-Kategori</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Kode</th>
                                        <th>Jumlah Buku</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category->children as $child)
                                    <tr>
                                        <td>{{ $child->name }}</td>
                                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $child->code }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $child->books_count > 0 ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $child->books_count > 0 ? 'success' : 'secondary' }}">
                                                {{ $child->books_count }} Buku
                                            </span>
                                        </td>
                                        <td>
                                            @if($child->is_active)
                                                <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
.card {
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08) !important;
}

.table th {
    font-weight: 600;
    color: #495057;
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-weight: 500;
    font-size: 0.85rem;
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

/* Responsive */
@media (max-width: 768px) {
    .table {
        font-size: 0.9rem;
    }
    
    .pagination .page-link {
        padding: 0.3rem 0.6rem;
    }
}
</style>

@push('scripts')
<script>
// Auto submit filter
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
@endpush
@endsection