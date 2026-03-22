{{-- resources/views/admin/borrowings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Permintaan Masuk Koleksi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-inbox text-primary me-2"></i>
            Daftar Permintaan Masuk Koleksi
        </h4>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-envelope fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Permintaan</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['total'] ?? 0 }}</h3>
                            <small class="text-muted">Semua permintaan</small>
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
                            <small class="text-warning">Perlu diproses</small>
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
                            <i class="bi bi-check-circle fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Disetujui</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['approved'] ?? 0 }}</h3>
                            <small class="text-success">Siap diambil</small>
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
                            <i class="bi bi-x-circle fs-4 text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Tidak Tersedia</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['cancelled'] ?? 0 }}</h3>
                            <small class="text-danger">Ditolak / Dibatalkan</small>
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
                    <label class="form-label">Cari</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text"
                            name="search"
                            class="form-control border-start-0 ps-0"
                            placeholder="No. Permintaan / Peminjam"
                            value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Permintaan -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-table text-primary me-2"></i>
                Daftar Permintaan Masuk
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3" width="5%">#</th>
                            <th class="px-4 py-3" width="12%">No. Permintaan</th>
                            <th class="px-4 py-3" width="15%">Peminjam</th>
                            <th class="px-4 py-3" width="10%">Tanggal</th>
                            <th class="px-4 py-3" width="20%">Buku yang Diminta</th>
                            <th class="px-4 py-3 text-center" width="8%">Jumlah</th>
                            <th class="px-4 py-3 text-center" width="10%">Status</th>
                            <th class="px-4 py-3 text-center" width="10%">Aksi</th>
            </div>
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
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                style="width: 35px; height: 35px;">
                                {{ strtoupper(substr($borrowing->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <strong>{{ $borrowing->user->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $borrowing->user->faculty ?? '-' }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="px-4">{{ $borrowing->created_at->format('d/m/Y') }}</td>
                    <td class="px-4">
                        @foreach($borrowing->items as $item)
                        <div class="d-flex align-items-center mb-1">
                            <i class="bi bi-book text-primary me-1"></i>
                            <span>{{ $item->book->title }}</span>
                        </div>
                        @endforeach
                    </td>
                    <td class="px-4 text-center">{{ $borrowing->total_items }}</td>
                    <td class="px-4 text-center">
                        @php
                        $badges = [
                        'pending' => ['bg-warning', 'Menunggu'],
                        'approved' => ['bg-success', 'Disetujui'],
                        'cancelled' => ['bg-danger', 'Tidak Tersedia']
                        ];
                        $badge = $badges[$borrowing->status] ?? ['bg-secondary', $borrowing->status];
                        @endphp
                        <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                            {{ $badge[1] }}
                        </span>
                    </td>
                    <td class="px-4 text-center">
                        <a href="{{ route('admin.borrowings.show', $borrowing->id) }}"
                            class="btn btn-sm btn-outline-info"
                            title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        <h5 class="text-muted">Tidak ada permintaan masuk</h5>
                    </td>
                </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
            <div class="text-muted small">
                Menampilkan {{ $borrowings->firstItem() ?? 0 }} - {{ $borrowings->lastItem() ?? 0 }}
                dari {{ $borrowings->total() }} data
            </div>
            @if($borrowings->hasPages())
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if($borrowings->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">&laquo;</span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $borrowings->previousPageUrl() }}" rel="prev">&laquo;</a>
                    </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                    $start = max(1, $borrowings->currentPage() - 2);
                    $end = min($borrowings->lastPage(), $borrowings->currentPage() + 2);
                    @endphp

                    @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $borrowings->url(1) }}">1</a>
                    </li>
                    @if($start > 2)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                    @endif
                    @endif

                    @for($page = $start; $page <= $end; $page++)
                        @if($page==$borrowings->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                        @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $borrowings->url($page) }}">{{ $page }}</a>
                        </li>
                        @endif
                        @endfor

                        @if($end < $borrowings->lastPage())
                            @if($end < $borrowings->lastPage() - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $borrowings->url($borrowings->lastPage()) }}">{{ $borrowings->lastPage() }}</a>
                                </li>
                                @endif

                                {{-- Next Page Link --}}
                                @if($borrowings->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $borrowings->nextPageUrl() }}" rel="next">&raquo;</a>
                                </li>
                                @else
                                <li class="page-item disabled">
                                    <span class="page-link">&raquo;</span>
                                </li>
                                @endif
                </ul>
            </nav>
            @endif
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
        gap: 4px;
    }

    .pagination .page-link {
        border: none;
        color: #6c757d;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s;
        background-color: transparent;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
    }

    .pagination .page-link:hover:not(.active) {
        background-color: #e9ecef;
        color: #667eea;
    }

    .pagination .page-item.disabled .page-link {
        background-color: transparent;
        color: #adb5bd;
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
    }

    @media (max-width: 768px) {
        .table {
            font-size: 0.85rem;
        }

        .pagination .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .table th,
        .table td {
            padding: 0.5rem;
        }
    }
</style>
@endsection