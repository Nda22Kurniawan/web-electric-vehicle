@extends('layouts.layout')

@section('title', 'Tambah Jadwal Layanan')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-calendar-plus me-1"></i>
            Tambah Jadwal Layanan
        </div>
        <div class="card-body">
            <form action="{{ route('service-schedules.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="day_of_week" class="form-label">Hari <span class="text-danger">*</span></label>
                        <select class="form-select" id="day_of_week" name="day_of_week" required>
                            <option value="" selected disabled>Pilih Hari</option>
                            @foreach($days as $key => $day)
                                <option value="{{ $key }}">{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check form-switch pt-4">
                            <input class="form-check-input" type="checkbox" id="is_closed" name="is_closed">
                            <label class="form-check-label" for="is_closed">Libur/Tutup</label>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3" id="time_fields">
                    <div class="col-md-4">
                        <label for="open_time" class="form-label">Jam Buka <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="open_time" name="open_time">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="close_time" class="form-label">Jam Tutup <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="close_time" name="close_time">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="max_appointments" class="form-label">Kuota Janji <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="max_appointments" name="max_appointments" min="1" value="10">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('service-schedules.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        // Toggle time fields based on is_closed checkbox
        $('#is_closed').change(function() {
            if($(this).is(':checked')) {
                $('#time_fields').hide();
                $('#open_time, #close_time').removeAttr('required');
            } else {
                $('#time_fields').show();
                $('#open_time, #close_time').attr('required', 'required');
            }
        });
        
        // Trigger change event on page load
        $('#is_closed').trigger('change');
    });
</script>
@endsection
@endsection