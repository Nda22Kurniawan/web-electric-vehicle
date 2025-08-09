<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Service::with('categories');

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%');
                });
            }

            // Filter by category
            if ($request->has('category_id') && !empty($request->category_id)) {
                $query->whereHas('categories', function($q) use ($request) {
                    $q->where('service_categories.id', $request->category_id);
                });
            }

            // Filter by price range
            if ($request->has('min_price') && is_numeric($request->min_price)) {
                $query->where('price', '>=', $request->min_price);
            }
            
            if ($request->has('max_price') && is_numeric($request->max_price)) {
                $query->where('price', '<=', $request->max_price);
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['name', 'price', 'duration_estimate', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $perPage = min($perPage, 100); // Limit max per page

            $services = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Services retrieved successfully',
                'data' => $services
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve services',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all services without pagination (for dropdowns)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $services = Service::with('categories:id,name')
                ->select('id', 'name', 'price', 'duration_estimate')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'All services retrieved successfully',
                'data' => $services
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve services',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search services
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'q' => 'required|string|min:2|max:255',
                'limit' => 'nullable|integer|min:1|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $searchTerm = $request->q;
            $limit = $request->get('limit', 10);

            $services = Service::with('categories:id,name')
                ->where(function($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('description', 'like', '%' . $searchTerm . '%');
                })
                ->select('id', 'name', 'description', 'price', 'duration_estimate')
                ->orderBy('name')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => $services,
                'search_term' => $searchTerm
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:services,name',
                'description' => 'nullable|string|max:1000',
                'price' => 'required|numeric|min:0',
                'duration_estimate' => 'nullable|integer|min:1|max:1440', // max 24 hours in minutes
                'categories' => 'nullable|array',
                'categories.*' => 'exists:service_categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $service = Service::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'duration_estimate' => $request->duration_estimate,
            ]);

            if ($request->has('categories') && is_array($request->categories)) {
                $service->categories()->attach($request->categories);
            }

            $service->load('categories');

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $service
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $service = Service::with('categories')->find($id);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Service retrieved successfully',
                'data' => $service
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get service with statistics
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics($id): JsonResponse
    {
        try {
            $service = Service::with('categories')->find($id);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            // Get service statistics (you may need to adjust based on your models)
            $stats = [
                'total_appointments' => $service->appointments()->count() ?? 0,
                'completed_appointments' => $service->appointments()->where('status', 'completed')->count() ?? 0,
                'total_revenue' => $service->appointments()->where('status', 'completed')->sum('total_amount') ?? 0,
                'average_rating' => $service->feedbacks()->avg('rating') ?? 0,
                'total_reviews' => $service->feedbacks()->count() ?? 0,
            ];

            $serviceWithStats = $service->toArray();
            $serviceWithStats['statistics'] = $stats;

            return response()->json([
                'success' => true,
                'message' => 'Service statistics retrieved successfully',
                'data' => $serviceWithStats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:services,name,' . $id,
                'description' => 'nullable|string|max:1000',
                'price' => 'required|numeric|min:0',
                'duration_estimate' => 'nullable|integer|min:1|max:1440',
                'categories' => 'nullable|array',
                'categories.*' => 'exists:service_categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $service->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'duration_estimate' => $request->duration_estimate,
            ]);

            $service->categories()->sync($request->categories ?? []);
            $service->load('categories');

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified service from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found'
                ], 404);
            }

            // Check if service is being used in appointments or work orders
            $appointmentsCount = $service->appointments()->count() ?? 0;
            if ($appointmentsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete service. It is being used in ' . $appointmentsCount . ' appointment(s).'
                ], 422);
            }

            $service->categories()->detach();
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get popular services
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function popular(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            
            $services = Service::with('categories:id,name')
                ->withCount('appointments')
                ->orderBy('appointments_count', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Popular services retrieved successfully',
                'data' => $services
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve popular services',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get services by category
     *
     * @param  int  $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function byCategory($categoryId): JsonResponse
    {
        try {
            $category = ServiceCategory::find($categoryId);
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service category not found'
                ], 404);
            }

            $services = $category->services()
                ->with('categories:id,name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Services by category retrieved successfully',
                'data' => [
                    'category' => $category,
                    'services' => $services
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve services by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}