{{-- resources/views/admin/categories/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-plus-circle text-primary"></i> 
                Tambah Kategori Baru
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.create') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="Contoh: Fiksi, Non-Fiksi, Agama, dll"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Nama kategori harus unik dan tidak boleh sama dengan kategori lain</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Kategori</label>
                        <input type="text" 
                               name="code" 
                               class="form-control @error('code') is-invalid @enderror" 
                               value="{{ old('code') }}" 
                               placeholder="Contoh: FIC, NONFIC, AGR">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Kode unik untuk kategori (opsional)</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori Induk</label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">Tidak Ada (Kategori Utama)</option>
                            @foreach($parentCategories ?? [] as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Pilih jika ini adalah sub-kategori</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input type="checkbox" 
                                   name="is_active" 
                                   class="form-check-input" 
                                   id="isActive" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">
                                Aktif
                            </label>
                        </div>
                        <small class="text-muted d-block">Nonaktifkan jika kategori tidak ingin ditampilkan</small>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="4" 
                                  placeholder="Masukkan deskripsi kategori...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-generate code from name (optional)
    document.getElementById('name').addEventListener('keyup', function() {
        let name = this.value;
        let codeField = document.querySelector('input[name="code"]');
        
        if (name && !codeField.value) {
            // Generate code from first 3 letters of each word
            let words = name.split(' ');
            let code = '';
            
            if (words.length === 1) {
                code = name.substring(0, 3).toUpperCase();
            } else {
                words.forEach(word => {
                    if (word.length > 0) {
                        code += word.substring(0, 1).toUpperCase();
                    }
                });
            }
            
            codeField.value = code;
        }
    });
</script>
@endpush