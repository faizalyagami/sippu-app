{{-- resources/views/admin/borrowings/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Permintaan Koleksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.borrowings.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Permintaan
        </a>
        <div>
            <h4 class="mb-1 fw-semibold">
                <i class="bi bi-file-text text-primary me-2"></i>
                Detail Permintaan Koleksi
            </h4>
            <p class="text-muted small mb-0">
                <i class="bi bi-hash"></i> {{ $borrowing->borrowing_number }}
            </p>
        </div>
        <div class="ms-auto">
            @if($borrowing->status == 'pending')
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="bi bi-check-circle me-1"></i> Setujui
                </button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle me-1"></i> Tolak
                </button>
            @endif
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-4">
        @php
            $badges = [
                'pending' => ['bg-warning', 'Menunggu'],
                'approved' => ['bg-success', 'Disetujui'],
                'cancelled' => ['bg-danger', 'Ditolak']
            ];
            $badge = $badges[$borrowing->status] ?? ['bg-secondary', $borrowing->status];
        @endphp
        <div class="d-inline-block">
            <span class="badge {{ $badge[0] }} px-4 py-2 rounded-pill">
                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>
                Status: {{ $badge[1] }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Informasi Permintaan -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informasi Permintaan
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">No. Permintaan</td>
                            <td class="fw-semibold">{{ $borrowing->borrowing_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Permintaan</td>
                            <td>{{ $borrowing->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Buku</td>
                            <td>{{ $borrowing->total_items }} Buku</td>
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
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Nama</td>
                            <td class="fw-semibold">{{ $borrowing->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIP</td>
                            <td>{{ $borrowing->user->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fakultas</td>
                            <td>{{ $borrowing->user->faculty ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Prodi</td>
                            <td>{{ $borrowing->user->department ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>{{ $borrowing->user->email }}</td>
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
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Disetujui Oleh</td>
                            <td class="fw-semibold">{{ $borrowing->approvedBy->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal</td>
                            <td>{{ $borrowing->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Alasan Penolakan (Jika Ditolak) -->
    @if($borrowing->status == 'cancelled' && $borrowing->rejection_reason)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-x-circle me-2"></i>
                        Alasan Penolakan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $borrowing->rejection_reason }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tujuan Permintaan -->
    @if($borrowing->purpose)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-chat-text text-primary me-2"></i>
                        Tujuan Permintaan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $borrowing->purpose }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Daftar Buku yang Diminta -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-book text-primary me-2"></i>
                        Daftar Buku yang Diminta
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">#</th>
                                    <th class="px-4 py-3">Judul Buku</th>
                                    <th class="px-4 py-3">Penulis</th>
                                    <th class="px-4 py-3">Penerbit</th>
                                    <th class="px-4 py-3 text-center">Jumlah</th>
                                    <th class="px-4 py-3">Stok Tersedia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($borrowing->items as $item)
                                <tr>
                                    <td class="px-4">{{ $loop->iteration }}</td>
                                    <td class="px-4">
                                        <strong>{{ $item->book->title }}</strong>
                                        @if($item->book->isbn)
                                            <br><small class="text-muted">ISBN: {{ $item->book->isbn }}</small>
                                        @endif
                                    </td>
                                    <td class="px-4">{{ $item->book->author }}</td>
                                    <td class="px-4">{{ $item->book->publisher }}</td>
                                    <td class="px-4 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4">
                                        @if($item->book->available_stock >= $item->quantity)
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i> Tersedia ({{ $item->book->available_stock }})
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                                <i class="bi bi-exclamation-triangle me-1"></i> Stok Kurang ({{ $item->book->available_stock }})
                                            </span>
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

<!-- Modal Approve -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-check-circle text-success me-2"></i>
                    Setujui Permintaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.borrowings.approve', $borrowing->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Yakin ingin menyetujui permintaan ini?</p>
                    <div class="bg-light p-3 rounded-3">
                        <p class="mb-1"><strong>No. Permintaan:</strong> {{ $borrowing->borrowing_number }}</p>
                        <p class="mb-1"><strong>Pemohon:</strong> {{ $borrowing->user->name }}</p>
                        <p class="mb-0"><strong>Total Buku:</strong> {{ $borrowing->total_items }}</p>
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

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-x-circle text-danger me-2"></i>
                    Tolak Permintaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.borrowings.reject', $borrowing->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Yakin ingin menolak permintaan ini?</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i> Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection