{{-- resources/views/admin/procurements/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Pengadaan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.procurements.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <div>
            <h4 class="mb-1 fw-semibold">
                <i class="bi bi-cart-plus text-primary me-2"></i>
                Detail Pengadaan
            </h4>
            <p class="text-muted small mb-0">
                <i class="bi bi-hash"></i> {{ $procurement->procurement_number }}
            </p>
        </div>
        <div class="ms-auto">
            @if($procurement->status == 'pending')
                <form action="{{ route('admin.procurements.confirm', $procurement->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary me-2" onclick="return confirm('Konfirmasi pesanan ke supplier? Status akan berubah menjadi Ordered.')">
                        <i class="bi bi-check-circle me-1"></i> Konfirmasi ke Supplier
                    </button>
                </form>
                
                <a href="{{ route('admin.procurements.edit', $procurement->id) }}" class="btn btn-warning me-2">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="bi bi-x-circle me-1"></i> Batalkan
                </button>
            @endif
            
            @if($procurement->status == 'ordered' || $procurement->status == 'partial')
                <a href="{{ route('admin.procurements.receive', $procurement->id) }}" class="btn btn-success">
                    <i class="bi bi-box-seam me-1"></i> Terima Barang
                </a>
            @endif
            
            @if($procurement->status == 'completed')
                <a href="{{ route('admin.procurements.print', $procurement->id) }}" class="btn btn-outline-secondary" target="_blank">
                    <i class="bi bi-printer me-1"></i> Cetak PO
                </a>
            @endif
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-4">
        @php
            $statusColors = [
                'pending' => ['bg-warning', 'text-dark', 'Menunggu Konfirmasi'],
                'ordered' => ['bg-info', 'text-white', 'Telah Dipesan ke Supplier'],
                'partial' => ['bg-primary', 'text-white', 'Sebagian Diterima'],
                'completed' => ['bg-success', 'text-white', 'Selesai'],
                'cancelled' => ['bg-danger', 'text-white', 'Dibatalkan']
            ];
            $status = $statusColors[$procurement->status] ?? ['bg-secondary', 'text-white', $procurement->status];
        @endphp
        <div class="d-inline-block">
            <span class="badge {{ $status[0] }} {{ $status[1] }} px-4 py-2 rounded-pill">
                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>
                Status: {{ $status[2] }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Informasi Pengadaan -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informasi Pengadaan
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="45%" class="text-muted">No. Purchase Order</td>
                            <td class="fw-semibold">{{ $procurement->procurement_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal PO</td>
                            <td>{{ \Carbon\Carbon::parse($procurement->procurement_date)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Diharapkan</td>
                            <td>{{ $procurement->expected_date ? \Carbon\Carbon::parse($procurement->expected_date)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Diterima</td>
                            <td>{{ $procurement->received_date ? \Carbon\Carbon::parse($procurement->received_date)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Nilai</td>
                            <td class="fw-bold text-primary">Rp {{ number_format($procurement->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Informasi Supplier -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-truck text-primary me-2"></i>
                        Informasi Supplier
                    </h5>
                </div>
                <div class="card-body">
                    @if($procurement->vendor)
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded-circle p-3 me-3">
                            <i class="bi bi-building fs-4 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-1">{{ $procurement->vendor->name }}</h6>
                            <p class="text-muted small mb-0">{{ $procurement->vendor->company_name }}</p>
                        </div>
                    </div>
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Email</td>
                            <td>
                                <a href="mailto:{{ $procurement->vendor->email }}" class="text-decoration-none">
                                    {{ $procurement->vendor->email }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Telepon</td>
                            <td>{{ $procurement->vendor->phone_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kontak Person</td>
                            <td>
                                <strong>{{ $procurement->vendor->contact_person }}</strong>
                                <br>
                                <small class="text-muted">{{ $procurement->vendor->cp_phone }}</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td>{{ $procurement->vendor->address }}</td>
                        </tr>
                    </table>
                    @else
                    <p class="text-muted text-center mb-0">Data supplier tidak tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informasi Lainnya -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-person text-primary me-2"></i>
                        Informasi Lainnya
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="45%" class="text-muted">Dibuat Oleh</td>
                            <td class="fw-semibold">{{ $procurement->createdBy->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Dibuat</td>
                            <td>{{ $procurement->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Terakhir Update</td>
                            <td>{{ $procurement->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($procurement->invoice_number)
                        <tr>
                            <td class="text-muted">No. Invoice</td>
                            <td><span class="badge bg-info bg-opacity-10 text-info px-3 py-2">{{ $procurement->invoice_number }}</span></td>
                        </tr>
                        @endif
                        @if($procurement->receipt_number)
                        <tr>
                            <td class="text-muted">No. Receipt</td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">{{ $procurement->receipt_number }}</span></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Catatan -->
        @if($procurement->notes)
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-chat-text text-primary me-2"></i>
                        Catatan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="bg-light p-3 rounded-3">
                        <p class="mb-0">{{ $procurement->notes }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Daftar Item -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-cart text-primary me-2"></i>
                        Daftar Item
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
                                    <th class="px-4 py-3" width="12%">ISBN</th>
                                    <th class="px-4 py-3 text-center" width="8%">Jumlah</th>
                                    <th class="px-4 py-3 text-end" width="12%">Harga Satuan</th>
                                    <th class="px-4 py-3 text-end" width="12%">Total</th>
                                    <th class="px-4 py-3 text-center" width="11%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($procurement->items as $item)
                                <tr>
                                    <td class="px-4">{{ $loop->iteration }}</td>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-2">
                                                <i class="bi bi-book text-primary"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $item->book->title }}</strong>
                                                @if($item->book->publisher)
                                                <br>
                                                <small class="text-muted">{{ $item->book->publisher }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4">{{ $item->book->author }}</td>
                                    <td class="px-4">{{ $item->book->isbn ?? '-' }}</td>
                                    <td class="px-4 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-4 text-end fw-semibold">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                    <td class="px-4 text-center">
                                        @php
                                            $itemStatusColors = [
                                                'pending' => ['bg-warning', 'text-dark', 'Pending'],
                                                'ordered' => ['bg-info', 'text-white', 'Ordered'],
                                                'partial' => ['bg-primary', 'text-white', 'Partial'],
                                                'completed' => ['bg-success', 'text-white', 'Completed'],
                                                'cancelled' => ['bg-danger', 'text-white', 'Cancelled']
                                            ];
                                            $itemStatus = $itemStatusColors[$item->status] ?? ['bg-secondary', 'text-white', $item->status];
                                        @endphp
                                        <span class="badge {{ $itemStatus[0] }} {{ $itemStatus[1] }} px-3 py-2">
                                            {{ $itemStatus[2] }}
                                        </span>
                                        @if($item->received_quantity > 0)
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="bi bi-check-circle"></i> Diterima: {{ $item->received_quantity }}
                                                </small>
                                            </div>
                                        @endif
                                        @if($item->damaged_quantity > 0)
                                            <div class="mt-1">
                                                <small class="text-danger">
                                                    <i class="bi bi-exclamation-triangle"></i> Rusak: {{ $item->damaged_quantity }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-end fw-semibold">Grand Total:</td>
                                    <td class="px-4 py-3 text-end fw-bold text-primary">Rp {{ number_format($procurement->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cancel -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="bi bi-x-circle text-danger me-2"></i>
                    Batalkan Pengadaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.procurements.cancel', $procurement->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Yakin ingin membatalkan pengadaan ini?</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-check-circle me-1"></i> Batalkan Pengadaan
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

@media (max-width: 768px) {
    .table {
        font-size: 0.9rem;
    }
}
</style>
@endsection