<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Vehicle;
use App\Models\ServiceSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the appointments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['vehicle', 'customer']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_range')) {
            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) {
                $query->whereBetween('appointment_date', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay()
                ]);
            }
        }

        if ($request->filled('vehicle_type')) {
            $query->whereHas('vehicle', function ($q) use ($request) {
                $q->where('type', $request->vehicle_type);
            });
        }

        $appointments = $query->latest()->paginate(15);

        // Get statistics
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $today_count = Appointment::whereDate('appointment_date', $today)
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->count();

        $pending_count = Appointment::where('status', 'pending')->count();
        $in_progress_count = Appointment::where('status', 'in_progress')->count();
        $completed_count = Appointment::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->count();

        // Get recent appointments for the dashboard section
        $recent_appointments = Appointment::with(['vehicle', 'customer'])
            ->latest()
            ->limit(10)
            ->get();

        return view('appointments.index', compact(
            'appointments',
            'today_count',
            'pending_count',
            'in_progress_count',
            'completed_count',
            'recent_appointments'
        ));
    }

    /**
     * Show the form for creating a new appointment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
{
    // Get all customers (users with role 'customer')
    $customers = User::where('role', 'customer')
        ->orderBy('name')
        ->get();
    
    // Get all schedules
    $schedules = ServiceSchedule::orderBy('day_of_week')->get();

    // Get all vehicles
    $vehicles = Vehicle::all(); // <-- Tambahkan baris ini

    return view('appointments.create', compact('customers', 'schedules', 'vehicles'));
}

    /**
     * Store a newly created appointment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = [
            'customer_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'service_description' => 'required|string',
            'notes' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        // Additional validation to ensure vehicle belongs to customer
        $validator->after(function ($validator) use ($request) {
            if ($request->customer_id && $request->vehicle_id) {
                $vehicle = Vehicle::find($request->vehicle_id);
                if ($vehicle && $vehicle->customer_id != $request->customer_id) {
                    $validator->errors()->add('vehicle_id', 'Kendaraan yang dipilih tidak milik customer tersebut.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate appointment against service schedule
        $appointmentDate = Carbon::parse($request->appointment_date);
        $dayOfWeek = $appointmentDate->dayOfWeek;

        $schedule = ServiceSchedule::where('day_of_week', $dayOfWeek)->first();

        if (!$schedule || $schedule->is_closed) {
            return redirect()->back()
                ->withErrors(['appointment_date' => 'Kami tidak menerima janji pada hari ini.'])
                ->withInput();
        }

        $appointmentTime = Carbon::createFromFormat('H:i', $request->appointment_time);
        $openTime = Carbon::createFromFormat('H:i', $schedule->open_time->format('H:i'));
        $closeTime = Carbon::createFromFormat('H:i', $schedule->close_time->format('H:i'));

        if ($appointmentTime->lt($openTime) || $appointmentTime->gt($closeTime)) {
            return redirect()->back()
                ->withErrors(['appointment_time' => 'Waktu janji di luar jam operasional.'])
                ->withInput();
        }

        // Check if max appointments for the day has been reached
        $appointmentsCount = Appointment::whereDate('appointment_date', $appointmentDate)
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->count();

        if ($appointmentsCount >= $schedule->max_appointments) {
            return redirect()->back()
                ->withErrors(['appointment_date' => 'Kuota janji untuk tanggal ini sudah penuh.'])
                ->withInput();
        }

        // Create appointment
        $appointment = Appointment::create([
            'customer_id' => $request->customer_id,
            'vehicle_id' => $request->vehicle_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'service_description' => $request->service_description,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Janji service berhasil dibuat. Kode pelacakan: ' . $appointment->tracking_code);
    }

    /**
     * Get customer details by ID (for AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerDetails(Request $request)
    {
        $customerId = $request->get('customer_id');
        
        if (!$customerId) {
            return response()->json(['success' => false]);
        }

        $customer = User::with('vehicles')->find($customerId);
        
        if (!$customer || $customer->role !== 'customer') {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
            'customer' => [
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email
            ],
            'vehicles' => $customer->vehicles->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'display_name' => $vehicle->brand . ' ' . $vehicle->model . ' (' . $vehicle->license_plate . ')'
                ];
            })
        ]);
    }

    /**
     * Display the specified appointment.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['vehicle', 'customer']);
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Appointment $appointment)
    {
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $schedules = ServiceSchedule::orderBy('day_of_week')->get();

        $appointment->load(['vehicle', 'customer']);

        return view('appointments.edit', compact('appointment', 'customers', 'schedules'));
    }

    /**
     * Update the specified appointment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Check if this is just a status update (quick action)
        if ($request->has('status') && count($request->all()) <= 3) { // status, _token, _method
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Update only status and notes
            $updateData = ['status' => $request->status];

            if ($request->has('notes')) {
                $updateData['notes'] = $request->notes;
            }

            $appointment->update($updateData);

            $statusMessages = [
                'confirmed' => 'Janji service berhasil dikonfirmasi.',
                'in_progress' => 'Status berhasil diubah ke dalam proses.',
                'completed' => 'Servis berhasil diselesaikan.',
                'cancelled' => 'Janji service berhasil dibatalkan.',
                'pending' => 'Status berhasil diubah ke pending.'
            ];

            $message = $statusMessages[$request->status] ?? 'Status berhasil diperbarui.';

            return redirect()->route('appointments.show', $appointment)
                ->with('success', $message);
        }

        // Full update validation (for edit form)
        $validationRules = [
            'customer_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'service_description' => 'required|string',
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        // Additional validation to ensure vehicle belongs to customer
        $validator->after(function ($validator) use ($request) {
            if ($request->customer_id && $request->vehicle_id) {
                $vehicle = Vehicle::find($request->vehicle_id);
                if ($vehicle && $vehicle->customer_id != $request->customer_id) {
                    $validator->errors()->add('vehicle_id', 'Kendaraan yang dipilih tidak milik customer tersebut.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Additional validations only if changing the date/time
        if (
            $request->appointment_date != $appointment->appointment_date->format('Y-m-d') ||
            $request->appointment_time != $appointment->appointment_time->format('H:i')
        ) {

            // Validate appointment against service schedule
            $appointmentDate = Carbon::parse($request->appointment_date);
            $dayOfWeek = $appointmentDate->dayOfWeek;

            $schedule = ServiceSchedule::where('day_of_week', $dayOfWeek)->first();

            if (!$schedule || $schedule->is_closed) {
                return redirect()->back()
                    ->withErrors(['appointment_date' => 'Kami tidak menerima janji pada hari ini.'])
                    ->withInput();
            }

            $appointmentTime = Carbon::createFromFormat('H:i', $request->appointment_time);
            $openTime = Carbon::createFromFormat('H:i', $schedule->open_time->format('H:i'));
            $closeTime = Carbon::createFromFormat('H:i', $schedule->close_time->format('H:i'));

            if ($appointmentTime->lt($openTime) || $appointmentTime->gt($closeTime)) {
                return redirect()->back()
                    ->withErrors(['appointment_time' => 'Waktu janji di luar jam operasional.'])
                    ->withInput();
            }

            // Check if max appointments for the day has been reached (excluding this appointment)
            $appointmentsCount = Appointment::whereDate('appointment_date', $appointmentDate)
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->where('id', '!=', $appointment->id)
                ->count();

            if ($appointmentsCount >= $schedule->max_appointments) {
                return redirect()->back()
                    ->withErrors(['appointment_date' => 'Kuota janji untuk tanggal ini sudah penuh.'])
                    ->withInput();
            }
        }

        // Update appointment
        $appointment->update([
            'customer_id' => $request->customer_id,
            'vehicle_id' => $request->vehicle_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'service_description' => $request->service_description,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Janji service berhasil diperbarui.');
    }

    /**
     * Quick status update method
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = ['status' => $request->status];

        if ($request->has('notes') && !empty($request->notes)) {
            $updateData['notes'] = $request->notes;
        }

        $appointment->update($updateData);

        $statusMessages = [
            'confirmed' => 'Janji service berhasil dikonfirmasi.',
            'in_progress' => 'Status berhasil diubah ke dalam proses.',
            'completed' => 'Servis berhasil diselesaikan.',
            'cancelled' => 'Janji service berhasil dibatalkan.',
            'pending' => 'Status berhasil diubah ke pending.'
        ];

        $message = $statusMessages[$request->status] ?? 'Status berhasil diperbarui.';

        return redirect()->route('appointments.show', $appointment)
            ->with('success', $message);
    }

    /**
     * Remove the specified appointment from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Janji service berhasil dihapus.');
    }

    /**
     * Display appointments for today.
     *
     * @return \Illuminate\Http\Response
     */
    public function today()
    {
        $appointments = Appointment::with(['vehicle', 'customer'])
            ->today()
            ->latest()
            ->get();

        return view('appointments.today', compact('appointments'));
    }

    /**
     * Track appointment by tracking code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function track(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'tracking_code' => 'required|string|max:20',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $appointment = Appointment::with(['vehicle', 'customer'])
                ->where('tracking_code', $request->tracking_code)
                ->first();

            if (!$appointment) {
                return redirect()->back()
                    ->withErrors(['tracking_code' => 'Kode pelacakan tidak valid.'])
                    ->withInput();
            }

            return view('appointments.track-result', compact('appointment'));
        }

        return view('appointments.track');
    }
}