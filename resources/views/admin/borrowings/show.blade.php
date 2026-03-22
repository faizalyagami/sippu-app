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
                            <td width="45%" class="text-muted">No. Permintaan</td>
                            <td class="fw-semibold">{{ $borrowing->borrowing_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Permintaan</td>
                            <td>{{ \Carbon\Carbon::parse($borrowing->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pemohon</td>
                            <td>{{ $borrowing->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Buku Diminta</td>
                            <td>{{ $borrowing->total_items }} Buku</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tujuan</td>
                            <td>{{ $borrowing->purpose ?? '-' }}</td>
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
                            style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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

        <!-- Statistik Status Item -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-bar-chart text-primary me-2"></i>
                        Statistik Status Item
                    </h5>
                </div>
                <div class="card-body">
                    @php
                    $totalItems = $borrowing->items->count();
                    $pendingCount = $borrowing->items->where('status', 'pending')->count();
                    $approvedCount = $borrowing->items->where('status', 'approved')->count();
                    $rejectedCount = $borrowing->items->where('status', 'rejected')->count();
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Menunggu</span>
                            <span class="fw-semibold">{{ $pendingCount }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ $totalItems > 0 ? ($pendingCount/$totalItems)*100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Disetujui</span>
                            <span class="fw-semibold">{{ $approvedCount }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $totalItems > 0 ? ($approvedCount/$totalItems)*100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Ditolak</span>
                            <span class="fw-semibold">{{ $rejectedCount }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ $totalItems > 0 ? ($rejectedCount/$totalItems)*100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Buku yang Diminta (dengan tombol approve/reject per item) -->
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
                                    <th class="px-4 py-3" width="5%">#</th>
                                    <th class="px-4 py-3" width="25%">Judul Buku</th>
                                    <th class="px-4 py-3" width="15%">Penulis</th>
                                    <th class="px-4 py-3" width="15%">Penerbit</th>
                                    <th class="px-4 py-3 text-center" width="8%">Jumlah</th>
                                    <th class="px-4 py-3 text-center" width="10%">Stok Tersedia</th>
                                    <th class="px-4 py-3 text-center" width="12%">Status</th>
                                    <th class="px-4 py-3 text-center" width="15%">Aksi</th>
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
                                    <td class="px-4 text-center">
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
                                    <td class="px-4 text-center">
                                        @php
                                        $statusBadges = [
                                        'pending' => ['bg-warning', 'Menunggu'],
                                        'approved' => ['bg-success', 'Disetujui'],
                                        'rejected' => ['bg-danger', 'Ditolak']
                                        ];
                                        $status = $statusBadges[$item->status] ?? ['bg-secondary', $item->status];
                                        @endphp
                                        <span class="badge {{ $status[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $status[0]) }} px-3 py-2">
                                            {{ $status[1] }}
                                        </span>
                                    </td>
                                    <td class="px-4 text-center">
                                        @if($item->status == 'pending')
                                        <div class="btn-group">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-success"
                                                onclick="approveItem({{ $item->id }}, {{ $item->book->available_stock }}, {{ $item->quantity }})"
                                                title="Setujui"
                                                {{ $item->book->available_stock < $item->quantity ? 'disabled' : '' }}>
                                                <i class="bi bi-check-lg"></i> Setujui
                                            </button>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectItemModal{{ $item->id }}"
                                                title="Tolak">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </button>
                                        </div>
                                        @elseif($item->status == 'approved')
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                            <i class="bi bi-check-circle me-1"></i> Sudah Disetujui
                                        </span>
                                        @elseif($item->status == 'rejected')
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                            <i class="bi bi-x-circle me-1"></i> Ditolak
                                        </span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Modal Reject Item -->
                                <div class="modal fade" id="rejectItemModal{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-semibold">
                                                    <i class="bi bi-x-circle text-danger me-2"></i>
                                                    Tolak Permintaan Buku
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.borrowings.reject-item', $item->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>Tolak permintaan buku <strong>{{ $item->book->title }}</strong>?</p>
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
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
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

    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    .modal-content {
        border-radius: 16px;
    }

    .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 16px 16px 0 0;
    }

    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 0 0 16px 16px;
    }
</style>

<script>
    function approveItem(itemId, availableStock, requestedQuantity) {
        if (availableStock < requestedQuantity) {
            alert('Stok tidak mencukupi!');
            return;
        }

        if (confirm('Setujui permintaan buku ini?')) {
            fetch(`/admin/borrowings/items/${itemId}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal menyetujui permintaan');
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan jaringan');
                });
        }
    }
</script>
@endsection