{{-- resources/views/admin/reports/monthly.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Bulanan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-calendar-month text-primary me-2"></i>
            Laporan Bulanan
        </h4>
        <div>
            <a href="{{ route('admin.reports.monthly', ['export' => 'pdf']) }}" class="btn btn-danger">
                <i class="bi bi-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('admin.reports.monthly', ['export' => 'excel']) }}" class="btn btn-success">
                <i class="bi bi-file-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Filter Bulan -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Bulan</label>
                    <select name="month" class="form-select">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month', date('m')) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun</label>
                    <select name="year" class="form-select">
                        @foreach(range(date('Y') - 5, date('Y')) as $y)
                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Tampilkan
                    </button>
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
                            <h6 class="text-muted mb-1">Buku Ditambahkan</h6>
                            <h3 class="mb-0 fw-bold">{{ $monthlyStats['books_added'] }}</h3>
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
                            <i class="bi bi-arrow-left-right fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Peminjaman</h6>
                            <h3 class="mb-0 fw-bold">{{ $monthlyStats['total_borrowings'] }}</h3>
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
                            <i class="bi bi-arrow-return-left fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pengembalian</h6>
                            <h3 class="mb-0 fw-bold">{{ $monthlyStats['total_returns'] }}</h3>
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
                            <i class="bi bi-cart-plus fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Pengadaan</h6>
                            <h3 class="mb-0 fw-bold">{{ $monthlyStats['total_procurements'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Aktivitas Bulanan -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-graph-up text-primary me-2"></i>
                        Grafik Aktivitas {{ \Carbon\Carbon::create()->month(request('month', date('m')))->format('F') }} {{ request('year', date('Y')) }}
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Peminjaman Terbaru Bulan Ini -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-arrow-left-right text-primary me-2"></i>
                        Peminjaman Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">No. Pinjam</th>
                                    <th class="px-4 py-3">Peminjam</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBorrowings as $borrowing)
                                <tr>
                                    <td class="px-4">
                                        <strong>{{ $borrowing->borrowing_number }}</strong>
                                    </td>
                                    <td class="px-4">{{ $borrowing->user->name }}</td>
                                    <td class="px-4">{{ $borrowing->created_at->format('d/m/Y') }}</td>
                                    <td class="px-4">
                                        @php
                                            $badges = [
                                                'pending' => ['bg-warning', 'Menunggu'],
                                                'approved' => ['bg-info', 'Disetujui'],
                                                'borrowed' => ['bg-primary', 'Dipinjam'],
                                                'returned' => ['bg-success', 'Dikembalikan']
                                            ];
                                            $badge = $badges[$borrowing->status] ?? ['bg-secondary', $borrowing->status];
                                        @endphp
                                        <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                                            {{ $badge[1] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Tidak ada data peminjaman</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-cart-plus text-primary me-2"></i>
                        Pengadaan Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">No. PO</th>
                                    <th class="px-4 py-3">Supplier</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentProcurements as $procurement)
                                <tr>
                                    <td class="px-4">
                                        <strong>{{ $procurement->procurement_number }}</strong>
                                    </td>
                                    <td class="px-4">{{ $procurement->vendor->name ?? '-' }}</td>
                                    <td class="px-4">{{ $procurement->created_at->format('d/m/Y') }}</td>
                                    <td class="px-4">
                                        @php
                                            $badges = [
                                                'pending' => ['bg-warning', 'Pending'],
                                                'ordered' => ['bg-info', 'Ordered'],
                                                'partial' => ['bg-primary', 'Partial'],
                                                'completed' => ['bg-success', 'Completed']
                                            ];
                                            $badge = $badges[$procurement->status] ?? ['bg-secondary', $procurement->status];
                                        @endphp
                                        <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                                            {{ $badge[1] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                        <span class="text-muted">Tidak ada data pengadaan</span>
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

    <!-- Ringkasan per Minggu -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-calendar-week text-primary me-2"></i>
                Ringkasan per Minggu
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Minggu Ke-</th>
                            <th class="px-4 py-3 text-center">Peminjaman</th>
                            <th class="px-4 py-3 text-center">Pengembalian</th>
                            <th class="px-4 py-3 text-center">Pengadaan</th>
                            <th class="px-4 py-3 text-center">Buku Ditambahkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weeklyStats as $week)
                        <tr>
                            <td class="px-4 fw-semibold">Minggu {{ $week['week'] }}</td>
                            <td class="px-4 text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                    {{ $week['borrowings'] }}
                                </span>
                            </td>
                            <td class="px-4 text-center">
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                    {{ $week['returns'] }}
                                </span>
                            </td>
                            <td class="px-4 text-center">
                                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                    {{ $week['procurements'] }}
                                </span>
                            </td>
                            <td class="px-4 text-center">
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                    {{ $week['books_added'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('activityChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5'],
                datasets: [
                    {
                        label: 'Peminjaman',
                        data: {!! json_encode($weeklyStats->pluck('borrowings')) !!},
                        borderColor: 'rgba(13, 110, 253, 1)',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Pengadaan',
                        data: {!! json_encode($weeklyStats->pluck('procurements')) !!},
                        borderColor: 'rgba(25, 135, 84, 1)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
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