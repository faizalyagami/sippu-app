{{-- resources/views/kaprodi/borrowings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-clock-history text-primary me-2"></i>
            Riwayat Peminjaman
        </h4>
        <a href="{{ route('kaprodi.borrowings.checkout') }}" class="btn btn-primary">
            <i class="bi bi-cart-plus me-1"></i> Pinjam Buku Baru
        </a>
    </div>

    <!-- Statistik Cards -->
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
                            <h3 class="mb-0 fw-bold">{{ $totalBorrowings }}</h3>
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
                            <h3 class="mb-0 fw-bold">{{ $activeBorrowings }}</h3>
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
                            <h3 class="mb-0 fw-bold">{{ $pendingBorrowings }}</h3>
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
                            <h3 class="mb-0 fw-bold">{{ $overdueBorrowings }}</h3>
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
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="Cari No. Peminjaman..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="date_range" class="form-select">
                        <option value="">Semua Waktu</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Borrowings Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">No. Peminjaman</th>
                            <th class="px-4 py-3">Tanggal Pinjam</th>
                            <th class="px-4 py-3">Tenggat</th>
                            <th class="px-4 py-3">Jumlah Buku</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($borrowings as $borrowing)
                        <tr>
                            <td class="px-4">{{ $loop->iteration }}</td>
                            <td class="px-4">
                                <strong>{{ $borrowing->borrowing_number }}</strong>
                            </td>
                            <td class="px-4">{{ $borrowing->borrowing_date->format('d/m/Y') }}</td>
                            <td class="px-4">
                                {{ $borrowing->expected_return_date->format('d/m/Y') }}
                                @if($borrowing->status == 'borrowed' && $borrowing->expected_return_date < now())
                                    <br>
                                    <span class="badge bg-danger bg-opacity-10 text-danger mt-1">Terlambat</span>
                                @endif
                            </td>
                            <td class="px-4 text-center">{{ $borrowing->total_items }}</td>
                            <td class="px-4">
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
                            <td class="px-4 text-center">
                                <div class="btn-group">
                                    <a href="{{ route('kaprodi.borrowings.show', $borrowing->id) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($borrowing->status == 'pending')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="cancelBorrowing({{ $borrowing->id }})"
                                                title="Batalkan">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted mb-3">Tidak ada data peminjaman</h5>
                                <a href="{{ route('kaprodi.books.index') }}" class="btn btn-primary">
                                    <i class="bi bi-book me-2"></i> Lihat Katalog Buku
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top">
                <div class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $borrowings->firstItem() ?? 0 }} - {{ $borrowings->lastItem() ?? 0 }} 
                    dari {{ $borrowings->total() }} data
                </div>
                @if($borrowings->hasPages())
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
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function cancelBorrowing(id) {
    if (confirm('Yakin ingin membatalkan peminjaman ini?')) {
        fetch(`/kaprodi/borrowings/${id}/cancel`, {
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
                alert(data.message || 'Gagal membatalkan peminjaman');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan jaringan');
        });
    }
}

// Auto submit filter
document.querySelector('select[name="status"]')?.addEventListener('change', function() {
    this.form.submit();
});

document.querySelector('select[name="date_range"]')?.addEventListener('change', function() {
    this.form.submit();
});

// Search with debounce
let searchTimeout;
document.querySelector('input[name="search"]')?.addEventListener('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        this.form.submit();
    }, 500);
});
</script>

<style>
.card {
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08) !important;
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
}
</style>
@endsection