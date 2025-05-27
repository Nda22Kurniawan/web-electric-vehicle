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
                            <label for="customer_name" class="form-label">Nama Pelanggan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                                placeholder="Masukkan nama lengkap" required>
                            @error('customer_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="customer_phone" class="form-label">Nomor Telepon <span
                                    class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror"
                                id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}"
                                placeholder="Masukkan nomor telepon" required>
                            @error('customer_phone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="customer_email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                id="customer_email" name="customer_email" value="{{ old('customer_email') }}"
                                placeholder="Masukkan alamat email (opsional)">
                            @error('customer_email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="vehicle_id" class="form-label">Kendaraan <span class="text-danger">*</span></label>
                            <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id"
                                name="vehicle_id" required>
                                <option value="" selected disabled>Pilih kendaraan</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" @if(old('vehicle_id') == $vehicle->id) selected @endif>
                                        {{ $vehicle->brand }}
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
                                            $interval = 60; // Ubah dari 30 menjadi 60 menit
                                        @endphp

                                        @while($start->lt($end))
                                            <option value="{{ $start->format('H:i') }}"
                                                @if(old('appointment_time') == $start->format('H:i')) selected @endif
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
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
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
                    Kuota janji temui terbatas per hari. Pastikan memilih tanggal dan waktu yang tersedia.
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Auto focus on customer name field
            $('#customer_name').focus();

            // Auto hide error alert after 5 seconds
            setTimeout(function () {
                $('.alert-danger').fadeOut('slow');
            }, 5000);

            // Phone number input mask
            $('#customer_phone').inputmask('999999999999999', { placeholder: '' });

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

                // Trigger change to update time slot availability
                $('#appointment_time').trigger('change');
            });

            // Initialize date picker with disabled days
            const disabledDays = [];
            @foreach($schedules as $schedule)
                @if($schedule->is_closed)
                    disabledDays.push({{ $schedule->day_of_week }});
                @endif
            @endforeach

            $('#appointment_date').datepicker({
                format: 'yyyy-mm-dd',
                startDate: 'today',
                daysOfWeekDisabled: disabledDays,
                autoclose: true
            });
        });
    </script>
@endsection