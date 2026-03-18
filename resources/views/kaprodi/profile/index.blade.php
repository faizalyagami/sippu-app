@extends('layouts.app')

@section('title', 'Profile Kaprodi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-person-circle text-primary me-2"></i>
            Profile Saya
        </h4>
    </div>

    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="position-relative d-inline-block mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 120px; height: 120px; font-size: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <button type="button" 
                                class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle"
                                style="width: 35px; height: 35px;"
                                onclick="document.getElementById('photoInput').click();">
                            <i class="bi bi-camera"></i>
                        </button>
                    </div>
                    
                    <h5 class="fw-semibold mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted mb-3">{{ Auth::user()->role->display_name }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                            <i class="bi bi-building me-1"></i> {{ Auth::user()->faculty ?? '-' }}
                        </span>
                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                            <i class="bi bi-diagram-3 me-1"></i> {{ Auth::user()->department ?? '-' }}
                        </span>
                    </div>
                    
                    <form id="photoForm" enctype="multipart/form-data" style="display: none;">
                        @csrf
                        <input type="file" id="photoInput" name="photo" accept="image/*" onchange="uploadPhoto()">
                    </form>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="showEditForm()">
                            <i class="bi bi-pencil me-1"></i> Edit Profile
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="showPasswordForm()">
                            <i class="bi bi-key me-1"></i> Ubah Password
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Profile -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" id="profileInfo">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informasi Profile
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted">Nama Lengkap</td>
                                    <td class="fw-semibold">{{ Auth::user()->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">NIP</td>
                                    <td>{{ Auth::user()->nip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Fakultas</td>
                                    <td>{{ Auth::user()->faculty ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Program Studi</td>
                                    <td>{{ Auth::user()->department ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email</td>
                                    <td>{{ Auth::user()->email }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%" class="text-muted">Username</td>
                                    <td>{{ Auth::user()->username }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. Telepon</td>
                                    <td>{{ Auth::user()->phone_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Alamat</td>
                                    <td>{{ Auth::user()->address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td>
                                        @if(Auth::user()->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                                <i class="bi bi-x-circle me-1"></i> Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Bergabung</td>
                                    <td><small>{{ Auth::user()->created_at->format('d F Y') }}</small></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Edit Profile (Hidden) -->
            <div class="card border-0 shadow-sm mt-4" id="profileForm" style="display: none;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-pencil-square text-warning me-2"></i>
                        Edit Profile
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('kaprodi.profile.update') }}" id="editProfileForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ Auth::user()->name }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ Auth::user()->email }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">No. Telepon</label>
                                <input type="text" 
                                       name="phone_number" 
                                       class="form-control @error('phone_number') is-invalid @enderror" 
                                       value="{{ Auth::user()->phone_number }}">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">NIP</label>
                                <input type="text" 
                                       class="form-control bg-light" 
                                       value="{{ Auth::user()->nip }}" 
                                       readonly disabled>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Fakultas</label>
                                <input type="text" 
                                       class="form-control bg-light" 
                                       value="{{ Auth::user()->faculty }}" 
                                       readonly disabled>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Program Studi</label>
                                <input type="text" 
                                       class="form-control bg-light" 
                                       value="{{ Auth::user()->department }}" 
                                       readonly disabled>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Alamat</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ Auth::user()->address }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="hideEditForm()">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Form Ubah Password (Hidden) -->
            <div class="card border-0 shadow-sm mt-4" id="passwordForm" style="display: none;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-key text-warning me-2"></i>
                        Ubah Password
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('kaprodi.profile.update') }}" id="changePasswordForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Password Saat Ini</label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="current_password" 
                                           id="current_password"
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           required>
                                    <span class="input-group-text bg-white" onclick="togglePassword('current_password')" style="cursor: pointer;">
                                        <i class="bi bi-eye"></i>
                                    </span>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="new_password" 
                                           id="new_password"
                                           class="form-control @error('new_password') is-invalid @enderror" 
                                           required>
                                    <span class="input-group-text bg-white" onclick="togglePassword('new_password')" style="cursor: pointer;">
                                        <i class="bi bi-eye"></i>
                                    </span>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="new_password_confirmation" 
                                           id="new_password_confirmation"
                                           class="form-control" 
                                           required>
                                    <span class="input-group-text bg-white" onclick="togglePassword('new_password_confirmation')" style="cursor: pointer;">
                                        <i class="bi bi-eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div id="passwordMatchMessage" class="mb-3"></div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="hidePasswordForm()">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-warning" id="changePasswordBtn">
                                <i class="bi bi-key me-1"></i> Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistik Peminjaman -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-bar-chart text-primary me-2"></i>
                        Statistik Peminjaman
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <h3 class="mb-1 text-primary fw-bold">{{ $totalBorrowings ?? 0 }}</h3>
                                <small class="text-muted">Total Request</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <h3 class="mb-1 text-success fw-bold">{{ $activeBorrowings ?? 0 }}</h3>
                                <small class="text-muted">Menunggu</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <h3 class="mb-1 text-warning fw-bold">{{ $pendingBorrowings ?? 0 }}</h3>
                                <small class="text-muted">Disetujui</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="text-center p-3 bg-light rounded-3">
                                <h3 class="mb-1 text-info fw-bold">{{ $returnedBorrowings ?? 0 }}</h3>
                                <small class="text-muted">Permintaan Tidak Tersedia</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08) !important;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem 0;
}

.badge {
    font-weight: 500;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
}

.input-group-text {
    border: 1px solid #ced4da;
    background-color: white;
}
</style>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = field.parentElement.querySelector('i');
    
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
document.getElementById('new_password_confirmation')?.addEventListener('keyup', function() {
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
    })
    .catch(error => {
        alert('Terjadi kesalahan jaringan');
    });
}

// Prevent double submit
document.getElementById('changePasswordForm')?.addEventListener('submit', function(e) {
    const password = document.getElementById('new_password').value;
    const confirm = document.getElementById('new_password_confirmation').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Password tidak cocok!');
        return false;
    }
    
    document.getElementById('changePasswordBtn').disabled = true;
    document.getElementById('changePasswordBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';
});

document.getElementById('editProfileForm')?.addEventListener('submit', function(e) {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
});
</script>
@endsection