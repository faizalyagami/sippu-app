{{-- resources/views/admin/procurements/receive.blade.php --}}
@extends('layouts.app')

@section('title', 'Terima Barang')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.procurements.show', $procurement->id) }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="mb-0">
            <i class="bi bi-box-seam text-success"></i> 
            Terima Barang: {{ $procurement->procurement_number }}
        </h4>
    </div>

    <div class="glass-card p-4">
        <form method="POST" action="{{ route('admin.procurements.process-receive', $procurement->id) }}" id="receiveForm">
            @csrf

            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Terima <span class="text-danger">*</span></label>
                    <input type="date" 
                           name="received_date" 
                           class="form-control @error('received_date') is-invalid @enderror" 
                           value="{{ old('received_date', date('Y-m-d')) }}" 
                           required>
                    @error('received_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">No. Invoice</label>
                    <input type="text" 
                           name="invoice_number" 
                           class="form-control @error('invoice_number') is-invalid @enderror" 
                           value="{{ old('invoice_number') }}">
                    @error('invoice_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">No. Receipt</label>
                    <input type="text" 
                           name="receipt_number" 
                           class="form-control @error('receipt_number') is-invalid @enderror" 
                           value="{{ old('receipt_number') }}">
                    @error('receipt_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <h5 class="mb-3">Detail Penerimaan Barang</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Judul Buku</th>
                            <th>Jumlah Pesan</th>
                            <th>Sudah Diterima</th>
                            <th>Sisa</th>
                            <th>Diterima Sekarang</th>
                            <th>Rusak</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($procurement->items as $index => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $item->book->title }}</strong>
                                <br>
                                <small class="text-muted">{{ $item->book->author }}</small>
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center">{{ $item->received_quantity }}</td>
                            <td class="text-center">{{ $item->quantity - $item->received_quantity }}</td>
                            <td>
                                <input type="number" 
                                       name="items[{{ $item->id }}][received_quantity]" 
                                       class="form-control received-qty" 
                                       min="0" 
                                       max="{{ $item->quantity - $item->received_quantity }}"
                                       value="0"
                                       data-max="{{ $item->quantity - $item->received_quantity }}">
                            </td>
                            <td>
                                <input type="number" 
                                       name="items[{{ $item->id }}][damaged_quantity]" 
                                       class="form-control damaged-qty" 
                                       min="0" 
                                       value="0">
                            </td>
                            <td>
                                <input type="text" 
                                       name="items[{{ $item->id }}][notes]" 
                                       class="form-control" 
                                       placeholder="Catatan">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle me-2"></i>
                Pastikan jumlah barang yang diterima sesuai dengan faktur. Barang rusak akan diproses terpisah.
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.procurements.show', $procurement->id) }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-success" id="submitBtn">
                    <i class="bi bi-check-circle"></i> Proses Penerimaan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Validasi agar received + damaged tidak melebihi sisa
document.querySelectorAll('.received-qty').forEach(input => {
    input.addEventListener('input', function() {
        const max = parseInt(this.dataset.max);
        const value = parseInt(this.value) || 0;
        const damagedInput = this.closest('tr').querySelector('.damaged-qty');
        const damagedValue = parseInt(damagedInput.value) || 0;
        
        if (value > max) {
            this.value = max;
        }
        
        if (value + damagedValue > max) {
            damagedInput.value = Math.max(0, max - value);
        }
    });
});

document.querySelectorAll('.damaged-qty').forEach(input => {
    input.addEventListener('input', function() {
        const max = parseInt(this.closest('tr').querySelector('.received-qty').dataset.max);
        const receivedInput = this.closest('tr').querySelector('.received-qty');
        const receivedValue = parseInt(receivedInput.value) || 0;
        const value = parseInt(this.value) || 0;
        
        if (receivedValue + value > max) {
            this.value = Math.max(0, max - receivedValue);
        }
    });
});

// Form submission
document.getElementById('receiveForm').addEventListener('submit', function(e) {
    let hasItems = false;
    
    document.querySelectorAll('.received-qty').forEach(input => {
        if (parseInt(input.value) > 0) {
            hasItems = true;
        }
    });
    
    if (!hasItems) {
        e.preventDefault();
        alert('Minimal harus menerima 1 item!');
        return false;
    }
    
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
});
</script>
@endpush
@endsection