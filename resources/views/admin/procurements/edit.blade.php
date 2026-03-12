{{-- resources/views/admin/procurements/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Pengadaan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.procurements.show', $procurement->id) }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="mb-0">
            <i class="bi bi-pencil-square text-warning"></i> 
            Edit Pengadaan: {{ $procurement->procurement_number }}
        </h4>
    </div>

    <div class="glass-card p-4">
        <form method="POST" action="{{ route('admin.procurements.update', $procurement->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror" required>
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('vendor_id', $procurement->vendor_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }} - {{ $supplier->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Tanggal PO <span class="text-danger">*</span></label>
                    <input type="date" 
                           name="procurement_date" 
                           class="form-control @error('procurement_date') is-invalid @enderror" 
                           value="{{ old('procurement_date', $procurement->procurement_date->format('Y-m-d')) }}" 
                           required>
                    @error('procurement_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Tanggal Diharapkan</label>
                    <input type="date" 
                           name="expected_date" 
                           class="form-control @error('expected_date') is-invalid @enderror" 
                           value="{{ old('expected_date', $procurement->expected_date ? $procurement->expected_date->format('Y-m-d') : '') }}">
                    @error('expected_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                              rows="3">{{ old('notes', $procurement->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Untuk mengubah item, silakan batalkan pengadaan ini dan buat baru.
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.procurements.show', $procurement->id) }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-save"></i> Update Pengadaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection