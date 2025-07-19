<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Vehicle;
use App\Models\Service;
use App\Models\WorkOrder;
use App\Models\User;
use Carbon\Carbon;

class CustomerController extends Controller
{
    /**
     * Display customer dashboard
     */
    public function dashboard()
{
    $user = Auth::user();
    
    // Get customer statistics
    $activeBookings = Appointment::where('customer_id', $user->id)
        ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
        ->count();
    
    $totalVehicles = Vehicle::where('customer_id', $user->id)->count();
    
    $completedServices = WorkOrder::where('customer_id', $user->id)
        ->where('status', 'completed')
        ->count();

    // Get last completed service and estimate next service date
    $lastService = WorkOrder::with('vehicle')
        ->where('customer_id', $user->id)
        ->where('status', 'completed')
        ->orderBy('end_time', 'desc') // bisa juga 'start_time'
        ->first();

    $nextService = null;
    $lastServiceVehicle = null;

    if ($lastService && $lastService->end_time) {
        $nextService = Carbon::parse($lastService->end_time)
            ->addMonths(3)
            ->format('d M');
        $lastServiceVehicle = $lastService->vehicle;
    }

    // Get active bookings list
    $activeBookingsList = Appointment::with(['vehicle'])
        ->where('customer_id', $user->id)
        ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
        ->orderBy('appointment_date', 'desc')
        ->limit(5)
        ->get();

    // Get recent completed services
    $recentServices = WorkOrder::with(['vehicle'])
        ->where('customer_id', $user->id)
        ->where('status', 'completed')
        ->orderBy('end_time', 'desc')
        ->limit(5)
        ->get();

    return view('customer.dashboard', compact(
        'activeBookings',
        'totalVehicles',
        'completedServices',
        'nextService',
        'lastServiceVehicle',
        'activeBookingsList',
        'recentServices'
    ));
}

    
    /**
     * Display customer bookings
     */
    public function bookings()
    {
        $user = Auth::user();
        
        $bookings = Appointment::with(['vehicle', 'service'])
            ->where('customer_id', $user->id)
            ->orderBy('appointment_date', 'desc')
            ->paginate(10);
        
        return view('customer.bookings.index', compact('bookings'));
    }
    
    /**
     * Show form to create new booking
     */
    public function createBooking()
    {
        $user = Auth::user();
        
        $vehicles = Vehicle::where('customer_id', $user->id)->get();
        $services = Service::where('is_active', true)->get();
        
        return view('customer.bookings.create', compact('vehicles', 'services'));
    }
    
    /**
     * Store new booking
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'notes' => 'nullable|string|max:500'
        ]);
        
        // Verify vehicle belongs to current user
        $vehicle = Vehicle::where('id', $request->vehicle_id)
            ->where('customer_id', Auth::id())
            ->firstOrFail();
        
        $appointment = Appointment::create([
            'customer_id' => Auth::id(),
            'vehicle_id' => $request->vehicle_id,
            'service_id' => $request->service_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);
        
        return redirect()->route('customer.bookings')
            ->with('success', 'Booking berhasil dibuat! Kami akan segera menghubungi Anda untuk konfirmasi.');
    }
    
    /**
     * Display booking history
     */
    public function bookingHistory()
    {
        $user = Auth::user();
        
        $bookings = Appointment::with(['vehicle', 'service'])
            ->where('customer_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderBy('appointment_date', 'desc')
            ->paginate(15);
        
        return view('customer.bookings.history', compact('bookings'));
    }
    
    /**
     * Display customer vehicles
     */
    public function vehicles()
    {
        $user = Auth::user();
        
        $vehicles = Vehicle::where('customer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('customer.vehicles.index', compact('vehicles'));
    }
    
    /**
     * Show form to add new vehicle
     */
    public function createVehicle()
    {
        return view('customer.vehicles.create');
    }
    
    /**
     * Store new vehicle
     */
    public function storeVehicle(Request $request)
    {
        $request->validate([
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate',
            'color' => 'required|string|max:50',
            'engine_number' => 'nullable|string|max:100',
            'chassis_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'mileage' => 'nullable|integer|min:0'
        ]);
        
        Vehicle::create([
            'customer_id' => Auth::id(),
            'brand' => $request->brand,
            'model' => $request->model,
            'year' => $request->year,
            'license_plate' => strtoupper($request->license_plate),
            'color' => $request->color,
            'engine_number' => $request->engine_number,
            'chassis_number' => $request->chassis_number,
            'purchase_date' => $request->purchase_date,
            'mileage' => $request->mileage ?? 0
        ]);
        
        return redirect()->route('customer.vehicles')
            ->with('success', 'Kendaraan berhasil ditambahkan!');
    }
    
    /**
     * Show form to edit vehicle
     */
    public function editVehicle(Vehicle $vehicle)
    {
        // Ensure vehicle belongs to current user
        if ($vehicle->customer_id !== Auth::id()) {
            abort(403);
        }
        
        return view('customer.vehicles.edit', compact('vehicle'));
    }
    
    /**
     * Update vehicle information
     */
    public function updateVehicle(Request $request, Vehicle $vehicle)
    {
        // Ensure vehicle belongs to current user
        if ($vehicle->customer_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate,' . $vehicle->id,
            'color' => 'required|string|max:50',
            'engine_number' => 'nullable|string|max:100',
            'chassis_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'mileage' => 'nullable|integer|min:0'
        ]);
        
        $vehicle->update([
            'brand' => $request->brand,
            'model' => $request->model,
            'year' => $request->year,
            'license_plate' => strtoupper($request->license_plate),
            'color' => $request->color,
            'engine_number' => $request->engine_number,
            'chassis_number' => $request->chassis_number,
            'purchase_date' => $request->purchase_date,
            'mileage' => $request->mileage ?? $vehicle->mileage
        ]);
        
        return redirect()->route('customer.vehicles')
            ->with('success', 'Informasi kendaraan berhasil diperbarui!');
    }
    
    /**
     * Display customer profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('customer.profile.index', compact('user'));
    }
    
    /**
     * Update customer profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|min:8|confirmed'
        ]);
        
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address
        ];
        
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }
        
        $user->update($updateData);
        
        return redirect()->route('customer.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}