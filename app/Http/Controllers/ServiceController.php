<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::with('categories')->latest()->paginate(10);
        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ServiceCategory::all();
        return view('services.create', compact('categories'));
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_estimate' => 'nullable|integer|min:1',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:service_categories,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration_estimate' => $request->duration_estimate,
        ]);

        if ($request->has('categories')) {
            $service->categories()->attach($request->categories);
        }

        return redirect()->route('services.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Display the specified service.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        $service->load('categories');
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        $categories = ServiceCategory::all();
        $selectedCategories = $service->categories->pluck('id')->toArray();
        return view('services.edit', compact('service', 'categories', 'selectedCategories'));
    }

    /**
     * Update the specified service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_estimate' => 'nullable|integer|min:1',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:service_categories,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration_estimate' => $request->duration_estimate,
        ]);

        $service->categories()->sync($request->categories ?? []);

        return redirect()->route('services.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified service from storage.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        $service->categories()->detach();
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Layanan berhasil dihapus.');
    }
}