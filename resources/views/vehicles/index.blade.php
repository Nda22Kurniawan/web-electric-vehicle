@extends('layouts.layout')

@section('title', 'Kendaraan')

@section('page-title', 'Manajemen Kendaraan')
@section('page-subtitle', 'Pengelolaan kendaraan')

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
                        <i class="fas fa-motorcycle me-1"></i>
                        Daftar Kendaraan
                    </div>
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Kendaraan
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="vehiclesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pelanggan</th>
                                <th>No. Telepon</th>
                                <th>Jenis</th>
                                <th>Merek & Model</th>
                                <th>Plat Nomor</th>
                                <th>Tahun</th>
                                <th>Warna</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicles as $index => $vehicle)
                                <tr>
                                    <td>{{ $vehicles->firstItem() + $index }}</td>
                                    <td>{{ $vehicle->customer_name }}</td>
                                    <td>{{ $vehicle->customer_phone }}</td>
                                    <td>
                                        @if($vehicle->type == 'motorcycle')
                                            <span class="badge bg-primary">Sepeda Motor</span>
                                        @elseif($vehicle->type == 'electric_bike')
                                            <span class="badge bg-success">Sepeda Listrik</span>
                                        @endif
                                    </td>
                                    <td>{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                                    <td>{{ $vehicle->license_plate }}</td>
                                    <td>{{ $vehicle->year }}</td>
                                    <td>
                                        @if($vehicle->color)
                                            <span class="badge" style="color: black;">
                                                {{ $vehicle->color }}
                                            </span>
                                        @else
                                            <span class="badge" style="color: black;">
                                                -
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn btn-info btn-sm"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('vehicles.edit', $vehicle->id) }}" class="btn btn-warning btn-sm"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus kendaraan ini?')">
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
                                    <td colspan="9" class="text-center">Tidak ada data kendaraan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $vehicles->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize DataTable with minimal configuration
            // We're using Laravel's pagination so we don't need DataTable's pagination
            $('#vehiclesTable').DataTable({
                paging: false,
                info: false,
                searching: true,
                ordering: true,
                responsive: true
            });
        });
    </script>
@endsection