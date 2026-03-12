@extends('layouts.app')

@section('title', 'Kelola Buku')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-book text-primary me-2"></i>
            Kelola Buku
        </h4>
        <a href="{{ route('admin.books.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Buku
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="Cari judul/penulis/isbn..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
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
                    <select name="availability" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Stok Habis</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>

            <!-- Tabel Buku -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="8%">Cover</th>
                            <th width="20%">Judul</th>
                            <th width="15%">Penulis</th>
                            <th width="12%">Kategori</th>
                            <th width="12%">ISBN</th>
                            <th width="8%">Stok</th>
                            <th width="10%">Lokasi</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/'.$book->cover_image) }}" 
                                         alt="Cover" 
                                         style="width: 40px; height: 50px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 50px; border-radius: 4px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $book->title }}</strong>
                                @if($book->publisher)
                                    <br>
                                    <small class="text-muted">{{ $book->publisher }}</small>
                                @endif
                            </td>
                            <td>{{ $book->author }}</td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $book->category->name ?? '-' }}</span>
                            </td>
                            <td><small>{{ $book->isbn ?? '-' }}</small></td>
                            <td>
                                @if($book->available_stock > 0)
                                    <span class="badge bg-success">
                                        {{ $book->available_stock }}/{{ $book->total_stock }}
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        0/{{ $book->total_stock }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $book->location_rack ?? '-' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.books.edit', $book->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
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
                                    <p class="text-muted">Coba reset filter Anda</p>
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
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                <div class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }} 
                    dari {{ $books->total() }} data
                </div>
                
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
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

                        {{-- Pagination Elements --}}
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

                        {{-- Next Page Link --}}
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
            <div class="d-flex justify-content-end mt-4">
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
}

.table th {
    font-weight: 600;
    color: #495057;
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

.badge.bg-light {
    background-color: #f8f9fa !important;
    color: #495057;
    font-weight: normal;
    padding: 0.35em 0.65em;
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