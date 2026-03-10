{{-- resources/views/admin/reports/borrowings.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-arrow-left-right text-primary"></i> Laporan Peminjaman Buku
        </h4>
        <div>
            <a href="{{ route('admin.reports.borrowings', ['export' => 'pdf']) }}" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> Export PDF
            </a>
            <a href="{{ route('admin.reports.borrowings', ['export' => 'excel']) }}" class="btn btn-success">
                <i class="bi bi-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Peminjam</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua Peminjam</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Tampilkan Laporan
                    </button>
                    <a href="{{ route('admin.reports.borrowings') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Peminjaman</h6>
                    <h3>{{ $statistics['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h3>{{ $statistics['pending'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Approved</h6>
                    <h3>{{ $statistics['approved'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Borrowed</h6>
                    <h3>{{ $statistics['borrowed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h6>Returned</h6>
                    <h3>{{ $statistics['returned'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>Overdue</h6>
                    <h3>{{ $statistics['overdue'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h6>Total Buku Dipinjam</h6>
                    <h3>{{ $statistics['total_books_borrowed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Total Denda</h6>
                    <h3>Rp {{ number_format($statistics['total_penalty'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Peminjaman -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Grafik Peminjaman per Hari</h5>
        </div>
        <div class="card-body">
            <canvas id="borrowingChart" height="100"></canvas>
        </div>
    </div>

    <div class="row">
        <!-- Tabel Peminjaman -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detail Peminjaman</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>No. Pinjam</th>
                                    <th>Peminjam</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Tenggat</th>
                                    <th>Tgl Kembali</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Denda</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($borrowings as $borrowing)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $borrowing->borrowing_number }}</td>
                                    <td>{{ $borrowing->user->name }}</td>
                                    <td>{{ $borrowing->borrowing_date->format('d/m/Y') }}</td>
                                    <td>{{ $borrowing->expected_return_date->format('d/m/Y') }}</td>
                                    <td>{{ $borrowing->actual_return_date ? $borrowing->actual_return_date->format('d/m/Y') : '-' }}</td>
                                    <td>{{ $borrowing->total_items }}</td>
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
                                        @if($borrowing->penalty_amount > 0)
                                            Rp {{ number_format($borrowing->penalty_amount, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data peminjaman</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buku Populer -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Buku Paling Populer</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Judul Buku</th>
                                    <th>Penulis</th>
                                    <th>Total Dipinjam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($popularBooks as $book)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $book->title }}</td>
                                    <td>{{ $book->author }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $book->total_borrowed }}x</span>
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
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('borrowingChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($borrowingsByDay->pluck('date')) !!},
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: {!! json_encode($borrowingsByDay->pluck('total')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1
                }
            }
        }
    });
</script>
@endpush
@endsection