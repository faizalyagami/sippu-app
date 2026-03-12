{{-- resources/views/admin/reports/borrowings.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-arrow-left-right text-primary me-2"></i>
            Laporan Peminjaman
        </h4>
        <div>
            <a href="{{ route('admin.reports.borrowings', ['export' => 'pdf']) }}" class="btn btn-danger">
                <i class="bi bi-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('admin.reports.borrowings', ['export' => 'excel']) }}" class="btn btn-success">
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
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Peminjam</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua Peminjam</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.reports.borrowings') }}" class="btn btn-outline-secondary ms-2">
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
                            <i class="bi bi-book fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Peminjaman</h6>
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
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-check-circle fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Disetujui</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['approved'] ?? 0 }}</h3>
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
                            <i class="bi bi-arrow-left-right fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Dipinjam</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['borrowed'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-secondary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-check2-all fs-4 text-secondary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Dikembalikan</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['returned'] ?? 0 }}</h3>
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
                            <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Terlambat</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['overdue'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-dark bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-x-circle fs-4 text-dark"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Dibatalkan</h6>
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
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-cash-stack fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Denda</h6>
                            <h3 class="mb-0 fw-bold">Rp {{ number_format($statistics['total_penalty'] ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Peminjaman -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-bar-chart text-primary me-2"></i>
                Grafik Peminjaman per Hari
            </h5>
        </div>
        <div class="card-body">
            <canvas id="borrowingChart" height="100"></canvas>
        </div>
    </div>

    <div class="row">
        <!-- Tabel Peminjaman -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-table text-primary me-2"></i>
                        Detail Peminjaman
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3" width="5%">#</th>
                                    <th class="px-4 py-3" width="12%">No. Pinjam</th>
                                    <th class="px-4 py-3" width="15%">Peminjam</th>
                                    <th class="px-4 py-3" width="10%">Tgl Pinjam</th>
                                    <th class="px-4 py-3" width="10%">Tenggat</th>
                                    <th class="px-4 py-3" width="10%">Tgl Kembali</th>
                                    <th class="px-4 py-3 text-center" width="8%">Jumlah</th>
                                    <th class="px-4 py-3 text-center" width="10%">Status</th>
                                    <th class="px-4 py-3 text-end" width="10%">Denda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($borrowings as $borrowing)
                                <tr>
                                    <td class="px-4">{{ $loop->iteration }}</td>
                                    <td class="px-4">
                                        <strong>{{ $borrowing->borrowing_number }}</strong>
                                    </td>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            {{ $borrowing->user->name }}
                                        </div>
                                    </td>
                                    <td class="px-4">{{ $borrowing->borrowing_date->format('d/m/Y') }}</td>
                                    <td class="px-4">{{ $borrowing->expected_return_date->format('d/m/Y') }}</td>
                                    <td class="px-4">{{ $borrowing->actual_return_date ? $borrowing->actual_return_date->format('d/m/Y') : '-' }}</td>
                                    <td class="px-4 text-center">{{ $borrowing->total_items }}</td>
                                    <td class="px-4 text-center">
                                        @php
                                            $badges = [
                                                'pending' => ['bg-warning', 'Pending'],
                                                'approved' => ['bg-info', 'Approved'],
                                                'borrowed' => ['bg-primary', 'Borrowed'],
                                                'returned' => ['bg-success', 'Returned'],
                                                'overdue' => ['bg-danger', 'Overdue'],
                                                'cancelled' => ['bg-secondary', 'Cancelled']
                                            ];
                                            $badge = $badges[$borrowing->status] ?? ['bg-secondary', ucfirst($borrowing->status)];
                                        @endphp
                                        <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                                            {{ $badge[1] }}
                                        </span>
                                    </td>
                                    <td class="px-4 text-end fw-semibold">
                                        @if(isset($borrowing->penalty_amount) && $borrowing->penalty_amount > 0)
                                            <span class="text-danger">Rp {{ number_format($borrowing->penalty_amount, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                        <h5 class="text-muted">Tidak ada data peminjaman</h5>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Informasi Jumlah Data (Tanpa Pagination) -->
                    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                        <div class="text-muted small">
                            Menampilkan {{ $borrowings->count() }} data peminjaman
                        </div>
                        <div>
                            <span class="text-muted small">
                                Total: {{ $statistics['total'] ?? 0 }} peminjaman
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buku Populer -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-trophy text-warning me-2"></i>
                        Buku Paling Populer
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3 py-2">#</th>
                                    <th class="px-3 py-2">Judul Buku</th>
                                    <th class="px-3 py-2 text-center">Total Dipinjam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularBooks ?? [] as $index => $book)
                                <tr>
                                    <td class="px-3">
                                        <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }} rounded-circle p-2" 
                                              style="width: 28px; height: 28px;">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-3">
                                        <strong>{{ $book->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $book->author }}</small>
                                    </td>
                                    <td class="px-3 text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                            {{ $book->total_borrowed }}x
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <i class="bi bi-book fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Tidak ada data</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
        const ctx = document.getElementById('borrowingChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($borrowingsByDay->pluck('date') ?? []) !!},
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: {!! json_encode($borrowingsByDay->pluck('total') ?? []) !!},
                    borderColor: 'rgba(13, 110, 253, 1)',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
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
                        grid: {
                            display: true,
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            stepSize: 1
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

@media (max-width: 768px) {
    .table {
        font-size: 0.9rem;
    }
}
</style>
@endsection