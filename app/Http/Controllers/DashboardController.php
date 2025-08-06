<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get dashboard data based on user role
        if ($user->role === 'customer') {
            return $this->getCustomerDashboard($user);
        } else {
            return $this->getAdminDashboard($user);
        }
    }

    /**
     * Get dashboard data for customer users.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    private function getCustomerDashboard($user)
    {
        try {
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();
            $lastMonth = Carbon::now()->subMonth()->startOfMonth();
            
            // Today's bookings for customer
            $today_bookings = Appointment::where('customer_id', $user->id)
                ->whereDate('appointment_date', $today)
                ->whereIn('status', ['confirmed', 'in_progress'])
                ->count();

            // Total vehicles owned by customer
            $total_vehicles = Vehicle::where('customer_id', $user->id)->count();

            // Completed services this month
            $completed_services = Appointment::where('customer_id', $user->id)
                ->where('status', 'completed')
                ->where('created_at', '>=', $thisMonth)
                ->count();

            // Completed services last month for trend
            $completed_last_month = Appointment::where('customer_id', $user->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$lastMonth, $thisMonth])
                ->count();

            $completed_trend = $completed_services - $completed_last_month;

            // Booking trend comparison
            $bookings_this_month = Appointment::where('customer_id', $user->id)
                ->where('created_at', '>=', $thisMonth)
                ->count();

            $bookings_last_month = Appointment::where('customer_id', $user->id)
                ->whereBetween('created_at', [$lastMonth, $thisMonth])
                ->count();

            $booking_trend = $bookings_this_month - $bookings_last_month;

            // Next appointment
            $next_appointment = Appointment::where('customer_id', $user->id)
                ->where('appointment_date', '>=', $today)
                ->whereIn('status', ['pending', 'confirmed'])
                ->orderBy('appointment_date')
                ->first();

            // Recent appointments (last 10)
            $recent_appointments = Appointment::with(['vehicle'])
                ->where('customer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Chart data - last 12 months booking data
            $chart_data = $this->getCustomerBookingChartData($user->id);

            // Status distribution for customer's appointments
            $status_data = $this->getCustomerStatusData($user->id);

            // PERBAIKAN: Pastikan semua variabel yang diperlukan ada
            $data = compact(
                'today_bookings',
                'total_vehicles', 
                'completed_services',
                'completed_trend',
                'booking_trend',
                'next_appointment',
                'recent_appointments',
                'chart_data',
                'status_data'
            );

            // Tambahkan variabel default jika tidak ada
            $data['active_work_orders'] = 0;
            $data['pending_appointments'] = 0;
            $data['recent_work_orders'] = collect();

            return view('admin.dashboard', $data);

        } catch (\Exception $e) {
            // FALLBACK: Jika ada error, berikan data default
            Log::error('Dashboard Customer Error: ' . $e->getMessage());
            
            return view('admin.dashboard', [
                'today_bookings' => 0,
                'total_vehicles' => 0,
                'completed_services' => 0,
                'completed_trend' => 0,
                'booking_trend' => 0,
                'next_appointment' => null,
                'recent_appointments' => collect(),
                'active_work_orders' => 0,
                'pending_appointments' => 0,
                'recent_work_orders' => collect(),
                'chart_data' => [
                    'chart_labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    'chart_data' => [0, 0, 0, 0, 0, 0]
                ],
                'status_data' => [
                    'status_labels' => ['Tidak ada data'],
                    'status_data' => [1]
                ]
            ]);
        }
    }

    /**
     * Get dashboard data for admin/owner users.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    private function getAdminDashboard($user)
    {
        try {
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();
            $lastMonth = Carbon::now()->subMonth()->startOfMonth();

            // Today's appointments
            $today_bookings = Appointment::whereDate('appointment_date', $today)
                ->whereIn('status', ['confirmed', 'in_progress'])
                ->count();

            // Active work orders
            $active_work_orders = WorkOrder::whereIn('status', ['pending', 'in_progress'])->count();

            // Completed services this month
            $completed_services = WorkOrder::where('status', 'completed')
                ->where('created_at', '>=', $thisMonth)
                ->count();

            // Completed services last month for trend
            $completed_last_month = WorkOrder::where('status', 'completed')
                ->whereBetween('created_at', [$lastMonth, $thisMonth])
                ->count();

            $completed_trend = $completed_services - $completed_last_month;

            // Booking trend comparison
            $bookings_this_month = Appointment::where('created_at', '>=', $thisMonth)->count();
            $bookings_last_month = Appointment::whereBetween('created_at', [$lastMonth, $thisMonth])->count();
            $booking_trend = $bookings_this_month - $bookings_last_month;

            // Pending appointments
            $pending_appointments = Appointment::where('status', 'pending')->count();

            // Recent appointments (last 10)
            $recent_appointments = Appointment::with(['vehicle', 'customer'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Recent work orders (last 10)
            $recent_work_orders = WorkOrder::with(['vehicle', 'customer', 'mechanic'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Chart data - last 12 months booking data
            $chart_data = $this->getBookingChartData();

            // Service distribution data
            $status_data = $this->getServiceDistributionData();

            // PERBAIKAN: Pastikan semua variabel yang diperlukan ada
            $data = compact(
                'today_bookings',
                'active_work_orders',
                'completed_services',
                'completed_trend',
                'booking_trend',
                'pending_appointments',
                'recent_appointments',
                'recent_work_orders',
                'chart_data',
                'status_data'
            );

            // Tambahkan variabel untuk customer yang mungkin dibutuhkan
            $data['total_vehicles'] = 0;
            $data['next_appointment'] = null;

            return view('admin.dashboard', $data);

        } catch (\Exception $e) {
            // FALLBACK: Jika ada error, berikan data default
            Log::error('Dashboard Admin Error: ' . $e->getMessage());
            
            return view('admin.dashboard', [
                'today_bookings' => 0,
                'active_work_orders' => 0,
                'completed_services' => 0,
                'completed_trend' => 0,
                'booking_trend' => 0,
                'pending_appointments' => 0,
                'recent_appointments' => collect(),
                'recent_work_orders' => collect(),
                'total_vehicles' => 0,
                'next_appointment' => null,
                'chart_data' => [
                    'chart_labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    'chart_data' => [0, 0, 0, 0, 0, 0]
                ],
                'status_data' => [
                    'status_labels' => ['Tidak ada data'],
                    'status_data' => [1]
                ]
            ]);
        }
    }

    /**
     * Get booking chart data for customer.
     *
     * @param  int  $customerId
     * @return array
     */
    private function getCustomerBookingChartData($customerId)
    {
        try {
            $months = [];
            $data = [];
            $labels = [];

            // Get last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months[] = $date;
                $labels[] = $date->format('M Y');
                
                $count = Appointment::where('customer_id', $customerId)
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();
                
                $data[] = $count;
            }

            return [
                'chart_labels' => $labels,
                'chart_data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'chart_labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                'chart_data' => [0, 0, 0, 0, 0, 0]
            ];
        }
    }

    /**
     * Get booking chart data for admin.
     *
     * @return array
     */
    private function getBookingChartData()
    {
        try {
            $months = [];
            $data = [];
            $labels = [];

            // Get last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months[] = $date;
                $labels[] = $date->format('M');
                
                $count = Appointment::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();
                
                $data[] = $count;
            }

            return [
                'chart_labels' => $labels,
                'chart_data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'chart_labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                'chart_data' => [0, 0, 0, 0, 0, 0]
            ];
        }
    }

    /**
     * Get status distribution data for customer.
     *
     * @param  int  $customerId
     * @return array
     */
    private function getCustomerStatusData($customerId)
    {
        try {
            $statusCounts = Appointment::where('customer_id', $customerId)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();

            $labels = [];
            $data = [];

            $statusLabels = [
                'pending' => 'Menunggu',
                'confirmed' => 'Dikonfirmasi',
                'in_progress' => 'Dalam Proses',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan'
            ];

            foreach ($statusLabels as $status => $label) {
                if (isset($statusCounts[$status]) && $statusCounts[$status] > 0) {
                    $labels[] = $label;
                    $data[] = $statusCounts[$status];
                }
            }

            // Jika tidak ada data, berikan data default
            if (empty($labels)) {
                $labels = ['Tidak ada data'];
                $data = [1];
            }

            return [
                'status_labels' => $labels,
                'status_data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'status_labels' => ['Tidak ada data'],
                'status_data' => [1]
            ];
        }
    }

    /**
     * Get service distribution data for admin.
     *
     * @return array
     */
    private function getServiceDistributionData()
    {
        try {
            // Get most used services from work orders with total quantity
            $serviceTypes = DB::table('work_order_services')
                ->join('services', 'work_order_services.service_id', '=', 'services.id')
                ->select('services.name', DB::raw('SUM(work_order_services.quantity) as total_quantity'))
                ->groupBy('services.id', 'services.name')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get();

            $labels = [];
            $data = [];

            foreach ($serviceTypes as $service) {
                $labels[] = Str::limit($service->name, 20);
                $data[] = (int) $service->total_quantity;
            }

            // If no data, provide default
            if (empty($labels)) {
                $labels = ['Servis Rutin', 'Ganti Oli', 'Tune Up', 'Ganti Ban', 'Service AC'];
                $data = [0, 0, 0, 0, 0];
            }

            return [
                'status_labels' => $labels,
                'status_data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Service Distribution Data Error: ' . $e->getMessage());
            return [
                'status_labels' => ['Tidak ada data'],
                'status_data' => [1]
            ];
        }
    }

    /**
     * Get service revenue distribution data for admin.
     *
     * @return array
     */
    private function getServiceRevenueData()
    {
        try {
            // Get services by revenue (price * quantity)
            $serviceRevenue = DB::table('work_order_services')
                ->join('services', 'work_order_services.service_id', '=', 'services.id')
                ->join('work_orders', 'work_order_services.work_order_id', '=', 'work_orders.id')
                ->where('work_orders.status', 'completed')
                ->select('services.name', DB::raw('SUM(work_order_services.price * work_order_services.quantity) as total_revenue'))
                ->groupBy('services.id', 'services.name')
                ->orderBy('total_revenue', 'desc')
                ->limit(5)
                ->get();

            $labels = [];
            $data = [];

            foreach ($serviceRevenue as $service) {
                $labels[] = Str::limit($service->name, 20);
                $data[] = (float) $service->total_revenue;
            }

            // If no data, provide default
            if (empty($labels)) {
                $labels = ['Tidak ada data'];
                $data = [0];
            }

            return [
                'revenue_labels' => $labels,
                'revenue_data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Service Revenue Data Error: ' . $e->getMessage());
            return [
                'revenue_labels' => ['Tidak ada data'],
                'revenue_data' => [0]
            ];
        }
    }

    /**
     * Get popular services statistics.
     *
     * @return array
     */
    private function getPopularServicesStats()
    {
        try {
            // Get top 10 services by usage
            $popularServices = DB::table('work_order_services')
                ->join('services', 'work_order_services.service_id', '=', 'services.id')
                ->select(
                    'services.name',
                    'services.category',
                    DB::raw('COUNT(*) as usage_count'),
                    DB::raw('SUM(work_order_services.quantity) as total_quantity'),
                    DB::raw('AVG(work_order_services.price) as avg_price'),
                    DB::raw('SUM(work_order_services.price * work_order_services.quantity) as total_revenue')
                )
                ->groupBy('services.id', 'services.name', 'services.category')
                ->orderBy('usage_count', 'desc')
                ->limit(10)
                ->get();

            return $popularServices->toArray();
        } catch (\Exception $e) {
            Log::error('Popular Services Stats Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get statistics for API calls or AJAX requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        try {
            $user = Auth::user();
            $period = $request->get('period', 'month'); // day, week, month, year

            $startDate = match ($period) {
                'day' => Carbon::today(),
                'week' => Carbon::now()->startOfWeek(),
                'month' => Carbon::now()->startOfMonth(),
                'year' => Carbon::now()->startOfYear(),
                default => Carbon::now()->startOfMonth(),
            };

            if ($user->role === 'customer') {
                $appointments = Appointment::where('customer_id', $user->id)
                    ->where('created_at', '>=', $startDate)
                    ->count();
                    
                $completed = Appointment::where('customer_id', $user->id)
                    ->where('status', 'completed')
                    ->where('created_at', '>=', $startDate)
                    ->count();
                    
                $vehicles = Vehicle::where('customer_id', $user->id)->count();
                
                return response()->json([
                    'appointments' => $appointments,
                    'completed' => $completed,
                    'vehicles' => $vehicles,
                    'period' => $period
                ]);
            } else {
                $appointments = Appointment::where('created_at', '>=', $startDate)->count();
                $workOrders = WorkOrder::where('created_at', '>=', $startDate)->count();
                $completed = WorkOrder::where('status', 'completed')
                    ->where('created_at', '>=', $startDate)
                    ->count();
                $revenue = WorkOrder::where('status', 'completed')
                    ->where('created_at', '>=', $startDate)
                    ->sum('total_amount');
                
                return response()->json([
                    'appointments' => $appointments,
                    'work_orders' => $workOrders,
                    'completed' => $completed,
                    'revenue' => $revenue,
                    'period' => $period
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get stats'], 500);
        }
    }

    /**
     * Get recent activities for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentActivities(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 10);

            if ($user->role === 'customer') {
                $appointments = Appointment::with(['vehicle'])
                    ->where('customer_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
                    
                $activities = $appointments->map(function ($appointment) {
                    return [
                        'id' => $appointment->id,
                        'type' => 'appointment',
                        'title' => 'Janji Servis',
                        'description' => $appointment->service_description,
                        'vehicle' => $appointment->vehicle->brand . ' ' . $appointment->vehicle->model,
                        'date' => $appointment->appointment_date,
                        'status' => $appointment->status,
                        'created_at' => $appointment->created_at,
                    ];
                });
                
                return response()->json($activities);
            } else {
                // For admin/owner, get both appointments and work orders
                $appointments = Appointment::with(['vehicle', 'customer'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limit / 2)
                    ->get()
                    ->map(function ($appointment) {
                        return [
                            'id' => $appointment->id,
                            'type' => 'appointment',
                            'title' => 'Appointment Baru',
                            'description' => $appointment->service_description,
                            'customer' => $appointment->customer->name,
                            'vehicle' => $appointment->vehicle->brand . ' ' . $appointment->vehicle->model,
                            'date' => $appointment->appointment_date,
                            'status' => $appointment->status,
                            'created_at' => $appointment->created_at,
                        ];
                    });
                    
                $workOrders = WorkOrder::with(['vehicle', 'customer', 'mechanic'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limit / 2)
                    ->get()
                    ->map(function ($workOrder) {
                        return [
                            'id' => $workOrder->id,
                            'type' => 'work_order',
                            'title' => 'Work Order',
                            'description' => $workOrder->work_order_number,
                            'customer' => $workOrder->customer_name ?? $workOrder->customer->name ?? 'Walk-in',
                            'vehicle' => $workOrder->vehicle->brand . ' ' . $workOrder->vehicle->model,
                            'mechanic' => $workOrder->mechanic->name,
                            'amount' => $workOrder->total_amount,
                            'status' => $workOrder->status,
                            'created_at' => $workOrder->created_at,
                        ];
                    });
                    
                $activities = $appointments->merge($workOrders)->sortByDesc('created_at')->take($limit);
                
                return response()->json($activities->values());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get activities'], 500);
        }
    }

    /**
     * Get dashboard widgets data for dynamic updates.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWidgets()
    {
        try {
            $user = Auth::user();
            
            if ($user->role === 'customer') {
                $today = Carbon::today();
                $thisMonth = Carbon::now()->startOfMonth();
                
                return response()->json([
                    'today_bookings' => Appointment::where('customer_id', $user->id)
                        ->whereDate('appointment_date', $today)
                        ->whereIn('status', ['confirmed', 'in_progress'])
                        ->count(),
                    'total_vehicles' => Vehicle::where('customer_id', $user->id)->count(),
                    'completed_this_month' => Appointment::where('customer_id', $user->id)
                        ->where('status', 'completed')
                        ->where('created_at', '>=', $thisMonth)
                        ->count(),
                    'next_appointment' => Appointment::where('customer_id', $user->id)
                        ->where('appointment_date', '>=', $today)
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->orderBy('appointment_date')
                        ->first(),
                ]);
            } else {
                $today = Carbon::today();
                $thisMonth = Carbon::now()->startOfMonth();
                
                return response()->json([
                    'today_bookings' => Appointment::whereDate('appointment_date', $today)
                        ->whereIn('status', ['confirmed', 'in_progress'])
                        ->count(),
                    'active_work_orders' => WorkOrder::whereIn('status', ['pending', 'in_progress'])->count(),
                    'completed_this_month' => WorkOrder::where('status', 'completed')
                        ->where('created_at', '>=', $thisMonth)
                        ->count(),
                    'pending_appointments' => Appointment::where('status', 'pending')->count(),
                    'total_revenue' => WorkOrder::where('status', 'completed')
                        ->where('created_at', '>=', $thisMonth)
                        ->sum('total_amount'),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get widgets data'], 500);
        }
    }

    /**
     * Get service statistics for dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceStats(Request $request)
    {
        try {
            $period = $request->get('period', 'month');
            $type = $request->get('type', 'quantity'); // quantity, revenue, usage
            
            $startDate = match ($period) {
                'day' => Carbon::today(),
                'week' => Carbon::now()->startOfWeek(),
                'month' => Carbon::now()->startOfMonth(),
                'year' => Carbon::now()->startOfYear(),
                default => Carbon::now()->startOfMonth(),
            };

            $query = DB::table('work_order_services')
                ->join('services', 'work_order_services.service_id', '=', 'services.id')
                ->join('work_orders', 'work_order_services.work_order_id', '=', 'work_orders.id')
                ->where('work_orders.created_at', '>=', $startDate);

            switch ($type) {
                case 'revenue':
                    $services = $query->select(
                        'services.name',
                        DB::raw('SUM(work_order_services.price * work_order_services.quantity) as value')
                    )
                    ->where('work_orders.status', 'completed')
                    ->groupBy('services.id', 'services.name')
                    ->orderBy('value', 'desc')
                    ->limit(10)
                    ->get();
                    break;
                    
                case 'usage':
                    $services = $query->select(
                        'services.name',
                        DB::raw('COUNT(*) as value')
                    )
                    ->groupBy('services.id', 'services.name')
                    ->orderBy('value', 'desc')
                    ->limit(10)
                    ->get();
                    break;
                    
                default: // quantity
                    $services = $query->select(
                        'services.name',
                        DB::raw('SUM(work_order_services.quantity) as value')
                    )
                    ->groupBy('services.id', 'services.name')
                    ->orderBy('value', 'desc')
                    ->limit(10)
                    ->get();
                    break;
            }

            return response()->json([
                'services' => $services,
                'period' => $period,
                'type' => $type
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get service stats'], 500);
        }
    }

    /**
     * Get popular services data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPopularServices()
    {
        try {
            $popularServices = $this->getPopularServicesStats();
            return response()->json($popularServices);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to get popular services'], 500);
        }
    }
}