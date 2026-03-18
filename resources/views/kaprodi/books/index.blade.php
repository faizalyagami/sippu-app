@extends('layouts.app')

@section('title', 'Permintaan Buku')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-book text-primary me-2"></i>
            Katalog Buku
        </h4>
        <a href="{{ route('kaprodi.requests.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Request Buku Baru
        </a>
    </div>

    <!-- Search and Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="Cari judul, penulis, penerbit, ISBN..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="availability" class="form-select">
                        <option value="">Semua</option>
                        <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Stok Habis</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Books Grid -->
    <div class="row g-4">
        @forelse($books as $book)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="position-relative">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/'.$book->cover_image) }}" 
                             class="card-img-top" 
                             alt="{{ $book->title }}"
                             style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="bi bi-image fs-1 text-muted"></i>
                        </div>
                    @endif
                    <span class="position-absolute top-0 end-0 m-2 badge rounded-pill bg-{{ $book->available_stock > 0 ? 'success' : 'secondary' }}">
                        {{ $book->available_stock > 0 ? 'Tersedia' : 'Stok Habis' }}
                    </span>
                </div>
                <div class="card-body">
                    <h6 class="card-title fw-semibold mb-1">{{ Str::limit($book->title, 50) }}</h6>
                    <p class="text-muted small mb-2">{{ $book->author }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-light text-dark">{{ $book->category->name }}</span>
                        <small class="text-muted">{{ $book->publisher_year }}</small>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-box me-1"></i> Stok: {{ $book->available_stock }}
                        </small>
                        @if($book->available_stock > 0)
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary add-to-cart" 
                                    data-id="{{ $book->id }}"
                                    data-title="{{ $book->title }}"
                                    data-stock="{{ $book->available_stock }}">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                        @else
                            <span class="text-muted small">
                                <i class="bi bi-x-circle"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm p-5 text-center">
                <i class="bi bi-emoji-frown fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">Tidak ada buku ditemukan</h5>
                @if(request('search') || request('category') || request('availability'))
                    <p class="text-muted mb-3">Coba atur ulang filter pencarian Anda</p>
                    <a href="{{ route('kaprodi.books.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                    </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($books->hasPages())
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pt-3 border-top">
        <div class="text-muted small mb-2 mb-md-0">
            Menampilkan {{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }} 
            dari {{ $books->total() }} data
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0">
                @if($books->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $books->previousPageUrl() }}" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                @endif

                @foreach($books->getUrlRange(max(1, $books->currentPage() - 2), min($books->lastPage(), $books->currentPage() + 2)) as $page => $url)
                    @if($page == $books->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                @if($books->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $books->nextPageUrl() }}" aria-label="Next">
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
    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <div class="text-muted small">
            Menampilkan {{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }} 
            dari {{ $books->total() }} data
        </div>
    </div>
    @endif
</div>

<!-- Floating Cart -->
<div class="floating-cart" id="floatingCart" style="display: none;">
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-white py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-cart me-2"></i>
                    Keranjang Peminjaman
                </h6>
                <span class="badge bg-primary rounded-pill" id="cartCount">0</span>
            </div>
        </div>
        <div class="card-body p-2">
            <div id="cartItems" class="mb-2" style="max-height: 200px; overflow-y: auto;">
                <!-- Cart items will be inserted here -->
            </div>
            <div class="d-grid gap-1">
                <a href="{{ route('kaprodi.borrowings.checkout') }}" class="btn btn-sm btn-success">
                    <i class="bi bi-arrow-right me-1"></i> Lanjut Checkout
                </a>
                <button class="btn btn-sm btn-outline-danger" onclick="clearCart()">
                    <i class="bi bi-trash me-1"></i> Kosongkan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.floating-cart {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 280px;
    z-index: 1000;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.card {
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08) !important;
}

.card-img-top {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
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

.badge.bg-light {
    background-color: #f8f9fa !important;
    color: #495057;
    font-weight: normal;
    padding: 0.35em 0.65em;
}

.add-to-cart {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}

.border-top {
    border-top: 1px solid rgba(0,0,0,0.05) !important;
}

@media (max-width: 768px) {
    .floating-cart {
        width: 240px;
    }
    
    .pagination .page-link {
        padding: 0.3rem 0.6rem;
    }
}
</style>

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
    updateCartDisplay();
}

function updateCartDisplay() {
    const cartCount = document.getElementById('cartCount');
    const cartItems = document.getElementById('cartItems');
    const floatingCart = document.getElementById('floatingCart');
    
    if (!cartCount || !cartItems || !floatingCart) return;
    
    cartCount.textContent = cart.length;
    
    if (cart.length === 0) {
        floatingCart.style.display = 'none';
        return;
    }
    
    floatingCart.style.display = 'block';
    
    let html = '';
    cart.forEach((item, index) => {
        html += `
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <div>
                    <small class="fw-semibold d-block">${item.title.length > 30 ? item.title.substring(0, 30) + '...' : item.title}</small>
                    <small class="text-muted">${item.quantity}x</small>
                </div>
                <button class="btn btn-sm btn-link text-danger p-0" onclick="removeFromCart(${index})">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        `;
    });
    cartItems.innerHTML = html;
}

function addToCart(bookId, title, maxStock) {
    const existing = cart.find(item => item.id === bookId);
    
    if (existing) {
        if (existing.quantity < maxStock) {
            existing.quantity++;
        } else {
            alert('Stok tidak mencukupi!');
            return;
        }
    } else {
        cart.push({
            id: parseInt(bookId),
            title: title,
            quantity: 1,
            maxStock: parseInt(maxStock)
        });
    }
    
    localStorage.setItem('borrowingCart', JSON.stringify(cart));
    updateCartDisplay();
    
    // Visual feedback
    const button = event.target.closest('.add-to-cart');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="bi bi-check"></i>';
    button.classList.remove('btn-outline-primary');
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalHtml;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-primary');
    }, 1000);
}

function removeFromCart(index) {
    cart.splice(index, 1);
    localStorage.setItem('borrowingCart', JSON.stringify(cart));
    updateCartDisplay();
}

function clearCart() {
    if (confirm('Kosongkan keranjang?')) {
        cart = [];
        localStorage.setItem('borrowingCart', JSON.stringify(cart));
        updateCartDisplay();
    }
}

// Hapus cart jika ada parameter clear_cart dari server
@if(session('clear_cart'))
    localStorage.removeItem('borrowingCart');
    cart = [];
@endif

// Load cart on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
});

// Auto submit filters
let searchTimeout;
document.querySelector('input[name="search"]')?.addEventListener('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        this.form.submit();
    }, 500);
});

document.querySelector('select[name="category"]')?.addEventListener('change', function() {
    this.form.submit();
});

document.querySelector('select[name="availability"]')?.addEventListener('change', function() {
    this.form.submit();
});
</script>
@endpush
@endsection