@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-pencil-square text-warning me-2"></i>
            Edit Buku: {{ $book->title }}
        </h4>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $book->title) }}" 
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">ISBN</label>
                        <input type="text" 
                               name="isbn" 
                               class="form-control @error('isbn') is-invalid @enderror" 
                               value="{{ old('isbn', $book->isbn) }}">
                        @error('isbn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Penulis <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="author" 
                               class="form-control @error('author') is-invalid @enderror" 
                               value="{{ old('author', $book->author) }}" 
                               required>
                        @error('author')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Penerbit <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="publisher" 
                               class="form-control @error('publisher') is-invalid @enderror" 
                               value="{{ old('publisher', $book->publisher) }}" 
                               required>
                        @error('publisher')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Tahun Terbit <span class="text-danger">*</span></label>
                        <input type="number" 
                               name="publication_year" 
                               class="form-control @error('publication_year') is-invalid @enderror" 
                               value="{{ old('publication_year', $book->publication_year) }}" 
                               min="1900" 
                               max="{{ date('Y') }}" 
                               required>
                        @error('publication_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Jumlah Halaman</label>
                        <input type="number" 
                               name="pages" 
                               class="form-control @error('pages') is-invalid @enderror" 
                               value="{{ old('pages', $book->pages) }}" 
                               min="1">
                        @error('pages')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Cover Buku</label>
                        <input type="file" 
                               name="cover_image" 
                               class="form-control @error('cover_image') is-invalid @enderror" 
                               accept="image/*">
                        @error('cover_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($book->cover_image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$book->cover_image) }}" 
                                     alt="Cover" 
                                     style="height: 100px; width: auto; border-radius: 4px;">
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Lokasi Rak</label>
                        <input type="text" 
                               name="location_rack" 
                               class="form-control @error('location_rack') is-invalid @enderror" 
                               value="{{ old('location_rack', $book->location_rack) }}" 
                               placeholder="Contoh: RA-01-02">
                        @error('location_rack')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Stok Total <span class="text-danger">*</span></label>
                        <input type="number" 
                               name="total_stock" 
                               class="form-control @error('total_stock') is-invalid @enderror" 
                               value="{{ old('total_stock', $book->total_stock) }}" 
                               min="0" 
                               required>
                        <small class="text-muted">Stok tersedia saat ini: {{ $book->available_stock }}</small>
                        @error('total_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" 
                                   name="price" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   value="{{ old('price', $book->price) }}" 
                                   min="0"
                                   step="1000">
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Bahasa</label>
                        <input type="text" 
                               name="language" 
                               class="form-control @error('language') is-invalid @enderror" 
                               value="{{ old('language', $book->language) }}">
                        @error('language')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="4">{{ old('description', $book->description) }}</textarea>
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
                                   {{ old('is_active', $book->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">
                                Aktif (buku dapat dipinjam)
                            </label>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save me-1"></i> Update Buku
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection