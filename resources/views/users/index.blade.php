@extends('layouts.layout')

@section('title', 'Manajemen Pengguna')

@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Daftar seluruh pengguna sistem')

@section('content')
<div class="container-fluid px-4">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari Pengguna</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Nama, email, atau telepon...">
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label">Filter Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="mechanic" {{ request('role') === 'mechanic' ? 'selected' : '' }}>Mechanic</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-users me-1"></i>
                    Daftar Pengguna
                    @if(request('search') || request('role'))
                        <small class="text-muted">
                            ({{ $users->total() }} dari {{ App\Models\User::count() }} total pengguna)
                        </small>
                    @else
                        <small class="text-muted">({{ $users->total() }} pengguna)</small>
                    @endif
                </div>
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus me-1"></i> Tambah Pengguna
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Nama</th>
                            <th width="20%">Email</th>
                            <th width="15%">Telepon</th>
                            <th width="10%">Role</th>
                            <th width="15%">Dibuat</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-success">{{ ucfirst($user->role) }}</span>
                                @elseif($user->role === 'mechanic')
                                    <span class="badge bg-info">{{ ucfirst($user->role) }}</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($user->role) }}</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(Auth::id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    @if(request('search') || request('role'))
                                        <h5 class="text-muted">Tidak ada pengguna yang ditemukan</h5>
                                        <p class="text-muted">Cobalah dengan kata kunci atau filter yang berbeda.</p>
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Semua Pengguna
                                        </a>
                                    @else
                                        <h5 class="text-muted">Belum ada pengguna</h5>
                                        <p class="text-muted">Mulai dengan menambahkan pengguna pertama Anda.</p>
                                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                                            <i class="fas fa-user-plus me-1"></i> Tambah Pengguna Pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Custom Pagination -->
            @if($users->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} pengguna
                </div>
                
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Button --}}
                        @if($users->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $users->appends(request()->query())->previousPageUrl() }}">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $start = max($users->currentPage() - 2, 1);
                            $end = min($start + 4, $users->lastPage());
                            $start = max($end - 4, 1);
                        @endphp

                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $users->appends(request()->query())->url(1) }}">1</a>
                            </li>
                            @if($start > 2)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++)
                            @if($i == $users->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $i }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $users->appends(request()->query())->url($i) }}">{{ $i }}</a>
                                </li>
                            @endif
                        @endfor

                        @if($end < $users->lastPage())
                            @if($end < $users->lastPage() - 1)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $users->appends(request()->query())->url($users->lastPage()) }}">{{ $users->lastPage() }}</a>
                            </li>
                        @endif

                        {{-- Next Button --}}
                        @if($users->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $users->appends(request()->query())->nextPageUrl() }}">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    Next <i class="fas fa-chevron-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
    
    <!-- User Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Pengguna</div>
                            <div class="h5">{{ App\Models\User::count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Admin</div>
                            <div class="h5">{{ App\Models\User::where('role', 'admin')->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-shield fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Mekanik</div>
                            <div class="h5">{{ App\Models\User::where('role', 'mechanic')->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Customer</div>
                            <div class="h5">{{ App\Models\User::where('role', 'customer')->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-friends fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto hide success alert after 5 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut('slow');
        }, 5000);
    });
</script>
@endsection