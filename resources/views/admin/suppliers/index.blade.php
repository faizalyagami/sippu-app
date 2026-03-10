{{-- resources/views/admin/suppliers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Supplier Buku')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-truck text-primary"></i> Supplier Buku
        </h4>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Supplier
        </a>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Supplier</h6>
                    <h3>{{ $suppliers->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Supplier Aktif</h6>
                    <h3>{{ $suppliers->where('is_active', true)->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Total Pengadaan</h6>
                    <h3>{{ $totalProcurements ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Nilai Pengadaan</h6>
                    <h3>Rp {{ number_format($totalSpent ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Supplier</th>
                            <th>Perusahaan</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Kontak Person</th>
                            <th>Total Pengadaan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->company_name }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->phone_number }}</td>
                            <td>
                                {{ $supplier->contact_person }}<br>
                                <small class="text-muted">{{ $supplier->cp_phone }}</small>
                            </td>
                            <td>{{ $supplier->procurements_count }} Pengadaan</td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailModal{{ $supplier->id }}"
                                            title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" 
                                          method="POST" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Yakin ingin menghapus supplier ini?')"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Detail Supplier -->
                        <div class="modal fade" id="detailModal{{ $supplier->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Supplier: {{ $supplier->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <th width="40%">Nama Supplier</th>
                                                        <td>: {{ $supplier->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Perusahaan</th>
                                                        <td>: {{ $supplier->company_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td>: {{ $supplier->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>No. Telepon</th>
                                                        <td>: {{ $supplier->phone_number }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>NPWP</th>
                                                        <td>: {{ $supplier->npwp ?? '-' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <th width="40%">Kontak Person</th>
                                                        <td>: {{ $supplier->contact_person }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>CP Telepon</th>
                                                        <td>: {{ $supplier->cp_phone }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Alamat</th>
                                                        <td>: {{ $supplier->address }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status</th>
                                                        <td>: 
                                                            @if($supplier->is_active)
                                                                <span class="badge bg-success">Aktif</span>
                                                            @else
                                                                <span class="badge bg-danger">Nonaktif</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6>Deskripsi:</h6>
                                                <p>{{ $supplier->description ?? 'Tidak ada deskripsi' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="bi bi-emoji-frown fs-1 d-block mb-3"></i>
                                <h5>Tidak ada data supplier</h5>
                                <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary mt-3">
                                    Tambah Supplier Baru
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $suppliers->firstItem() ?? 0 }} - {{ $suppliers->lastItem() ?? 0 }} 
                    dari {{ $suppliers->total() }} data
                </div>
                <div>
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection