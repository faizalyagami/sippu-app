@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('kaprodi.borrowings.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <div>
            <h4 class="mb-1 fw-semibold">
                <i class="bi bi-file-text text-primary me-2"></i>
                Detail Peminjaman
            </h4>
            <p class="text-muted small mb-0">
                <i class="bi bi-hash"></i> {{ $borrowing->borrowing_number }}
            </p>
        </div>
        <div class="ms-auto">
            @php
                $statusClasses = [
                    'pending' => ['bg-warning', 'text-dark', 'Menunggu'],
                    'approved' => ['bg-info', 'text-white', 'Disetujui'],
                    'borrowed' => ['bg-primary', 'text-white', 'Dipinjam'],
                    'returned' => ['bg-success', 'text-white', 'Dikembalikan'],
                    'overdue' => ['bg-danger', 'text-white', 'Terlambat'],
                    'cancelled' => ['bg-secondary', 'text-white', 'Dibatalkan']
                ];
                $status = $statusClasses[$borrowing->status] ?? ['bg-secondary', 'text-white', $borrowing->status];
            @endphp
            <span class="badge {{ $status[0] }} {{ $status[1] }} px-4 py-2 rounded-pill">
                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>
                Status: {{ $status[2] }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Informasi Peminjaman -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informasi Peminjaman
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="45%" class="text-muted">No. Peminjaman</td>
                            <td class="fw-semibold">{{ $borrowing->borrowing_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Pinjam</td>
                            <td>{{ $borrowing->borrowing_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tenggat Pengembalian</td>
                            <td>
                                {{ $borrowing->expected_return_date->format('d/m/Y') }}
                                @if($borrowing->status == 'borrowed' && $borrowing->expected_return_date < now())
                                    <span class="badge bg-danger bg-opacity-10 text-danger ms-2">Terlambat</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Kembali</td>
                            <td>
                                @if($borrowing->actual_return_date)
                                    {{ $borrowing->actual_return_date->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Buku</td>
                            <td><span class="fw-bold">{{ $borrowing->total_items }}</span> Buku</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Informasi Pemohon -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-person text-primary me-2"></i>
                        Informasi Pemohon
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; font-size: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            {{ strtoupper(substr($borrowing->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-1">{{ $borrowing->user->name }}</h6>
                            <p class="text-muted small mb-0">{{ $borrowing->user->email }}</p>
                        </div>
                    </div>
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">NIP</td>
                            <td>{{ $borrowing->user->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fakultas</td>
                            <td>{{ $borrowing->user->faculty ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Program Studi</td>
                            <td>{{ $borrowing->user->department ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Telepon</td>
                            <td>{{ $borrowing->user->phone_number ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Informasi Persetujuan -->
        @if($borrowing->approved_by)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Informasi Persetujuan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; font-size: 20px;">
                            {{ strtoupper(substr($borrowing->approvedBy->name, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-1">{{ $borrowing->approvedBy->name }}</h6>
                            <p class="text-muted small mb-0">Administrator</p>
                        </div>
                    </div>
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Tanggal Setuju</td>
                            <td>{{ $borrowing->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Catatan -->
        @if($borrowing->purpose || $borrowing->notes || $borrowing->rejection_reason)
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-chat-text text-primary me-2"></i>
                        Catatan
                    </h5>
                </div>
                <div class="card-body">
                    @if($borrowing->purpose)
                        <div class="mb-2">
                            <span class="fw-semibold">Tujuan:</span>
                            <p class="mb-0 mt-1">{{ $borrowing->purpose }}</p>
                        </div>
                    @endif
                    @if($borrowing->notes)
                        <div class="mb-2">
                            <span class="fw-semibold">Catatan:</span>
                            <p class="mb-0 mt-1">{{ $borrowing->notes }}</p>
                        </div>
                    @endif
                    @if($borrowing->rejection_reason)
                        <div class="mb-2">
                            <span class="fw-semibold text-danger">Alasan Ditolak:</span>
                            <p class="mb-0 mt-1 text-danger">{{ $borrowing->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Daftar Buku -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-book text-primary me-2"></i>
                        Daftar Buku
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3" width="5%">#</th>
                                    <th class="px-4 py-3" width="35%">Judul Buku</th>
                                    <th class="px-4 py-3" width="20%">Penulis</th>
                                    <th class="px-4 py-3" width="15%">ISBN</th>
                                    <th class="px-4 py-3 text-center" width="10%">Jumlah</th>
                                    <th class="px-4 py-3 text-center" width="15%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($borrowing->items as $item)
                                <tr>
                                    <td class="px-4">{{ $loop->iteration }}</td>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-2">
                                                <i class="bi bi-book text-primary"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $item->book->title }}</strong>
                                                @if($item->book->publisher)
                                                    <br>
                                                    <small class="text-muted">{{ $item->book->publisher }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4">{{ $item->book->author }}</td>
                                    <td class="px-4"><small>{{ $item->book->isbn ?? '-' }}</small></td>
                                    <td class="px-4 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 text-center">
                                        @php
                                            $itemStatusClasses = [
                                                'borrowed' => ['bg-primary', 'text-white', 'Dipinjam'],
                                                'partial' => ['bg-warning', 'text-dark', 'Sebagian'],
                                                'returned' => ['bg-success', 'text-white', 'Dikembalikan'],
                                                'damaged' => ['bg-danger', 'text-white', 'Rusak'],
                                                'lost' => ['bg-dark', 'text-white', 'Hilang']
                                            ];
                                            $itemStatus = $itemStatusClasses[$item->status] ?? ['bg-secondary', 'text-white', $item->status];
                                        @endphp
                                        <span class="badge {{ $itemStatus[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $itemStatus[0]) }} px-3 py-2">
                                            {{ $itemStatus[2] }}
                                        </span>
                                        @if($item->returned_quantity > 0)
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="bi bi-check-circle"></i> Dikembalikan: {{ $item->returned_quantity }}
                                                </small>
                                            </div>
                                        @endif
                                        @if($item->damaged_quantity > 0)
                                            <div class="mt-1">
                                                <small class="text-danger">
                                                    <i class="bi bi-exclamation-triangle"></i> Rusak: {{ $item->damaged_quantity }}
                                                </small>
                                            </div>
                                        @endif
                                        @if($item->lost_quantity > 0)
                                            <div class="mt-1">
                                                <small class="text-dark">
                                                    <i class="bi bi-question-circle"></i> Hilang: {{ $item->lost_quantity }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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

.table td {
    vertical-align: middle;
    padding: 0.75rem 0;
}

.badge {
    font-weight: 500;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.bg-primary.bg-opacity-10 {
    color: #0d6efd !important;
}

.bg-success.bg-opacity-10 {
    color: #198754 !important;
}

.bg-warning.bg-opacity-10 {
    color: #ffc107 !important;
}

.bg-danger.bg-opacity-10 {
    color: #dc3545 !important;
}

.bg-dark.bg-opacity-10 {
    color: #212529 !important;
}

/* Gradient avatar */
.bg-primary.rounded-circle {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

@media (max-width: 768px) {
    .table {
        font-size: 0.9rem;
    }
}
</style>
@endsection