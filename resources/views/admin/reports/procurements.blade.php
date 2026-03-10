{{-- resources/views/admin/reports/procurements.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Pengadaan Buku')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-cart-plus text-primary"></i> Laporan Pengadaan Buku
        </h4>
        <div>
            <a href="{{ route('admin.reports.procurements', ['export' => 'pdf']) }}" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> Export PDF
            </a>
            <a href="{{ route('admin.reports.procurements', ['export' => 'excel']) }}" class="btn btn-success">
                <i class="bi bi-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') ?? now()->subMonth()->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') ?? now()->format('Y-m-d') }}">
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
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Tampilkan Laporan
                    </button>
                    <a href="{{ route('admin.reports.procurements') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6 class="card-title">Total Pengadaan</h6>
                    <h3 class="mb-0">{{ $statistics['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6 class="card-title">Pending</h6>
                    <h3 class="mb-0">{{ $statistics['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6 class="card-title">Ordered</h6>
                    <h3 class="mb-0">{{ $statistics['ordered'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6 class="card-title">Completed</h6>
                    <h3 class="mb-0">{{ $statistics['completed'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6 class="card-title">Total Nilai</h6>
                    <h3 class="mb-0">Rp {{ number_format($statistics['total_value'] ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6 class="card-title">Total Buku</h6>
                    <h3 class="mb-0">{{ $statistics['total_books'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h6 class="card-title">Rata-rata Lead Time</h6>
                    <h3 class="mb-0">{{ number_format($statistics['avg_lead_time'] ?? 0, 1) }} hari</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h6 class="card-title">Total Supplier</h6>
                    <h3 class="mb-0">{{ $vendors->count() ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Pengadaan -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Grafik Pengadaan per Bulan</h5>
        </div>
        <div class="card-body">
            <canvas id="procurementChart" height="100"></canvas>
            <p class="text-muted text-center mt-2">Grafik jumlah dan nilai pengadaan 6 bulan terakhir</p>
        </div>
    </div>

    <div class="row">
        <!-- Tabel Pengadaan -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detail Pengadaan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>No. PO</th>
                                    <th>Supplier</th>
                                    <th>Tanggal PO</th>
                                    <th>Tgl Terima</th>
                                    <th>Jumlah Item</th>
                                    <th>Total Nilai</th>
                                    <th>Status</th>
                                    <th>Lead Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($procurements as $procurement)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $procurement->procurement_number }}</strong>
                                    </td>
                                    <td>{{ $procurement->vendor->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($procurement->procurement_date)->format('d/m/Y') }}</td>
                                    <td>{{ $procurement->received_date ? \Carbon\Carbon::parse($procurement->received_date)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-center">{{ $procurement->items_count ?? $procurement->items->count() }} item</td>
                                    <td class="text-end">Rp {{ number_format($procurement->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $badges = [
                                                'pending' => 'warning',
                                                'ordered' => 'info',
                                                'partial' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $texts = [
                                                'pending' => 'Pending',
                                                'ordered' => 'Ordered',
                                                'partial' => 'Partial',
                                                'completed' => 'Completed',
                                                'cancelled' => 'Cancelled'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $badges[$procurement->status] }}">
                                            {{ $texts[$procurement->status] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($procurement->received_date && $procurement->procurement_date)
                                            @php
                                                $leadTime = \Carbon\Carbon::parse($procurement->received_date)->diffInDays(\Carbon\Carbon::parse($procurement->procurement_date));
                                            @endphp
                                            {{ $leadTime }} hari
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        <h6>Tidak ada data pengadaan</h6>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-end mt-3">
                        {{ $procurements->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Performa Supplier -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Performa Supplier</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Supplier</th>
                                    <th>Total PO</th>
                                    <th>Completed</th>
                                    <th>Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vendorPerformance ?? [] as $vendor)
                                <tr>
                                    <td>{{ $vendor['name'] }}</td>
                                    <td class="text-center">{{ $vendor['total_procurements'] }}</td>
                                    <td class="text-center">{{ $vendor['completed'] }}</td>
                                    <td class="text-end">Rp {{ number_format($vendor['total_value'], 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ringkasan per Status -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan per Status</h5>
                </div>
                <div class="card-body">
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'ordered' => 'info',
                            'partial' => 'primary',
                            'completed' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $statusCounts = [
                            'pending' => $statistics['pending'] ?? 0,
                            'ordered' => $statistics['ordered'] ?? 0,
                            'partial' => $statistics['partial'] ?? 0,
                            'completed' => $statistics['completed'] ?? 0,
                            'cancelled' => $statistics['cancelled'] ?? 0
                        ];
                        $total = array_sum($statusCounts);
                    @endphp
                    
                    @foreach($statusCounts as $status => $count)
                        @php
                            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <span class="badge bg-{{ $statusColors[$status] }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </span>
                                <span>{{ $count }} ({{ $percentage }}%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $statusColors[$status] }}" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Export Options -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Export Laporan</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports.procurements', ['export' => 'pdf'] + request()->all()) }}" 
                           class="btn btn-outline-danger">
                            <i class="bi bi-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('admin.reports.procurements', ['export' => 'excel'] + request()->all()) }}" 
                           class="btn btn-outline-success">
                            <i class="bi bi-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('admin.reports.procurements', ['export' => 'print'] + request()->all()) }}" 
                           class="btn btn-outline-secondary" target="_blank">
                            <i class="bi bi-printer"></i> Cetak Laporan
                        </a>
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
        
        // Data dari controller
        const months = {!! json_encode(($procurementChart ?? collect([]))->pluck('month')) !!};
        const totals = {!! json_encode(($procurementChart ?? collect([]))->pluck('total')) !!};
        const values = {!! json_encode(($procurementChart ?? collect([]))->pluck('value')->map(function($v) { return $v / 1000000; })) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months.length ? months : ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [
                    {
                        label: 'Jumlah Pengadaan',
                        data: totals.length ? totals : [0, 0, 0, 0, 0, 0],
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Nilai Pengadaan (Juta)',
                        data: values.length ? values : [0, 0, 0, 0, 0, 0],
                        backgroundColor: 'rgba(255, 159, 64, 0.5)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1,
                        yAxisID: 'y1',
                        type: 'line'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Jumlah Pengadaan'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Nilai (Juta Rupiah)'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection