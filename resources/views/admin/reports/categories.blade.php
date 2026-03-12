{{-- resources/views/admin/reports/categories.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Kategori')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-tags text-primary me-2"></i>
            Laporan Kategori
        </h4>
        <div>
            <a href="{{ route('admin.reports.categories', ['export' => 'pdf']) }}" class="btn btn-danger">
                <i class="bi bi-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('admin.reports.categories', ['export' => 'excel']) }}" class="btn btn-success">
                <i class="bi bi-file-excel me-1"></i> Export Excel
            </a>
        </div>
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
                            <h3 class="mb-0 fw-bold">{{ $statistics['total'] }}</h3>
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
                            <h3 class="mb-0 fw-bold">{{ $statistics['active'] }}</h3>
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
                            <h3 class="mb-0 fw-bold">{{ $statistics['total_books'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Distribusi Buku per Kategori -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-bar-chart text-primary me-2"></i>
                Distribusi Buku per Kategori
            </h5>
        </div>
        <div class="card-body">
            <canvas id="categoryChart" height="100"></canvas>
        </div>
    </div>

    <!-- Tabel Kategori -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-table text-primary me-2"></i>
                Detail Kategori
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="5%">#</th>
                            <th class="px-4 py-3" width="20%">Nama Kategori</th>
                            <th class="px-4 py-3" width="10%">Kode</th>
                            <th class="px-4 py-3" width="15%">Slug</th>
                            <th class="px-4 py-3" width="15%">Parent</th>
                            <th class="px-4 py-3 text-center" width="10%">Jumlah Buku</th>
                            <th class="px-4 py-3 text-center" width="10%">Status</th>
                            <th class="px-4 py-3 text-center" width="15%">Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td class="px-4">{{ $loop->iteration }}</td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-folder2 text-warning me-2"></i>
                                    <div>
                                        <strong>{{ $category->name }}</strong>
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
                                <span class="badge bg-light text-dark">{{ $category->code ?? '-' }}</span>
                            </td>
                            <td class="px-4"><code class="text-primary">{{ $category->slug }}</code></td>
                            <td class="px-4">
                                @if($category->parent)
                                    <span class="badge bg-info bg-opacity-10 text-info">
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
                                <small>{{ $category->created_at->format('d/m/Y') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-tags fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted">Tidak ada data kategori</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($categories->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top">
                <div class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} 
                    dari {{ $categories->total() }} data
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
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
            </div>
            @else
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                <div class="text-muted small">
                    Menampilkan {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} 
                    dari {{ $categories->total() }} data
                </div>
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
@endsection