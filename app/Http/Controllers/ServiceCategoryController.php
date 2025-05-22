<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the service categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = ServiceCategory::latest()->paginate(10);
        return view('service-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new service category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('service-categories.create');
    }

    /**
     * Store a newly created service category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ServiceCategory::create($request->all());

        return redirect()->route('service-categories.index')
            ->with('success', 'Kategori layanan berhasil ditambahkan.');
    }

    /**
     * Display the specified service category.
     *
     * @param  \App\Models\ServiceCategory  $serviceCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceCategory $serviceCategory)
    {
        return view('service-categories.show', compact('serviceCategory'));
    }

    /**
     * Show the form for editing the specified service category.
     *
     * @param  \App\Models\ServiceCategory  $serviceCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceCategory $serviceCategory)
    {
        return view('service-categories.edit', compact('serviceCategory'));
    }

    /**
     * Update the specified service category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceCategory  $serviceCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $serviceCategory->update($request->all());

        return redirect()->route('service-categories.index')
            ->with('success', 'Kategori layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified service category from storage.
     *
     * @param  \App\Models\ServiceCategory  $serviceCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();

        return redirect()->route('service-categories.index')
            ->with('success', 'Kategori layanan berhasil dihapus.');
    }
}