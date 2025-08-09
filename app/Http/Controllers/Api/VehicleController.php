<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    /**
     * Display a listing of vehicles
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        
        // Jika user adalah customer, hanya tampilkan kendaraan milik customer tersebut
        if ($user->role === 'customer') {
            $vehicles = Vehicle::with('customer')
                ->where('customer_id', $user->id)
                ->latest()
                ->paginate($perPage);
        } else {
            // Admin/mechanic dapat melihat semua kendaraan
            $vehicles = Vehicle::with('customer')->latest()->paginate($perPage);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vehicles retrieved successfully',
            'data' => $vehicles->items(),
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total(),
            ]
        ]);
    }

    /**
     * Store a newly created vehicle
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
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
                if ($customer && $customer->role !== 'customer') {
                    $validator->errors()->add('customer_id', 'Selected user must have customer role.');
                }
                
                // Jika user adalah customer, pastikan mereka hanya bisa menambah untuk diri sendiri
                if ($user->role === 'customer' && $request->customer_id != $user->id) {
                    $validator->errors()->add('customer_id', 'You can only add vehicles for yourself.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $vehicle = Vehicle::create($request->only([
            'customer_id', 'type', 'brand', 'model', 'year', 
            'license_plate', 'color', 'notes'
        ]));

        $vehicle->load('customer');

        return response()->json([
            'success' => true,
            'message' => 'Vehicle created successfully',
            'data' => $vehicle
        ], 201);
    }

    /**
     * Display the specified vehicle
     */
    public function show(Request $request, Vehicle $vehicle)
    {
        $user = $request->user();
        
        // Jika customer, pastikan mereka hanya bisa melihat kendaraan milik mereka
        if ($user->role === 'customer' && $vehicle->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this vehicle'
            ], 403);
        }
        
        $vehicle->load('customer', 'appointments', 'workOrders');

        return response()->json([
            'success' => true,
            'message' => 'Vehicle retrieved successfully',
            'data' => $vehicle
        ]);
    }

    /**
     * Update the specified vehicle
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $user = $request->user();
        
        // Customer tidak boleh update kendaraan (hanya admin/mechanic)
        if ($user->role === 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update vehicles'
            ], 403);
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
                $customer = User::find($request->customer_id);
                if ($customer && $customer->role !== 'customer') {
                    $validator->errors()->add('customer_id', 'Selected user must have customer role.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $vehicle->update($request->only([
            'customer_id', 'type', 'brand', 'model', 'year', 
            'license_plate', 'color', 'notes'
        ]));

        $vehicle->load('customer');

        return response()->json([
            'success' => true,
            'message' => 'Vehicle updated successfully',
            'data' => $vehicle
        ]);
    }

    /**
     * Remove the specified vehicle
     */
    public function destroy(Request $request, Vehicle $vehicle)
    {
        $user = $request->user();
        
        // Customer tidak boleh hapus kendaraan (hanya admin/mechanic)
        if ($user->role === 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete vehicles'
            ], 403);
        }
        
        // Check if vehicle has related appointments or work orders
        if ($vehicle->appointments()->exists() || $vehicle->workOrders()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete vehicle that has appointments or work orders'
            ], 400);
        }

        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle deleted successfully'
        ]);
    }

    /**
     * Search vehicles by license plate, brand, model or customer
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $user = $request->user();
        $perPage = $request->get('per_page', 15);
        
        if (empty($query)) {
            return $this->index($request);
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
            
        $vehicles = $vehicleQuery->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Search results retrieved successfully',
            'data' => $vehicles->items(),
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total(),
                'search_query' => $query
            ]
        ]);
    }

    /**
     * Get vehicles by customer
     */
    public function getByCustomer(Request $request)
    {
        $customerId = $request->get('customer_id');
        $user = $request->user();
        
        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Customer ID is required',
                'data' => []
            ], 400);
        }
        
        // Jika customer, pastikan mereka hanya bisa mengakses kendaraan milik mereka
        if ($user->role === 'customer' && $customerId != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $vehicles = Vehicle::where('customer_id', $customerId)
            ->select('id', 'brand', 'model', 'year', 'license_plate', 'type', 'color')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Vehicles retrieved successfully',
            'data' => $vehicles
        ]);
    }

    /**
     * Get vehicle details with customer info
     */
    public function getDetails(Request $request, Vehicle $vehicle)
    {
        $user = $request->user();
        
        // Jika customer, pastikan mereka hanya bisa mengakses kendaraan milik mereka
        if ($user->role === 'customer' && $vehicle->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }
        
        $vehicle->load('customer');
        
        return response()->json([
            'success' => true,
            'message' => 'Vehicle details retrieved successfully',
            'data' => [
                'vehicle' => $vehicle,
                'customer_name' => $vehicle->customer->name,
                'customer_phone' => $vehicle->customer->phone,
                'customer_email' => $vehicle->customer->email,
            ]
        ]);
    }
}