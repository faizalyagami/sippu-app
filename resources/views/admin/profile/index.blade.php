@extends('layouts.app')

@section('title', 'Profile Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle text-primary"></i> 
                        Profile Admin
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="mb-3">
                                    @if(Auth::user()->photo)
                                        <img src="{{ Auth::user()->photo_url }}" 
                                             alt="Profile" 
                                             class="img-fluid rounded-circle mb-3"
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                             style="width: 150px; height: 150px; font-size: 48px;">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <label for="photo" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-camera"></i> Ganti Foto
                                        </label>
                                        <input type="file" id="photo" name="photo" class="d-none" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control" 
                                               value="{{ Auth::user()->name }}" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" 
                                               value="{{ Auth::user()->email }}" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" 
                                               value="{{ Auth::user()->username }}" readonly disabled>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIP</label>
                                        <input type="text" name="nip" class="form-control" 
                                               value="{{ Auth::user()->nip }}">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No. Telepon</label>
                                        <input type="text" name="phone_number" class="form-control" 
                                               value="{{ Auth::user()->phone_number }}">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fakultas</label>
                                        <input type="text" name="faculty" class="form-control" 
                                               value="{{ Auth::user()->faculty }}">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Departemen</label>
                                        <input type="text" name="department" class="form-control" 
                                               value="{{ Auth::user()->department }}">
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea name="address" class="form-control" rows="3">{{ Auth::user()->address }}</textarea>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <h5>Ubah Password</h5>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Password Saat Ini</label>
                                        <input type="password" name="current_password" class="form-control">
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Password Baru</label>
                                        <input type="password" name="new_password" class="form-control">
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Konfirmasi Password</label>
                                        <input type="password" name="new_password_confirmation" class="form-control">
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('photo').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            var formData = new FormData();
            formData.append('photo', this.files[0]);
            
            fetch('{{ route("admin.profile.photo") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal mengupload foto');
                }
            });
        }
    });
</script>
@endpush