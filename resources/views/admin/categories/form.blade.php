@extends('layouts.app')

@section('title', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                @if(isset($category))
                    <i class="bi bi-pencil-square text-warning"></i> Edit Kategori: {{ $category->name }}
                @else
                    <i class="bi bi-plus-circle text-primary"></i> Tambah Kategori Baru
                @endif
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" 
                  method="POST">
                @csrf
                @if(isset($category))
                    @method('PUT')
                @endif
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $category->name ?? '') }}" 
                               placeholder="Contoh: Fiksi, Non-Fiksi, Agama"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode Kategori <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="code" 
                               class="form-control @error('code') is-invalid @enderror" 
                               value="{{ old('code', $category->code ?? '') }}" 
                               placeholder="Contoh: FIC, NFIC, AGR"
                               required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Kode unik untuk kategori (maksimal 50 karakter)</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori Induk</label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">Tidak Ada (Kategori Utama)</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" 
                                    {{ old('parent_id', $category->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
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
                                   {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
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
                                  placeholder="Masukkan deskripsi kategori...">{{ old('description', $category->description ?? '') }}</textarea>
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
                    <button type="submit" class="btn btn-{{ isset($category) ? 'warning' : 'primary' }}">
                        <i class="bi bi-save"></i> 
                        {{ isset($category) ? 'Update Kategori' : 'Simpan Kategori' }}
                    </button>
                </div>
            </form>

            @if(isset($category) && $category->children->count() > 0)
            <div class="mt-4">
                <h6 class="text-warning">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Kategori ini memiliki {{ $category->children->count() }} sub-kategori
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Sub-Kategori</th>
                                <th>Kode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->children as $child)
                            <tr>
                                <td>{{ $child->name }}</td>
                                <td>{{ $child->code }}</td>
                                <td>
                                    @if($child->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-generate code from name (optional)
    document.querySelector('input[name="name"]').addEventListener('keyup', function() {
        let name = this.value;
        let codeField = document.querySelector('input[name="code"]');
        
        // Only auto-generate if code field is empty
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
                // Take first 3 characters if code is too long
                if (code.length > 3) {
                    code = code.substring(0, 3);
                }
            }
            
            codeField.value = code;
        }
    });

    // Prevent selecting self as parent
    @if(isset($category))
    document.querySelector('select[name="parent_id"]').addEventListener('change', function() {
        if (this.value == {{ $category->id }}) {
            alert('Kategori tidak bisa menjadi parent dari dirinya sendiri!');
            this.value = '';
        }
    });
    @endif
</script>
@endpush