@extends('layouts.layout')

@section('title', 'Suku Cadang')

@section('page-title', 'Manajemen Suku Cadang')
@section('page-subtitle', 'Pengelolaan inventori suku cadang')

@section('content')
<div class="container-fluid px-4">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-cogs me-1"></i>
                    Daftar Suku Cadang
                </div>
                <div>
                    <a href="{{ route('parts.low-stock') }}" class="btn btn-warning btn-sm me-2">
                        <i class="fas fa-exclamation-triangle me-1"></i> Stok Rendah
                    </a>
                    <a href="{{ route('parts.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Suku Cadang
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="partsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Suku Cadang</th>
                            <th>Nomor Part</th>
                            <th>Deskripsi</th>
                            <th>Harga Jual</th>
                            <th>Harga Beli</th>
                            <th>Stok</th>
                            <th>Min. Stok</th>
                            <th>Jenis Kendaraan</th>
                            <th>Status Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parts as $index => $part)
                        <tr class="{{ $part->stock <= $part->min_stock ? 'table-warning' : '' }}">
                            <td>{{ $parts->firstItem() + $index }}</td>
                            <td>{{ $part->name }}</td>
                            <td>{{ $part->part_number ?: '-' }}</td>
                            <td>{{ $part->description ?: '-' }}</td>
                            <td>Rp {{ number_format($part->price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($part->cost, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $part->stock <= $part->min_stock ? 'bg-danger' : 'bg-success' }}">
                                    {{ $part->stock }}
                                </span>
                            </td>
                            <td>{{ $part->min_stock }}</td>
                            <td>
                                @switch($part->vehicle_type)
                                    @case('motorcycle')
                                        <span class="badge bg-primary">Motor</span>
                                        @break
                                    @case('electric_bike')
                                        <span class="badge bg-info">Motor Listrik</span>
                                        @break
                                    @case('both')
                                        <span class="badge bg-secondary">Keduanya</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark">-</span>
                                @endswitch
                            </td>
                            <td>
                                @if($part->stock <= $part->min_stock)
                                    <span class="badge bg-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Stok Rendah
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> Normal
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('parts.show', $part->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('parts.edit', $part->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('parts.destroy', $part->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus suku cadang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">Tidak ada data suku cadang</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $parts->links() }}
            </div>
        </div>
    </div>
    
    <!-- Info Card -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Suku Cadang</div>
                            <div class="h5">{{ $parts->total() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cogs fa-2x"></i>
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
                            <div class="small text-white-50">Stok Rendah</div>
                            <div class="h5">{{ $parts->where('stock', '<=', DB::raw('min_stock'))->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('parts.low-stock') }}">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Nilai Stok</div>
                            <div class="h5">Rp {{ number_format($parts->sum(function($part) { return $part->stock * $part->cost; }), 0, ',', '.') }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
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
                            <div class="small text-white-50">Rata-rata Margin</div>
                            <div class="h5">{{ number_format($parts->avg(function($part) { return $part->cost > 0 ? (($part->price - $part->cost) / $part->cost) * 100 : 0; }), 1) }}%</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-percentage fa-2x"></i>
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
        // Initialize DataTable with minimal configuration
        $('#partsTable').DataTable({
            paging: false,
            info: false,
            searching: true,
            ordering: true,
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [10] } // Disable sorting on action column
            ]
        });
    });
</script>
@endsection