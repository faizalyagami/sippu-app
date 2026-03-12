{{-- resources/views/kaprodi/profile/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Profile Kaprodi')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-person-circle text-primary"></i> 
            Profile Saya
        </h4>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <!-- Profile Card -->
            <div class="glass-card p-4 text-center">
                <div class="position-relative d-inline-block">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 120px; height: 120px; font-size: 48px;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <button type="button" 
                            class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle"
                            style="width: 35px; height: 35px;"
                            onclick="document.getElementById('photoInput').click();">
                        <i class="bi bi-camera"></i>
                    </button>
                </div>
                
                <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                <p class="text-muted mb-3">{{ Auth::user()->role->display_name }}</p>
                
                <form id="photoForm" enctype="multipart/form-data" style="display: none;">
                    @csrf
                    <input type="file" id="photoInput" name="photo" accept="image/*" onchange="uploadPhoto()">
                </form>
                
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="showEditForm()">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </button>
                    <button type="button" class="btn btn-outline-warning" onclick="showPasswordForm()">
                        <i class="bi bi-key"></i> Ubah Password
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <!-- Informasi Profile -->
            <div class="glass-card p-4" id="profileInfo">
                <h5 class="mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Profile
                </h5>
                
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">Nama Lengkap</th>
                        <td>: <strong>{{ Auth::user()->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>NIP</th>
                        <td>: {{ Auth::user()->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Fakultas</th>
                        <td>: {{ Auth::user()->faculty ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Program Studi</th>
                        <td>: {{ Auth::user()->department ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>: {{ Auth::user()->email }}</td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td>: {{ Auth::user()->username }}</td>
                    </tr>
                    <tr>
                        <th>No. Telepon</th>
                        <td>: {{ Auth::user()->phone_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>: {{ Auth::user()->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>: 
                            @if(Auth::user()->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Bergabung</th>
                        <td>: {{ Auth::user()->created_at->format('d F Y') }}</td>
                    </tr>
                </table>
            </div>

            <!-- Form Edit Profile (Hidden) -->
            <div class="glass-card p-4" id="profileForm" style="display: none;">
                <h5 class="mb-3">
                    <i class="bi bi-pencil-square me-2"></i>
                    Edit Profile
                </h5>
                
                <form method="POST" action="{{ route('kaprodi.profile.update') }}" id="editProfileForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control" 
                                   value="{{ Auth::user()->name }}" 
                                   required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control" 
                                   value="{{ Auth::user()->email }}" 
                                   required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" 
                                   name="phone_number" 
                                   class="form-control" 
                                   value="{{ Auth::user()->phone_number }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" 
                                   name="nip" 
                                   class="form-control" 
                                   value="{{ Auth::user()->nip }}" 
                                   readonly 
                                   disabled>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fakultas</label>
                            <input type="text" 
                                   name="faculty" 
                                   class="form-control" 
                                   value="{{ Auth::user()->faculty }}" 
                                   readonly 
                                   disabled>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Program Studi</label>
                            <input type="text" 
                                   name="department" 
                                   class="form-control" 
                                   value="{{ Auth::user()->department }}" 
                                   readonly 
                                   disabled>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" class="form-control" rows="3">{{ Auth::user()->address }}</textarea>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="hideEditForm()">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Form Ubah Password (Hidden) -->
            <div class="glass-card p-4 mt-4" id="passwordForm" style="display: none;">
                <h5 class="mb-3">
                    <i class="bi bi-key me-2"></i>
                    Ubah Password
                </h5>
                
                <form method="POST" action="{{ route('kaprodi.profile.update') }}" id="changePasswordForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <div class="input-group">
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password"
                                       class="form-control" 
                                       required>
                                <span class="input-group-text" onclick="togglePassword('current_password')" style="cursor: pointer;">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" 
                                       name="new_password" 
                                       id="new_password"
                                       class="form-control" 
                                       required>
                                <span class="input-group-text" onclick="togglePassword('new_password')" style="cursor: pointer;">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" 
                                       name="new_password_confirmation" 
                                       id="new_password_confirmation"
                                       class="form-control" 
                                       required>
                                <span class="input-group-text" onclick="togglePassword('new_password_confirmation')" style="cursor: pointer;">
                                    <i class="bi bi-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div id="passwordMatchMessage" class="mb-3"></div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="hidePasswordForm()">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-warning" id="changePasswordBtn">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Statistik Peminjaman -->
            <div class="glass-card p-4 mt-4">
                <h5 class="mb-3">
                    <i class="bi bi-bar-chart me-2"></i>
                    Statistik Peminjaman
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h3 class="mb-1 text-primary">{{ $totalBorrowings ?? 0 }}</h3>
                            <small class="text-muted">Total Pinjam</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h3 class="mb-1 text-success">{{ $activeBorrowings ?? 0 }}</h3>
                            <small class="text-muted">Sedang Dipinjam</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h3 class="mb-1 text-warning">{{ $pendingBorrowings ?? 0 }}</h3>
                            <small class="text-muted">Menunggu</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h3 class="mb-1 text-info">{{ $returnedBorrowings ?? 0 }}</h3>
                            <small class="text-muted">Dikembalikan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

function showEditForm() {
    document.getElementById('profileInfo').style.display = 'none';
    document.getElementById('profileForm').style.display = 'block';
    document.getElementById('passwordForm').style.display = 'none';
}

function hideEditForm() {
    document.getElementById('profileInfo').style.display = 'block';
    document.getElementById('profileForm').style.display = 'none';
    document.getElementById('passwordForm').style.display = 'none';
}

function showPasswordForm() {
    document.getElementById('profileInfo').style.display = 'none';
    document.getElementById('profileForm').style.display = 'none';
    document.getElementById('passwordForm').style.display = 'block';
}

function hidePasswordForm() {
    document.getElementById('profileInfo').style.display = 'block';
    document.getElementById('profileForm').style.display = 'none';
    document.getElementById('passwordForm').style.display = 'none';
}

// Password match validation
document.getElementById('new_password_confirmation').addEventListener('keyup', function() {
    const password = document.getElementById('new_password').value;
    const confirm = this.value;
    const message = document.getElementById('passwordMatchMessage');
    
    if (confirm.length === 0) {
        message.innerHTML = '';
    } else if (password === confirm) {
        message.innerHTML = '<small class="text-success"><i class="bi bi-check-circle"></i> Password cocok</small>';
    } else {
        message.innerHTML = '<small class="text-danger"><i class="bi bi-exclamation-circle"></i> Password tidak cocok</small>';
    }
});

// Photo upload
function uploadPhoto() {
    const formData = new FormData(document.getElementById('photoForm'));
    
    fetch('{{ route("kaprodi.profile.photo") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Gagal mengupload foto');
        }
    });
}

// Prevent double submit
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const password = document.getElementById('new_password').value;
    const confirm = document.getElementById('new_password_confirmation').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Password tidak cocok!');
        return false;
    }
    
    document.getElementById('changePasswordBtn').disabled = true;
    document.getElementById('changePasswordBtn').innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
});
</script>
@endsection