@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square text-warning"></i> 
                        Form Edit Buku
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-8">
                                <!-- Sama dengan form create, tapi value diisi dengan $book -->
                                <div class="mb-3">
                                    <label class="form-label">Judul Buku <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="title" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           value="{{ old('title', $book->title) }}"
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
                                                   value="{{ old('author', $book->author) }}"
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
                                                   value="{{ old('publisher', $book->publisher) }}"
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
                                                   value="{{ old('isbn', $book->isbn) }}">
                                            @error('isbn')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Tahun Terbit <span class="text-danger">*</span></label>
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
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Jumlah Halaman</label>
                                            <input type="number" 
                                                   name="pages" 
                                                   class="form-control @error('pages') is-invalid @enderror" 
                                                   value="{{ old('pages', $book->pages) }}"
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
                                                        {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
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
                                                   value="{{ old('language', $book->language) }}">
                                            @error('language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="description" 