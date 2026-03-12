@extends('layouts.app')

@section('title', 'Peminjaman Buku')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-arrow-left-right text-primary me-2"></i>
            Peminjaman Buku
        </h4>
    </div>

    <!-- Statistik Cards Modern -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-book fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Peminjaman</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['total'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-clock-history fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Menunggu</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['pending'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-arrow-left-right fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Aktif</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['active'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Terlambat</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['overdue'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Cari</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="No. Pinjam / Peminjam"
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Peminjam</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua Peminjam</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Peminjaman -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-table text-primary me-2"></i>
                Daftar Peminjaman
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="5%">#</th>
                            <th class="px-4 py-3" width="12%">No. Pinjam</th>
                            <th class="px-4 py-3" width="15%">Peminjam</th>
                            <th class="px-4 py-3" width="10%">Tgl Pinjam</th>
                            <th class="px-4 py-3" width="10%">Tenggat</th>
                            <th class="px-4 py-3 text-center" width="8%">Jumlah</th>
                            <th class="px-4 py-3 text-center" width="10%">Status</th>
                            <th class="px-4 py-3 text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($borrowings as $borrowing)
                        <tr>
                            <td class="px-4">{{ $loop->iteration }}</td>
                            <td class="px-4">
                                <strong>{{ $borrowing->borrowing_number }}</strong>
                            </td>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <strong>{{ $borrowing->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $borrowing->user->faculty ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4">{{ $borrowing->borrowing_date->format('d/m/Y') }}</td>
                            <td class="px-4">
                                {{ $borrowing->expected_return_date->format('d/m/Y') }}
                                @if($borrowing->status == 'borrowed' && $borrowing->expected_return_date < now())
                                    <br>
                                    <span class="badge bg-danger bg-opacity-10 text-danger mt-1">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Terlambat
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 text-center">{{ $borrowing->total_items }}</td>
                            <td class="px-4 text-center">
                                @php
                                    $badges = [
                                        'pending' => ['bg-warning', 'Pending'],
                                        'approved' => ['bg-info', 'Approved'],
                                        'borrowed' => ['bg-primary', 'Borrowed'],
                                        'returned' => ['bg-success', 'Returned'],
                                        'overdue' => ['bg-danger', 'Overdue'],
                                        'cancelled' => ['bg-secondary', 'Cancelled']
                                    ];
                                    $badge = $badges[$borrowing->status] ?? ['bg-secondary', ucfirst($borrowing->status)];
                                @endphp
                                <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                                    {{ $badge[1] }}
                                </span>
                            </td>
                            <td class="px-4 text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.borrowings.show', $borrowing->id) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @if($borrowing->status == 'pending')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal{{ $borrowing->id }}"
                                                title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $borrowing->id }}"
                                                title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                    
                                    @if($borrowing->status == 'approved')
                                        <form action="{{ route('admin.borrowings.mark-borrowed', $borrowing->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    onclick="return confirm('Tandai bahwa buku sudah diambil?')"
                                                    title="Tandai Dipinjam">
                                                <i class="bi bi-arrow-right-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($borrowing->status == 'borrowed')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#returnModal{{ $borrowing->id }}"
                                                title="Proses Pengembalian">
                                            <i class="bi bi-arrow-left-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Approve -->
                        <div class="modal fade" id="approveModal{{ $borrowing->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-semibold">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            Setujui Peminjaman
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.borrowings.approve', $borrowing->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <p>Yakin ingin menyetujui peminjaman ini?</p>
                                            <div class="bg-light p-3 rounded-3">
                                                <p class="mb-1"><strong>No. Peminjaman:</strong> {{ $borrowing->borrowing_number }}</p>
                                                <p class="mb-1"><strong>Peminjam:</strong> {{ $borrowing->user->name }}</p>
                                                <p class="mb-0"><strong>Jumlah Buku:</strong> {{ $borrowing->total_items }}</p>
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
                        <div class="modal fade" id="rejectModal{{ $borrowing->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-semibold">
                                            <i class="bi bi-x-circle text-danger me-2"></i>
                                            Tolak Peminjaman
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.borrowings.reject', $borrowing->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <p class="mb-3">Yakin ingin menolak peminjaman ini?</p>
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
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted">Tidak ada data peminjaman</h5>
                                @if(request('search') || request('status') || request('user_id'))
                                    <p class="text-muted mt-2">Coba atur ulang filter Anda</p>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($borrowings->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top">
                <div class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $borrowings->firstItem() ?? 0 }} - {{ $borrowings->lastItem() ?? 0 }} 
                    dari {{ $borrowings->total() }} data
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        @if($borrowings->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link" aria-hidden="true">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $borrowings->previousPageUrl() }}" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        @foreach($borrowings->getUrlRange(max(1, $borrowings->currentPage() - 2), min($borrowings->lastPage(), $borrowings->currentPage() + 2)) as $page => $url)
                            @if($page == $borrowings->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        @if($borrowings->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $borrowings->nextPageUrl() }}" aria-label="Next">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link" aria-hidden="true">
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @else
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                <div class="text-muted small">
                    Menampilkan {{ $borrowings->firstItem() ?? 0 }} - {{ $borrowings->lastItem() ?? 0 }} 
                    dari {{ $borrowings->total() }} data
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Return (Satu untuk semua, akan diisi dinamis) -->
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-arrow-left-circle text-warning me-2"></i>
                    Proses Pengembalian Buku
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="returnForm">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Periksa kondisi setiap buku yang dikembalikan:</p>
                    <div id="returnItemsContainer">
                        <!-- Items will be loaded here via JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle me-1"></i> Proses Pengembalian
                    </button>
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

.table th {
    font-weight: 600;
    color: #495057;
    white-space: nowrap;
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

.pagination {
    gap: 2px;
}

.pagination .page-link {
    border: none;
    color: #6c757d;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    color: white;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    color: #0d6efd;
}

.pagination .page-item.disabled .page-link {
    background-color: transparent;
    color: #adb5bd;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
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

@media (max-width: 768px) {
    .table {
        font-size: 0.9rem;
    }
    
    .pagination .page-link {
        padding: 0.3rem 0.6rem;
    }
}
</style>

<script>
function openReturnModal(borrowingId, items) {
    const modal = new bootstrap.Modal(document.getElementById('returnModal'));
    const form = document.getElementById('returnForm');
    const container = document.getElementById('returnItemsContainer');
    
    form.action = `/admin/borrowings/${borrowingId}/return`;
    
    let html = '';
    items.forEach(item => {
        html += `
            <div class="card mb-3 border">
                <div class="card-body">
                    <h6 class="fw-semibold">${item.book.title}</h6>
                    <p class="small text-muted mb-2">Jumlah dipinjam: ${item.quantity}</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Kondisi</label>
                            <select name="items[${item.id}][condition]" class="form-select" required>
                                <option value="good">Baik</option>
                                <option value="damaged">Rusak</option>
                                <option value="lost">Hilang</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="items[${item.id}][notes]" class="form-control" placeholder="Catatan (opsional)">
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    modal.show();
}
</script>
@endsection