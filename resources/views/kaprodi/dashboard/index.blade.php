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

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-book fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Peminjaman</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalBorrowings ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-arrow-left-right fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Sedang Dipinjam</h6>
                            <h3 class="mb-0 fw-bold">{{ $activeBorrowings ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-clock-history fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Menunggu</h6>
                            <h3 class="mb-0 fw-bold">{{ $pendingBorrowings ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Terlambat</h6>
                            <h3 class="mb-0 fw-bold">{{ $overdueBorrowings ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
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
                            <i class="bi bi-cart-plus me-1"></i> Pinjam Buku
                        </a>
                        <a href="{{ route('kaprodi.borrowings.index') }}" class="btn btn-info">
                            <i class="bi bi-list-check me-1"></i> Riwayat Peminjaman
                        </a>
                        <a href="{{ route('kaprodi.requests.create') }}" class="btn btn-warning">
                            <i class="bi bi-envelope me-1"></i> Request Buku
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Peminjaman Aktif -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-arrow-left-right text-primary me-2"></i>
                        Peminjaman Aktif
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($currentBorrowings) && $currentBorrowings->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">No. Pinjam</th>
                                    <th class="px-4 py-3">Tanggal Pinjam</th>
                                    <th class="px-4 py-3">Tenggat</th>
                                    <th class="px-4 py-3">Jumlah Buku</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currentBorrowings as $borrowing)
                                <tr>
                                    <td class="px-4">
                                        <strong>{{ $borrowing->borrowing_number }}</strong>
                                    </td>
                                    <td class="px-4">{{ $borrowing->borrowing_date->format('d/m/Y') }}</td>
                                    <td class="px-4">
                                        {{ $borrowing->expected_return_date->format('d/m/Y') }}
                                        @if($borrowing->expected_return_date < now())
                                            <span class="badge bg-danger bg-opacity-10 text-danger ms-2">Terlambat</span>
                                        @endif
                                    </td>
                                    <td class="px-4">{{ $borrowing->total_items }}</td>
                                    <td class="px-4">
                                        @php
                                            $badges = [
                                                'approved' => ['bg-info', 'Disetujui'],
                                                'borrowed' => ['bg-primary', 'Dipinjam'],
                                            ];
                                            $badge = $badges[$borrowing->status] ?? ['bg-secondary', $borrowing->status];
                                        @endphp
                                        <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                                            {{ $badge[1] }}
                                        </span>
                                    </td>
                                    <td class="px-4 text-center">
                                        <a href="{{ route('kaprodi.borrowings.show', $borrowing->id) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted">Tidak ada peminjaman aktif</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Recent Activities -->
    <div class="row g-4">
        <!-- Riwayat Peminjaman Terbaru -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Riwayat Peminjaman Terbaru
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
                                        {{ $borrowing->borrowing_date->format('d/m/Y') }} - 
                                        {{ $borrowing->total_items }} buku
                                    </p>
                                </div>
                                <span class="badge bg-{{ $borrowing->status == 'returned' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $borrowing->status == 'returned' ? 'success' : 'secondary' }} px-3 py-2">
                                    {{ $borrowing->status == 'returned' ? 'Dikembalikan' : ucfirst($borrowing->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted">Belum ada riwayat peminjaman</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Request Buku Terbaru -->
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
@endsection