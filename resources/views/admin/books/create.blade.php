@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle text-primary"></i> 
                        Input Buku
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Judul Buku <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           value="{{ old('title') }}"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Penulis <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="author" 
                                                   class="form-control @error('author') is-invalid @enderror" 
                                                   value="{{ old('author') }}"
                                                   required>
                                            @error('author')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Penerbit <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   name="publisher" 
                                                   class="form-control @error('publisher') is-invalid @enderror" 
                                                   value="{{ old('publisher') }}"
                                                   required>
                                            @error('publisher')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">ISBN</label>
                                            <input type="text" 
                                                   name="isbn" 
                                                   class="form-control @error('isbn') is-invalid @enderror" 
                                                   value="{{ old('isbn') }}"
                                                   placeholder="978-602-1234-56-7">
                                            @error('isbn')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Tahun Terbit <span class="text-danger">*</span></label>
                                            <input type="number" 
                                                   name="publisher_year" 
                                                   class="form-control @error('publisher_year') is-invalid @enderror" 
                                                   value="{{ old('publisher_year', date('Y')) }}"
                                                   min="1900"
                                                   max="{{ date('Y') }}"
                                                   required>
                                            @error('publisher_year')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Jumlah Halaman</label>
                                            <input type="number" 
                                                   name="pages" 
                                                   class="form-control @error('pages') is-invalid @enderror" 
                                                   value="{{ old('pages') }}"
                                                   min="1">
                                            @error('pages')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                                <option value="">Pilih Kategori</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Bahasa</label>
                                            <input type="text" 
                                                   name="language" 
                                                   class="form-control @error('language') is-invalid @enderror" 
                                                   value="{{ old('language', 'Indonesia') }}">
                                            @error('language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="description" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-4">
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Informasi Stok & Lokasi</h6>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Stok Total <span class="text-danger">*</span></label>
                                            <input type="number" 
                                                   name="total_stock" 
                                                   class="form-control @error('total_stock') is-invalid @enderror" 
                                                   value="{{ old('total_stock', 1) }}"
                                                   min="0"
                                                   required>
                                            @error('total_stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Lokasi Rak</label>
                                            <input type="text" 
                                                   name="location_rack" 
                                                   class="form-control @error('location_rack') is-invalid @enderror" 
                                                   value="{{ old('location_rack') }}"
                                                   placeholder="Contoh: RA-01-02">
                                            @error('location_rack')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Harga</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" 
                                                       name="price" 
                                                       class="form-control @error('price') is-invalid @enderror" 
                                                       value="{{ old('price') }}"
                                                       min="0"
                                                       step="1000">
                                            </div>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Cover Buku</label>
                                            <input type="file" 
                                                   name="cover_image" 
                                                   class="form-control @error('cover_image') is-invalid @enderror"
                                                   accept="image/*"
                                                   onchange="previewImage(this)">
                                            @error('cover_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="mt-2" id="imagePreview" style="display: none;">
                                                <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   name="is_active" 
                                                   class="form-check-input" 
                                                   id="isActive"
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="isActive">
                                                Aktif (bisa dipinjam)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Buku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endpush