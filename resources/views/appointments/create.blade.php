@extends('layouts.layout')

@section('title', 'Buat Janji Temu Servis')

@section('page-title', 'Buat Janji Temu Baru')
@section('page-subtitle', 'Form pendaftaran janji servis kendaraan')

@section('content')
<div class="container-fluid px-4">
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-calendar-plus me-1"></i>
            Form Janji Temu Servis
        </div>
        <div class="card-body">
            <form action="{{ route('appointments.store') }}" method="POST" id="appointmentForm">
                @csrf

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
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="customer_id" class="form-label">Pilih Pelanggan <span
                                class="text-danger">*</span></label>
                        <select class="form-select @error('customer_id') is-invalid @enderror"
                            id="customer_id" name="customer_id" required>
                            <option value="" selected disabled>Pilih pelanggan</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @if(old('customer_id')==$customer->id) selected @endif>
                                {{ $customer->name }} - {{ $customer->phone }}
                            </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="vehicle_id" class="form-label">Pilih Kendaraan <span class="text-danger">*</span></label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror"
                            id="vehicle_id" name="vehicle_id" required>
                            <option value="">Pilih kendaraan</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" @if(old('vehicle_id')==$vehicle->id) selected @endif>
                                {{ $vehicle->brand }} - {{ $vehicle->model }} ({{ $vehicle->license_plate }})
                            </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">

                    <div class="col-md-6">
                        <label for="appointment_date" class="form-label">Tanggal Janji <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('appointment_date') is-invalid @enderror"
                            id="appointment_date" name="appointment_date" value="{{ old('appointment_date') }}"
                            min="{{ date('Y-m-d') }}" required>
                        @error('appointment_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                        <small class="text-muted">Minimal hari ini</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="appointment_time" class="form-label">Waktu Janji <span
                                class="text-danger">*</span></label>
                        <select class="form-select @error('appointment_time') is-invalid @enderror"
                            id="appointment_time" name="appointment_time" required>
                            <option value="" selected disabled>Pilih waktu</option>
                            @forelse($schedules as $schedule)
                            @if(!$schedule->is_closed)
                            @php
                            $start = \Carbon\Carbon::parse($schedule->open_time);
                            $end = \Carbon\Carbon::parse($schedule->close_time);
                            $interval = 60; // 60 menit interval
                            @endphp

                            @while($start->lt($end))
                            <option value="{{ $start->format('H:i') }}"
                                @if(old('appointment_time')==$start->format('H:i')) selected @endif
                                data-day="{{ $schedule->day_of_week }}">
                                {{ $start->format('H:i') }}
                            </option>
                            @php $start->addMinutes($interval); @endphp
                            @endwhile
                            @endif
                            @empty
                            <option value="" disabled>Tidak ada jadwal tersedia</option>
                            @endforelse
                        </select>
                        @error('appointment_time')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="service_description" class="form-label">Deskripsi Servis <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('service_description') is-invalid @enderror"
                            id="service_description" name="service_description" rows="3"
                            placeholder="Jelaskan jenis servis yang dibutuhkan"
                            required>{{ old('service_description') }}</textarea>
                        @error('service_description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="notes" class="form-label">Catatan Tambahan</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                            rows="2" placeholder="Masukkan catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-check me-1"></i> Buat Janji
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Service Schedule Info -->
    <div class="card bg-light mb-4">
        <div class="card-header">
            <i class="fas fa-calendar-alt me-1"></i>
            Jadwal Servis
        </div>
        <div class="card-body">
            <h5 class="card-title">Jam Operasional Servis</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Hari</th>
                            <th>Jam Buka</th>
                            <th>Jam Tutup</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->day_name }}</td>
                            <td>{{ $schedule->open_time->format('H:i') }}</td>
                            <td>{{ $schedule->close_time->format('H:i') }}</td>
                            <td>
                                @if($schedule->is_closed)
                                <span class="badge bg-danger">Tutup</span>
                                @else
                                <span class="badge bg-success">Buka</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                Kuota janji temu terbatas per hari. Pastikan memilih tanggal dan waktu yang tersedia.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto hide error alert after 5 seconds
        setTimeout(function() {
            $('.alert-danger').fadeOut('slow');
        }, 5000);

        // Handle customer selection change
        $('#customer_id').change(function() {
            const customerId = $(this).val();
            console.log('Customer ID selected:', customerId); // Debug log

            if (!customerId) {
                $('#vehicle_id').html('<option value="" selected disabled>Pilih pelanggan terlebih dahulu</option>');
                return;
            }

            // Show loading state
            $('#vehicle_id').html('<option value="" selected disabled>Memuat kendaraan...</option>');

            // Fetch vehicles for selected customer
            $.ajax({
                url: '{{ route("vehicles.getByCustomer") }}',
                type: 'GET',
                data: {
                    customer_id: customerId
                },
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX Response:', response); // Debug log

                    let vehicleOptions = '<option value="" selected disabled>Pilih kendaraan</option>';

                    if (response.vehicles && response.vehicles.length > 0) {
                        response.vehicles.forEach(function(vehicle) {
                            // Format vehicle display name
                            const displayName = `${vehicle.brand} ${vehicle.model} (${vehicle.license_plate})`;
                            vehicleOptions += `<option value="${vehicle.id}">${displayName}</option>`;
                        });
                    } else {
                        vehicleOptions = '<option value="" disabled>Pelanggan belum memiliki kendaraan</option>';
                    }

                    $('#vehicle_id').html(vehicleOptions);

                    // Restore selected vehicle if exists in old input
                    const oldVehicleId = '{{ old("vehicle_id") }}';
                    if (oldVehicleId) {
                        $('#vehicle_id').val(oldVehicleId);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText); // Debug log
                    alert('Terjadi kesalahan saat mengambil data kendaraan: ' + error);
                    $('#vehicle_id').html('<option value="" selected disabled>Error loading vehicles</option>');
                }
            });
        });

        // Trigger customer change if there's old input (for validation errors)
        const oldCustomerId = '{{ old("customer_id") }}';
        if (oldCustomerId) {
            $('#customer_id').trigger('change');
        }

        // Filter available times based on selected date
        $('#appointment_date').change(function() {
            const selectedDate = new Date($(this).val());
            const dayOfWeek = selectedDate.getDay(); // 0 (Sunday) to 6 (Saturday)

            // Enable all options first
            $('#appointment_time option').prop('disabled', false).show();

            // Disable options that don't match the selected day
            $('#appointment_time option').each(function() {
                const optionDay = $(this).data('day');
                if (optionDay !== undefined && optionDay != dayOfWeek) {
                    $(this).prop('disabled', true).hide();
                }
            });

            // Reset selection if current selection is not available
            const currentSelection = $('#appointment_time').val();
            if (currentSelection && $('#appointment_time option[value="' + currentSelection + '"]').is(':disabled')) {
                $('#appointment_time').val('');
            }
        });

        // Initialize date picker with disabled days if datepicker library is available
        const disabledDays = [];
        @foreach($schedules as $schedule)
        @if($schedule -> is_closed)
        disabledDays.push({
            {
                $schedule - > day_of_week
            }
        });
        @endif
        @endforeach

        if ($.fn.datepicker) {
            $('#appointment_date').datepicker({
                format: 'yyyy-mm-dd',
                startDate: 'today',
                daysOfWeekDisabled: disabledDays,
                autoclose: true
            });
        }
    });
</script>
@endsection