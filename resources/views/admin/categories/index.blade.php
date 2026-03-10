@extends('layouts.app')

@section('title', 'Kategori Buku')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-tags text-primary"></i> Kategori Buku
        </h4>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Kategori
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Cari nama kategori, kode, atau deskripsi..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Kategori</h6>
                    <h3 class="mb-0">{{ $totalCategories }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Kategori Aktif</h6>
                    <h3 class="mb-0">{{ $activeCategories }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Buku</h6>
                    <h3 class="mb-0">{{ $totalBooksInCategories }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Nama Kategori</th>
                            <th width="10%">Kode</th>
                            <th width="15%">Slug</th>
                            <th width="15%">Parent</th>
                            <th width="10%">Jumlah Buku</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                                @if($category->parent)
                                    <br>
                                    <small class="text-muted">
                                        <i class="bi bi-arrow-return-right"></i> 
                                        Sub dari: {{ $category->parent->name }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $category->code ?? '-' }}</span>
                            </td>
                            <td><code>{{ $category->slug }}</code></td>
                            <td>
                                @if($category->parent)
                                    <span class="badge bg-info">{{ $category->parent->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $category->books_count > 0 ? 'success' : 'secondary' }}">
                                    {{ $category->books_count }} Buku
                                </span>
                            </td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Edit"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $category->id }}"
                                            title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($category->books_count == 0 && $category->children_count == 0)
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Yakin ingin menghapus kategori {{ $category->name }}?')"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            disabled
                                            title="Tidak dapat dihapus (masih memiliki relasi)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Detail Kategori -->
                        <div class="modal fade" id="detailModal{{ $category->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="bi bi-info-circle"></i> 
                                            Detail Kategori: {{ $category->name }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <th width="40%">Nama Kategori</th>
                                                        <td>: <strong>{{ $category->name }}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Kode</th>
                                                        <td>: <span class="badge bg-secondary">{{ $category->code ?? '-' }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Slug</th>
                                                        <td>: <code>{{ $category->slug }}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Kategori Induk</th>
                                                        <td>: 
                                                            @if($category->parent)
                                                                <span class="badge bg-info">{{ $category->parent->name }}</span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <th width="40%">Jumlah Buku</th>
                                                        <td>: 
                                                            <span class="badge bg-{{ $category->books_count > 0 ? 'success' : 'secondary' }}">
                                                                {{ $category->books_count }} Buku
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jumlah Sub-Kategori</th>
                                                        <td>: 
                                                            <span class="badge bg-info">
                                                                {{ $category->children_count }} Sub
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status</th>
                                                        <td>: 
                                                            @if($category->is_active)
                                                                <span class="badge bg-success">Aktif</span>
                                                            @else
                                                                <span class="badge bg-danger">Nonaktif</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Dibuat</th>
                                                        <td>: {{ $category->created_at->format('d/m/Y H:i') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        
                                        @if($category->description)
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6>Deskripsi:</h6>
                                                <p class="border p-2 rounded">{{ $category->description }}</p>
                                            </div>
                                        </div>
                                        @endif

                                        @if($category->children->count() > 0)
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6>Sub-Kategori:</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Nama</th>
                                                                <th>Kode</th>
                                                                <th>Jumlah Buku</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($category->children as $child)
                                                            <tr>
                                                                <td>{{ $child->name }}</td>
                                                                <td>{{ $child->code }}</td>
                                                                <td>{{ $child->books_count }} Buku</td>
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
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-emoji-frown fs-1 d-block mb-3 text-muted"></i>
                                <h5 class="text-muted">Tidak ada data kategori</h5>
                                @if(request('search') || request('status'))
                                    <p class="text-muted">Coba reset filter Anda</p>
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                                    </a>
                                @else
                                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mt-3">
                                        <i class="bi bi-plus-circle"></i> Tambah Kategori Baru
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} 
                    dari {{ $categories->total() }} data
                </div>
                <div class="d-flex gap-2">
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                        </a>
                    @endif
                    <div>
                        {{ $categories->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Auto submit filter when status changes
    document.querySelector('select[name="status"]').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endpush