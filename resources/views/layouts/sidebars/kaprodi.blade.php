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
        <a class="nav-link {{ request()->routeIs('kaprodi.dashboard') ? 'active' : '' }}" 
           href="{{ route('kaprodi.dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i> 
            <span>Dashboard</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.books.*') ? 'active' : '' }}" 
           href="{{ route('kaprodi.books.index') }}">
            <i class="bi bi-book me-2"></i> 
            <span>Katalog Buku</span>
        </a>
    </li>
    
    <!-- <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.borrowings.checkout') ? 'active' : '' }}" 
           href="{{ route('kaprodi.borrowings.checkout') }}">
            <i class="bi bi-cart-check me-2"></i> 
            <span>Pinjam Buku</span>
        </a>
    </li> -->
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.borrowings.index') ? 'active' : '' }}" 
           href="{{ route('kaprodi.borrowings.index') }}">
            <i class="bi bi-list-check me-2"></i> 
            <span>Status Permintaan</span>
        </a>
    </li>
    
    <!-- <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.requests.*') ? 'active' : '' }}" 
           href="{{ route('kaprodi.requests.index') }}">
            <i class="bi bi-envelope me-2"></i> 
            <span>Request Buku</span>
        </a>
    </li> -->

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
        <a class="nav-link {{ request()->routeIs('kaprodi.profile') ? 'active' : '' }}" 
           href="{{ route('kaprodi.profile') }}">
            <i class="bi bi-person me-2"></i> 
            <span>Profile</span>
        </a>
    </li>
</ul>

<style>
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
    min-width: 24px;
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

.badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}
</style>