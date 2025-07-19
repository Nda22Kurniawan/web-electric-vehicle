<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    /**
     * Display a listing of the vehicles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicles = Vehicle::with('customer')->latest()->paginate(10);
        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = User::orderBy('name')->get(); // Assuming customers are in users table
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
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:users,id',
            'customer_name' => 'required_without:customer_id|string|max:255',
            'customer_phone' => 'required_without:customer_id|string|max:15',
            'type' => 'required|in:motorcycle,electric_bike',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'license_plate' => 'required|string|max:20|unique:vehicles',
            'color' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare data for creation
        $data = $request->all();
        
        // If customer_id is provided, clear the standalone customer fields
        if ($request->customer_id) {
            $data['customer_name'] = null;
            $data['customer_phone'] = null;
        }

        Vehicle::create($data);

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
        $customers = User::orderBy('name')->get();
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
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:users,id',
            'customer_name' => 'required_without:customer_id|string|max:255',
            'customer_phone' => 'required_without:customer_id|string|max:15',
            'type' => 'required|in:motorcycle,electric_bike',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate,' . $vehicle->id,
            'color' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Prepare data for update
        $data = $request->all();
        
        // If customer_id is provided, clear the standalone customer fields
        if ($request->customer_id) {
            $data['customer_name'] = null;
            $data['customer_phone'] = null;
        }

        $vehicle->update($data);

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
        
        $vehicles = Vehicle::with('customer')
            ->where('license_plate', 'like', "%{$query}%")
            ->orWhere('customer_name', 'like', "%{$query}%")
            ->orWhereHas('customer', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate(10);

        return view('vehicles.index', compact('vehicles', 'query'));
    }
}