@extends('layouts.app')

@section('title', 'Dashboard Kaprodi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-speedometer2 text-primary"></i> 
            Dashboard Kaprodi
        </h1>
        <span class="text-muted">
            <i class="bi bi-calendar3"></i> {{ now()->format('l, d F Y') }}
        </span>
    </div>

    <!-- Welcome Card -->
    <div class="glass-card p-4 mb-4">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 60px; height: 60px; font-size: 24px;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <h4 class="mb-1">Selamat Datang, {{ Auth::user()->name }}!</h4>
                <p class="text-muted mb-0">
                    {{ Auth::user()->faculty }} - {{ Auth::user()->department }}
                </p>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-book fs-4 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Total Peminjaman</h6>
                        <h4 class="mb-0">{{ $totalBorrowings }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-arrow-left-right fs-4 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Sedang Dipinjam</h6>
                        <h4 class="mb-0">{{ $activeBorrowings }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-clock-history fs-4 text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Menunggu</h6>
                        <h4 class="mb-0">{{ $pendingBorrowings }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Terlambat</h6>
                        <h4 class="mb-0">{{ $overdueBorrowings }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Debug (Hanya Tampil di Local) -->
    @if(config('app.debug'))
    <div class="alert alert-info">
        <h6>Debug Information:</h6>
        <pre>{{ json_encode([
            'totalBorrowings' => $totalBorrowings,
            'activeBorrowings' => $activeBorrowings,
            'pendingBorrowings' => $pendingBorrowings,
            'overdueBorrowings' => $overdueBorrowings,
            'totalBooks' => $totalBooks,
            'availableBooks' => $availableBooks,
        ], JSON_PRETTY_PRINT) }}</pre>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="glass-card p-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('kaprodi.books.index') }}" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari Buku
                    </a>
                    <a href="{{ route('kaprodi.borrowings.checkout') }}" class="btn btn-success">
                        <i class="bi bi-cart-plus"></i> Pinjam Buku
                    </a>
                    <a href="{{ route('kaprodi.borrowings.index') }}" class="btn btn-info">
                        <i class="bi bi-list-check"></i> Riwayat Peminjaman
                    </a>
                    <a href="{{ route('kaprodi.requests.create') }}" class="btn btn-warning">
                        <i class="bi bi-envelope"></i> Request Buku
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Peminjaman Aktif -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="mb-3">
                    <i class="bi bi-arrow-left-right text-primary me-2"></i>
                    Peminjaman Aktif
                </h5>
                @if($currentBorrowings->isEmpty())
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                        <p class="text-muted">Tidak ada peminjaman aktif</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. Pinjam</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tenggat</th>
                                    <th>Jumlah Buku</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currentBorrowings as $borrowing)
                                <tr>
                                    <td>{{ $borrowing->borrowing_number }}</td>
                                    <td>{{ $borrowing->borrowing_date->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $borrowing->expected_return_date->format('d/m/Y') }}
                                        @if($borrowing->expected_return_date < now())
                                            <span class="badge bg-danger ms-2">Terlambat</span>
                                        @endif
                                    </td>
                                    <td>{{ $borrowing->total_items }}</td>
                                    <td>
                                        <span class="badge bg-{{ $borrowing->status == 'approved' ? 'info' : 'primary' }}">
                                            {{ ucfirst($borrowing->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.glass-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}
</style>
@endpush