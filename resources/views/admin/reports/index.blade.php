{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">
        <i class="bi bi-file-text text-primary"></i> Laporan
    </h4>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-book fs-1 text-primary"></i>
                    <h5 class="card-title mt-3">Laporan Buku</h5>
                    <p class="card-text text-muted">
                        Lihat laporan data buku, stok, dan kondisi buku
                    </p>
                    <a href="{{ route('admin.reports.books') }}" class="btn btn-primary">
                        <i class="bi bi-eye"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-left-right fs-1 text-success"></i>
                    <h5 class="card-title mt-3">Laporan Peminjaman</h5>
                    <p class="card-text text-muted">
                        Lihat laporan peminjaman, pengembalian, dan denda
                    </p>
                    <a href="{{ route('admin.reports.borrowings') }}" class="btn btn-success">
                        <i class="bi bi-eye"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cart-plus fs-1 text-warning"></i>
                    <h5 class="card-title mt-3">Laporan Pengadaan</h5>
                    <p class="card-text text-muted">
                        Lihat laporan pengadaan buku dan supplier
                    </p>
                    <a href="{{ route('admin.reports.procurements') }}" class="btn btn-warning">
                        <i class="bi bi-eye"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 text-info"></i>
                    <h5 class="card-title mt-3">Laporan User</h5>
                    <p class="card-text text-muted">
                        Lihat laporan data user dan aktivitas
                    </p>
                    <a href="{{ route('admin.reports.users') }}" class="btn btn-info">
                        <i class="bi bi-eye"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-tags fs-1 text-secondary"></i>
                    <h5 class="card-title mt-3">Laporan Kategori</h5>
                    <p class="card-text text-muted">
                        Lihat laporan berdasarkan kategori buku
                    </p>
                    <a href="{{ route('admin.reports.categories') }}" class="btn btn-secondary">
                        <i class="bi bi-eye"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-date fs-1 text-danger"></i>
                    <h5 class="card-title mt-3">Laporan Bulanan</h5>
                    <p class="card-text text-muted">
                        Lihat laporan rekap bulanan
                    </p>
                    <a href="{{ route('admin.reports.monthly') }}" class="btn btn-danger">
                        <i class="bi bi-eye"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection