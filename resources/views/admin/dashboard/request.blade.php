{{-- resources/views/admin/dashboard/requests.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Permintaan Koleksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-inbox text-primary me-2"></i>
            Dashboard Permintaan Masuk Koleksi
        </h4>
        <span class="text-muted">
            <i class="bi bi-calendar3 me-1"></i> {{ now()->format('l, d F Y') }}
        </span>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-envelope fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Permintaan</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['total'] ?? 0 }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> Semua permintaan masuk
                            </small>
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
                            <h6 class="text-muted mb-1">Menunggu</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['pending'] ?? 0 }}</h3>
                            <small class="text-warning">
                                <i class="bi bi-hourglass-split"></i> Perlu diproses
                            </small>
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
                            <small class="text-success">
                                <i class="bi bi-check-circle-fill"></i> Siap diambil
                            </small>
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
                            <h6 class="text-muted mb-1">Tidak Tersedia</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['cancelled'] ?? 0 }}</h3>
                            <small class="text-danger">
                                <i class="bi bi-x-circle-fill"></i> Ditolak / batal
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Permintaan (Opsional) -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-bar-chart text-primary me-2"></i>
                        Grafik Permintaan 7 Hari Terakhir
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="requestsChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-pie-chart text-primary me-2"></i>
                        Distribusi Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Menunggu</span>
                            <span class="fw-semibold">{{ $statistics['pending'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php $pendingPercent = $statistics['total'] > 0 ? round(($statistics['pending'] / $statistics['total']) * 100) : 0; @endphp
                            <div class="progress-bar bg-warning" style="width: {{ $pendingPercent }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Disetujui</span>
                            <span class="fw-semibold">{{ $statistics['approved'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php $approvedPercent = $statistics['total'] > 0 ? round(($statistics['approved'] / $statistics['total']) * 100) : 0; @endphp
                            <div class="progress-bar bg-success" style="width: {{ $approvedPercent }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Tidak Tersedia</span>
                            <span class="fw-semibold">{{ $statistics['cancelled'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            @php $cancelledPercent = $statistics['total'] > 0 ? round(($statistics['cancelled'] / $statistics['total']) * 100) : 0; @endphp
                            <div class="progress-bar bg-danger" style="width: {{ $cancelledPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Permintaan Terbaru -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-table text-primary me-2"></i>
                        Permintaan Terbaru
                    </h5>
                    <a href="{{ route('admin.borrowings.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">No. Permintaan</th>
                                    <th class="px-4 py-3">Pemohon</th>
                                    <th class="px-4 py-3">Fakultas</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Jumlah Buku</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentRequests ?? [] as $request)
                                <tr>
                                    <td class="px-4">
                                        <strong>{{ $request->borrowing_number }}</strong>
                                    </td>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 35px; height: 35px;">
                                                {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $request->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $request->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4">{{ $request->user->faculty ?? '-' }}</td>
                                    <td class="px-4">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 text-center">{{ $request->total_items }} Buku</td>
                                    <td class="px-4">
                                        @php
                                            $badges = [
                                                'pending' => ['bg-warning', 'Menunggu'],
                                                'approved' => ['bg-success', 'Disetujui'],
                                                'cancelled' => ['bg-danger', 'Tidak Tersedia']
                                            ];
                                            $badge = $badges[$request->status] ?? ['bg-secondary', $request->status];
                                        @endphp
                                        <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                                            {{ $badge[1] }}
                                        </span>
                                    </td>
                                    <td class="px-4">
                                        <a href="{{ route('admin.borrowings.show', $request->id) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                        @if($request->status == 'pending')
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#quickApproveModal{{ $request->id }}">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                        <h5 class="text-muted">Belum ada permintaan</h5>
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

<!-- Quick Approve Modals -->
@foreach($recentRequests ?? [] as $request)
@if($request->status == 'pending')
<div class="modal fade" id="quickApproveModal{{ $request->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-check-circle text-success me-2"></i>
                    Setujui Permintaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.borrowings.approve', $request->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Setujui permintaan dari <strong>{{ $request->user->name }}</strong>?</p>
                    <div class="bg-light p-3 rounded-3">
                        <p class="mb-1"><strong>No. Permintaan:</strong> {{ $request->borrowing_number }}</p>
                        <p class="mb-1"><strong>Jumlah Buku:</strong> {{ $request->total_items }}</p>
                        <p class="mb-0"><strong>Buku yang diminta:</strong></p>
                        <ul class="mb-0 mt-1">
                            @foreach($request->items as $item)
                                <li>{{ $item->book->title }} ({{ $item->quantity }} eks)</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('requestsChart')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels ?? ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']) !!},
                    datasets: [{
                        label: 'Jumlah Permintaan',
                        data: {!! json_encode($chartData ?? [0, 0, 0, 0, 0, 0, 0]) !!},
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
        }
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

.progress {
    background-color: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
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