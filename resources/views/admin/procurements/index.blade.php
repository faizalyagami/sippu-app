{{-- resources/views/admin/procurements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengadaan Buku')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-cart-plus text-primary"></i> 
            Pengadaan Buku
        </h4>
        <a href="{{ route('admin.procurements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Pengadaan Baru
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-cart-plus fs-4 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Total Pengadaan</h6>
                        <h4 class="mb-0">{{ $statistics['total'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Pending</h6>
                        <h4 class="mb-0">{{ $statistics['pending'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-check-circle fs-4 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Selesai</h6>
                        <h4 class="mb-0">{{ $statistics['completed'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-cash-stack fs-4 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Total Nilai</h6>
                        <h4 class="mb-0">Rp {{ number_format($statistics['total_value'] ?? 0, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="glass-card p-4 mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control bg-light border-0" 
                           placeholder="No. PO..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select bg-light border-0">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Supplier</label>
                <select name="vendor_id" class="form-select bg-light border-0">
                    <option value="">Semua Supplier</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>
            <div class="col-12 text-end">
                <a href="{{ route('admin.procurements.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Procurements Table -->
    <div class="glass-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>No. PO</th>
                        <th>Supplier</th>
                        <th>Tanggal PO</th>
                        <th>Tgl Terima</th>
                        <th>Total Item</th>
                        <th>Total Nilai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($procurements as $procurement)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $procurement->procurement_number }}</strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-soft-info rounded-circle p-2 me-2">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div>
                                    {{ $procurement->vendor->name ?? '-' }}
                                    <br>
                                    <small class="text-muted">{{ $procurement->vendor->company_name ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($procurement->procurement_date)->format('d/m/Y') }}</td>
                        <td>
                            @if($procurement->received_date)
                                {{ \Carbon\Carbon::parse($procurement->received_date)->format('d/m/Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $procurement->items_count ?? $procurement->items->count() }} item</span>
                        </td>
                        <td class="fw-bold">Rp {{ number_format($procurement->total_amount, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => ['bg-warning', 'Pending'],
                                    'ordered' => ['bg-info', 'Ordered'],
                                    'partial' => ['bg-primary', 'Partial'],
                                    'completed' => ['bg-success', 'Completed'],
                                    'cancelled' => ['bg-danger', 'Cancelled']
                                ];
                                $status = $statusColors[$procurement->status] ?? ['bg-secondary', $procurement->status];
                            @endphp
                            <span class="badge {{ $status[0] }}">{{ $status[1] }}</span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.procurements.show', $procurement->id) }}" 
                                   class="btn btn-sm btn-outline-info" 
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($procurement->status == 'pending')
                                    <a href="{{ route('admin.procurements.edit', $procurement->id) }}" 
                                       class="btn btn-sm btn-outline-warning" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                                @if(in_array($procurement->status, ['ordered', 'partial']))
                                    <a href="{{ route('admin.procurements.receive', $procurement->id) }}" 
                                       class="btn btn-sm btn-outline-success" 
                                       title="Terima Barang">
                                        <i class="bi bi-box-seam"></i>
                                    </a>
                                @endif
                                @if($procurement->status == 'completed')
                                    <a href="{{ route('admin.procurements.print', $procurement->id) }}" 
                                       class="btn btn-sm btn-outline-secondary" 
                                       title="Cetak PO" 
                                       target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                @endif
                                @if($procurement->status == 'pending')
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#cancelModal{{ $procurement->id }}"
                                            title="Batalkan">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Cancel -->
                    <div class="modal fade" id="cancelModal{{ $procurement->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.procurements.cancel', $procurement->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Batalkan Pengadaan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Yakin ingin membatalkan pengadaan <strong>{{ $procurement->procurement_number }}</strong>?</p>
                                        <div class="mb-3">
                                            <label class="form-label">Alasan Pembatalan</label>
                                            <textarea name="cancellation_reason" class="form-control" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-danger">Batalkan Pengadaan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                            <h5 class="text-muted">Tidak ada data pengadaan</h5>
                            @if(request('search') || request('status') || request('vendor_id'))
                                <p class="text-muted">Coba reset filter Anda</p>
                                <a href="{{ route('admin.procurements.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                                </a>
                            @else
                                <a href="{{ route('admin.procurements.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle"></i> Buat Pengadaan Baru
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($procurements->hasPages())
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <small class="text-muted">
                Menampilkan {{ $procurements->firstItem() ?? 0 }} - {{ $procurements->lastItem() ?? 0 }} 
                dari {{ $procurements->total() }} data
            </small>
            {{ $procurements->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-soft-info {
    background-color: rgba(13, 202, 240, 0.1);
}
</style>
@endpush