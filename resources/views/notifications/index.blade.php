{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-bell text-primary me-2"></i>
            Notifikasi
        </h4>
        <div>
            @if($statistics['unread'] > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary me-2" onclick="return confirm('Tandai semua notifikasi sebagai sudah dibaca?')">
                    <i class="bi bi-check2-all me-1"></i> Tandai Semua Dibaca
                </button>
            </form>
            @endif
            <form action="{{ route('notifications.destroy-all') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Hapus semua notifikasi?')">
                    <i class="bi bi-trash me-1"></i> Hapus Semua
                </button>
            </form>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="bi bi-bell fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Notifikasi</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['total'] }}</h3>
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
                            <i class="bi bi-envelope fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Belum Dibaca</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['unread'] }}</h3>
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
                            <h6 class="text-muted mb-1">Sudah Dibaca</h6>
                            <h3 class="mb-0 fw-bold">{{ $statistics['read'] }}</h3>
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
                <div class="col-md-9">
                    <label class="form-label">Filter Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Notifikasi</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
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

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-list-ul text-primary me-2"></i>
                Daftar Notifikasi
            </h5>
        </div>
        <div class="card-body p-0">
            @if($notifications->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-bell-slash fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">Tidak ada notifikasi</h5>
                <p class="text-muted mb-0">Notifikasi akan muncul di sini ketika ada aktivitas</p>
            </div>
            @else
            <div class="list-group list-group-flush">
                @foreach($notifications as $notification)
                <div class="list-group-item p-4 {{ !$notification->is_read ? 'bg-light' : '' }}">
                    <div class="d-flex">
                        <!-- Icon -->
                        <div class="flex-shrink-0 me-3">
                            @php
                                $icons = [
                                    'success' => 'bi-check-circle-fill text-success',
                                    'warning' => 'bi-exclamation-triangle-fill text-warning',
                                    'danger' => 'bi-x-circle-fill text-danger',
                                    'info' => 'bi-info-circle-fill text-info',
                                ];
                                $icon = $icons[$notification->type] ?? 'bi-bell-fill text-primary';
                            @endphp
                            <div class="bg-light rounded-circle p-3">
                                <i class="bi {{ $icon }} fs-4"></i>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-semibold mb-1">{{ $notification->title }}</h6>
                                    <p class="text-muted mb-2">{{ $notification->message }}</p>
                                </div>
                                <div class="text-end ms-3">
                                    <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                                    @if(!$notification->is_read)
                                    <span class="badge bg-warning bg-opacity-10 text-warning mt-2 px-3 py-2">
                                        Baru
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="d-flex gap-2">
                                @if(!$notification->is_read)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-check-circle me-1"></i> Tandai Dibaca
                                    </button>
                                </form>
                                @endif
                                
                                @if($notification->link)
                                <a href="{{ $notification->link }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye me-1"></i> Lihat Detail
                                </a>
                                @endif
                                
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus notifikasi ini?')">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Pagination -->
            @if($notifications->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top">
                <div class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $notifications->firstItem() ?? 0 }} - {{ $notifications->lastItem() ?? 0 }} 
                    dari {{ $notifications->total() }} data
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        @if($notifications->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link" aria-hidden="true">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $notifications->previousPageUrl() }}" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        @foreach($notifications->getUrlRange(max(1, $notifications->currentPage() - 2), min($notifications->lastPage(), $notifications->currentPage() + 2)) as $page => $url)
                            @if($page == $notifications->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        @if($notifications->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $notifications->nextPageUrl() }}" aria-label="Next">
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
                    Menampilkan {{ $notifications->firstItem() ?? 0 }} - {{ $notifications->lastItem() ?? 0 }} 
                    dari {{ $notifications->total() }} data
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 12px;
}

.list-group-item {
    transition: background-color 0.2s;
    border-left: 3px solid transparent;
}

.list-group-item.bg-light {
    border-left-color: #0d6efd;
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

@media (max-width: 768px) {
    .pagination .page-link {
        padding: 0.3rem 0.6rem;
    }
}
</style>
@endsection