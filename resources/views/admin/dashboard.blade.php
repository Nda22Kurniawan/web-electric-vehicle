@extends('layouts.layout')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang kembali, ' . Auth::user()->name . '!')

@section('content')
    <!-- Dashboard Stats -->
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-primary-light">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h5 class="card-title">
                        @if(Auth::user()->role === 'customer')
                            Booking Aktif
                        @else
                            Janji Hari Ini
                        @endif
                    </h5>
                    <h3 class="card-value">{{ $today_bookings ?? 0 }}</h3>
                    <span class="card-change {{ ($booking_trend ?? 0) > 0 ? 'change-up' : (($booking_trend ?? 0) < 0 ? 'change-down' : '') }}">
                        <i class="fas fa-{{ ($booking_trend ?? 0) > 0 ? 'arrow-up' : (($booking_trend ?? 0) < 0 ? 'arrow-down' : 'equals') }}"></i> 
                        {{ abs($booking_trend ?? 0) }} dari bulan lalu
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-secondary-light">
                        @if(Auth::user()->role === 'customer')
                            <i class="fas fa-motorcycle"></i>
                        @else
                            <i class="fas fa-tools"></i>
                        @endif
                    </div>
                    <h5 class="card-title">
                        @if(Auth::user()->role === 'customer')
                            Kendaraan Saya
                        @else
                            Work Order Aktif
                        @endif
                    </h5>
                    <h3 class="card-value">
                        @if(Auth::user()->role === 'customer')
                            {{ $total_vehicles ?? 0 }}
                        @else
                            {{ $active_work_orders ?? 0 }}
                        @endif
                    </h3>
                    <span class="card-change">
                        <i class="fas fa-equals"></i> 
                        @if(Auth::user()->role === 'customer')
                            kendaraan terdaftar
                        @else
                            sedang dikerjakan
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-success-light">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h5 class="card-title">Servis Selesai</h5>
                    <h3 class="card-value">{{ $completed_services ?? 0 }}</h3>
                    <span class="card-change change-up">
                        <i class="fas fa-arrow-up"></i> {{ $completed_trend ?? 0 }} bulan ini
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="dashboard-card card">
                <div class="card-body">
                    <div class="card-icon bg-warning-light">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5 class="card-title">
                        @if(Auth::user()->role === 'customer')
                            Janji Berikutnya
                        @else
                            Pending Konfirmasi
                        @endif
                    </h5>
                    <h3 class="card-value">
                        @if(Auth::user()->role === 'customer')
                            @if($next_appointment ?? null)
                                {{ \Carbon\Carbon::parse($next_appointment->appointment_date)->format('d M') }}
                            @else
                                -
                            @endif
                        @else
                            {{ $pending_appointments ?? 0 }}
                        @endif
                    </h3>
                    <span class="card-change">
                        <i class="fas fa-clock"></i> 
                        @if(Auth::user()->role === 'customer')
                            @if($next_appointment ?? null)
                                {{ \Carbon\Carbon::parse($next_appointment->appointment_date)->diffForHumans() }}
                            @else
                                Tidak ada janji
                            @endif
                        @else
                            menunggu konfirmasi
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Booking Chart -->
        <div class="col-lg-8">
            <div class="chart-container">
                <h5 class="mb-4">
                    @if(Auth::user()->role === 'customer')
                        Riwayat Booking Saya
                    @else
                        Statistik Booking Servis
                    @endif
                </h5>
                <canvas id="bookingChart" height="250"></canvas>
            </div>
        </div>

        <!-- Status Chart -->
        <div class="col-lg-4">
            <div class="chart-container">
                <h5 class="mb-4">
                    @if(Auth::user()->role === 'customer')
                        Status Booking Saya
                    @else
                        Layanan Populer
                    @endif
                </h5>
                <small class="text-muted mb-3 d-block">
                    @if(Auth::user()->role === 'customer')
                        Distribusi status appointment Anda
                    @else
                        Berdasarkan penggunaan layanan di work order
                    @endif
                </small>
                <canvas id="statusChart" height="250"></canvas>
                
                @if(Auth::user()->role !== 'customer')
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-primary" onclick="toggleServiceView('quantity')">Kuantitas</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleServiceView('revenue')">Revenue</button>
                        <button class="btn btn-sm btn-outline-info" onclick="toggleServiceView('usage')">Penggunaan</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-12">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">
                        @if(Auth::user()->role === 'customer')
                            Aktivitas Terakhir
                        @else
                            Booking Terakhir
                        @endif
                    </h5>
                    <a href="{{ Auth::user()->role === 'customer' ? route('appointments.index') : route('appointments.index') }}" 
                       class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                @if(Auth::user()->role === 'customer')
                                    <th>ID Booking</th>
                                    <th>Tanggal</th>
                                    <th>Kendaraan</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                @else
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Tanggal</th>
                                    <th>Kendaraan</th>
                                    <th>Layanan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($recent_appointments ?? collect()) as $appointment)
                                <tr>
                                    @if(Auth::user()->role === 'customer')
                                        <td>{{ $appointment->tracking_code ?? '-' }}</td>
                                        <td>{{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') : '-' }}</td>
                                        <td>{{ ($appointment->vehicle->brand ?? '') . ' ' . ($appointment->vehicle->model ?? '') }}</td>
                                        <td>{{ Str::limit($appointment->service_description ?? '', 30) }}</td>
                                        <td>
                                            <span class="booking-status status-{{ $appointment->status ?? 'unknown' }}">
                                                @switch($appointment->status ?? 'unknown')
                                                    @case('pending')
                                                        Menunggu
                                                        @break
                                                    @case('confirmed')
                                                        Dikonfirmasi
                                                        @break
                                                    @case('in_progress')
                                                        Dalam Proses
                                                        @break
                                                    @case('completed')
                                                        Selesai
                                                        @break
                                                    @case('cancelled')
                                                        Dibatalkan
                                                        @break
                                                    @default
                                                        {{ ucfirst($appointment->status ?? 'Unknown') }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('appointments.show', $appointment) }}" 
                                               class="btn btn-sm btn-outline-primary">Detail</a>
                                        </td>
                                    @else
                                        <td>{{ $appointment->tracking_code ?? '-' }}</td>
                                        <td>{{ $appointment->customer->name ?? '-' }}</td>
                                        <td>{{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') : '-' }}</td>
                                        <td>{{ ($appointment->vehicle->brand ?? '') . ' ' . ($appointment->vehicle->model ?? '') }}</td>
                                        <td>{{ Str::limit($appointment->service_description ?? '', 25) }}</td>
                                        <td>
                                            <span class="booking-status status-{{ $appointment->status ?? 'unknown' }}">
                                                @switch($appointment->status ?? 'unknown')
                                                    @case('pending')
                                                        Menunggu
                                                        @break
                                                    @case('confirmed')
                                                        Dikonfirmasi
                                                        @break
                                                    @case('in_progress')
                                                        Dalam Proses
                                                        @break
                                                    @case('completed')
                                                        Selesai
                                                        @break
                                                    @case('cancelled')
                                                        Dibatalkan
                                                        @break
                                                    @default
                                                        {{ ucfirst($appointment->status ?? 'Unknown') }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('appointments.show', $appointment) }}" 
                                                   class="btn btn-outline-primary">Detail</a>
                                                @if(($appointment->status ?? '') === 'pending')
                                                    <button type="button" class="btn btn-outline-success" 
                                                            onclick="updateStatus({{ $appointment->id }}, 'confirmed')">
                                                        Konfirmasi
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->role === 'customer' ? '6' : '7' }}" class="text-center">
                                        Belum ada data appointment
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->role !== 'customer')
        <!-- Work Orders Summary (Admin/Owner only) -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="chart-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Work Order Terbaru</h5>
                        <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. WO</th>
                                    <th>Customer</th>
                                    <th>Kendaraan</th>
                                    <th>Mechanic</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($recent_work_orders ?? collect()) as $wo)
                                    <tr>
                                        <td>{{ $wo->work_order_number ?? '-' }}</td>
                                        <td>{{ $wo->customer_name ?? $wo->customer->name ?? 'Walk-in' }}</td>
                                        <td>{{ ($wo->vehicle->brand ?? '') . ' ' . ($wo->vehicle->model ?? '') }}</td>
                                        <td>{{ $wo->mechanic->name ?? '-' }}</td>
                                        <td>Rp {{ number_format($wo->total_amount ?? 0, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="booking-status status-{{ $wo->status ?? 'unknown' }}">
                                                @switch($wo->status ?? 'unknown')
                                                    @case('pending')
                                                        Pending
                                                        @break
                                                    @case('in_progress')
                                                        Dikerjakan
                                                        @break
                                                    @case('completed')
                                                        Selesai
                                                        @break
                                                    @case('cancelled')
                                                        Dibatalkan
                                                        @break
                                                    @default
                                                        {{ ucfirst($wo->status ?? 'Unknown') }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('work-orders.show', $wo) }}" 
                                               class="btn btn-sm btn-outline-primary">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada work order</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        // Data dari controller dengan null checking
        const chartLabels = @json(($chart_data['chart_labels'] ?? null)) || ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
        const chartData = @json(($chart_data['chart_data'] ?? null)) || [5, 8, 6, 9, 7, 10];
        const statusLabels = @json(($status_data['status_labels'] ?? null)) || ['Pending', 'Completed', 'In Progress'];
        const statusData = @json(($status_data['status_data'] ?? null)) || [35, 25, 20];

        // Pastikan Chart.js sudah dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Booking Chart
            const bookingCtx = document.getElementById('bookingChart');
            if (bookingCtx && typeof Chart !== 'undefined') {
                window.bookingChart = new Chart(bookingCtx, {
                    type: 'line',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            label: 'Booking Servis',
                            data: chartData,
                            backgroundColor: 'rgba(46, 91, 255, 0.1)',
                            borderColor: '#2e5bff',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Status Chart
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx && typeof Chart !== 'undefined') {
                window.statusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusData,
                            backgroundColor: [
                                '#2e5bff',
                                '#ff6b35',
                                '#28a745',
                                '#6c757d',
                                '#ffc107',
                                '#17a2b8',
                                '#6f42c1'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });

        @if(Auth::user()->role !== 'customer')
        // Toggle service view function
        function toggleServiceView(type) {
            // Update button states
            document.querySelectorAll('.btn-outline-primary, .btn-outline-secondary, .btn-outline-info').forEach(btn => {
                btn.classList.remove('btn-primary', 'btn-secondary', 'btn-info');
                btn.classList.add('btn-outline-primary');
            });
            
            event.target.classList.remove('btn-outline-primary');
            event.target.classList.add('btn-primary');
            
            // Fetch new data
            fetch(`{{ route('dashboard.service-stats') }}?type=${type}`)
                .then(response => response.json())
                .then(data => {
                    if (window.statusChart && data.services) {
                        const labels = data.services.map(service => service.name);
                        const values = data.services.map(service => service.value);
                        
                        window.statusChart.data.labels = labels;
                        window.statusChart.data.datasets[0].data = values;
                        window.statusChart.update();
                        
                        // Update chart title based on type
                        const title = document.querySelector('.chart-container h5');
                        switch(type) {
                            case 'revenue':
                                title.textContent = 'Revenue per Layanan';
                                break;
                            case 'usage':
                                title.textContent = 'Frekuensi Penggunaan';
                                break;
                            default:
                                title.textContent = 'Layanan Populer';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching service stats:', error);
                });
        }
        @endif

        // Quick status update function dengan error handling
        @if(Auth::user()->role !== 'customer')
        function updateStatus(appointmentId, status) {
            if (!appointmentId || !status) {
                alert('Data tidak lengkap');
                return;
            }
            
            if (confirm('Apakah Anda yakin ingin mengubah status appointment ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/appointments/' + appointmentId + '/status';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = status;
                
                form.appendChild(csrfToken);
                form.appendChild(statusInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        @endif
    </script>
@endsection