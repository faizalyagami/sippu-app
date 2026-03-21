{{-- resources/views/kaprodi/borrowings/checkout.blade.php --}}
@extends('layouts.app')

@section('title', 'Checkout Peminjaman')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('kaprodi.books.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Katalog
        </a>
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-cart-check text-success me-2"></i>
            Checkout Permintaan
        </h4>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Daftar Buku yang Dipinjam -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-cart me-2"></i>
                        Daftar Permintaan Buku
                    </h5>
                </div>
                <div class="card-body">
                    <div id="cartItemsContainer">
                        <!-- Cart items will be loaded here -->
                    </div>
                    
                    <div id="emptyCartMessage" class="text-center py-5" style="display: none;">
                        <i class="bi bi-cart-x fs-1 text-muted d-block mb-3"></i>
                        <h5 class="text-muted">Keranjang masih kosong</h5>
                        <p class="text-muted mb-3">Tambahkan buku dari katalog terlebih dahulu</p>
                        <a href="{{ route('kaprodi.books.index') }}" class="btn btn-primary">
                            <i class="bi bi-book me-2"></i> Pilih Buku
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Ringkasan Peminjaman -->
            <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-file-text me-2"></i>
                        Ringkasan Permintaan
                    </h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Jumlah Buku</td>
                                <td class="text-end fw-semibold" id="totalItems">0</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Item</td>
                                <td class="text-end fw-semibold" id="totalQuantity">0</td>
                            </tr>
                        </table>
                    </div>

                    <form action="{{ route('kaprodi.borrowings.process-checkout') }}" method="POST" id="checkoutForm">
                        @csrf
                        
                        <!-- <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Tanggal Pengembalian <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="expected_return_date" 
                                   class="form-control @error('expected_return_date') is-invalid @enderror" 
                                   min="{{ now()->addDay()->format('Y-m-d') }}"
                                   max="{{ now()->addMonths(3)->format('Y-m-d') }}"
                                   value="{{ old('expected_return_date', now()->addWeeks(2)->format('Y-m-d')) }}"
                                   required>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-info-circle me-1"></i>
                                Maksimal 3 bulan dari sekarang
                            </small>
                            @error('expected_return_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> -->

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tujuan/Keperluan</label>
                            <textarea name="purpose" 
                                      class="form-control @error('purpose') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Contoh: Bahan ajar, penelitian, tugas akhir, dll">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="hiddenInputsContainer"></div>

                        <button type="submit" class="btn btn-success w-100" id="submitBtn" disabled>
                            <i class="bi bi-check-circle me-2"></i> Ajukan Permintaan
                        </button>
                    </form>

                    <div class="mt-3">
                        <a href="{{ route('kaprodi.books.index') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i> Tambah Buku Lagi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cart = [];

function loadCart() {
    try {
        const savedCart = localStorage.getItem('borrowingCart');
        if (savedCart) {
            cart = JSON.parse(savedCart);
            if (!Array.isArray(cart)) {
                cart = [];
                localStorage.removeItem('borrowingCart');
            }
        }
    } catch (e) {
        console.error('Error loading cart:', e);
        cart = [];
        localStorage.removeItem('borrowingCart');
    }
    updateDisplay();
}

function updateDisplay() {
    const container = document.getElementById('cartItemsContainer');
    const emptyMessage = document.getElementById('emptyCartMessage');
    const submitBtn = document.getElementById('submitBtn');
    const totalItems = document.getElementById('totalItems');
    const totalQuantity = document.getElementById('totalQuantity');
    
    if (!container || !emptyMessage || !submitBtn || !totalItems || !totalQuantity) return;
    
    if (cart.length === 0) {
        emptyMessage.style.display = 'block';
        container.innerHTML = '';
        submitBtn.disabled = true;
        totalItems.textContent = '0';
        totalQuantity.textContent = '0';
        return;
    }
    
    emptyMessage.style.display = 'none';
    submitBtn.disabled = false;
    
    displayCartItems();
    updateHiddenInputs();
}

function displayCartItems() {
    const container = document.getElementById('cartItemsContainer');
    const totalItems = document.getElementById('totalItems');
    const totalQuantity = document.getElementById('totalQuantity');
    
    let html = '<div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Buku</th><th>Jumlah</th><th>Aksi</th></tr></thead><tbody>';
    
    let totalQty = 0;
    
    cart.forEach((item, index) => {
        totalQty += item.quantity;
        
        html += `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle p-2 me-2">
                            <i class="bi bi-book text-primary"></i>
                        </div>
                        <div>
                            <strong>${item.title.length > 50 ? item.title.substring(0, 50) + '...' : item.title}</strong>
                            <br>
                            <small class="text-muted">Stok tersedia: ${item.maxStock}</small>
                        </div>
                    </div>
                </td>
                <td style="width: 120px;">
                    <div class="input-group input-group-sm">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${index}, -1)">-</button>
                        <input type="text" class="form-control text-center bg-white" value="${item.quantity}" readonly style="width: 40px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                </td>
                <td style="width: 50px;">
                    <button class="btn btn-sm btn-link text-danger p-0" onclick="removeItem(${index})">
                        <i class="bi bi-trash fs-5"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
    
    totalItems.textContent = cart.length;
    totalQuantity.textContent = totalQty;
}

function updateHiddenInputs() {
    const container = document.getElementById('hiddenInputsContainer');
    container.innerHTML = '';
    
    cart.forEach((item, index) => {
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = `books[${index}][id]`;
        idInput.value = item.id;
        container.appendChild(idInput);
        
        const qtyInput = document.createElement('input');
        qtyInput.type = 'hidden';
        qtyInput.name = `books[${index}][quantity]`;
        qtyInput.value = item.quantity;
        container.appendChild(qtyInput);
    });
}

function updateQuantity(index, change) {
    const item = cart[index];
    const newQty = item.quantity + change;
    
    if (newQty < 1) {
        removeItem(index);
        return;
    }
    
    if (item.maxStock && newQty > item.maxStock) {
        alert('Melebihi stok yang tersedia!');
        return;
    }
    
    item.quantity = newQty;
    localStorage.setItem('borrowingCart', JSON.stringify(cart));
    displayCartItems();
    updateHiddenInputs();
}

function removeItem(index) {
    if (confirm('Hapus buku ini dari keranjang?')) {
        cart.splice(index, 1);
        localStorage.setItem('borrowingCart', JSON.stringify(cart));
        
        if (cart.length === 0) {
            updateDisplay();
        } else {
            displayCartItems();
            updateHiddenInputs();
        }
    }
}

document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    if (cart.length === 0) {
        e.preventDefault();
        alert('Keranjang masih kosong!');
        return false;
    }
    
    for (let item of cart) {
        if (item.quantity < 1) {
            e.preventDefault();
            alert('Jumlah buku harus minimal 1');
            return false;
        }
        if (item.maxStock && item.quantity > item.maxStock) {
            e.preventDefault();
            alert(`Stok buku "${item.title}" tidak mencukupi`);
            return false;
        }
    }
    
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
});

@if(session('clear_cart'))
    localStorage.removeItem('borrowingCart');
    cart = [];
@endif

document.addEventListener('DOMContentLoaded', function() {
    loadCart();
});
</script>
@endpush

<style>
.table th {
    font-weight: 600;
    color: #495057;
}

.input-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.input-group-sm input {
    font-size: 0.875rem;
}

.btn-link {
    text-decoration: none;
}

.btn-link:hover {
    color: #dc3545 !important;
}

.sticky-top {
    z-index: 1020;
}

@media (max-width: 768px) {
    .table {
        font-size: 0.9rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
    }
}
</style>
@endsection