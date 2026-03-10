{{-- resources/views/layouts/sidebars/kaprodi.blade.php --}}
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.dashboard') ? 'active' : '' }}" 
           href="{{ route('kaprodi.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.books.*') ? 'active' : '' }}" 
           href="{{ route('kaprodi.books.index') }}">
            <i class="bi bi-book"></i> Katalog Buku
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.borrowings.checkout') ? 'active' : '' }}" 
           href="{{ route('kaprodi.borrowings.checkout') }}">
            <i class="bi bi-cart-check"></i> Pinjam Buku
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.borrowings.index') ? 'active' : '' }}" 
           href="{{ route('kaprodi.borrowings.index') }}">
            <i class="bi bi-list-check"></i> Riwayat Peminjaman
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.requests.*') ? 'active' : '' }}" 
           href="{{ route('kaprodi.requests.index') }}">
            <i class="bi bi-envelope"></i> Request Buku
        </a>
    </li>
    
    <li class="nav-item mt-3">
        <hr>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.profile') ? 'active' : '' }}" 
           href="{{ route('kaprodi.profile') }}">
            <i class="bi bi-person"></i> Profile
        </a>
    </li>
</ul>