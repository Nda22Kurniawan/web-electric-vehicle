@extends('layouts.layout')

@section('title', 'Edit Pengguna')

@section('page-title', 'Edit Data Pengguna')
@section('page-subtitle', 'Form edit informasi pengguna')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit me-1"></i>
            Form Edit Pengguna
        </div>
        <div class="card-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Terjadi kesalahan!</strong> Silakan periksa form di bawah.
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                        
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" 
                               placeholder="Masukkan nama lengkap" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" 
                               placeholder="Masukkan alamat email" required>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                               placeholder="Masukkan nomor telepon" required>
                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" name="role" required>
                            <option value="admin" @if(old('role', $user->role) == 'admin') selected @endif>Admin</option>
                            <option value="mechanic" @if(old('role', $user->role) == 'mechanic') selected @endif>Mekanik</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" 
                               placeholder="Kosongkan jika tidak ingin mengubah">
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" 
                               placeholder="Konfirmasi password baru">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-info">
                                    <i class="fas fa-eye me-1"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- User Information Card -->
    <div class="card bg-light mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i>
            Informasi Pengguna
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Data Sistem</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Dibuat Pada
                            <span class="badge bg-primary rounded-pill">
                                {{ $user->created_at->format('d M Y H:i') }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Terakhir Diupdate
                            <span class="badge bg-primary rounded-pill">
                                {{ $user->updated_at->format('d M Y H:i') }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Terakhir Login
                            <span class="badge bg-info rounded-pill">
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('d M Y H:i') }}
                                @else
                                    Belum pernah login
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5 class="card-title">Status Akun</h5>
                    <div class="alert alert-{{ $user->email_verified_at ? 'success' : 'warning' }}">
                        <i class="fas fa-{{ $user->email_verified_at ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                        @if($user->email_verified_at)
                            Email terverifikasi pada {{ $user->email_verified_at->format('d M Y') }}
                        @else
                            Email belum terverifikasi
                        @endif
                    </div>
                    
                    @if(auth()->id() !== $user->id)
                        <a href="#" class="btn btn-sm btn-outline-secondary me-2" 
                           onclick="return confirm('Kirim ulang email verifikasi ke {{ $user->email }}?')">
                            <i class="fas fa-envelope me-1"></i> Kirim Verifikasi
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Danger Zone Card -->
    <div class="card border-danger">
        <div class="card-header text-white bg-danger">
            <i class="fas fa-exclamation-triangle me-1"></i>
            Zona Berbahaya
        </div>
        <div class="card-body">
            <h5 class="card-title text-danger">Hapus Akun Pengguna</h5>
            <p class="card-text">
                Menghapus akun pengguna akan menghapus semua data terkait secara permanen. 
                Aksi ini tidak dapat dibatalkan.
            </p>
            
            @if(auth()->id() === $user->id)
                <button class="btn btn-danger" disabled>
                    <i class="fas fa-trash-alt me-1"></i> Tidak Dapat Menghapus Akun Sendiri
                </button>
                <small class="text-danger d-block mt-2">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    Anda tidak dapat menghapus akun yang sedang digunakan.
                </small>
            @else
                <form action="{{ route('users.destroy', $user->id) }}" method="POST" 
                      class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini secara permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Hapus Pengguna
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto focus on name field
        $('#name').focus();
        
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert-success, .alert-danger').fadeOut('slow');
        }, 5000);
        
        // Phone number input mask
        $('#phone').inputmask('999999999999999', { placeholder: '' });
        
        // Show password confirmation when password field is filled
        $('#password').on('input', function() {
            if($(this).val().length > 0) {
                $('#password_confirmation').prop('required', true);
            } else {
                $('#password_confirmation').prop('required', false);
            }
        });
    });
</script>
@endsection