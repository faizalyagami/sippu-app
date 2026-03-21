{{-- resources/views/layouts/sidebars/admin.blade.php --}}
@php
    use App\Models\Notification;
    $unreadCount = 0;
    if (Auth::check()) {
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }
@endphp

<ul class="nav flex-column">
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
            <span>Permintaan Koleksi</span>
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
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }} d-flex justify-content-between align-items-center" 
           href="#userSubmenu" 
           data-bs-toggle="collapse" 
           aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}">
            <span>
                <i class="bi bi-people me-2"></i>
                <span>Manajemen User</span>
            </span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul class="collapse list-unstyled ms-4 mt-2 {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="userSubmenu">
            <li class="nav-item">
                <a class="nav-link small py-2 {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" 
                   href="{{ route('admin.users.index') }}">
                    <i class="bi bi-list-ul me-2"></i>
                    <span>Daftar User</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link small py-2 {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" 
                   href="{{ route('admin.users.create') }}">
                    <i class="bi bi-person-plus me-2"></i>
                    <span>Tambah User</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link small py-2 {{ request()->routeIs('admin.users.kaprodi') ? 'active' : '' }}" 
                   href="{{ route('admin.users.kaprodi') }}">
                    <i class="bi bi-mortarboard me-2"></i>
                    <span>Daftar Kaprodi</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }} d-flex justify-content-between align-items-center" 
           href="{{ route('notifications.index') }}">
            <span>
                <i class="bi bi-bell me-2"></i>
                <span>Notifikasi</span>
            </span>
            @if($unreadCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item mt-3">
        <hr class="my-2 bg-white opacity-25">
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" 
           href="{{ route('admin.profile') }}">
            <i class="bi bi-person me-2"></i>
            <span>Profile</span>
        </a>
    </li>
</ul>

<style>
/* Style untuk submenu */
#userSubmenu {
    padding-left: 0.5rem;
}

#userSubmenu .nav-link {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    border-radius: 8px;
    margin-bottom: 2px;
    color: rgba(255,255,255,0.7);
    display: flex;
    align-items: center;
}

#userSubmenu .nav-link i {
    font-size: 1rem;
}

#userSubmenu .nav-link.active {
    background: rgba(255,255,255,0.2);
    color: white;
}

#userSubmenu .nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}

/* Style untuk chevron */
.bi-chevron-down {
    transition: transform 0.3s ease;
}

[aria-expanded="true"] .bi-chevron-down {
    transform: rotate(180deg);
}

/* Style untuk nav link utama */
.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin-bottom: 2px;
    color: rgba(255,255,255,0.8);
    transition: all 0.3s ease;
}

.nav-link i {
    font-size: 1.2rem;
    margin-right: 0.75rem;
}

.nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}

.nav-link.active {
    background: rgba(255,255,255,0.2);
    color: white;
    border-left: 3px solid #4facfe;
}

hr {
    border-color: rgba(255,255,255,0.1);
    margin: 1rem 0;
}
</style>