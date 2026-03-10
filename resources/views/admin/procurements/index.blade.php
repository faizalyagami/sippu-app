{{-- resources/views/admin/procurements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengadaan Buku')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-cart-plus text-primary"></i> Pengadaan Buku
        </h4>
        <a href="{{ route('admin.procurements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Pengadaan Baru
        </a>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Pengadaan</h6>
                    <h3>{{ $statistics['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Menunggu</h6>
                    <h3>{{ $statistics['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Selesai</h6>
                    <h3>{{ $statistics['completed'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Total Nilai</h6>
                    <h3>Rp {{ number_format($statistics['total_value'] ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari No. PO..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="vendor_id" class="form-select">
                        <option value="">Semua Supplier</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>No. PO</th>
                            <th>Supplier</th>
                            <th>Tanggal</th>
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
                            <td>{{ $procurement->vendor->name ?? '-' }}</td>
                            <td>{{ $procurement->procurement_date->format('d/m/Y') }}</td>
                            <td>{{ $procurement->items_count }} Item</td>
                            <td>Rp {{ number_format($procurement->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $badges = [
                                        'pending' => 'warning',
                                        'ordered' => 'info',
                                        'partial' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $texts = [
                                        'pending' => 'Pending',
                                        'ordered' => 'Ordered',
                                        'partial' => 'Partial',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $badges[$procurement->status] }}">
                                    {{ $texts[$procurement->status] }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.procurements.show', $procurement->id) }}" 
                                       class="btn btn-sm btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($procurement->status == 'pending')
                                        <a href="{{ route('admin.procurements.edit', $procurement->id) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if(in_array($procurement->status, ['ordered', 'partial']))
                                        <a href="{{ route('admin.procurements.receive', $procurement->id) }}" 
                                           class="btn btn-sm btn-success" title="Terima Barang">
                                            <i class="bi bi-box-seam"></i>
                                        </a>
                                    @endif
                                    @if($procurement->status == 'completed')
                                        <a href="{{ route('admin.procurements.print', $procurement->id) }}" 
                                           class="btn btn-sm btn-secondary" title="Cetak PO" target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-emoji-frown fs-1 d-block mb-3"></i>
                                <h5>Tidak ada data pengadaan</h5>
                                <a href="{{ route('admin.procurements.create') }}" class="btn btn-primary mt-3">
                                    Buat Pengadaan Baru
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $procurements->firstItem() ?? 0 }} - {{ $procurements->lastItem() ?? 0 }} 
                    dari {{ $procurements->total() }} data
                </div>
                <div>
                    {{ $procurements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection