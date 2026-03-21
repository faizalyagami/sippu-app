@extends('layouts.app')

@section('title', 'Dashboard Kaprodi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 fw-semibold">
            <i class="bi bi-speedometer2 text-primary me-2"></i>
            Dashboard Kaprodi
        </h1>
        <span class="text-muted">
            <i class="bi bi-calendar3 me-1"></i> {{ now()->format('l, d F Y') }}
        </span>
    </div>

    <!-- Welcome Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 70px; height: 70px; font-size: 28px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <h4 class="mb-1 fw-semibold">Selamat Datang, {{ Auth::user()->name }}!</h4>
                    <p class="text-muted mb-0">
                        <i class="bi bi-building me-1"></i> {{ Auth::user()->faculty ?? '-' }} | 
                        <i class="bi bi-diagram-3 me-1"></i> {{ Auth::user()->department ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards (Total Buku, Total Eksemplar, Tersedia, Total Permintaan) -->
    <div class="row g-4 mb-4">
        <!-- Total Buku (Judul Buku Unik) -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-book fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Buku</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalBooks ?? 0 }}</h3>
                            <small class="text-muted">Judul buku unik</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Eksemplar (Total Stok Semua Buku) -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-boxes fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Eksemplar</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalStock ?? 0 }}</h3>
                            <small class="text-muted">Semua salinan buku</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tersedia (Stok Tersedia) -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-check-circle fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tersedia</h6>
                            <h3 class="mb-0 fw-bold">{{ $availableStock ?? 0 }}</h3>
                            <small class="text-success">Siap dipinjam</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Permintaan (Semua Permintaan yang pernah diajukan) -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-envelope fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Permintaan</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalRequests ?? 0 }}</h3>
                            <small class="text-warning">Yang pernah diajukan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions (Sesuai terminologi baru) -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-lightning-charge text-primary me-2"></i>
                        Aksi Cepat
                    </h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('kaprodi.books.index') }}" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Cari Buku
                        </a>
                        <a href="{{ route('kaprodi.borrowings.checkout') }}" class="btn btn-success">
                            <i class="bi bi-cart-plus me-1"></i> Ajukan Permintaan
                        </a>
                        <a href="{{ route('kaprodi.borrowings.index') }}" class="btn btn-info">
                            <i class="bi bi-list-check me-1"></i> Status Permintaan
                        </a>
                        <a href="{{ route('kaprodi.requests.create') }}" class="btn btn-warning">
                            <i class="bi bi-envelope me-1"></i> Request Buku Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Permintaan Aktif -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-envelope-open text-primary me-2"></i>
                        Status Permintaan Aktif
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($currentRequests) && $currentRequests->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">No. Permintaan</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Buku</th>
                                    <th class="px-4 py-3 text-center">Jumlah</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currentRequests as $request)
                                <tr>
                                    <td class="px-4">
                                        <strong>{{ $request->borrowing_number }}</strong>
                                    </td>
                                    <td class="px-4">{{ $request->created_at->format('d/m/Y') }}</td>
                                    <td class="px-4">
                                        @foreach($request->items as $item)
                                            <div>{{ $item->book->title }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-4 text-center">{{ $request->total_items }}</td>
                                    <td class="px-4">
                                        @php
                                            $badges = [
                                                'pending' => ['bg-warning', 'Menunggu'],
                                                'approved' => ['bg-success', 'Disetujui'],
                                                'cancelled' => ['bg-danger', 'Ditolak']
                                            ];
                                            $badge = $badges[$request->status] ?? ['bg-secondary', $request->status];
                                        @endphp
                                        <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                                            {{ $badge[1] }}
                                        </span>
                                    </td>
                                    <td class="px-4 text-center">
                                        <a href="{{ route('kaprodi.borrowings.show', $request->id) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                        @if($request->status == 'pending')
                                            <form action="{{ route('kaprodi.borrowings.cancel', $request->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Batalkan permintaan ini?')">
                                                    <i class="bi bi-x-circle me-1"></i> Batalkan
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted">Tidak ada permintaan aktif</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Recent Activities -->
    <div class="row g-4">
        <!-- Riwayat Permintaan Terbaru -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Riwayat Permintaan Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentBorrowings) && $recentBorrowings->isNotEmpty())
                    <div class="list-group list-group-flush">
                        @foreach($recentBorrowings as $borrowing)
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-semibold">{{ $borrowing->borrowing_number }}</h6>
                                    <p class="mb-0 small text-muted">
                                        {{ $borrowing->created_at->format('d/m/Y') }} - 
                                        {{ $borrowing->total_items }} buku
                                    </p>
                                </div>
                                @php
                                    $badges = [
                                        'pending' => ['bg-warning', 'Menunggu'],
                                        'approved' => ['bg-success', 'Disetujui'],
                                        'cancelled' => ['bg-danger', 'Ditolak']
                                    ];
                                    $badge = $badges[$borrowing->status] ?? ['bg-secondary', $borrowing->status];
                                @endphp
                                <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                                    {{ $badge[1] }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted">Belum ada riwayat permintaan</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Request Buku Terbaru (dari BookRequest) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-envelope text-primary me-2"></i>
                        Request Buku Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentRequests) && $recentRequests->isNotEmpty())
                    <div class="list-group list-group-flush">
                        @foreach($recentRequests as $request)
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-semibold">{{ $request->book_title }}</h6>
                                    <p class="mb-0 small text-muted">
                                        {{ $request->created_at->format('d/m/Y') }} - 
                                        {{ $request->quantity_requested }} eksemplar
                                    </p>
                                </div>
                                <span class="badge bg-{{ $request->status == 'pending' ? 'warning' : 'success' }} bg-opacity-10 text-{{ $request->status == 'pending' ? 'warning' : 'success' }} px-3 py-2">
                                    {{ $request->status == 'pending' ? 'Menunggu' : 'Disetujui' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted">Belum ada request buku</p>
                    </div>
                    @endif
                </div>
            </div>
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

.list-group-item {
    transition: background-color 0.2s;
}

.list-group-item:hover {
    background-color: rgba(13, 110, 253, 0.02);
}

@media (max-width: 768px) {
    .table {
        font-size: 0.9rem;
    }
}
</style>
@endsection