{{-- resources/views/admin/books/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Kelola Buku')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-book text-primary me-2"></i>
            Kelola Buku
        </h4>
        <a href="{{ route('admin.books.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Buku
        </a>
        <a href="{{ route('admin.procurements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Pengadaan Baru
        </a>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-book fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Buku</h6>
                            <h3 class="mb-0 fw-bold">{{ $books->total() }}</h3>
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
                            <h6 class="text-muted mb-1">Tersedia</h6>
                            <h3 class="mb-0 fw-bold">{{ $books->sum('available_stock') }}</h3>
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
                            <i class="bi bi-arrow-left-right fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Dipinjam</h6>
                            <h3 class="mb-0 fw-bold">{{ $books->sum('borrowed_stock') }}</h3>
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
                            <i class="bi bi-tags fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Kategori</h6>
                            <h3 class="mb-0 fw-bold">{{ $categories->count() }}</h3>
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
                <div class="col-md-4">
                    <label class="form-label">Cari</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="Judul, penulis, ISBN..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Stok</label>
                    <select name="availability" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="low" {{ request('availability') == 'low' ? 'selected' : '' }}>Stok Menipis (≤5)</option>
                        <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Stok Habis</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
                @if(request('search') || request('category') || request('availability'))
                <div class="col-12 text-end">
                    <a href="{{ route('admin.books.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Tabel Buku -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-table text-primary me-2"></i>
                Daftar Buku
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="5%">#</th>
                            <th class="px-4 py-3" width="8%">Cover</th>
                            <th class="px-4 py-3" width="20%">Judul</th>
                            <th class="px-4 py-3" width="15%">Penulis</th>
                            <th class="px-4 py-3" width="12%">Kategori</th>
                            <th class="px-4 py-3" width="10%">ISBN</th>
                            <th class="px-4 py-3 text-center" width="8%">Stok</th>
                            <th class="px-4 py-3" width="10%">Lokasi</th>
                            <th class="px-4 py-3 text-center" width="12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                        <tr>
                            <td class="px-4">{{ $loop->iteration }}</td>
                            <td class="px-4">
                                @if($book->cover_image && file_exists(public_path('storage/'.$book->cover_image)))
                                    <img src="{{ asset('storage/'.$book->cover_image) }}" 
                                         alt="Cover" 
                                         class="rounded" 
                                         style="width: 45px; height: 60px; object-fit: cover;"
                                         onerror="this.onerror=null; this.src='{{ asset('images/no-cover.jpg') }}';">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                         style="width: 45px; height: 60px;">
                                        <i class="bi bi-image fs-4 text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4">
                                <div>
                                    <strong>{{ $book->title }}</strong>
                                    @if($book->publisher)
                                        <br>
                                        <small class="text-muted">{{ $book->publisher }}</small>
                                    @endif
                                    @if($book->publisher_year)
                                        <br>
                                        <small class="text-muted">{{ $book->publisher_year }}</small>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4">{{ $book->author }}</td>
                            <td class="px-4">
                                <span class="badge bg-light text-dark">{{ $book->category->name ?? '-' }}</span>
                            </td>
                            <td class="px-4"><small>{{ $book->isbn ?? '-' }}</small></td>
                            <td class="px-4 text-center">
                                @if($book->available_stock > 0)
                                    @if($book->available_stock <= 5)
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                            {{ $book->available_stock }}/{{ $book->total_stock }}
                                        </span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                            {{ $book->available_stock }}/{{ $book->total_stock }}
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                        0/{{ $book->total_stock }}
                                    </span>
                                @endif
                                @if($book->borrowed_stock > 0)
                                    <br>
                                    <small class="text-muted">Dipinjam: {{ $book->borrowed_stock }}</small>
                                @endif
                            </td>
                            <td class="px-4">{{ $book->location_rack ?? '-' }}</td>
                            <td class="px-4 text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.books.edit', $book->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.books.show', $book->id) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.books.destroy', $book->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Yakin ingin menghapus buku ini?')"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-book fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted">Tidak ada data buku</h5>
                                @if(request('search') || request('category') || request('availability'))
                                    <p class="text-muted mb-3">Coba atur ulang filter pencarian Anda</p>
                                    <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                    </a>
                                @else
                                    <a href="{{ route('admin.books.create') }}" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle me-1"></i> Tambah Buku
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($books->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top">
                <div class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }} 
                    dari {{ $books->total() }} data
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        @if($books->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link" aria-hidden="true">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $books->previousPageUrl() }}" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        @foreach($books->getUrlRange(max(1, $books->currentPage() - 2), min($books->lastPage(), $books->currentPage() + 2)) as $page => $url)
                            @if($page == $books->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        @if($books->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $books->nextPageUrl() }}" aria-label="Next">
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
            @else
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                <div class="text-muted small">
                    Menampilkan {{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }} 
                    dari {{ $books->total() }} data
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
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
}

.badge.bg-light {
    background-color: #f8f9fa !important;
    color: #495057;
    font-weight: normal;
    padding: 0.35em 0.65em;
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
document.querySelector('select[name="category"]')?.addEventListener('change', function() {
    this.form.submit();
});

document.querySelector('select[name="availability"]')?.addEventListener('change', function() {
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