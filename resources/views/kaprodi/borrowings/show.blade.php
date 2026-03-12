{{-- resources/views/kaprodi/borrowings/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('kaprodi.borrowings.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="mb-0">
            <i class="bi bi-file-text text-primary"></i> 
            Detail Peminjaman: {{ $borrowing->borrowing_number }}
        </h4>
    </div>

    <div class="row">
        <!-- Informasi Peminjaman -->
        <div class="col-md-4 mb-4">
            <div class="glass-card p-4">
                <h5 class="mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Informasi Peminjaman
                </h5>
                <table class="table table-borderless">
                    <tr>
                        <th width="45%">No. Peminjaman</th>
                        <td>: <strong>{{ $borrowing->borrowing_number }}</strong></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>: 
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-warning',
                                    'approved' => 'bg-info',
                                    'borrowed' => 'bg-primary',
                                    'returned' => 'bg-success',
                                    'overdue' => 'bg-danger',
                                    'cancelled' => 'bg-secondary'
                                ];
                                $statusTexts = [
                                    'pending' => 'Menunggu',
                                    'approved' => 'Disetujui',
                                    'borrowed' => 'Dipinjam',
                                    'returned' => 'Dikembalikan',
                                    'overdue' => 'Terlambat',
                                    'cancelled' => 'Dibatalkan'
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$borrowing->status] }}">
                                {{ $statusTexts[$borrowing->status] }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Pinjam</th>
                        <td>: {{ $borrowing->borrowing_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tenggat Pengembalian</th>
                        <td>: {{ $borrowing->expected_return_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Kembali</th>
                        <td>: 
                            @if($borrowing->actual_return_date)
                                {{ $borrowing->actual_return_date->format('d/m/Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Total Buku</th>
                        <td>: {{ $borrowing->total_items }} Buku</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Informasi Pemohon -->
        <div class="col-md-4 mb-4">
            <div class="glass-card p-4">
                <h5 class="mb-3">
                    <i class="bi bi-person me-2"></i>
                    Informasi Pemohon
                </h5>
                <table class="table table-borderless">
                    <tr>
                        <th width="45%">Nama</th>
                        <td>: <strong>{{ $borrowing->user->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>NIP</th>
                        <td>: {{ $borrowing->user->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Fakultas</th>
                        <td>: {{ $borrowing->user->faculty ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Program Studi</th>
                        <td>: {{ $borrowing->user->department ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>: {{ $borrowing->user->email }}</td>
                    </tr>
                    <tr>
                        <th>Telepon</th>
                        <td>: {{ $borrowing->user->phone_number ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Informasi Persetujuan -->
        @if($borrowing->approved_by)
        <div class="col-md-4 mb-4">
            <div class="glass-card p-4">
                <h5 class="mb-3">
                    <i class="bi bi-check-circle me-2 text-success"></i>
                    Informasi Persetujuan
                </h5>
                <table class="table table-borderless">
                    <tr>
                        <th width="45%">Disetujui Oleh</th>
                        <td>: <strong>{{ $borrowing->approvedBy->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal Setuju</th>
                        <td>: {{ $borrowing->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        <!-- Catatan -->
        @if($borrowing->purpose || $borrowing->notes || $borrowing->rejection_reason)
        <div class="col-12 mb-4">
            <div class="glass-card p-4">
                <h5 class="mb-3">
                    <i class="bi bi-chat-text me-2"></i>
                    Catatan
                </h5>
                @if($borrowing->purpose)
                    <p><strong>Tujuan:</strong> {{ $borrowing->purpose }}</p>
                @endif
                @if($borrowing->notes)
                    <p><strong>Catatan:</strong> {{ $borrowing->notes }}</p>
                @endif
                @if($borrowing->rejection_reason)
                    <p class="text-danger"><strong>Alasan Ditolak:</strong> {{ $borrowing->rejection_reason }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Daftar Buku -->
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="mb-3">
                    <i class="bi bi-book me-2"></i>
                    Daftar Buku
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Judul Buku</th>
                                <th>Penulis</th>
                                <th>ISBN</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($borrowing->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $item->book->title }}</strong>
                                </td>
                                <td>{{ $item->book->author }}</td>
                                <td>{{ $item->book->isbn ?? '-' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td>
                                    @php
                                        $itemStatusClasses = [
                                            'borrowed' => 'bg-primary',
                                            'partial' => 'bg-warning',
                                            'returned' => 'bg-success',
                                            'damaged' => 'bg-danger',
                                            'lost' => 'bg-dark'
                                        ];
                                        $itemStatusTexts = [
                                            'borrowed' => 'Dipinjam',
                                            'partial' => 'Sebagian',
                                            'returned' => 'Dikembalikan',
                                            'damaged' => 'Rusak',
                                            'lost' => 'Hilang'
                                        ];
                                    @endphp
                                    <span class="badge {{ $itemStatusClasses[$item->status] ?? 'bg-secondary' }}">
                                        {{ $itemStatusTexts[$item->status] ?? $item->status }}
                                    </span>
                                    @if($item->returned_quantity > 0)
                                        <br><small>Dikembalikan: {{ $item->returned_quantity }}</small>
                                    @endif
                                    @if($item->damaged_quantity > 0)
                                        <br><small class="text-danger">Rusak: {{ $item->damaged_quantity }}</small>
                                    @endif
                                    @if($item->lost_quantity > 0)
                                        <br><small class="text-dark">Hilang: {{ $item->lost_quantity }}</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection