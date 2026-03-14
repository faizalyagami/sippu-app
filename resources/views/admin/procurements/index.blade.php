@extends('layouts.app')

@section('title', 'Pengadaan Buku')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-cart-plus text-primary"></i>
            Pengadaan Buku
        </h4>

        <a href="{{ route('admin.procurements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Pengadaan Baru
        </a>
    </div>

    <!-- Statistics -->
    <div class="row g-4 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-cart-plus fs-4 text-primary"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1 text-muted">Total Pengadaan</h6>
                        <h4 class="mb-0">{{ $statistics['total'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1 text-muted">Pending</h6>
                        <h4 class="mb-0">{{ $statistics['pending'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-check-circle fs-4 text-success"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1 text-muted">Selesai</h6>
                        <h4 class="mb-0">{{ $statistics['completed'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="glass-card p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-cash-stack fs-4 text-info"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1 text-muted">Total Nilai</h6>
                        <h4 class="mb-0">
                            Rp {{ number_format($statistics['total_value'] ?? 0,0,',','.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Filter -->
    <div class="glass-card p-4 mb-4">

        <form method="GET" class="row g-3">

            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="No PO..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>

                <select name="status" class="form-select">
                    <option value="">Semua</option>

                    <option value="pending"
                        {{ request('status')=='pending'?'selected':'' }}>
                        Pending
                    </option>

                    <option value="ordered"
                        {{ request('status')=='ordered'?'selected':'' }}>
                        Ordered
                    </option>

                    <option value="partial"
                        {{ request('status')=='partial'?'selected':'' }}>
                        Partial
                    </option>

                    <option value="completed"
                        {{ request('status')=='completed'?'selected':'' }}>
                        Completed
                    </option>

                    <option value="cancelled"
                        {{ request('status')=='cancelled'?'selected':'' }}>
                        Cancelled
                    </option>
                </select>

            </div>

            <div class="col-md-3">
                <label class="form-label">Supplier</label>

                <select name="vendor_id" class="form-select">
                    <option value="">Semua Supplier</option>

                    @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}"
                        {{ request('vendor_id')==$vendor->id?'selected':'' }}>
                        {{ $vendor->name }}
                    </option>
                    @endforeach

                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>

        </form>

    </div>

    <!-- Table -->
    <div class="glass-card">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>No PO</th>
                        <th>Supplier</th>
                        <th>Tanggal</th>
                        <th>Total Item</th>
                        <th>Total Nilai</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($procurements as $procurement)

                    <tr>

                        <td>
                            {{ $procurements->firstItem() + $loop->index }}
                        </td>

                        <td>
                            <strong>
                                {{ $procurement->procurement_number }}
                            </strong>
                        </td>

                        <td>
                            {{ $procurement->vendor->name ?? '-' }}
                        </td>

                        <td>
                            {{ $procurement->procurement_date->format('d/m/Y') }}
                        </td>

                        <td>
                            <span class="badge bg-info">
                                {{ $procurement->items_count ?? 0 }}
                            </span>
                        </td>

                        <td class="fw-bold">
                            Rp {{ number_format($procurement->total_amount,0,',','.') }}
                        </td>

                        <td>

                            @php
                            $statusColors=[
                                'pending'=>'warning',
                                'ordered'=>'info',
                                'partial'=>'primary',
                                'completed'=>'success',
                                'cancelled'=>'danger'
                            ];
                            @endphp

                            <span class="badge bg-{{ $statusColors[$procurement->status] ?? 'secondary' }}">
                                {{ ucfirst($procurement->status) }}
                            </span>

                        </td>

                        <td>

                            <div class="btn-group">

                                <a href="{{ route('admin.procurements.show',$procurement->id) }}"
                                   class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @if($procurement->status=='pending')

                                <a href="{{ route('admin.procurements.edit',$procurement->id) }}"
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                @endif

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="8" class="text-center py-5">

                            <i class="bi bi-inbox fs-1 text-muted"></i>

                            <p class="mt-3 text-muted">
                                Tidak ada data pengadaan
                            </p>

                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <!-- Pagination -->

        <div class="p-3 border-top d-flex justify-content-between">

            <small class="text-muted">
                Menampilkan
                {{ $procurements->firstItem() ?? 0 }}
                -
                {{ $procurements->lastItem() ?? 0 }}
                dari
                {{ $procurements->total() }}
                data
            </small>

            {{ $procurements->withQueryString()->links() }}

        </div>

    </div>

</div>
@endsection


@push('styles')

<style>

.glass-card{
    background:#fff;
    border-radius:12px;
    border:1px solid #e9ecef;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
}

.table thead th{
    font-weight:600;
}

</style>

@endpush