{{-- resources/views/admin/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Admin')

@push('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        --warning-gradient: linear-gradient(135deg, #fad961 0%, #f76b1c 100%);
        --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --secondary-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }

    .gradient-card {
        border: none;
        border-radius: 15px;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .gradient-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: rgba(255,255,255,0.1);
        transform: rotate(45deg);
        transition: all 0.3s ease;
    }

    .gradient-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    }

    .gradient-card:hover::before {
        transform: rotate(45deg) translate(20%, 20%);
    }

    .card-icon {
        font-size: 3rem;
        opacity: 0.3;
        position: absolute;
        right: 15px;
        bottom: 15px;
        transition: all 0.3s ease;
    }

    .gradient-card:hover .card-icon {
        transform: scale(1.2);
        opacity: 0.5;
    }

    .bg-primary-gradient {
        background: var(--primary-gradient);
    }

    .bg-success-gradient {
        background: var(--success-gradient);
        color: #333;
    }

    .bg-warning-gradient {
        background: var(--warning-gradient);
    }

    .bg-danger-gradient {
        background: var(--danger-gradient);
    }

    .bg-info-gradient {
        background: var(--info-gradient);
    }

    .bg-secondary-gradient {
        background: var(--secondary-gradient);
        color: #333;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.9;
    }

    .stat-change {
        font-size: 0.8rem;
        background: rgba(255,255,255,0.2);
        padding: 2px 8px;
        border-radius: 20px;
        display: inline-block;
        margin-top: 5px;
    }

    .glass-card {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        height: 100%;
    }

    .glass-card:hover {
        background: rgba(255,255,255,1);
    }

    .activity-timeline {
        position: relative;
        padding-left: 30px;
    }

    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 10px;
        bottom: 10px;
        width: 2px;
        background: linear-gradient(to bottom, #667eea, #764ba2);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #667eea;
        border: 2px solid white;
        box-shadow: 0 0 0 2px #667eea;
    }

    .badge-soft-primary {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .badge-soft-success {
        background: rgba(132, 250, 176, 0.2);
        color: #2e7d32;
    }

    .badge-soft-warning {
        background: rgba(250, 217, 97, 0.2);
        color: #b26a00;
    }

    .badge-soft-danger {
        background: rgba(240, 147, 251, 0.2);
        color: #c2185b;
    }

    .badge-soft-info {
        background: rgba(79, 172, 254, 0.2);
        color: #0d6efd;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header dengan Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar3 me-2"></i>{{ now()->format('l, d F Y') }}
                            <i class="bi bi-clock ms-3 me-2"></i>{{ now()->format('H:i') }} WIB
                        </p>
                    </div>
                    <div class="d-none d-md-block">
                        <img src="https://cdn-icons-png.flaticon.com/512/4290/4290854.png" 
                             alt="Welcome" 
                             style="height: 80px; opacity: 0.5;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="glass-card p-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.books.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Buku
                    </a>
                    <a href="{{ route('admin.procurements.create') }}" class="btn btn-success">
                        <i class="bi bi-cart-plus me-2"></i>Buat Pengadaan
                    </a>
                    <a href="{{ route('admin.borrowings.index', ['status' => 'pending']) }}" class="btn btn-warning">
                        <i class="bi bi-clock-history me-2"></i>Pending Peminjaman
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-info">
                        <i class="bi bi-file-text me-2"></i>Lihat Laporan
                    </a>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-secondary">
                        <i class="bi bi-tags me-2"></i>Tambah Kategori
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 1: Main Stats with Gradient Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Buku -->
        <div class="col-xl-3 col-md-6">
            <div class="gradient-card bg-primary-gradient p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">TOTAL BUKU</div>
                        <div class="stat-value">{{ $totalBooks }}</div>
                        <div class="stat-change">
                            <i class="bi bi-arrow-up me-1"></i>{{ $totalAvailable }} Tersedia
                        </div>
                    </div>
                    <i class="bi bi-book-half card-icon"></i>
                </div>
            </div>
        </div>

        <!-- Total User -->
        <div class="col-xl-3 col-md-6">
            <div class="gradient-card bg-success-gradient p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">TOTAL USER</div>
                        <div class="stat-value">{{ $totalUsers }}</div>
                        <div class="stat-change">
                            <i class="bi bi-people me-1"></i>{{ $activeUsers }} Aktif
                        </div>
                    </div>
                    <i class="bi bi-people-fill card-icon"></i>
                </div>
            </div>
        </div>

        <!-- Peminjaman Aktif -->
        <div class="col-xl-3 col-md-6">
            <div class="gradient-card bg-warning-gradient p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">PEMINJAMAN AKTIF</div>
                        <div class="stat-value">{{ $activeBorrowings }}</div>
                        <div class="stat-change">
                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $overdueBorrowings }} Terlambat
                        </div>
                    </div>
                    <i class="bi bi-arrow-left-right card-icon"></i>
                </div>
            </div>
        </div>

        <!-- Total Pengadaan -->
        <div class="col-xl-3 col-md-6">
            <div class="gradient-card bg-info-gradient p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">TOTAL PENGADAAN</div>
                        <div class="stat-value">{{ $totalProcurements }}</div>
                        <div class="stat-change">
                            <i class="bi bi-cash-stack me-1"></i>Rp {{ number_format($totalProcurementValue, 0, ',', '.') }}
                        </div>
                    </div>
                    <i class="bi bi-cart-plus card-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Secondary Stats -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="gradient-card bg-secondary-gradient p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">SUPPLIER AKTIF</div>
                        <div class="stat-value">{{ $activeSuppliers }}</div>
                        <div class="stat-change">
                            <i class="bi bi-truck me-1"></i>Dari {{ $totalSuppliers }}
                        </div>
                    </div>
                    <i class="bi bi-truck card-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="gradient-card bg-info-gradient p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">KATEGORI AKTIF</div>
                        <div class="stat-value">{{ $activeCategories }}</div>
                        <div class="stat-change">
                            <i class="bi bi-tags me-1"></i>Dari {{ $totalCategories }}
                        </div>
                    </div>
                    <i class="bi bi-tags card-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="gradient-card bg-warning-gradient p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">PENDING PEMINJAMAN</div>
                        <div class="stat-value">{{ $pendingBorrowings }}</div>
                        <div class="stat-change">
                            <i class="bi bi-clock me-1"></i>Menunggu
                        </div>
                    </div>
                    <i class="bi bi-clock-history card-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="gradient-card bg-danger-gradient p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">REQUEST BUKU</div>
                        <div class="stat-value">{{ $pendingRequests }}</div>
                        <div class="stat-change">
                            <i class="bi bi-envelope me-1"></i>Dari {{ $totalRequests }}
                        </div>
                    </div>
                    <i class="bi bi-envelope card-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Charts and Stats -->
    <div class="row g-4 mb-4">
        <!-- Chart Peminjaman -->
        <div class="col-xl-8">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up text-primary me-2"></i>
                        Grafik Peminjaman (7 Hari Terakhir)
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary active">Minggu</button>
                        <button type="button" class="btn btn-sm btn-outline-primary">Bulan</button>
                        <button type="button" class="btn btn-sm btn-outline-primary">Tahun</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="borrowingChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Statistik Ringkasan -->
        <div class="col-xl-4">
            <div class="glass-card p-4">
                <h5 class="mb-4">
                    <i class="bi bi-pie-chart text-primary me-2"></i>
                    Ringkasan Statistik
                </h5>
                <div class="vstack gap-3">
                    <div class="p-3 border rounded-3 bg-light">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Total Buku</small>
                            <small class="text-primary fw-bold">{{ $totalBooks }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="p-3 border rounded-3 bg-light">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Buku Dipinjam</small>
                            <small class="text-warning fw-bold">{{ $totalBorrowed }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ $totalBooks > 0 ? ($totalBorrowed/$totalBooks)*100 : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="p-3 border rounded-3 bg-light">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">User Aktif</small>
                            <small class="text-success fw-bold">{{ $activeUsers }} / {{ $totalUsers }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalUsers > 0 ? ($activeUsers/$totalUsers)*100 : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="p-3 border rounded-3 bg-light">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Supplier Aktif</small>
                            <small class="text-info fw-bold">{{ $activeSuppliers }} / {{ $totalSuppliers }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ $totalSuppliers > 0 ? ($activeSuppliers/$totalSuppliers)*100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Buku Terpopuler -->
                <div class="mt-4">
                    <h5 class="mb-3">
                        <i class="bi bi-trophy text-warning me-2"></i>
                        Buku Terpopuler
                    </h5>
                    <div class="vstack gap-2">
                        @if(isset($popularBooks) && count($popularBooks) > 0)
                            @foreach($popularBooks as $index => $book)
                            <div class="d-flex align-items-center gap-2 p-2 border rounded-3">
                                <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }} rounded-circle p-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $book->title }}</h6>
                                    <small class="text-muted">{{ $book->author }}</small>
                                </div>
                                <span class="badge bg-primary">{{ $book->borrowed_count }}x</span>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-book fs-1 d-block mb-2 text-muted"></i>
                                <p class="text-muted">Belum ada data buku populer</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Tables -->
    <div class="row g-4 mb-4">
        <!-- Peminjaman Terbaru -->
        <div class="col-xl-6">
            <div class="glass-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history text-primary me-2"></i>
                            Peminjaman Terbaru
                            <span class="badge bg-soft-primary ms-2">{{ $recentBorrowings->count() }}</span>
                        </h5>
                        <a href="{{ route('admin.borrowings.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="activity-timeline">
                        @forelse($recentBorrowings as $borrowing)
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <a href="{{ route('admin.borrowings.show', $borrowing->id) }}" class="text-decoration-none">
                                            {{ $borrowing->borrowing_number }}
                                        </a>
                                    </h6>
                                    <p class="mb-1 text-muted small">
                                        <i class="bi bi-person me-1"></i>{{ $borrowing->user->name }}
                                    </p>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>{{ $borrowing->created_at->format('d M Y H:i') }}
                                    </small>
                                </div>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'borrowed' => 'primary',
                                        'returned' => 'success',
                                        'overdue' => 'danger',
                                        'cancelled' => 'secondary'
                                    ];
                                    $statusColor = $statusColors[$borrowing->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-soft-{{ $statusColor }}">
                                    {{ ucfirst($borrowing->status) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                            <p class="text-muted">Belum ada peminjaman</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengadaan Terbaru -->
        <div class="col-xl-6">
            <div class="glass-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-cart-plus text-success me-2"></i>
                            Pengadaan Terbaru
                            <span class="badge bg-soft-success ms-2">{{ $recentProcurements->count() }}</span>
                        </h5>
                        <a href="{{ route('admin.procurements.index') }}" class="btn btn-sm btn-outline-success">
                            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No. PO</th>
                                    <th>Supplier</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentProcurements as $procurement)
                                <tr>
                                    <td>
                                        <strong>{{ $procurement->procurement_number }}</strong>
                                    </td>
                                    <td>{{ $procurement->vendor->name ?? '-' }}</td>
                                    <td>{{ $procurement->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $procurementBadges = [
                                                'pending' => 'bg-soft-warning',
                                                'ordered' => 'bg-soft-info',
                                                'partial' => 'bg-soft-primary',
                                                'completed' => 'bg-soft-success',
                                                'cancelled' => 'bg-soft-danger'
                                            ];
                                            $badgeClass = $procurementBadges[$procurement->status] ?? 'bg-soft-secondary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ ucfirst($procurement->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.procurements.show', $procurement->id) }}" 
                                           class="btn btn-sm btn-link text-primary p-0">
                                            <i class="bi bi-eye fs-5"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                                        <p class="text-muted">Belum ada pengadaan</p>
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

    <!-- Row 5: Low Stock and Requests -->
    <div class="row g-4">
        <!-- Buku Stok Menipis -->
        <div class="col-xl-6">
            <div class="glass-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-danger">
                            <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                            Buku Stok Menipis (≤ 5)
                            <span class="badge bg-soft-danger ms-2">{{ $lowStockBooks->count() }}</span>
                        </h5>
                        <a href="{{ route('admin.books.index', ['availability' => 'low']) }}" class="btn btn-sm btn-outline-danger">
                            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        @forelse($lowStockBooks as $book)
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light position-relative">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ Str::limit($book->title, 30) }}</h6>
                                        <small class="text-muted">{{ $book->author }}</small>
                                    </div>
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                        {{ $book->available_stock }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-4">
                            <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-3"></i>
                            <p class="text-muted">Semua stok buku aman</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Buku Terbaru -->
        <div class="col-xl-6">
            <div class="glass-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-warning">
                            <i class="bi bi-envelope text-warning me-2"></i>
                            Request Buku Terbaru
                            <span class="badge bg-soft-warning ms-2">{{ $recentRequests->count() }}</span>
                        </h5>
                        <a href="{{ route('admin.reports.books') }}" class="btn btn-sm btn-outline-warning">
                            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        @forelse($recentRequests as $request)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $request->book_title }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-person me-1"></i>{{ $request->user->name }}
                                        <i class="bi bi-calendar ms-2 me-1"></i>{{ $request->created_at->format('d M Y') }}
                                    </small>
                                </div>
                                <span class="badge bg-soft-{{ $request->status == 'pending' ? 'warning' : 'success' }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                            <p class="text-muted">Belum ada request buku</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    @if(isset($activities) && count($activities) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="mb-4">
                    <i class="bi bi-activity text-primary me-2"></i>
                    Aktivitas Terbaru
                </h5>
                <div class="row g-3">
                    @foreach($activities as $activity)
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3 p-3 border rounded-3">
                            <div class="bg-soft-{{ $activity->type }} rounded-circle p-3">
                                <i class="bi bi-{{ $activity->icon }}"></i>
                            </div>
                            <div>
                                <p class="mb-1">{{ $activity->description }}</p>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Grafik Peminjaman
        const ctx = document.getElementById('borrowingChart')?.getContext('2d');
        
        if (ctx) {
            // Data contoh (ganti dengan data dari controller)
            const labels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            const data = [5, 8, 12, 7, 15, 10, 6];

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Peminjaman',
                        data: data,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.4,
                        fill: true
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

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush