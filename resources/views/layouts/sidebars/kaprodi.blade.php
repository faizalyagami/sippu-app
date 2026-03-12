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
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.books.*') ? 'active' : '' }}" 
           href="{{ route('kaprodi.books.index') }}">
            <i class="bi bi-book me-2"></i> Katalog Buku
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.borrowings.checkout') ? 'active' : '' }}" 
           href="{{ route('kaprodi.borrowings.checkout') }}">
            <i class="bi bi-cart-check me-2"></i> Pinjam Buku
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.borrowings.index') ? 'active' : '' }}" 
           href="{{ route('kaprodi.borrowings.index') }}">
            <i class="bi bi-list-check me-2"></i> Riwayat Peminjaman
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.requests.*') ? 'active' : '' }}" 
           href="{{ route('kaprodi.requests.index') }}">
            <i class="bi bi-envelope me-2"></i> Request Buku
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }} d-flex justify-content-between align-items-center" 
           href="{{ route('notifications.index') }}">
            <span>
                <i class="bi bi-bell me-2"></i> Notifikasi
            </span>
            @if($unreadCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
            @endif
        </a>
    </li>
    
    <li class="nav-item mt-3">
        <hr class="my-2">
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('kaprodi.profile') ? 'active' : '' }}" 
           href="{{ route('kaprodi.profile') }}">
            <i class="bi bi-person me-2"></i> Profile
        </a>
    </li>
</ul>