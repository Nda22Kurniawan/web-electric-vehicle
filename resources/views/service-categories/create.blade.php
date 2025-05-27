@extends('layouts.layout')

@section('title', 'Tambah Kategori Layanan')

@section('page-title', 'Tambah Kategori Layanan')
@section('page-subtitle', 'Form tambah kategori layanan baru')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i>
            Form Tambah Kategori Layanan
        </div>
        <div class="card-body">
            <form action="{{ route('service-categories.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Terjadi kesalahan!</strong> Silakan periksa form di bawah.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="Masukkan nama kategori" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Contoh: Servis Rutin, Perbaikan Mesin, dll.</small>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Masukkan deskripsi kategori (opsional)">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('service-categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Kategori
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Info Card -->
    <div class="card bg-light mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i>
            Informasi
        </div>
        <div class="card-body">
            <h5 class="card-title">Panduan Pengisian</h5>
            <ul>
                <li>Field dengan tanda (<span class="text-danger">*</span>) wajib diisi</li>
                <li>Nama kategori harus unik dan deskriptif</li>
                <li>Deskripsi membantu untuk memberikan penjelasan lebih detail tentang kategori</li>
                <li>Setelah dibuat, kategori dapat digunakan untuk mengelompokkan layanan</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto focus on name field
        $('#name').focus();
        
        // Auto hide error alert after 5 seconds
        setTimeout(function() {
            $('.alert-danger').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection