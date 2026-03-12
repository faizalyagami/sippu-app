{{-- resources/views/admin/suppliers/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Supplier')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="mb-0">
            <i class="bi bi-plus-circle text-primary me-2"></i>
            Tambah Supplier Baru
        </h4>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Informasi Perusahaan -->
                    <div class="col-12 mb-4">
                        <h5 class="border-bottom pb-2">
                            <i class="bi bi-building me-2"></i>
                            Informasi Perusahaan
                        </h5>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Nama Supplier <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="Masukkan nama supplier"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Nama Perusahaan <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="company_name" 
                               class="form-control @error('company_name') is-invalid @enderror" 
                               value="{{ old('company_name') }}" 
                               placeholder="Masukkan nama perusahaan"
                               required>
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" 
                               placeholder="supplier@example.com"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            No. Telepon <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="phone_number" 
                               class="form-control @error('phone_number') is-invalid @enderror" 
                               value="{{ old('phone_number') }}" 
                               placeholder="021-12345678"
                               required>
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">NPWP</label>
                        <input type="text" 
                               name="npwp" 
                               class="form-control @error('npwp') is-invalid @enderror" 
                               value="{{ old('npwp') }}" 
                               placeholder="00.000.000.0-000.000">
                        @error('npwp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">
                            Alamat <span class="text-danger">*</span>
                        </label>
                        <textarea name="address" 
                                  class="form-control @error('address') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Jl. Contoh No. 123, Kota, Provinsi"
                                  required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Informasi Kontak -->
                    <div class="col-12 mb-4 mt-2">
                        <h5 class="border-bottom pb-2">
                            <i class="bi bi-person me-2"></i>
                            Informasi Kontak Person
                        </h5>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Nama Kontak Person <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="contact_person" 
                               class="form-control @error('contact_person') is-invalid @enderror" 
                               value="{{ old('contact_person') }}" 
                               placeholder="Nama lengkap kontak person"
                               required>
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Telepon Kontak Person <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="cp_phone" 
                               class="form-control @error('cp_phone') is-invalid @enderror" 
                               value="{{ old('cp_phone') }}" 
                               placeholder="0812-3456-7890"
                               required>
                        @error('cp_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Deskripsi / Catatan</label>
                        <textarea name="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Catatan tambahan tentang supplier (opsional)">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="is_active" 
                                   class="form-check-input" 
                                   id="isActive" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">
                                Aktif (supplier dapat melakukan transaksi)
                            </label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
}

.card {
    border-radius: 12px;
}

.border-bottom {
    border-bottom: 2px solid #dee2e6 !important;
}
</style>
@endsection