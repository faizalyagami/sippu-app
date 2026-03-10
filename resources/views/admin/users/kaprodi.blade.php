@extends('layouts.app')

@section('title', 'Daftar Kaprodi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-mortarboard text-primary"></i> 
            Daftar Ketua Program Studi (Kaprodi)
        </h4>
        <a href="{{ route('admin.users.create') }}?role=kaprodi" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Tambah Kaprodi
        </a>
    </div>

    <!-- Filter Card -->
    <div class="glass-card p-4 mb-4">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control bg-light border-0" 
                           placeholder="Cari nama, email, NIP, prodi..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="faculty" class="form-select bg-light border-0">
                    <option value="">Semua Fakultas</option>
                    @foreach($faculties as $faculty)
                        <option value="{{ $faculty }}" {{ request('faculty') == $faculty ? 'selected' : '' }}>
                            {{ $faculty }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-light border-0">
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
            <div class="col-12 text-end">
                <a href="{{ route('admin.users.kaprodi') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Kaprodi Cards -->
    <div class="row g-4">
        @forelse($kaprodis as $kaprodi)
        <div class="col-xl-4 col-md-6">
            <div class="glass-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; font-size: 24px;">
                            {{ strtoupper(substr($kaprodi->name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $kaprodi->name }}</h5>
                            <p class="mb-0 text-muted small">{{ $kaprodi->email }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">
                                <i class="bi bi-building me-1"></i> Fakultas:
                            </span>
                            <span class="fw-medium">{{ $kaprodi->faculty ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">
                                <i class="bi bi-diagram-3 me-1"></i> Prodi:
                            </span>
                            <span class="fw-medium">{{ $kaprodi->department ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">
                                <i class="bi bi-person-badge me-1"></i> NIP:
                            </span>
                            <span class="fw-medium">{{ $kaprodi->nip ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">
                                <i class="bi bi-telephone me-1"></i> Telepon:
                            </span>
                            <span class="fw-medium">{{ $kaprodi->phone_number ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">
                                <i class="bi bi-calendar me-1"></i> Terdaftar:
                            </span>
                            <span class="fw-medium">{{ $kaprodi->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($kaprodi->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('admin.users.edit', $kaprodi->id) }}" 
                               class="btn btn-sm btn-outline-warning" 
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-info" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailModal{{ $kaprodi->id }}"
                                    title="Detail">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-{{ $kaprodi->is_active ? 'danger' : 'success' }}" 
                                    onclick="toggleStatus({{ $kaprodi->id }})"
                                    title="{{ $kaprodi->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i class="bi bi-{{ $kaprodi->is_active ? 'pause' : 'play' }}"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <div class="modal fade" id="detailModal{{ $kaprodi->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-person-circle me-2"></i>
                            Detail Kaprodi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <div class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width: 100px; height: 100px; font-size: 36px;">
                                    {{ strtoupper(substr($kaprodi->name, 0, 1)) }}
                                </div>
                                <h5>{{ $kaprodi->name }}</h5>
                                <p class="text-muted mb-1">{{ $kaprodi->email }}</p>
                                <p class="text-muted small">Username: {{ $kaprodi->username }}</p>
                            </div>
                            <div class="col-md-8">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="35%">NIP:</th>
                                        <td>{{ $kaprodi->nip ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Fakultas:</th>
                                        <td>{{ $kaprodi->faculty ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Program Studi:</th>
                                        <td>{{ $kaprodi->department ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>No. Telepon:</th>
                                        <td>{{ $kaprodi->phone_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat:</th>
                                        <td>{{ $kaprodi->address ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if($kaprodi->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Terdaftar:</th>
                                        <td>{{ $kaprodi->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Terakhir Update:</th>
                                        <td>{{ $kaprodi->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <a href="{{ route('admin.users.edit', $kaprodi->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="glass-card p-5 text-center">
                <i class="bi bi-people fs-1 d-block mb-3 text-muted"></i>
                <h5 class="text-muted">Tidak ada data kaprodi</h5>
                @if(request('search') || request('faculty') || request('status'))
                    <p class="text-muted">Coba reset filter Anda</p>
                    <a href="{{ route('admin.users.kaprodi') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                    </a>
                @else
                    <a href="{{ route('admin.users.create') }}?role=kaprodi" class="btn btn-primary mt-3">
                        <i class="bi bi-person-plus"></i> Tambah Kaprodi
                    </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($kaprodis->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <small class="text-muted">
            Menampilkan {{ $kaprodis->firstItem() ?? 0 }} - {{ $kaprodis->lastItem() ?? 0 }} 
            dari {{ $kaprodis->total() }} data
        </small>
        {{ $kaprodis->withQueryString()->links() }}
    </div>
    @endif
</div>

<script>
function toggleStatus(userId) {
    if (confirm('Yakin ingin mengubah status user ini?')) {
        fetch(`/admin/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan jaringan');
        });
    }
}

// Auto submit filter
document.querySelector('select[name="faculty"]').addEventListener('change', function() {
    this.form.submit();
});

document.querySelector('select[name="status"]').addEventListener('change', function() {
    this.form.submit();
});
</script>

<style>
.user-avatar {
    transition: all 0.3s ease;
}

.user-avatar:hover {
    transform: scale(1.05);
}

.glass-card {
    transition: transform 0.2s;
}

.glass-card:hover {
    transform: translateY(-2px);
}
</style>
@endsection