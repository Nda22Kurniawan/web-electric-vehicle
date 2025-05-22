<?php

namespace App\Http\Controllers;

use App\Models\ServiceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceScheduleController extends Controller
{
    /**
     * Display a listing of the service schedules.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedules = ServiceSchedule::orderBy('day_of_week')->get();
        return view('service-schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new service schedule.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        
        return view('service-schedules.create', compact('days'));
    }

    /**
     * Store a newly created service schedule in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'day_of_week' => 'required|integer|between:0,6|unique:service_schedules',
            'open_time' => 'required_if:is_closed,0|date_format:H:i',
            'close_time' => 'required_if:is_closed,0|date_format:H:i|after:open_time',
            'is_closed' => 'sometimes|boolean',
            'max_appointments' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ServiceSchedule::create([
            'day_of_week' => $request->day_of_week,
            'open_time' => $request->open_time,
            'close_time' => $request->close_time,
            'is_closed' => $request->has('is_closed'),
            'max_appointments' => $request->max_appointments,
            'notes' => $request->notes,
        ]);

        return redirect()->route('service-schedules.index')
            ->with('success', 'Jadwal layanan berhasil ditambahkan.');
    }

    /**
     * Display the specified service schedule.
     *
     * @param  \App\Models\ServiceSchedule  $serviceSchedule
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceSchedule $serviceSchedule)
    {
        return view('service-schedules.show', compact('serviceSchedule'));
    }

    /**
     * Show the form for editing the specified service schedule.
     *
     * @param  \App\Models\ServiceSchedule  $serviceSchedule
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceSchedule $serviceSchedule)
    {
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        
        return view('service-schedules.edit', compact('serviceSchedule', 'days'));
    }

    /**
     * Update the specified service schedule in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceSchedule  $serviceSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceSchedule $serviceSchedule)
    {
        $validator = Validator::make($request->all(), [
            'day_of_week' => 'required|integer|between:0,6|unique:service_schedules,day_of_week,' . $serviceSchedule->id,
            'open_time' => 'required_if:is_closed,0|date_format:H:i',
            'close_time' => 'required_if:is_closed,0|date_format:H:i|after:open_time',
            'is_closed' => 'sometimes|boolean',
            'max_appointments' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $serviceSchedule->update([
            'day_of_week' => $request->day_of_week,
            'open_time' => $request->open_time,
            'close_time' => $request->close_time,
            'is_closed' => $request->has('is_closed'),
            'max_appointments' => $request->max_appointments,
            'notes' => $request->notes,
        ]);

        return redirect()->route('service-schedules.index')
            ->with('success', 'Jadwal layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified service schedule from storage.
     *
     * @param  \App\Models\ServiceSchedule  $serviceSchedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceSchedule $serviceSchedule)
    {
        $serviceSchedule->delete();

        return redirect()->route('service-schedules.index')
            ->with('success', 'Jadwal layanan berhasil dihapus.');
    }
}