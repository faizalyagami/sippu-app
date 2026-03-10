{{-- resources/views/layouts/sidebars/supplier.blade.php --}}
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('supplier.dashboard') ? 'active' : '' }}" 
           href="{{ route('supplier.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('supplier.procurements.*') ? 'active' : '' }}" 
           href="{{ route('supplier.procurements.index') }}">
            <i class="bi bi-truck"></i> Pengadaan Saya
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('supplier.books.*') ? 'active' : '' }}" 
           href="{{ route('supplier.books.index') }}">
            <i class="bi bi-book"></i> Katalog Buku
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('supplier.invoices.*') ? 'active' : '' }}" 
           href="{{ route('supplier.invoices.index') }}">
            <i class="bi bi-receipt"></i> Invoice
        </a>
    </li>
    
    <li class="nav-item mt-3">
        <hr>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('supplier.profile') ? 'active' : '' }}" 
           href="{{ route('supplier.profile') }}">
            <i class="bi bi-person"></i> Profile
        </a>
    </li>
</ul>