@extends('layouts.app')

@section('title', 'Daftar - SIPPU UNISBA')

@section('content')
<div class="login-container">
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <div class="brand-icon">
                <i class="bi bi-person-plus"></i>
            </div>
            <h2>Daftar Akun</h2>
            <p>SIPPU - Universitas Islam Bandung</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <div class="form-group">
                    <label for="name">
                        <i class="bi bi-person me-1"></i> Nama Lengkap
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               placeholder="Masukkan nama lengkap"
                               required>
                    </div>
                    @error('name')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="bi bi-envelope me-1"></i> Email
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="Masukkan email"
                               required>
                    </div>
                    @error('email')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username">
                        <i class="bi bi-at me-1"></i> Username
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-at"></i>
                        </span>
                        <input type="text" 
                               class="form-control @error('username') is-invalid @enderror" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}" 
                               placeholder="Masukkan username"
                               required>
                    </div>
                    @error('username')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone_number">
                        <i class="bi bi-telephone me-1"></i> No. Telepon
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-telephone"></i>
                        </span>
                        <input type="text" 
                               class="form-control @error('phone_number') is-invalid @enderror" 
                               id="phone_number" 
                               name="phone_number" 
                               value="{{ old('phone_number') }}" 
                               placeholder="Masukkan nomor telepon"
                               required>
                    </div>
                    @error('phone_number')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="bi bi-lock me-1"></i> Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-key"></i>
                        </span>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Minimal 8 karakter"
                               required>
                        <span class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                            <i class="bi bi-eye" id="toggleIcon1"></i>
                        </span>
                    </div>
                    @error('password')
                        <small class="text-danger mt-1">{{ $message }}</small>
                    @enderror
                    <div class="password-strength mt-2">
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" id="passwordStrength" style="width: 0%;"></div>
                        </div>
                        <small class="text-muted" id="passwordHelp">Gunakan minimal 8 karakter dengan kombinasi huruf dan angka</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        <i class="bi bi-lock me-1"></i> Konfirmasi Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-key"></i>
                        </span>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="Masukkan ulang password"
                               required>
                        <span class="password-toggle" onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                            <i class="bi bi-eye" id="toggleIcon2"></i>
                        </span>
                    </div>
                    <div id="passwordMatch" class="mt-1"></div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                    <label class="form-check-label" for="terms">
                        Saya menyetujui <a href="#" class="text-primary">Syarat dan Ketentuan</a> serta 
                        <a href="#" class="text-primary">Kebijakan Privasi</a>
                    </label>
                </div>

                <button type="submit" class="btn-login" id="registerButton">
                    <span class="btn-text">
                        <i class="bi bi-person-plus"></i> Daftar
                    </span>
                    <div class="spinner"></div>
                </button>

                <div class="divider">
                    <span>atau</span>
                </div>

                <div class="text-center">
                    <p class="mb-0">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="text-primary fw-bold">Masuk</a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <p class="copyright">
                <i class="bi bi-c-circle"></i> {{ date('Y') }} SIPPU UNISBA. 
                Hak Cipta Dilindungi.
            </p>
        </div>
    </div>
</div>

<script>
    // Toggle Password Visibility
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        }
    }

    // Password Strength Indicator
    document.getElementById('password').addEventListener('keyup', function() {
        const password = this.value;
        const strengthBar = document.getElementById('passwordStrength');
        const helpText = document.getElementById('passwordHelp');
        
        let strength = 0;
        
        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]+/)) strength += 25;
        if (password.match(/[A-Z]+/)) strength += 25;
        if (password.match(/[0-9]+/)) strength += 25;
        if (password.match(/[$@#&!]+/)) strength += 25;
        
        strengthBar.style.width = Math.min(strength, 100) + '%';
        
        if (strength <= 25) {
            strengthBar.className = 'progress-bar bg-danger';
            helpText.innerHTML = 'Password lemah';
        } else if (strength <= 50) {
            strengthBar.className = 'progress-bar bg-warning';
            helpText.innerHTML = 'Password sedang';
        } else if (strength <= 75) {
            strengthBar.className = 'progress-bar bg-info';
            helpText.innerHTML = 'Password baik';
        } else {
            strengthBar.className = 'progress-bar bg-success';
            helpText.innerHTML = 'Password kuat';
        }
    });

    // Password Match Check
    document.getElementById('password_confirmation').addEventListener('keyup', function() {
        const password = document.getElementById('password').value;
        const confirm = this.value;
        const matchDiv = document.getElementById('passwordMatch');
        
        if (confirm.length === 0) {
            matchDiv.innerHTML = '';
        } else if (password === confirm) {
            matchDiv.innerHTML = '<small class="text-success"><i class="bi bi-check-circle"></i> Password cocok</small>';
        } else {
            matchDiv.innerHTML = '<small class="text-danger"><i class="bi bi-exclamation-circle"></i> Password tidak cocok</small>';
        }
    });

    // Loading Animation on Submit
    document.getElementById('registerForm').addEventListener('submit', function() {
        const button = document.getElementById('registerButton');
        button.classList.add('loading');
        button.disabled = true;
    });

    // Prevent double submit
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        if (this.classList.contains('submitted')) {
            e.preventDefault();
            return false;
        }
        
        // Validate terms
        if (!document.getElementById('terms').checked) {
            e.preventDefault();
            alert('Anda harus menyetujui Syarat dan Ketentuan');
            return false;
        }
        
        this.classList.add('submitted');
    });

    // Username availability check (optional)
    let usernameTimeout;
    document.getElementById('username').addEventListener('keyup', function() {
        clearTimeout(usernameTimeout);
        const username = this.value;
        
        if (username.length < 3) return;
        
        usernameTimeout = setTimeout(function() {
            // Simulate AJAX check
            console.log('Checking username availability for: ' + username);
            // You can implement actual AJAX check here
        }, 500);
    });

    // Email format validation
    document.getElementById('email').addEventListener('blur', function() {
        const email = this.value;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailPattern.test(email)) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });

    // Auto-focus first field
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('name').focus();
    });

    // Demo fill (Ctrl+Shift+F to fill demo data)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && e.key === 'F') {
            e.preventDefault();
            document.getElementById('name').value = 'John Doe';
            document.getElementById('email').value = 'john@example.com';
            document.getElementById('username').value = 'johndoe';
            document.getElementById('phone_number').value = '081234567890';
            document.getElementById('password').value = 'Password123';
            document.getElementById('password_confirmation').value = 'Password123';
        }
    });
</script>
@endsection