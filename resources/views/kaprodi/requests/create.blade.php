{{-- resources/views/kaprodi/requests/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Request Buku Baru')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('kaprodi.requests.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="mb-0">
            <i class="bi bi-plus-circle text-primary"></i> 
            Request Buku Baru
        </h4>
    </div>

    <div class="glass-card p-4">
        <form method="POST" action="{{ route('kaprodi.requests.store') }}" id="requestForm">
            @csrf

            <div class="row">
                <!-- Informasi Buku -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Judul Buku <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="book_title" 
                           class="form-control @error('book_title') is-invalid @enderror" 
                           value="{{ old('book_title') }}" 
                           placeholder="Masukkan judul buku"
                           required>
                    @error('book_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Penulis</label>
                    <input type="text" 
                           name="author" 
                           class="form-control @error('author') is-invalid @enderror" 
                           value="{{ old('author') }}" 
                           placeholder="Nama penulis">
                    @error('author')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">ISBN</label>
                    <input type="text" 
                           name="isbn" 
                           class="form-control @error('isbn') is-invalid @enderror" 
                           value="{{ old('isbn') }}" 
                           placeholder="ISBN (jika ada)">
                    @error('isbn')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Penerbit</label>
                    <input type="text" 
                           name="publisher" 
                           class="form-control @error('publisher') is-invalid @enderror" 
                           value="{{ old('publisher') }}" 
                           placeholder="Nama penerbit">
                    @error('publisher')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" 
                           name="publication_year" 
                           class="form-control @error('publication_year') is-invalid @enderror" 
                           value="{{ old('publication_year') }}" 
                           min="1900" 
                           max="{{ date('Y') }}"
                           placeholder="YYYY">
                    @error('publication_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Edisi</label>
                    <input type="text" 
                           name="edition" 
                           class="form-control @error('edition') is-invalid @enderror" 
                           value="{{ old('edition') }}" 
                           placeholder="Contoh: Edisi 3, Cetakan 2">
                    @error('edition')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Jumlah Request <span class="text-danger">*</span></label>
                    <input type="number" 
                           name="quantity_requested" 
                           class="form-control @error('quantity_requested') is-invalid @enderror" 
                           value="{{ old('quantity_requested', 1) }}" 
                           min="1" 
                           max="10"
                           required>
                    @error('quantity_requested')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Prioritas <span class="text-danger">*</span></label>
                    <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Alasan Request <span class="text-danger">*</span></label>
                    <textarea name="reason" 
                              class="form-control @error('reason') is-invalid @enderror" 
                              rows="3" 
                              placeholder="Jelaskan alasan mengapa buku ini diperlukan (untuk bahan ajar, penelitian, referensi, dll)"
                              required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Spesifikasi/Catatan Tambahan</label>
                    <textarea name="specifications" 
                              class="form-control @error('specifications') is-invalid @enderror" 
                              rows="2" 
                              placeholder="Contoh: edisi terbaru, hardcover, bahasa Indonesia">{{ old('specifications') }}</textarea>
                    @error('specifications')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Estimasi Budget (Rp)</label>
                    <input type="number" 
                           name="estimated_budget" 
                           class="form-control @error('estimated_budget') is-invalid @enderror" 
                           value="{{ old('estimated_budget') }}" 
                           min="0"
                           placeholder="Estimasi harga per buku">
                    @error('estimated_budget')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Alert Duplikasi -->
            <div id="duplicateAlert" class="alert alert-warning" style="display: none;">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <span id="duplicateMessage"></span>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between">
                <a href="{{ route('kaprodi.requests.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="bi bi-save"></i> Kirim Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Check duplicate book
let checkTimeout;
document.querySelector('input[name="book_title"]').addEventListener('keyup', function() {
    clearTimeout(checkTimeout);
    const title = this.value;
    const author = document.querySelector('input[name="author"]').value;
    
    if (title.length < 3) return;
    
    checkTimeout = setTimeout(() => {
        fetch(`{{ route('kaprodi.requests.check-duplicate') }}?title=${encodeURIComponent(title)}&author=${encodeURIComponent(author)}`)
        .then(response => response.json())
        .then(data => {
            const alert = document.getElementById('duplicateAlert');
            const message = document.getElementById('duplicateMessage');
            
            if (data.exists) {
                alert.style.display = 'block';
                message.innerHTML = 'Buku dengan judul dan penulis yang sama sudah ada di perpustakaan. Silakan cek katalog terlebih dahulu.';
            } else {
                alert.style.display = 'none';
            }
        });
    }, 500);
});

// Auto-fill from existing book
document.querySelector('input[name="isbn"]').addEventListener('blur', function() {
    const isbn = this.value;
    if (isbn.length < 10) return;
    
    fetch(`/api/books/search?isbn=${isbn}`)
    .then(response => response.json())
    .then(data => {
        if (data.book) {
            if (confirm('Buku ditemukan di katalog. Apakah Anda ingin mengisi data otomatis?')) {
                document.querySelector('input[name="book_title"]').value = data.book.title;
                document.querySelector('input[name="author"]').value = data.book.author;
                document.querySelector('input[name="publisher"]').value = data.book.publisher;
                document.querySelector('input[name="publication_year"]').value = data.book.publication_year;
                document.querySelector('select[name="category_id"]').value = data.book.category_id;
            }
        }
    });
});

// Prevent double submit
document.getElementById('requestForm').addEventListener('submit', function(e) {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...';
});
</script>
@endsection