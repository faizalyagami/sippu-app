@extends('layouts.app')

@section('title', 'Laporan Pengadaan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-cart-plus text-primary me-2"></i>
            Laporan Pengadaan
        </h4>
        <div>
            <a href="{{ route('admin.reports.procurements', ['export' => 'pdf']) }}" class="btn btn-danger">
                <i class="bi bi-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('admin.reports.procurements', ['export' => 'excel']) }}" class="btn btn-success">
                <i class="bi bi-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Supplier</label>
                    <select name="vendor_id" class="form-select">
                        <option value="">Semua Supplier</option>
                        @foreach($vendors ?? [] as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.reports.procurements') }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-cart-plus fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Pengadaan</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['total'] ?? 0 }}</h3>
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
                            <i class="bi bi-clock-history fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pending</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['pending'] ?? 0 }}</h3>
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
                            <i class="bi bi-truck fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Ordered</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['ordered'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-box-seam fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Partial</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['partial'] ?? 0 }}</h3>
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
                            <h6 class="text-muted mb-1">Completed</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['completed'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-x-circle fs-4 text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Cancelled</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['cancelled'] ?? 0 }}</h3>
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
                            <i class="bi bi-cash-stack fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Nilai</h6>
                            <h3 class="mb-0 fw-bold">Rp {{ number_format($statistics['total_value'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-book fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Buku</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['total_books'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Pengadaan -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-bar-chart text-primary me-2"></i>
                Grafik Pengadaan per Bulan
            </h5>
        </div>
        <div class="card-body">
            <canvas id="procurementChart" height="100"></canvas>
        </div>
    </div>

    <div class="row">
        <!-- Tabel Pengadaan -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-table text-primary me-2"></i>
                        Detail Pengadaan
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3" width="5%">#</th>
                                    <th class="px-4 py-3" width="12%">No. PO</th>
                                    <th class="px-4 py-3" width="15%">Supplier</th>
                                    <th class="px-4 py-3" width="10%">Tgl PO</th>
                                    <th class="px-4 py-3" width="10%">Tgl Terima</th>
                                    <th class="px-4 py-3 text-center" width="8%">Item</th>
                                    <th class="px-4 py-3 text-end" width="12%">Total Nilai</th>
                                    <th class="px-4 py-3 text-center" width="10%">Status</th>
                                    <th class="px-4 py-3 text-center" width="8%">Lead Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($procurements as $procurement)
                                <tr>
                                    <td class="px-4">{{ $loop->iteration }}</td>
                                    <td class="px-4">
                                        <strong>{{ $procurement->procurement_number }}</strong>
                                    </td>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-1 me-2">
                                                <i class="bi bi-building text-primary"></i>
                                            </div>
                                            <div>
                                                {{ $procurement->vendor->name ?? '-' }}
                                                <br>
                                                <small class="text-muted">{{ $procurement->vendor->company_name ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4">{{ \Carbon\Carbon::parse($procurement->procurement_date)->format('d/m/Y') }}</td>
                                    <td class="px-4">{{ $procurement->received_date ? \Carbon\Carbon::parse($procurement->received_date)->format('d/m/Y') : '-' }}</td>
                                    <td class="px-4 text-center">{{ $procurement->items->count() }}</td>
                                    <td class="px-4 text-end fw-semibold">Rp {{ number_format($procurement->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 text-center">
                                        @php
                                            $badges = [
                                                'pending' => ['bg-warning', 'Pending'],
                                                'ordered' => ['bg-info', 'Ordered'],
                                                'partial' => ['bg-primary', 'Partial'],
                                                'completed' => ['bg-success', 'Completed'],
                                                'cancelled' => ['bg-danger', 'Cancelled']
                                            ];
                                            $badge = $badges[$procurement->status] ?? ['bg-secondary', ucfirst($procurement->status ?? 'Unknown')];
                                        @endphp
                                        <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                                            {{ $badge[1] }}
                                        </span>
                                    </td>
                                    <td class="px-4 text-center">
                                        @if($procurement->received_date && $procurement->procurement_date)
                                            @php
                                                $leadTime = \Carbon\Carbon::parse($procurement->received_date)->diffInDays(\Carbon\Carbon::parse($procurement->procurement_date));
                                            @endphp
                                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                                {{ $leadTime }} hari
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                        <h5 class="text-muted">Tidak ada data pengadaan</h5>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($procurements->hasPages())
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top">
                        <div class="text-muted small mb-2 mb-md-0">
                            Menampilkan {{ $procurements->firstItem() ?? 0 }} - {{ $procurements->lastItem() ?? 0 }} 
                            dari {{ $procurements->total() }} data
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                @if($procurements->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link" aria-hidden="true">
                                            <i class="bi bi-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $procurements->previousPageUrl() }}" aria-label="Previous">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                @foreach($procurements->getUrlRange(max(1, $procurements->currentPage() - 2), min($procurements->lastPage(), $procurements->currentPage() + 2)) as $page => $url)
                                    @if($page == $procurements->currentPage())
                                        <li class="page-item active" aria-current="page">
                                            <span class="page-link">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                @if($procurements->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $procurements->nextPageUrl() }}" aria-label="Next">
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
                            Menampilkan {{ $procurements->firstItem() ?? 0 }} - {{ $procurements->lastItem() ?? 0 }} 
                            dari {{ $procurements->total() }} data
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Performa Supplier -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-trophy text-warning me-2"></i>
                        Performa Supplier
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3 py-2">Supplier</th>
                                    <th class="px-3 py-2 text-center">Total PO</th>
                                    <th class="px-3 py-2 text-center">Completed</th>
                                    <th class="px-3 py-2 text-end">Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vendorPerformance ?? [] as $vendor)
                                <tr>
                                    <td class="px-3">
                                        <strong>{{ $vendor['name'] }}</strong>
                                    </td>
                                    <td class="px-3 text-center">{{ $vendor['total_procurements'] }}</td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                            {{ $vendor['completed'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 text-end fw-semibold">
                                        Rp {{ number_format($vendor['total_value'] ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="bi bi-truck fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Tidak ada data supplier</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Ringkasan per Status -->
                    <div class="mt-4">
                        <h6 class="fw-semibold mb-3">Ringkasan per Status</h6>
                        @php
                            $statuses = ['pending', 'ordered', 'partial', 'completed', 'cancelled'];
                            $statusColors = [
                                'pending' => 'warning',
                                'ordered' => 'info',
                                'partial' => 'primary',
                                'completed' => 'success',
                                'cancelled' => 'danger'
                            ];
                            $statusNames = [
                                'pending' => 'Pending',
                                'ordered' => 'Ordered',
                                'partial' => 'Partial',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled'
                            ];
                        @endphp
                        
                        @foreach($statuses as $status)
                            @php
                                $count = $statistics[$status] ?? 0;
                                $percentage = $statistics['total'] > 0 ? round(($count / $statistics['total']) * 100) : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span>
                                        <span class="badge bg-{{ $statusColors[$status] }} bg-opacity-10 text-{{ $statusColors[$status] }} px-3 py-2">
                                            {{ $statusNames[$status] }}
                                        </span>
                                    </span>
                                    <small class="text-muted">{{ $count }} ({{ $percentage }}%)</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $statusColors[$status] }}" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('procurementChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($procurementChart->pluck('month') ?? []) !!},
                datasets: [
                    {
                        label: 'Jumlah Pengadaan',
                        data: {!! json_encode($procurementChart->pluck('total') ?? []) !!},
                        backgroundColor: 'rgba(13, 110, 253, 0.5)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Nilai Pengadaan (Juta)',
                        data: {!! json_encode($procurementChart->pluck('value')->map(function($v) { return $v ? $v / 1000000 : 0; }) ?? []) !!},
                        backgroundColor: 'rgba(255, 193, 7, 0.5)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                        yAxisID: 'y1',
                        type: 'line'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        grid: {
                            display: true,
                            color: 'rgba(0,0,0,0.05)'
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Pengadaan'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Nilai (Juta Rupiah)'
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

.progress {
    background-color: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    transition: width 0.3s ease;
}

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