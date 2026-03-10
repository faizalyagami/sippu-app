{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="container-fluid">
    <div class="glass-card p-4">
        <h4 class="mb-4">
            <i class="bi bi-person-plus text-primary"></i> 
            Tambah User Baru
        </h4>

        <form method="POST" action="{{ route('admin.users.store') }}" id="userForm">
            @csrf

            <div class="row">
                <!-- Informasi Dasar -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" 
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" 
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="username" 
                           class="form-control @error('username') is-invalid @enderror" 
                           value="{{ old('username') }}" 
                           required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-select" required>
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" 
                                {{ old('role_id', $selectedRole ?? '') == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Informasi Tambahan -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIP/NIM</label>
                    <input type="text" 
                           name="nip" 
                           class="form-control @error('nip') is-invalid @enderror" 
                           value="{{ old('nip') }}">
                    @error('nip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" 
                           name="phone_number" 
                           class="form-control @error('phone_number') is-invalid @enderror" 
                           value="{{ old('phone_number') }}">
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Fakultas</label>
                    <input type="text" 
                           name="faculty" 
                           class="form-control @error('faculty') is-invalid @enderror" 
                           value="{{ old('faculty') }}">
                    @error('faculty')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Departemen/Prodi</label>
                    <input type="text" 
                           name="department" 
                           class="form-control @error('department') is-invalid @enderror" 
                           value="{{ old('department') }}">
                    @error('department')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" 
                              class="form-control @error('address') is-invalid @enderror" 
                              rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Informasi Password -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="form-control @error('password') is-invalid @enderror" 
                               required>
                        <span class="input-group-text" onclick="togglePassword('password')" style="cursor: pointer;">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </span>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="password-strength mt-2">
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" id="passwordStrength" style="width: 0%;"></div>
                        </div>
                        <small class="text-muted" id="passwordHelp">Minimal 8 karakter</small>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation"
                               class="form-control" 
                               required>
                        <span class="input-group-text" onclick="togglePassword('password_confirmation')" style="cursor: pointer;">
                            <i class="bi bi-eye" id="toggleConfirmIcon"></i>
                        </span>
                    </div>
                    <div id="passwordMatch" class="mt-1"></div>
                </div>

                <!-- Supplier (khusus role supplier) -->
                <div class="col-12 mb-3" id="supplierField" style="display: none;">
                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }} - {{ $supplier->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="col-12 mb-3">
                    <div class="form-check">
                        <input type="checkbox" 
                               name="is_active" 
                               class="form-check-input" 
                               id="isActive" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">
                            Aktif (user dapat login)
                        </label>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Toggle password visibility
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

// Password strength indicator
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

// Password match check
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

// Show/hide supplier field based on role
document.querySelector('select[name="role_id"]').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const supplierField = document.getElementById('supplierField');
    
    if (selectedOption.text.includes('Supplier')) {
        supplierField.style.display = 'block';
        document.querySelector('select[name="supplier_id"]').setAttribute('required', 'required');
    } else {
        supplierField.style.display = 'none';
        document.querySelector('select[name="supplier_id"]').removeAttribute('required');
    }
});

// Auto-generate username from email
document.querySelector('input[name="email"]').addEventListener('keyup', function() {
    const usernameField = document.querySelector('input[name="username"]');
    if (!usernameField.value) {
        usernameField.value = this.value.split('@')[0];
    }
});

// Prevent double submit
document.getElementById('userForm').addEventListener('submit', function(e) {
    if (this.classList.contains('submitted')) {
        e.preventDefault();
        return false;
    }
    this.classList.add('submitted');
});
</script>
@endsection