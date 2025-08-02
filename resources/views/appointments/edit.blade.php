@extends('layouts.layout')

@section('title', 'Edit Janji Temu Servis')

@section('page-title', 'Edit Janji Temu')
@section('page-subtitle', 'Ubah informasi janji servis kendaraan')

@section('content')
    <div class="container-fluid px-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-calendar-edit me-1"></i>
                Edit Form Janji Temu Servis
            </div>
            <div class="card-body">
                <form action="{{ route('appointments.update', $appointment) }}" method="POST" id="appointmentForm">
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
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label">Pilih Pelanggan <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('customer_id') is-invalid @enderror"
                                id="customer_id" name="customer_id" required>
                                <option value="" disabled>Pilih pelanggan</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" 
                                        @if(old('customer_id', $appointment->customer_id) == $customer->id) selected @endif>
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
                            <label for="vehicle_id" class="form-label">Kendaraan <span class="text-danger">*</span></label>
                            <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id"
                                name="vehicle_id" required>
                                <option value="" disabled>Pilih kendaraan</option>
                                @if($appointment->customer && $appointment->customer->vehicles)
                                    @foreach($appointment->customer->vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" 
                                            @if(old('vehicle_id', $appointment->vehicle_id) == $vehicle->id) selected @endif>
                                            {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->license_plate }})
                                        </option>
                                    @endforeach
                                @endif
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
                                id="appointment_date" name="appointment_date" 
                                value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}"
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
                                <option value="" disabled>Pilih waktu</option>
                                @forelse($schedules as $schedule)
                                    @if(!$schedule->is_closed)
                                        @php
                                            $start = \Carbon\Carbon::parse($schedule->open_time);
                                            $end = \Carbon\Carbon::parse($schedule->close_time);
                                            $interval = 60; // 60 menit interval
                                        @endphp

                                        @while($start->lt($end))
                                            <option value="{{ $start->format('H:i') }}"
                                                @if(old('appointment_time', $appointment->appointment_time->format('H:i')) == $start->format('H:i')) selected @endif
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
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" @if(old('status', $appointment->status) == 'pending') selected @endif>Pending</option>
                                <option value="confirmed" @if(old('status', $appointment->status) == 'confirmed') selected @endif>Confirmed</option>
                                <option value="in_progress" @if(old('status', $appointment->status) == 'in_progress') selected @endif>In Progress</option>
                                <option value="completed" @if(old('status', $appointment->status) == 'completed') selected @endif>Completed</option>
                                <option value="cancelled" @if(old('status', $appointment->status) == 'cancelled') selected @endif>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="service_description" class="form-label">Deskripsi Servis <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control @error('service_description') is-invalid @enderror"
                                id="service_description" name="service_description" rows="3"
                                placeholder="Jelaskan jenis servis yang dibutuhkan"
                                required>{{ old('service_description', $appointment->service_description) }}</textarea>
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
                                rows="2" placeholder="Masukkan catatan tambahan (opsional)">{{ old('notes', $appointment->notes) }}</textarea>
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
                                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Janji
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
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Auto hide error alert after 5 seconds
            setTimeout(function () {
                $('.alert-danger').fadeOut('slow');
            }, 5000);

            // Handle customer selection change
            $('#customer_id').change(function () {
                const customerId = $(this).val();
                
                if (!customerId) {
                    $('#vehicle_id').html('<option value="" selected disabled>Pilih pelanggan terlebih dahulu</option>');
                    return;
                }

                // Show loading state
                $('#vehicle_id').html('<option value="" selected disabled>Loading...</option>');

                // Fetch vehicles for selected customer
                $.ajax({
                    url: '{{ route("vehicles.getByCustomer") }}',
                    type: 'GET',
                    data: { customer_id: customerId },
                    success: function(response) {
                        let vehicleOptions = '<option value="" disabled>Pilih kendaraan</option>';
                        
                        if (response.vehicles && response.vehicles.length > 0) {
                            response.vehicles.forEach(function(vehicle) {
                                const displayName = `${vehicle.brand} ${vehicle.model} (${vehicle.license_plate})`;
                                vehicleOptions += `<option value="${vehicle.id}">${displayName}</option>`;
                            });
                        } else {
                            vehicleOptions = '<option value="" disabled>Pelanggan belum memiliki kendaraan</option>';
                        }
                        
                        $('#vehicle_id').html(vehicleOptions);

                        // Restore selected vehicle if exists in old input or current appointment
                        const currentVehicleId = '{{ old("vehicle_id", $appointment->vehicle_id) }}';
                        if (currentVehicleId) {
                            $('#vehicle_id').val(currentVehicleId);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat mengambil data kendaraan');
                        $('#vehicle_id').html('<option value="" selected disabled>Error loading vehicles</option>');
                    }
                });
            });

            // Filter available times based on selected date
            $('#appointment_date').change(function () {
                const selectedDate = new Date($(this).val());
                const dayOfWeek = selectedDate.getDay(); // 0 (Sunday) to 6 (Saturday)

                // Enable all options first
                $('#appointment_time option').prop('disabled', false).show();

                // Disable options that don't match the selected day
                $('#appointment_time option').each(function () {
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

            // Initialize date picker with disabled days
            const disabledDays = [];
            @foreach($schedules as $schedule)
                @if($schedule->is_closed)
                    disabledDays.push({{ $schedule->day_of_week }});
                @endif
            @endforeach

            // If datepicker is available, use it
            if ($.fn.datepicker) {
                $('#appointment_date').datepicker({
                    format: 'yyyy-mm-dd',
                    startDate: 'today',
                    daysOfWeekDisabled: disabledDays,
                    autoclose: true
                });
            }

            // Trigger appointment date change on load to filter times
            $('#appointment_date').trigger('change');
        });
    </script>
@endsection