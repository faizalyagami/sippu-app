{{-- resources/views/admin/reports/books.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Buku')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-book text-primary me-2"></i>
            Laporan Buku
        </h4>
        <div>
            <a href="{{ route('admin.reports.books', ['export' => 'pdf']) }}" class="btn btn-danger">
                <i class="bi bi-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('admin.reports.books', ['export' => 'excel']) }}" class="btn btn-success">
                <i class="bi bi-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Statistik Cards Modern -->
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
                            <h3 class="mb-0 fw-bold">{{ $statistics['total'] }}</h3>
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
                            <i class="bi bi-boxes fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Stok</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['total_stock'] }}</h3>
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
                            <i class="bi bi-check-circle fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tersedia</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['available'] }}</h3>
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
                            <h3 class="mb-0 fw-bold">{{ $statistics['borrowed'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Kategori -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-bar-chart text-primary me-2"></i>
                Grafik Kategori Buku
            </h5>
        </div>
        <div class="card-body">
            <canvas id="categoryChart" height="100"></canvas>
        </div>
    </div>

    <!-- Filter Section (Optional) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Stok</label>
                    <select name="stock_status" class="form-select">
                        <option value="">Semua</option>
                        <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stok Menipis (≤5)</option>
                        <option value="unavailable" {{ request('stock_status') == 'unavailable' ? 'selected' : '' }}>Stok Habis</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Buku</label>
                    <select name="is_active" class="form-select">
                        <option value="">Semua</option>
                        <option value="active" {{ request('is_active') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('is_active') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Detail Buku -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-table text-primary me-2"></i>
                Detail Buku
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="5%">#</th>
                            <th class="px-4 py-3" width="15%">Judul</th>
                            <th class="px-4 py-3" width="12%">Penulis</th>
                            <th class="px-4 py-3" width="12%">Penerbit</th>
                            <th class="px-4 py-3" width="10%">Kategori</th>
                            <th class="px-4 py-3 text-center" width="8%">Stok Total</th>
                            <th class="px-4 py-3 text-center" width="8%">Tersedia</th>
                            <th class="px-4 py-3 text-center" width="8%">Dipinjam</th>
                            <th class="px-4 py-3 text-center" width="8%">Rusak</th>
                            <th class="px-4 py-3 text-center" width="8%">Hilang</th>
                            <th class="px-4 py-3 text-center" width="6%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                        <tr>
                            <td class="px-4">{{ $loop->iteration }}</td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    @if($book->cover_image)
                                        <img src="{{ asset('storage/'.$book->cover_image) }}" 
                                             alt="Cover" 
                                             style="width: 35px; height: 45px; object-fit: cover; border-radius: 4px;"
                                             class="me-2">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-2" 
                                             style="width: 35px; height: 45px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ Str::limit($book->title, 40) }}</strong>
                                        @if($book->isbn)
                                            <br><small class="text-muted">ISBN: {{ $book->isbn }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4">{{ Str::limit($book->author, 25) }}</td>
                            <td class="px-4">{{ Str::limit($book->publisher, 20) }}</td>
                            <td class="px-4">
                                <span class="badge bg-light text-dark">{{ $book->category->name }}</span>
                            </td>
                            <td class="px-4 text-center fw-semibold">{{ $book->total_stock }}</td>
                            <td class="px-4 text-center">
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                    {{ $book->available_stock }}
                                </span>
                            </td>
                            <td class="px-4 text-center">
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                    {{ $book->borrowed_stock }}
                                </span>
                            </td>
                            <td class="px-4 text-center">
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                    {{ $book->damaged_stock }}
                                </span>
                            </td>
                            <td class="px-4 text-center">
                                <span class="badge bg-dark bg-opacity-10 text-dark px-3 py-2">
                                    {{ $book->lost_stock }}
                                </span>
                            </td>
                            <td class="px-4 text-center">
                                @if($book->is_active)
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
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted">Tidak ada data buku</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(method_exists($books, 'links'))
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top">
                <div class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }} 
                    dari {{ $books->total() }} data
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{ $books->withQueryString()->links() }}
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('categoryChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($categoryStats->pluck('name')) !!},
                datasets: [{
                    label: 'Jumlah Buku',
                    data: {!! json_encode($categoryStats->pluck('total')) !!},
                    backgroundColor: 'rgba(13, 110, 253, 0.5)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

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
@endsection