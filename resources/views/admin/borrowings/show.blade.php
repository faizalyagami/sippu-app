@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.borrowings.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Peminjaman
        </a>
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-file-text text-primary me-2"></i>
            Detail Peminjaman: {{ $borrowing->borrowing_number }}
        </h4>
        <div class="ms-auto">
            @if($borrowing->status == 'pending')
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="bi bi-check-circle me-1"></i> Setujui
                </button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle me-1"></i> Tolak
                </button>
            @endif
            @if($borrowing->status == 'approved')
                <form action="{{ route('admin.borrowings.mark-borrowed', $borrowing->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Tandai bahwa buku sudah diambil?')">
                        <i class="bi bi-arrow-right-circle me-1"></i> Tandai Dipinjam
                    </button>
                </form>
            @endif
            @if($borrowing->status == 'borrowed')
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#returnModal">
                    <i class="bi bi-arrow-left-circle me-1"></i> Proses Pengembalian
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Informasi Peminjaman -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-info-circle me-2"></i>
                        Informasi Peminjaman
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">No. Peminjaman</td>
                            <td class="fw-semibold">{{ $borrowing->borrowing_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                @php
                                    $badges = [
                                        'pending' => ['bg-warning', 'Menunggu'],
                                        'approved' => ['bg-info', 'Disetujui'],
                                        'borrowed' => ['bg-primary', 'Dipinjam'],
                                        'returned' => ['bg-success', 'Dikembalikan'],
                                        'overdue' => ['bg-danger', 'Terlambat'],
                                        'cancelled' => ['bg-secondary', 'Dibatalkan']
                                    ];
                                    $badge = $badges[$borrowing->status] ?? ['bg-secondary', $borrowing->status];
                                @endphp
                                <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                                    {{ $badge[1] }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Pinjam</td>
                            <td>{{ $borrowing->borrowing_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tenggat</td>
                            <td>
                                {{ $borrowing->expected_return_date->format('d/m/Y') }}
                                @if($borrowing->status == 'borrowed' && $borrowing->expected_return_date < now())
                                    <br>
                                    <span class="badge bg-danger bg-opacity-10 text-danger mt-1">Terlambat</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Kembali</td>
                            <td>{{ $borrowing->actual_return_date ? $borrowing->actual_return_date->format('d/m/Y') : '-' }}</td>
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
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-person me-2"></i>
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
                            <td class="text-muted">Program Studi</td>
                            <td>{{ $borrowing->user->department ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>{{ $borrowing->user->email }}</td>
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
        <div class="col-md-4 mb-4">
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

    <!-- Catatan -->
    @if($borrowing->purpose || $borrowing->notes || $borrowing->rejection_reason)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-chat-text me-2"></i>
                        Catatan
                    </h5>
                </div>
                <div class="card-body">
                    @if($borrowing->purpose)
                        <p><strong>Tujuan:</strong> {{ $borrowing->purpose }}</p>
                    @endif
                    @if($borrowing->notes)
                        <p><strong>Catatan:</strong> {{ $borrowing->notes }}</p>
                    @endif
                    @if($borrowing->rejection_reason)
                        <p class="text-danger"><strong>Alasan Ditolak:</strong> {{ $borrowing->rejection_reason }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Daftar Buku -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-book me-2"></i>
                        Daftar Buku
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Judul Buku</th>
                                    <th>Penulis</th>
                                    <th>Penerbit</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($borrowing->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $item->book->title }}</strong>
                                        @if($item->book->isbn)
                                            <br><small class="text-muted">ISBN: {{ $item->book->isbn }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->book->author }}</td>
                                    <td>{{ $item->book->publisher }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td>
                                        @php
                                            $itemStatus = [
                                                'borrowed' => ['bg-primary', 'Dipinjam'],
                                                'partial' => ['bg-warning', 'Sebagian'],
                                                'returned' => ['bg-success', 'Dikembalikan'],
                                                'damaged' => ['bg-danger', 'Rusak'],
                                                'lost' => ['bg-dark', 'Hilang']
                                            ];
                                            $status = $itemStatus[$item->status] ?? ['bg-secondary', $item->status];
                                        @endphp
                                        <span class="badge {{ $status[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $status[0]) }} px-3 py-2">
                                            {{ $status[1] }}
                                        </span>
                                        @if($item->returned_quantity > 0)
                                            <br><small>Dikembalikan: {{ $item->returned_quantity }}</small>
                                        @endif
                                        @if($item->damaged_quantity > 0)
                                            <br><small class="text-danger">Rusak: {{ $item->damaged_quantity }}</small>
                                        @endif
                                        @if($item->lost_quantity > 0)
                                            <br><small class="text-dark">Hilang: {{ $item->lost_quantity }}</small>
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
                <h5 class="modal-title">Setujui Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.borrowings.approve', $borrowing->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Yakin ingin menyetujui peminjaman ini?</p>
                    <p><strong>No. Peminjaman:</strong> {{ $borrowing->borrowing_number }}</p>
                    <p><strong>Peminjam:</strong> {{ $borrowing->user->name }}</p>
                    <p><strong>Jumlah Buku:</strong> {{ $borrowing->total_items }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
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
                <h5 class="modal-title">Tolak Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.borrowings.reject', $borrowing->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Yakin ingin menolak peminjaman ini?</p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Return -->
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Pengembalian Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.borrowings.return', $borrowing->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Periksa kondisi setiap buku yang dikembalikan:</p>
                    
                    @foreach($borrowing->items as $item)
                    <div class="card mb-3 border">
                        <div class="card-body">
                            <h6 class="fw-semibold">{{ $item->book->title }}</h6>
                            <p class="small text-muted mb-2">Jumlah dipinjam: {{ $item->quantity }}</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Kondisi</label>
                                    <select name="items[{{ $item->id }}][condition]" class="form-select" required>
                                        <option value="good">Baik</option>
                                        <option value="damaged">Rusak</option>
                                        <option value="lost">Hilang</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Catatan</label>
                                    <input type="text" name="items[{{ $item->id }}][notes]" class="form-control" placeholder="Catatan (opsional)">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Proses Pengembalian</button>
                </div>
            </form>
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
}

.badge {
    font-weight: 500;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.modal-content {
    border-radius: 16px;
}

.modal-header {
    border-bottom: 1px solid rgba(0,0,0,0.05);
    border-radius: 16px 16px 0 0;
}

.modal-footer {
    border-top: 1px solid rgba(0,0,0,0.05);
    border-radius: 0 0 16px 16px;
}
</style>
@endsection