{{-- resources/views/kaprodi/borrowings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Status Permintaan Saya')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-list-check text-primary me-2"></i>
            Status Permintaan Saya
        </h4>
        <a href="{{ route('kaprodi.borrowings.checkout') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Ajukan Permintaan Baru
        </a>
    </div>

    <!-- Statistik Sederhana -->
    <!-- <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-envelope fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Permintaan</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalBorrowings }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-clock-history fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Menunggu</h6>
                            <h3 class="mb-0 fw-bold">{{ $pendingBorrowings }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-check-circle fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Disetujui</h6>
                            <h3 class="mb-0 fw-bold">{{ $approvedBorrowings ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Tabel Permintaan -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-table text-primary me-2"></i>
                Daftar Permintaan Saya
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">No. Permintaan</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Buku yang Diminta</th>
                            <th class="px-4 py-3 text-center">Jumlah</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($borrowings as $borrowing)
                        <tr>
                            <td class="px-4">
                                <strong>{{ $borrowing->borrowing_number }}</strong>
                            </td>
                            <td class="px-4">{{ $borrowing->created_at->format('d/m/Y') }}</td>
                            <td class="px-4">
                                @foreach($borrowing->items as $item)
                                    <div>{{ $item->book->title }}</div>
                                @endforeach
                            </td>
                            <td class="px-4 text-center">{{ $borrowing->total_items }}</td>
                            <td class="px-4 text-center">
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
                            </td>
                            <td class="px-4 text-center">
                                <a href="{{ route('kaprodi.borrowings.show', $borrowing->id) }}" 
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                                @if($borrowing->status == 'pending')
                                    <form action="{{ route('kaprodi.borrowings.cancel', $borrowing->id) }}" 
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
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted">Belum ada permintaan</h5>
                                <a href="{{ route('kaprodi.borrowings.checkout') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle me-1"></i> Ajukan Permintaan Baru
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection