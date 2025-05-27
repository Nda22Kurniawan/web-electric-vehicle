@extends('layouts.layout')

@section('title', 'Jadwal Layanan')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-calendar-alt me-1"></i>
                    Daftar Jadwal Layanan
                </div>
                <a href="{{ route('service-schedules.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Jadwal
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Jam Buka</th>
                        <th>Jam Tutup</th>
                        <th>Status</th>
                        <th>Kuota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $schedule)
                    <tr>
                        <td>{{ $schedule->day_name }}</td>
                        <td>{{ $schedule->open_time }}</td>
                        <td>{{ $schedule->close_time }}</td>
                        <td>
                            @if($schedule->is_closed)
                                <span class="badge bg-danger">Tutup</span>
                            @else
                                <span class="badge bg-success">Buka</span>
                            @endif
                        </td>
                        <td>{{ $schedule->max_appointments }}</td>
                        <td>
                            <a href="{{ route('service-schedules.edit', $schedule->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection