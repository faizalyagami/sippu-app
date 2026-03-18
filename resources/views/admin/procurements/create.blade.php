@extends('layouts.app')

@section('title', 'Buat Pengadaan Baru')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.procurements.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-plus-circle text-primary me-2"></i>
            Buat Pengadaan Baru
        </h4>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.procurements.store') }}" id="procurementForm">
                @csrf

                <div class="row">
                    <!-- Informasi Pengadaan -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
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
                        <label class="form-label fw-semibold">Tanggal PO <span class="text-danger">*</span></label>
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
                        <label class="form-label fw-semibold">Tanggal Diharapkan</label>
                        <input type="date" 
                               name="expected_date" 
                               class="form-control @error('expected_date') is-invalid @enderror" 
                               value="{{ old('expected_date') }}"
                               min="{{ date('Y-m-d') }}">
                        @error('expected_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="notes" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Masukkan catatan pengadaan (opsional)">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <!-- Daftar Item -->
                <h5 class="mb-3 fw-semibold">
                    <i class="bi bi-cart me-2"></i>
                    Daftar Buku
                </h5>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" width="5%">#</th>
                                <th width="40%">Buku</th>
                                <th class="text-center" width="15%">Jumlah</th>
                                <th class="text-center" width="15%">Harga Satuan</th>
                                <th class="text-center" width="15%">Total</th>
                                <th class="text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr id="noItemsRow">
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                    <p class="text-muted mb-3">Belum ada item ditambahkan</p>
                                    <button type="button" class="btn btn-primary" onclick="addItem()">
                                        <i class="bi bi-plus-circle me-1"></i> Tambah Item
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Grand Total:</th>
                                <th class="text-center" id="grandTotal">Rp. 0,00</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-success" onclick="addItem()">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Item
                    </button>
                    <div>
                        <a href="{{ route('admin.procurements.index') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-save me-1"></i> Simpan Pengadaan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template Item (Hidden) -->
<template id="itemTemplate">
    <tr class="item-row">
        <td class="item-number text-center align-middle"></td>
        <td>
            <select name="items[INDEX][book_id]" class="form-select book-select" required>
                <option value="">Pilih Buku</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}" 
                            data-price="{{ $book->price ?? 0 }}"
                            data-stock="{{ $book->available_stock }}">
                        {{ $book->title }} - {{ $book->author }} 
                        (Stok: {{ $book->available_stock }} | Harga: Rp. {{ number_format($book->price ?? 0, 0, ',', '.') }},00)
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" 
                   name="items[INDEX][quantity]" 
                   class="form-control quantity text-center" 
                   min="1" 
                   value="1"
                   required>
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-text">Rp.</span>
                <input type="number" 
                       name="items[INDEX][unit_price]" 
                       class="form-control unit-price text-end" 
                       min="0" 
                       step="100"
                       required>
                <span class="input-group-text">,00</span>
            </div>
        </td>
        <td class="total-price text-center align-middle fw-bold">Rp. 0,00</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<style>
.card {
    border-radius: 12px;
}

.table th {
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.item-number {
    font-weight: 600;
    color: #6c757d;
}

.total-price {
    color: #0d6efd;
}

.input-group-text {
    background-color: #f8f9fa;
}

.btn-outline-danger {
    border-color: transparent;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    color: white;
    border-color: #dc3545;
}
</style>

@push('scripts')
<script>
let itemCount = 0;

// Fungsi untuk format Rupiah dengan format Rp. 1.500.000,00
function formatRupiah(angka) {
    let number = angka.toString().replace(/[^,\d]/g, '');
    let split = number.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return 'Rp. ' + rupiah + ',00';
}

// Fungsi untuk parse format Rupiah ke number
function parseRupiah(rupiah) {
    if (!rupiah) return 0;
    return parseFloat(rupiah.replace(/[^0-9,-]/g, '').replace(',', '.')) || 0;
}

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
    
    // Set default quantity to 1
    quantityInput.value = 1;
    
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
    
    // Trigger initial calculation
    calculateRowTotal(newRow);
    
    itemCount++;
}

function removeItem(button) {
    if (confirm('Hapus item ini?')) {
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
            document.getElementById('grandTotal').textContent = 'Rp. 0,00';
        } else {
            calculateGrandTotal();
        }
    }
}

function calculateRowTotal(row) {
    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
    const price = parseFloat(row.querySelector('.unit-price').value) || 0;
    const total = quantity * price;
    
    // Format as Rupiah with format Rp. 1.500.000,00
    row.querySelector('.total-price').textContent = formatRupiah(total.toString());
    
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.total-price').forEach(cell => {
        const total = parseRupiah(cell.textContent);
        grandTotal += total;
    });
    
    document.getElementById('grandTotal').textContent = formatRupiah(grandTotal.toString());
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
        
        if (!book) {
            valid = false;
            row.querySelector('.book-select').classList.add('is-invalid');
        }
        if (!quantity || quantity < 1) {
            valid = false;
            row.querySelector('.quantity').classList.add('is-invalid');
        }
        if (!price || price < 0) {
            valid = false;
            row.querySelector('.unit-price').classList.add('is-invalid');
        }
    });
    
    if (!valid) {
        e.preventDefault();
        alert('Semua field item harus diisi dengan benar!');
        return false;
    }
    
    // Disable submit button
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
});
</script>
@endpush
@endsection