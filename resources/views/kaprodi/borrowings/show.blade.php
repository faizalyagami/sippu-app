@extends('layouts.app')

@section('title', 'Detail Permintaan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            Detail Permintaan
                        </h5>
                        <a href="{{ route('kaprodi.borrowings.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="alert alert-{{ $borrowing->status == 'pending' ? 'warning' : ($borrowing->status == 'approved' ? 'success' : 'danger') }} mb-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-{{ $borrowing->status == 'pending' ? 'clock-history' : ($borrowing->status == 'approved' ? 'check-circle' : 'x-circle') }} fs-4 me-2"></i>
                            <div>
                                <h6 class="mb-0">Status:
                                    @if($borrowing->status == 'pending')
                                    Menunggu Persetujuan
                                    @elseif($borrowing->status == 'approved')
                                    Disetujui
                                    @elseif($borrowing->status == 'cancelled')
                                    Dibatalkan
                                    @else
                                    Ditolak
                                    @endif
                                </h6>
                                @if($borrowing->status == 'pending')
                                <small>Permintaan Anda sedang menunggu persetujuan admin</small>
                                @elseif($borrowing->status == 'approved')
                                <small>Permintaan telah disetujui, Anda dapat mengambil buku di perpustakaan</small>
                                @elseif($borrowing->status == 'cancelled')
                                <small>Permintaan telah dibatalkan oleh Anda</small>
                                @else
                                <small>Permintaan ditolak oleh admin</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Peminjaman -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Nomor Permintaan</label>
                                <p class="fw-semibold mb-0">{{ $borrowing->borrowing_number ?? 'BRW-' . str_pad($borrowing->id, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small mb-1">Tanggal Permintaan</label>
                                <p class="fw-semibold mb-0">{{ $borrowing->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($borrowing->purpose)
                    <div class="mb-4">
                        <label class="text-muted small mb-1">Tujuan Peminjaman</label>
                        <p class="mb-0">{{ $borrowing->purpose }}</p>
                    </div>
                    @endif

                    <!-- Daftar Buku -->
                    <!-- Daftar Buku -->
                    <h6 class="fw-semibold mb-3">Daftar Buku</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Judul Buku</th>
                                    <th>Penulis</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($borrowing->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->book->title }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $item->book->isbn ?? '-' }}</small>
                                    </td>
                                    <td>{{ $item->book->author }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-center">
                                        @php
                                        // Ambil status dari borrowing jika item status pending tapi borrowing cancelled
                                        $displayStatus = $item->status;
                                        if ($displayStatus == 'pending' && $borrowing->status == 'cancelled') {
                                        $displayStatus = 'cancelled';
                                        }
                                        @endphp

                                        @if($displayStatus == 'pending')
                                        <span class="badge bg-warning">Menunggu</span>
                                        @elseif($displayStatus == 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                        @elseif($displayStatus == 'cancelled')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                        @elseif($displayStatus == 'rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                        @else
                                        <span class="badge bg-secondary">{{ $displayStatus }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada buku</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($borrowing->status == 'pending')
                    <div class="mt-4 text-end">
                        <button type="button" class="btn btn-danger" onclick="cancelBorrowing({{ $borrowing->id }})">
                            <i class="bi bi-x-circle me-1"></i> Batalkan Permintaan
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function cancelBorrowing(id) {
        if (confirm('Apakah Anda yakin ingin membatalkan permintaan ini?')) {
            fetch(`{{ url('kaprodi/borrowings') }}/${id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal membatalkan permintaan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan: ' + error.message);
                });
        }
    }
</script>
@endpush
@endsection

{{-- KODE UNTUK MEMBERSIHKAN KERANJANG --}}
@if(session('clear_cart_now'))
@push('scripts')
<script>
    (function() {
        console.log('Membersihkan keranjang...');
        // Bersihkan localStorage
        localStorage.removeItem('borrowingCart');

        // Hapus flag session via AJAX
        fetch('{{ route("kaprodi.borrowings.clear-cart-session") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Keranjang berhasil dikosongkan');
                }
            })
            .catch(err => console.error('Error clearing cart:', err));
    })();
</script>
@endpush
@endif