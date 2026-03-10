@extends('layouts.app')

@section('title', 'Detail Buku')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-book text-primary"></i> 
                        Detail Buku
                    </h5>
                    <div>
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('books.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Cover Buku -->
                        <div class="col-md-3 text-center">
                            <img src="{{ $book->cover_url }}" 
                                 alt="{{ $book->title }}" 
                                 class="img-fluid rounded shadow"
                                 style="max-height: 300px;">
                            
                            <div class="mt-3">
                                @if($book->is_active)
                                    <span class="badge bg-success p-2">Aktif</span>
                                @else
                                    <span class="badge bg-danger p-2">Nonaktif</span>
                                @endif
                                
                                <span class="badge bg-info p-2">{{ $book->category->name }}</span>
                            </div>
                        </div>
                        
                        <!-- Informasi Buku -->
                        <div class="col-md-9">
                            <h3>{{ $book->title }}</h3>
                            <p class="text-muted">oleh {{ $book->author }}</p>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">ISBN</th>
                                            <td>: {{ $book->isbn ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Penerbit</th>
                                            <td>: {{ $book->publisher }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tahun Terbit</th>
                                            <td>: {{ $book->publication_year }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bahasa</th>
                                            <td>: {{ $book->language }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jumlah Halaman</th>
                                            <td>: {{ $book->pages ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Total Stok</th>
                                            <td>: <strong>{{ $book->total_stock }}</strong> eksemplar</td>
                                        </tr>
                                        <tr>
                                            <th>Stok Tersedia</th>
                                            <td>: 
                                                <span class="badge bg-{{ $book->stock_status_color }}">
                                                    {{ $book->available_stock }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Stok Dipinjam</th>
                                            <td>: {{ $book->borrowed_stock }}</td>
                                        </tr>
                                        <tr>
                                            <th>Lokasi Rak</th>
                                            <td>: {{ $book->location_rack ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Harga</th>
                                            <td>: 
                                                @if($book->price)
                                                    Rp {{ number_format($book->price, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h6>Deskripsi:</h6>
                            <p class="text-justify">{{ $book->description ?? 'Tidak ada deskripsi' }}</p>
                        </div>
                    </div>
                    
                    <!-- Tab Informasi -->
                    <ul class="nav nav-tabs mt-4" id="bookTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="conditions-tab" data-bs-toggle="tab" 
                                    data-bs-target="#conditions" type="button" role="tab">
                                <i class="bi bi-box"></i> Kondisi Buku
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" 
                                    data-bs-target="#reviews" type="button" role="tab">
                                <i class="bi bi-star"></i> Ulasan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="history-tab" data-bs-toggle="tab" 
                                    data-bs-target="#history" type="button" role="tab">
                                <i class="bi bi-clock-history"></i> Riwayat Peminjaman
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="bookTabsContent">
                        <!-- Tab Kondisi Buku -->
                        <div class="tab-pane fade show active" id="conditions" role="tabpanel">
                            @if($book->bookConditions->count() > 0)
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Kode Buku</th>
                                            <th>Kondisi</th>
                                            <th>Status</th>
                                            <th>Terakhir Cek</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($book->bookConditions as $condition)
                                        <tr>
                                            <td>{{ $condition->book_code }}</td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $condition->condition == 'new' ? 'success' : 
                                                    ($condition->condition == 'good' ? 'info' : 
                                                    ($condition->condition == 'damaged' ? 'danger' : 'warning')) 
                                                }}">
                                                    {{ ucfirst($condition->condition) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($condition->is_available)
                                                    <span class="badge bg-success">Tersedia</span>
                                                @else
                                                    <span class="badge bg-danger">Dipinjam</span>
                                                @endif
                                            </td>
                                            <td>{{ $condition->last_check_date->format('d/m/Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-center py-3">Belum ada data kondisi buku</p>
                            @endif
                        </div>
                        
                        <!-- Tab Ulasan -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                            <p class="text-center py-3">Fitur ulasan akan segera hadir</p>
                        </div>
                        
                        <!-- Tab Riwayat -->
                        <div class="tab-pane fade" id="history" role="tabpanel">
                            <p class="text-center py-3">Fitur riwayat akan segera hadir</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection