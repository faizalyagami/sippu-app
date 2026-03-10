{{-- resources/views/admin/borrowings/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Peminjaman Buku')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-arrow-left-right text-primary"></i> Peminjaman Buku
        </h4>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Peminjaman</h6>
                    <h3>{{ $statistics['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Menunggu</h6>
                    <h3>{{ $statistics['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Aktif</h6>
                    <h3>{{ $statistics['active'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>Terlambat</h6>
                    <h3>{{ $statistics['overdue'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari No. Pinjam / Peminjam" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="user_id" class="form-select">
                        <option value="">Semua Peminjam</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>No. Pinjam</th>
                            <th>Peminjam</th>
                            <th>Tgl Pinjam</th>
                            <th>Tenggat</th>
                            <th>Jumlah Buku</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($borrowings as $borrowing)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $borrowing->borrowing_number }}</strong>
                            </td>
                            <td>{{ $borrowing->user->name }}</td>
                            <td>{{ $borrowing->borrowing_date->format('d/m/Y') }}</td>
                            <td>
                                {{ $borrowing->expected_return_date->format('d/m/Y') }}
                                @if($borrowing->status == 'borrowed' && $borrowing->expected_return_date < now())
                                    <span class="badge bg-danger">Terlambat</span>
                                @endif
                            </td>
                            <td>{{ $borrowing->total_items }} Buku</td>
                            <td>
                                @php
                                    $badges = [
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'borrowed' => 'primary',
                                        'returned' => 'success',
                                        'overdue' => 'danger',
                                        'cancelled' => 'secondary'
                                    ];
                                    $texts = [
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'borrowed' => 'Borrowed',
                                        'returned' => 'Returned',
                                        'overdue' => 'Overdue',
                                        'cancelled' => 'Cancelled'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $badges[$borrowing->status] }}">
                                    {{ $texts[$borrowing->status] }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.borrowings.show', $borrowing->id) }}" 
                                       class="btn btn-sm btn-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($borrowing->status == 'pending')
                                        <button type="button" 
                                                class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal{{ $borrowing->id }}"
                                                title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $borrowing->id }}"
                                                title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                    @if($borrowing->status == 'approved')
                                        <form action="{{ route('admin.borrowings.mark-borrowed', $borrowing->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" 
                                                    title="Tandai Dipinjam">
                                                <i class="bi bi-arrow-right-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($borrowing->status == 'borrowed')
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#returnModal{{ $borrowing->id }}"
                                                title="Proses Pengembalian">
                                            <i class="bi bi-arrow-left-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Approve -->
                        <div class="modal fade" id="approveModal{{ $borrowing->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.borrowings.approve', $borrowing->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Setujui Peminjaman</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Yakin ingin menyetujui peminjaman ini?</p>
                                            <p><strong>No. Peminjaman:</strong> {{ $borrowing->borrowing_number }}</p>
                                            <p><strong>Peminjam:</strong> {{ $borrowing->user->name }}</p>
                                            <p><strong>Jumlah Buku:</strong> {{ $borrowing->total_items }}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">Setujui</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Reject -->
                        <div class="modal fade" id="rejectModal{{ $borrowing->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.borrowings.reject', $borrowing->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tolak Peminjaman</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Yakin ingin menolak peminjaman ini?</p>
                                            <div class="mb-3">
                                                <label class="form-label">Alasan Penolakan</label>
                                                <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-emoji-frown fs-1 d-block mb-3"></i>
                                <h5>Tidak ada data peminjaman</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $borrowings->firstItem() ?? 0 }} - {{ $borrowings->lastItem() ?? 0 }} 
                    dari {{ $borrowings->total() }} data
                </div>
                <div>
                    {{ $borrowings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection