{{-- resources/views/layouts/sidebars/admin.blade.php --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
       href="{{ route('admin.dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}" 
       href="{{ route('admin.books.index') }}">
        <i class="bi bi-book"></i>
        <span>Kelola Buku</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" 
       href="{{ route('admin.categories.index') }}">
        <i class="bi bi-tags"></i>
        <span>Kategori</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}" 
       href="{{ route('admin.suppliers.index') }}">
        <i class="bi bi-truck"></i>
        <span>Supplier</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.procurements.*') ? 'active' : '' }}" 
       href="{{ route('admin.procurements.index') }}">
        <i class="bi bi-cart-plus"></i>
        <span>Pengadaan Koleksi</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.borrowings.*') ? 'active' : '' }}" 
       href="{{ route('admin.borrowings.index') }}">
        <i class="bi bi-arrow-left-right"></i>
        <span>Peminjaman</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
       href="{{ route('admin.reports.index') }}">
        <i class="bi bi-file-text"></i>
        <span>Laporan</span>
    </a>
</li>

{{-- Menu User Management dengan Submenu --}}
<li class="nav-item mt-3">
    <hr class="my-2 bg-white opacity-25">
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
       href="#userSubmenu" 
       data-bs-toggle="collapse" 
       aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}">
        <i class="bi bi-people"></i>
        <span>Manajemen User</span>
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul class="collapse list-unstyled ms-4 {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="userSubmenu">
        <li class="nav-item">
            <a class="nav-link small {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" 
               href="{{ route('admin.users.index') }}">
                <i class="bi bi-list-ul"></i>
                <span>Daftar User</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link small {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" 
               href="{{ route('admin.users.create') }}">
                <i class="bi bi-person-plus"></i>
                <span>Tambah User</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link small {{ request()->routeIs('admin.users.kaprodi') ? 'active' : '' }}" 
               href="{{ route('admin.users.kaprodi') }}">
                <i class="bi bi-mortarboard"></i>
                <span>Daftar Kaprodi</span>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" 
       href="{{ route('admin.profile') }}">
        <i class="bi bi-person"></i>
        <span>Profile</span>
    </a>
</li>

<style>
/* Style untuk submenu */
#userSubmenu .nav-link {
    padding: 8px 15px;
    font-size: 0.9rem;
}

#userSubmenu .nav-link i {
    font-size: 1rem;
}

#userSubmenu .nav-link.active {
    background: rgba(255,255,255,0.2);
    border-radius: 10px;
}

#userSubmenu .nav-link:hover {
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
}
</style>