<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ServiceScheduleController extends Controller
{
    /**
     * Display a listing of the service schedules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ServiceSchedule::query();

            // Filter by specific day
            if ($request->has('day') && is_numeric($request->day)) {
                $query->where('day_of_week', $request->day);
            }

            // Filter by availability (open/closed)
            if ($request->has('is_available')) {
                $isAvailable = filter_var($request->is_available, FILTER_VALIDATE_BOOLEAN);
                $query->where('is_closed', !$isAvailable);
            }

            // Sort by day of week
            $sortBy = $request->get('sort_by', 'day_of_week');
            $sortOrder = $request->get('sort_order', 'asc');
            
            if (in_array($sortBy, ['day_of_week', 'open_time', 'close_time', 'max_appointments'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            $schedules = $query->get();

            // Add day name for each schedule
            $schedules = $schedules->map(function ($schedule) {
                $days = [
                    0 => 'Minggu',
                    1 => 'Senin', 
                    2 => 'Selasa',
                    3 => 'Rabu',
                    4 => 'Kamis',
                    5 => 'Jumat',
                    6 => 'Sabtu'
                ];
                
                $schedule->day_name = $days[$schedule->day_of_week] ?? 'Unknown';
                $schedule->day_name_en = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$schedule->day_of_week] ?? 'Unknown';
                
                return $schedule;
            });

            return response()->json([
                'success' => true,
                'message' => 'Service schedules retrieved successfully',
                'data' => $schedules
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service schedules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all service schedules formatted for calendar/dropdown
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(): JsonResponse
    {
        try {
            $schedules = ServiceSchedule::orderBy('day_of_week')->get();

            $formattedSchedules = $schedules->map(function ($schedule) {
                $days = [
                    0 => 'Minggu',
                    1 => 'Senin',
                    2 => 'Selasa', 
                    3 => 'Rabu',
                    4 => 'Kamis',
                    5 => 'Jumat',
                    6 => 'Sabtu'
                ];

                return [
                    'id' => $schedule->id,
                    'day_of_week' => $schedule->day_of_week,
                    'day_name' => $days[$schedule->day_of_week],
                    'day_name_en' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$schedule->day_of_week],
                    'open_time' => $schedule->open_time,
                    'close_time' => $schedule->close_time,
                    'is_closed' => $schedule->is_closed,
                    'max_appointments' => $schedule->max_appointments,
                    'is_available' => !$schedule->is_closed
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'All service schedules retrieved successfully',
                'data' => $formattedSchedules
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service schedules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's schedule
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function today(): JsonResponse
    {
        try {
            $today = Carbon::now()->dayOfWeek; // 0 = Sunday, 6 = Saturday
            $schedule = ServiceSchedule::where('day_of_week', $today)->first();

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'No schedule found for today',
                    'data' => [
                        'day_of_week' => $today,
                        'day_name' => ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$today],
                        'is_open' => false,
                        'message' => 'Schedule not configured for today'
                    ]
                ], 404);
            }

            $days = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu', 
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu'
            ];

            $currentTime = Carbon::now()->format('H:i');
            $isCurrentlyOpen = false;
            
            if (!$schedule->is_closed && $schedule->open_time && $schedule->close_time) {
                $isCurrentlyOpen = $currentTime >= $schedule->open_time && $currentTime <= $schedule->close_time;
            }

            $scheduleData = [
                'id' => $schedule->id,
                'day_of_week' => $schedule->day_of_week,
                'day_name' => $days[$schedule->day_of_week],
                'open_time' => $schedule->open_time,
                'close_time' => $schedule->close_time,
                'is_closed' => $schedule->is_closed,
                'max_appointments' => $schedule->max_appointments,
                'notes' => $schedule->notes,
                'is_open' => !$schedule->is_closed,
                'is_currently_open' => $isCurrentlyOpen,
                'current_time' => $currentTime
            ];

            return response()->json([
                'success' => true,
                'message' => 'Today\'s schedule retrieved successfully',
                'data' => $scheduleData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve today\'s schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available time slots for a specific day
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function availableSlots(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'day' => 'required|integer|between:0,6',
                'date' => 'nullable|date|after_or_equal:today',
                'duration' => 'nullable|integer|min:15|max:480' // 15 minutes to 8 hours
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $dayOfWeek = $request->day;
            $duration = $request->get('duration', 60); // default 1 hour
            
            $schedule = ServiceSchedule::where('day_of_week', $dayOfWeek)->first();

            if (!$schedule || $schedule->is_closed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service is closed on this day',
                    'data' => [
                        'available_slots' => [],
                        'is_closed' => true
                    ]
                ]);
            }

            // Generate time slots
            $slots = [];
            $openTime = Carbon::createFromFormat('H:i', $schedule->open_time);
            $closeTime = Carbon::createFromFormat('H:i', $schedule->close_time);
            $slotInterval = 30; // 30 minutes interval

            while ($openTime->addMinutes($duration)->lte($closeTime)) {
                $slots[] = [
                    'start_time' => $openTime->copy()->subMinutes($duration)->format('H:i'),
                    'end_time' => $openTime->format('H:i'),
                    'available' => true // You can add logic to check existing appointments
                ];
                
                $openTime->addMinutes($slotInterval - $duration);
            }

            return response()->json([
                'success' => true,
                'message' => 'Available time slots retrieved successfully',
                'data' => [
                    'day_of_week' => $dayOfWeek,
                    'schedule' => $schedule,
                    'available_slots' => $slots,
                    'duration_minutes' => $duration
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available slots',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created service schedule in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'day_of_week' => 'required|integer|between:0,6|unique:service_schedules',
                'open_time' => 'required_if:is_closed,false|nullable|date_format:H:i',
                'close_time' => 'required_if:is_closed,false|nullable|date_format:H:i|after:open_time',
                'is_closed' => 'sometimes|boolean',
                'max_appointments' => 'required|integer|min:0',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $schedule = ServiceSchedule::create([
                'day_of_week' => $request->day_of_week,
                'open_time' => $request->is_closed ? null : $request->open_time,
                'close_time' => $request->is_closed ? null : $request->close_time,
                'is_closed' => $request->boolean('is_closed'),
                'max_appointments' => $request->max_appointments,
                'notes' => $request->notes,
            ]);

            // Add day name to response
            $days = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu'
            ];
            
            $schedule->day_name = $days[$schedule->day_of_week];

            return response()->json([
                'success' => true,
                'message' => 'Service schedule created successfully',
                'data' => $schedule
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified service schedule.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $schedule = ServiceSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service schedule not found'
                ], 404);
            }

            $days = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu'
            ];

            $schedule->day_name = $days[$schedule->day_of_week];
            $schedule->day_name_en = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$schedule->day_of_week];

            return response()->json([
                'success' => true,
                'message' => 'Service schedule retrieved successfully',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve service schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified service schedule in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $schedule = ServiceSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service schedule not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'day_of_week' => 'required|integer|between:0,6|unique:service_schedules,day_of_week,' . $id,
                'open_time' => 'required_if:is_closed,false|nullable|date_format:H:i',
                'close_time' => 'required_if:is_closed,false|nullable|date_format:H:i|after:open_time',
                'is_closed' => 'sometimes|boolean',
                'max_appointments' => 'required|integer|min:0',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $schedule->update([
                'day_of_week' => $request->day_of_week,
                'open_time' => $request->boolean('is_closed') ? null : $request->open_time,
                'close_time' => $request->boolean('is_closed') ? null : $request->close_time,
                'is_closed' => $request->boolean('is_closed'),
                'max_appointments' => $request->max_appointments,
                'notes' => $request->notes,
            ]);

            // Add day name to response
            $days = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu'
            ];
            
            $schedule->day_name = $days[$schedule->day_of_week];

            return response()->json([
                'success' => true,
                'message' => 'Service schedule updated successfully',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified service schedule from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $schedule = ServiceSchedule::find($id);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service schedule not found'
                ], 404);
            }

            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service schedule deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get service schedule by day of week
     *
     * @param  int  $dayOfWeek
     * @return \Illuminate\Http\JsonResponse
     */
    public function byDay($dayOfWeek): JsonResponse
    {
        try {
            $validator = Validator::make(['day' => $dayOfWeek], [
                'day' => 'required|integer|between:0,6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid day of week',
                    'errors' => $validator->errors()
                ], 422);
            }

            $schedule = ServiceSchedule::where('day_of_week', $dayOfWeek)->first();

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found for this day'
                ], 404);
            }

            $days = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu'
            ];

            $schedule->day_name = $days[$schedule->day_of_week];

            return response()->json([
                'success' => true,
                'message' => 'Schedule retrieved successfully',
                'data' => $schedule
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get operational hours summary
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function operationalHours(): JsonResponse
    {
        try {
            $schedules = ServiceSchedule::orderBy('day_of_week')->get();
            
            $operationalHours = [];
            $days = [
                0 => 'Minggu',
                1 => 'Senin', 
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu'
            ];

            foreach ($days as $dayNumber => $dayName) {
                $schedule = $schedules->firstWhere('day_of_week', $dayNumber);
                
                if ($schedule) {
                    $operationalHours[] = [
                        'day_of_week' => $dayNumber,
                        'day_name' => $dayName,
                        'is_closed' => $schedule->is_closed,
                        'open_time' => $schedule->open_time,
                        'close_time' => $schedule->close_time,
                        'max_appointments' => $schedule->max_appointments,
                        'status' => $schedule->is_closed ? 'Tutup' : $schedule->open_time . ' - ' . $schedule->close_time
                    ];
                } else {
                    $operationalHours[] = [
                        'day_of_week' => $dayNumber,
                        'day_name' => $dayName,
                        'is_closed' => true,
                        'open_time' => null,
                        'close_time' => null,
                        'max_appointments' => 0,
                        'status' => 'Belum diatur'
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Operational hours retrieved successfully',
                'data' => $operationalHours
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve operational hours',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}