<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of service categories
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $categories = ServiceCategory::latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Service categories retrieved successfully',
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ]
        ]);
    }

    /**
     * Store a newly created service category
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:service_categories,name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = ServiceCategory::create($request->only(['name', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'Service category created successfully',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified service category
     */
    public function show(ServiceCategory $serviceCategory)
    {
        // Load services if needed for detailed view
        $serviceCategory->load('services');

        return response()->json([
            'success' => true,
            'message' => 'Service category retrieved successfully',
            'data' => $serviceCategory
        ]);
    }

    /**
     * Update the specified service category
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:service_categories,name,' . $serviceCategory->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $serviceCategory->update($request->only(['name', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'Service category updated successfully',
            'data' => $serviceCategory->fresh()
        ]);
    }

    /**
     * Remove the specified service category
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        // Check if category has associated services
        if ($serviceCategory->services()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete service category that has associated services'
            ], 400);
        }

        $serviceCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service category deleted successfully'
        ]);
    }

    /**
     * Search service categories by name
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $perPage = $request->get('per_page', 15);
        
        if (empty($query)) {
            return $this->index($request);
        }

        $categories = ServiceCategory::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })
        ->latest()
        ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Search results retrieved successfully',
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'search_query' => $query
            ]
        ]);
    }

    /**
     * Get all service categories (for dropdown/select options)
     */
    public function all(Request $request)
    {
        $categories = ServiceCategory::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'All service categories retrieved successfully',
            'data' => $categories
        ]);
    }

    /**
     * Get service category with its services
     */
    public function withServices(ServiceCategory $serviceCategory)
    {
        $serviceCategory->load(['services' => function($query) {
            $query->select('id', 'service_category_id', 'name', 'price', 'duration', 'description');
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Service category with services retrieved successfully',
            'data' => $serviceCategory
        ]);
    }

    /**
     * Get statistics for service category
     */
    public function statistics(ServiceCategory $serviceCategory)
    {
        $stats = [
            'total_services' => $serviceCategory->services()->count(),
            'active_services' => $serviceCategory->services()->where('is_active', true)->count(),
            'average_price' => $serviceCategory->services()->avg('price'),
            'total_appointments' => $serviceCategory->services()
                ->withCount('appointments')
                ->get()
                ->sum('appointments_count'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Service category statistics retrieved successfully',
            'data' => [
                'category' => $serviceCategory,
                'statistics' => $stats
            ]
        ]);
    }
}