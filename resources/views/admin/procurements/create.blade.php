{{-- resources/views/admin/procurements/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Buat Pengadaan Baru')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.procurements.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="mb-0">
            <i class="bi bi-plus-circle text-primary"></i> 
            Buat Pengadaan Baru
        </h4>
    </div>

    <div class="glass-card p-4">
        <form method="POST" action="{{ route('admin.procurements.store') }}" id="procurementForm">
            @csrf

            <div class="row">
                <!-- Informasi Pengadaan -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror" required>
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('vendor_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }} - {{ $supplier->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Tanggal PO <span class="text-danger">*</span></label>
                    <input type="date" 
                           name="procurement_date" 
                           class="form-control @error('procurement_date') is-invalid @enderror" 
                           value="{{ old('procurement_date', date('Y-m-d')) }}" 
                           required>
                    @error('procurement_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Tanggal Diharapkan</label>
                    <input type="date" 
                           name="expected_date" 
                           class="form-control @error('expected_date') is-invalid @enderror" 
                           value="{{ old('expected_date') }}">
                    @error('expected_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                              rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-4">

            <!-- Daftar Item -->
            <h5 class="mb-3">
                <i class="bi bi-cart me-2"></i>
                Daftar Buku
            </h5>

            <div class="table-responsive mb-3">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="40%">Buku</th>
                            <th width="15%">Jumlah</th>
                            <th width="15%">Harga Satuan</th>
                            <th width="15%">Total</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr id="noItemsRow">
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-3 text-muted"></i>
                                <p class="text-muted">Belum ada item ditambahkan</p>
                                <button type="button" class="btn btn-primary" onclick="addItem()">
                                    <i class="bi bi-plus-circle"></i> Tambah Item
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Grand Total:</th>
                            <th id="grandTotal">Rp 0</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-success" onclick="addItem()">
                    <i class="bi bi-plus-circle"></i> Tambah Item
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="bi bi-save"></i> Simpan Pengadaan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template Item (Hidden) -->
<template id="itemTemplate">
    <tr class="item-row">
        <td class="item-number align-middle"></td>
        <td>
            <select name="items[INDEX][book_id]" class="form-select book-select" required>
                <option value="">Pilih Buku</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}" 
                            data-price="{{ $book->price ?? 0 }}"
                            data-stock="{{ $book->available_stock }}">
                        {{ $book->title }} - {{ $book->author }} (Stok: {{ $book->available_stock }})
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" 
                   name="items[INDEX][quantity]" 
                   class="form-control quantity" 
                   min="1" 
                   required>
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" 
                       name="items[INDEX][unit_price]" 
                       class="form-control unit-price" 
                       min="0" 
                       required>
            </div>
        </td>
        <td class="total-price align-middle fw-bold">Rp 0</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
let itemCount = 0;

function addItem() {
    const template = document.getElementById('itemTemplate');
    const clone = template.content.cloneNode(true);
    const row = clone.querySelector('tr');
    
    // Replace INDEX with current count
    row.innerHTML = row.innerHTML.replace(/INDEX/g, itemCount);
    
    // Set item number
    row.querySelector('.item-number').textContent = itemCount + 1;
    
    // Add to table
    const tbody = document.getElementById('itemsBody');
    tbody.appendChild(row);
    
    // Hide no items row
    document.getElementById('noItemsRow').style.display = 'none';
    
    // Add event listeners
    const newRow = tbody.lastElementChild;
    const bookSelect = newRow.querySelector('.book-select');
    const quantityInput = newRow.querySelector('.quantity');
    const priceInput = newRow.querySelector('.unit-price');
    
    bookSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const price = selected.dataset.price;
        if (price) {
            priceInput.value = price;
            calculateRowTotal(newRow);
        }
    });
    
    quantityInput.addEventListener('input', () => calculateRowTotal(newRow));
    priceInput.addEventListener('input', () => calculateRowTotal(newRow));
    
    itemCount++;
}

function removeItem(button) {
    const row = button.closest('tr');
    row.remove();
    
    // Renumber items
    document.querySelectorAll('.item-row').forEach((row, index) => {
        row.querySelector('.item-number').textContent = index + 1;
        row.innerHTML = row.innerHTML.replace(/items\[\d+\]/g, `items[${index}]`);
    });
    
    itemCount = document.querySelectorAll('.item-row').length;
    
    // Show no items row if empty
    if (itemCount === 0) {
        document.getElementById('noItemsRow').style.display = '';
        document.getElementById('grandTotal').textContent = 'Rp 0';
    } else {
        calculateGrandTotal();
    }
}

function calculateRowTotal(row) {
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    const price = parseFloat(row.querySelector('.unit-price').value) || 0;
    const total = quantity * price;
    
    row.querySelector('.total-price').textContent = 'Rp ' + total.toLocaleString('id-ID');
    
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.total-price').forEach(cell => {
        const total = parseFloat(cell.textContent.replace(/[^0-9]/g, '')) || 0;
        grandTotal += total;
    });
    
    document.getElementById('grandTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
}

// Form validation
document.getElementById('procurementForm').addEventListener('submit', function(e) {
    const itemCount = document.querySelectorAll('.item-row').length;
    
    if (itemCount === 0) {
        e.preventDefault();
        alert('Minimal harus menambahkan 1 item buku!');
        return false;
    }
    
    // Validate all items have required fields
    let valid = true;
    document.querySelectorAll('.item-row').forEach(row => {
        const book = row.querySelector('.book-select').value;
        const quantity = row.querySelector('.quantity').value;
        const price = row.querySelector('.unit-price').value;
        
        if (!book || !quantity || !price) {
            valid = false;
        }
    });
    
    if (!valid) {
        e.preventDefault();
        alert('Semua field item harus diisi!');
        return false;
    }
    
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
});
</script>
@endpush
@endsection