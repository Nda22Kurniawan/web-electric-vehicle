<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    /**
     * Display a listing of the vehicles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Jika user adalah customer, hanya tampilkan kendaraan milik customer tersebut
        if ($user->role === 'customer') {
            $vehicles = Vehicle::with('customer')
                ->where('customer_id', $user->id)
                ->latest()
                ->paginate(10);
        } else {
            // Admin/Owner dapat melihat semua kendaraan
            $vehicles = Vehicle::with('customer')->latest()->paginate(10);
        }
        
        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        
        // Jika customer, otomatis pilih dirinya sendiri
        if ($user->role === 'customer') {
            $customers = collect([$user]); // Hanya dirinya sendiri
        } else {
            // Only get customers (users with role 'customer')
            $customers = User::where('role', 'customer')->orderBy('name')->get();
        }
        
        return view('vehicles.create', compact('customers'));
    }

    /**
     * Store a newly created vehicle in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Jika customer, paksa customer_id ke ID mereka sendiri
        if ($user->role === 'customer') {
            $request->merge(['customer_id' => $user->id]);
        }
        
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'type' => 'required|in:motorcycle,electric_bike',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'license_plate' => 'required|string|max:20|unique:vehicles',
            'color' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        // Additional validation for customer role
        $validator->after(function ($validator) use ($request, $user) {
            if ($request->customer_id) {
                $customer = User::find($request->customer_id);
                if ($customer && !$customer->isCustomer()) {
                    $validator->errors()->add('customer_id', 'Pengguna yang dipilih harus memiliki role customer.');
                }
                
                // Jika user adalah customer, pastikan mereka hanya bisa menambah untuk diri sendiri
                if ($user->role === 'customer' && $request->customer_id != $user->id) {
                    $validator->errors()->add('customer_id', 'Anda hanya dapat menambahkan kendaraan untuk diri sendiri.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create vehicle
        Vehicle::create([
            'customer_id' => $request->customer_id,
            'type' => $request->type,
            'brand' => $request->brand,
            'model' => $request->model,
            'year' => $request->year,
            'license_plate' => $request->license_plate,
            'color' => $request->color,
            'notes' => $request->notes,
        ]);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    /**
     * Display the specified vehicle.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(Vehicle $vehicle)
    {
        $user = Auth::user();
        
        // Jika customer, pastikan mereka hanya bisa melihat kendaraan milik mereka
        if ($user->role === 'customer' && $vehicle->customer_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke kendaraan ini.');
        }
        
        $vehicle->load('customer', 'appointments', 'workOrders');
        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified vehicle.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function edit(Vehicle $vehicle)
    {
        $user = Auth::user();
        
        // Customer tidak boleh edit kendaraan (hanya admin/owner)
        if ($user->role === 'customer') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit kendaraan.');
        }
        
        // Only get customers (users with role 'customer')
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $vehicle->load('customer');
        return view('vehicles.edit', compact('vehicle', 'customers'));
    }

    /**
     * Update the specified vehicle in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $user = Auth::user();
        
        // Customer tidak boleh update kendaraan
        if ($user->role === 'customer') {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate kendaraan.');
        }
        
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'type' => 'required|in:motorcycle,electric_bike',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate,' . $vehicle->id,
            'color' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        // Validate customer role
        $validator->after(function ($validator) use ($request) {
            if ($request->customer_id) {
                $user = User::find($request->customer_id);
                if ($user && !$user->isCustomer()) {
                    $validator->errors()->add('customer_id', 'Pengguna yang dipilih harus memiliki role customer.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update vehicle with only the fields that exist in database
        $vehicle->update([
            'customer_id' => $request->customer_id,
            'type' => $request->type,
            'brand' => $request->brand,
            'model' => $request->model,
            'year' => $request->year,
            'license_plate' => $request->license_plate,
            'color' => $request->color,
            'notes' => $request->notes,
        ]);

        return redirect()->route('vehicles.index')
            ->with('success', 'Informasi kendaraan berhasil diperbarui.');
    }

    /**
     * Remove the specified vehicle from storage.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehicle $vehicle)
    {
        $user = Auth::user();
        
        // Customer tidak boleh hapus kendaraan
        if ($user->role === 'customer') {
            abort(403, 'Anda tidak memiliki akses untuk menghapus kendaraan.');
        }
        
        // Check if vehicle has related appointments or work orders
        if ($vehicle->appointments()->exists() || $vehicle->workOrders()->exists()) {
            return redirect()->route('vehicles.index')
                ->with('error', 'Tidak dapat menghapus kendaraan yang memiliki janji temu atau work order.');
        }

        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }

    /**
     * Search vehicles by license plate or customer name
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $user = Auth::user();
        
        if (empty($query)) {
            return redirect()->route('vehicles.index');
        }

        $vehicleQuery = Vehicle::with('customer')
            ->where(function ($q) use ($query) {
                $q->where('license_plate', 'like', "%{$query}%")
                  ->orWhere('brand', 'like', "%{$query}%")
                  ->orWhere('model', 'like', "%{$query}%")
                  ->orWhereHas('customer', function ($subQuery) use ($query) {
                      $subQuery->where('name', 'like', "%{$query}%")
                               ->orWhere('phone', 'like', "%{$query}%");
                  });
            });
            
        // Jika customer, filter hanya kendaraan milik mereka
        if ($user->role === 'customer') {
            $vehicleQuery->where('customer_id', $user->id);
        }
            
        $vehicles = $vehicleQuery->latest()
            ->paginate(10)
            ->appends(['q' => $query]);

        return view('vehicles.index', compact('vehicles', 'query'));
    }

    /**
     * Get vehicles by customer (for AJAX requests)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByCustomer(Request $request)
    {
        $customerId = $request->get('customer_id');
        $user = Auth::user();
        
        if (!$customerId) {
            return response()->json(['vehicles' => []]);
        }
        
        // Jika customer, pastikan mereka hanya bisa mengakses kendaraan milik mereka
        if ($user->role === 'customer' && $customerId != $user->id) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $vehicles = Vehicle::where('customer_id', $customerId)
            ->select('id', 'brand', 'model', 'year', 'license_plate', 'type')
            ->get();

        return response()->json(['vehicles' => $vehicles]);
    }

    /**
     * Get vehicle details (for AJAX requests)
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetails(Vehicle $vehicle)
    {
        $user = Auth::user();
        
        // Jika customer, pastikan mereka hanya bisa mengakses kendaraan milik mereka
        if ($user->role === 'customer' && $vehicle->customer_id !== $user->id) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        
        $vehicle->load('customer');
        
        return response()->json([
            'vehicle' => $vehicle,
            'customer_name' => $vehicle->customer->name,
            'customer_phone' => $vehicle->customer->phone,
        ]);
    }
}