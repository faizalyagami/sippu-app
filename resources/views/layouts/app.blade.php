{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'SIPPU - Sistem Perpustakaan UNISBA')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        :root {
            --primary-color: #1e3c72;
            --secondary-color: #2a5298;
            --accent-color: #4facfe;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
            --gradient-primary: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        body {
            background: #f4f7fc;
            overflow-x: hidden;
        }

        /* Wrapper */
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar */
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: var(--gradient-primary);
            color: #fff;
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 999;
            overflow-y: auto;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        #sidebar::-webkit-scrollbar {
            width: 5px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }

        #sidebar.active {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        #sidebar .sidebar-header {
            padding: 20px 25px;
            background: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        #sidebar .sidebar-header h5 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        #sidebar .sidebar-header small {
            color: rgba(255,255,255,0.7);
        }

        #sidebar .user-info {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(0,0,0,0.2);
        }

        #sidebar .user-info .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: var(--gradient-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.2rem;
            color: #1e3c72;
        }

        #sidebar .user-info .user-details h6 {
            color: #fff;
            margin-bottom: 2px;
            font-weight: 600;
        }

        #sidebar .user-info .user-details small {
            color: rgba(255,255,255,0.6);
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li {
            position: relative;
        }

        #sidebar ul li a {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        #sidebar ul li a i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        #sidebar ul li a:hover {
            color: #fff;
            background: rgba(255,255,255,0.1);
            border-left-color: var(--accent-color);
        }

        #sidebar ul li.active > a {
            color: #fff;
            background: rgba(255,255,255,0.15);
            border-left-color: var(--accent-color);
            font-weight: 500;
        }

        #sidebar ul li.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: var(--accent-color);
            border-radius: 0 3px 3px 0;
        }

        #sidebar .sidebar-footer {
            padding: 20px 25px;
            border-top: 1px solid rgba(255,255,255,0.1);
            position: sticky;
            bottom: 0;
            background: var(--gradient-primary);
        }

        /* Main Content */
        #content {
            width: calc(100% - var(--sidebar-width));
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
            background: #f4f7fc;
        }

        #content.active {
            width: 100%;
            margin-left: 0;
        }

        /* Navbar */
        .navbar {
            background: #fff;
            height: var(--header-height);
            padding: 0 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 998;
        }

        .navbar .navbar-brand {
            font-weight: 600;
            color: var(--primary-color);
            display: none;
        }

        .navbar .btn-toggle-sidebar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            background: #fff;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .navbar .btn-toggle-sidebar:hover {
            background: var(--gradient-primary);
            color: #fff;
            border-color: transparent;
        }

        .navbar .nav-link {
            color: #6c757d;
            padding: 8px 15px !important;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .navbar .nav-link:hover {
            background: #f8f9fa;
            color: var(--primary-color);
        }

        .navbar .nav-link i {
            font-size: 1.2rem;
        }

        .navbar .dropdown-menu {
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            border-radius: 12px;
            padding: 10px 0;
            margin-top: 10px;
        }

        .navbar .dropdown-menu .dropdown-item {
            padding: 8px 20px;
            color: #495057;
            transition: all 0.3s;
        }

        .navbar .dropdown-menu .dropdown-item:hover {
            background: #f8f9fa;
            color: var(--primary-color);
        }

        .navbar .dropdown-menu .dropdown-item i {
            margin-right: 10px;
            width: 20px;
            color: var(--secondary-color);
        }

        .navbar .dropdown-divider {
            margin: 8px 0;
            border-color: #e9ecef;
        }

        /* Notifications */
        .notification-badge {
            position: absolute;
            top: 2px;
            right: 8px;
            padding: 3px 6px;
            border-radius: 50px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: #fff;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            transition: all 0.3s;
        }

        .notification-item:hover {
            background: #f8f9fa;
        }

        .notification-item.unread {
            background: #e8f0fe;
        }

        .notification-icon {
            width: 35px;
            height: 35px;
            border-radius: 10px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary-color);
        }

        /* Page Content */
        .page-content {
            padding: 25px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                width: 100%;
                margin-left: 0;
            }
            #content.active {
                width: calc(100% - var(--sidebar-width));
                margin-left: var(--sidebar-width);
            }
            .navbar .navbar-brand {
                display: block;
            }
        }

        /* Custom Scrollbar for Content */
        #content::-webkit-scrollbar {
            width: 8px;
        }

        #content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #content::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 10px;
        }

        #content::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h5 class="mb-0">
                    <i class="bi bi-book-half me-2"></i>
                    SIPPUS
                </h5>
                <small class="text-white-50">Universitas Islam Bandung</small>
            </div>

            <div class="user-info">
                <div class="d-flex align-items-center gap-3">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <h6>{{ Auth::user()->name }}</h6>
                        <small>{{ Auth::user()->role->display_name ?? 'User' }}</small>
                    </div>
                </div>
            </div>

            <ul class="list-unstyled components">
                @include('layouts.sidebar')
            </ul>

            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-link text-white-50 p-0 text-decoration-none">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            @php
                use App\Models\Notification;
                
                $unreadNotifications = 0;
                $recentNotifications = collect([]);
                
                if (Auth::check()) {
                    $unreadNotifications = Notification::where('user_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
                        
                    $recentNotifications = Notification::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                }
            @endphp
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn-toggle-sidebar me-3">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <span class="navbar-brand">{{ Auth::user()->role->display_name ?? 'Dashboard' }}</span>

                    <div class="ms-auto d-flex align-items-center gap-2">
                        <!-- Search -->
                        <div class="d-none d-md-block">
                            <div class="input-group" style="width: 250px;">
                                <span class="input-group-text bg-light border-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-0 bg-light" 
                                       placeholder="Cari..." style="box-shadow: none;">
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="dropdown-menu dropdown-menu-end" style="width: 350px;">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                            <h6 class="mb-0">Notifikasi</h6>
                            <span class="badge bg-primary">{{ $unreadNotifications }} Baru</span>
                        </div>
                        <div style="max-height: 400px; overflow-y: auto;">
                            @forelse($recentNotifications as $notification)
                                <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}">
                                    <div class="d-flex gap-3">
                                        <div class="notification-icon">
                                            <i class="bi bi-{{ $notification->type == 'success' ? 'check-circle' : ($notification->type == 'warning' ? 'exclamation-triangle' : ($notification->type == 'danger' ? 'x-circle' : 'info-circle')) }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $notification->title }}</h6>
                                            <p class="mb-1 small text-muted">{{ Str::limit($notification->message, 50) }}</p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="bi bi-bell-slash fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Tidak ada notifikasi</span>
                                </div>
                            @endforelse
                        </div>
                        <div class="text-center p-3 border-top">
                            <a href="{{ route('notifications.index') }}" class="text-decoration-none small">Lihat Semua Notifikasi</a>
                        </div>
                    </div>

                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <button class="nav-link d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                <div class="user-avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 35px; height: 35px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="d-none d-lg-block">{{ Auth::user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                                        <i class="bi bi-person"></i> Profile Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-gear"></i> Pengaturan
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Alert Messages -->
            <div class="px-4 pt-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
            </div>

            <!-- Page Content -->
            <div class="page-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Toggle sidebar
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar, #content').toggleClass('active');
                
                // Update icon
                $(this).find('i').toggleClass('bi-list bi-x');
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Set active menu based on URL
            const currentUrl = window.location.pathname;
            $('.components li a').each(function() {
                if ($(this).attr('href') === currentUrl) {
                    $(this).parent().addClass('active');
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>