@extends('layouts.app')

@section('title', 'Status Permintaan Saya')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-semibold">
            <i class="bi bi-list-check text-primary me-2"></i>
            Status Permintaan Saya
        </h4>
        <a href="{{ route('kaprodi.borrowings.checkout') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Ajukan Permintaan Baru
        </a>
    </div>

    <!-- Tabel Permintaan -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-table text-primary me-2"></i>
                Daftar Permintaan Saya
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">No. Permintaan</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Buku yang Diminta</th>
                            <th class="px-4 py-3 text-center">Jumlah</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($borrowings as $borrowing)
                        <tr>
                            <td class="px-4">
                                <strong>{{ $borrowing->borrowing_number ?? 'BRW-' . str_pad($borrowing->id, 4, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td class="px-4">{{ $borrowing->created_at->format('d/m/Y') }}</td>
                            <td class="px-4">
                                @foreach($borrowing->items as $item)
                                <div>{{ $item->book->title }}</div>
                                @endforeach
                            </td>
                            <td class="px-4 text-center">{{ $borrowing->total_items }}</td>
                            <td class="px-4 text-center">
                                @php
                                $badges = [
                                'pending' => ['bg-warning', 'Menunggu'],
                                'approved' => ['bg-success', 'Disetujui'],
                                'cancelled' => ['bg-danger', 'Dibatalkan'],
                                'rejected' => ['bg-danger', 'Ditolak']
                                ];
                                $badge = $badges[$borrowing->status] ?? ['bg-secondary', $borrowing->status];
                                @endphp
                                <span class="badge {{ $badge[0] }} bg-opacity-10 text-{{ str_replace('bg-', '', $badge[0]) }} px-3 py-2">
                                    {{ $badge[1] }}
                                </span>
                            </td>
                            <td class="px-4 text-center">
                                <a href="{{ route('kaprodi.borrowings.show', $borrowing->id) }}"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                                @if($borrowing->status == 'pending')
                                <button type="button"
                                    onclick="cancelBorrowing({{ $borrowing->id }})"
                                    class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-x-circle me-1"></i> Batalkan
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted">Belum ada permintaan</h5>
                                <a href="{{ route('kaprodi.borrowings.checkout') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle me-1"></i> Ajukan Permintaan Baru
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if(isset($borrowings) && $borrowings->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $borrowings->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
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