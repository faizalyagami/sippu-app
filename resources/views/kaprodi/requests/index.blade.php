{{-- resources/views/kaprodi/requests/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Request Buku')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-envelope text-primary"></i> 
            Request Buku
        </h4>
        <a href="{{ route('kaprodi.requests.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Request Buku Baru
        </a>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-envelope fs-4 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Total Request</h6>
                        <h4 class="mb-0">{{ $statistics['total'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Menunggu</h6>
                        <h4 class="mb-0">{{ $statistics['pending'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-check-circle fs-4 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Disetujui</h6>
                        <h4 class="mb-0">{{ $statistics['approved'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="glass-card p-4 mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control bg-light border-0" 
                           placeholder="Cari judul buku..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-light border-0">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="procured" {{ request('status') == 'procured' ? 'selected' : '' }}>Tersedia</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select bg-light border-0">
                    <option value="">Semua Prioritas</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Requests Table -->
    <div class="glass-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>No. Request</th>
                        <th>Judul Buku</th>
                        <th>Penulis</th>
                        <th>Prioritas</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $request->request_number }}</strong>
                        </td>
                        <td>
                            <strong>{{ $request->book_title }}</strong>
                            @if($request->isbn)
                                <br><small class="text-muted">ISBN: {{ $request->isbn }}</small>
                            @endif
                        </td>
                        <td>{{ $request->author ?? '-' }}</td>
                        <td>
                            @php
                                $priorityClasses = [
                                    'low' => 'bg-secondary',
                                    'medium' => 'bg-info',
                                    'high' => 'bg-warning',
                                    'urgent' => 'bg-danger'
                                ];
                                $priorityTexts = [
                                    'low' => 'Rendah',
                                    'medium' => 'Sedang',
                                    'high' => 'Tinggi',
                                    'urgent' => 'Urgent'
                                ];
                            @endphp
                            <span class="badge {{ $priorityClasses[$request->priority] }}">
                                {{ $priorityTexts[$request->priority] }}
                            </span>
                        </td>
                        <td class="text-center">{{ $request->quantity_requested }}</td>
                        <td>{{ $request->created_at->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-warning',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'procured' => 'bg-info',
                                    'cancelled' => 'bg-secondary'
                                ];
                                $statusTexts = [
                                    'pending' => 'Menunggu',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    'procured' => 'Tersedia',
                                    'cancelled' => 'Dibatalkan'
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$request->status] }}">
                                {{ $statusTexts[$request->status] }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('kaprodi.requests.show', $request->id) }}" 
                                   class="btn btn-sm btn-outline-info" 
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($request->status == 'pending')
                                    <a href="{{ route('kaprodi.requests.edit', $request->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="cancelRequest({{ $request->id }})"
                                            title="Batalkan">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                            <h5 class="text-muted">Tidak ada request buku</h5>
                            @if(request('search') || request('status') || request('priority'))
                                <p class="text-muted">Coba reset filter Anda</p>
                                <a href="{{ route('kaprodi.requests.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                                </a>
                            @else
                                <a href="{{ route('kaprodi.requests.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle"></i> Request Buku Baru
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($requests->hasPages())
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <small class="text-muted">
                Menampilkan {{ $requests->firstItem() ?? 0 }} - {{ $requests->lastItem() ?? 0 }} 
                dari {{ $requests->total() }} data
            </small>
            {{ $requests->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function cancelRequest(id) {
    if (confirm('Yakin ingin membatalkan request ini?')) {
        fetch(`/kaprodi/requests/${id}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Gagal membatalkan request');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan jaringan');
        });
    }
}

// Auto submit filter
document.querySelector('select[name="status"]').addEventListener('change', function() {
    this.form.submit();
});

document.querySelector('select[name="priority"]').addEventListener('change', function() {
    this.form.submit();
});
</script>
@endsection