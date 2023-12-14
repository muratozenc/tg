<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{

    // TODO : add try catch

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('availabilities');
        }

        $user = auth()->user();
        $userType = $user->user_type;
        $userId = auth()->id();

        $query = Appointment::whereDate('start', '>=', $request->start)
            ->whereDate('end', '<=', $request->end);

        if ($userType == 'therapist') {
            $query->where('therapist_id', $userId);
        } elseif ($userType == 'client') {
            $query->where('client_id', $userId);
        }

        $availabilities = $query->get();
        $availabilities = $this->convertDatesToUserTimezone($availabilities, $user->current_timezone);

        return response()->json($availabilities);
    }

    public function ajax(Request $request)
    {
        if (in_array($request->type, ['add', 'update'])) {
            $utc_start_time = $this->convertToUtc($request->start, $request->timezone);
            $utc_end_time = $this->convertToUtc($request->end, $request->timezone);
        }

        switch ($request->type) {
            case 'add':
                return $this->addAppointment($request, $utc_start_time, $utc_end_time);

            case 'update':
                return $this->updateAppointment($request, $utc_start_time, $utc_end_time);

            case 'delete':
                return $this->deleteAppointment($request);

            default:
                return response()->json(['error' => 'Invalid request type'], 400);
        }
    }

    private function addAppointment($request, $utc_start_time, $utc_end_time)
    {
        $event = Appointment::create([
            'title' => $request->title,
            'start' => $utc_start_time,
            'end' => $utc_end_time,
            'timezone' => $request->timezone,
            'therapist_id' => auth()->id()
        ]);

        return response()->json($event);
    }

    private function updateAppointment($request, $utc_start_time, $utc_end_time)
    {
        $event = Appointment::find($request->id);
        if ($event) {
            $event->update([
                'title' => $request->title,
                'start' => $utc_start_time,
                'end' => $utc_end_time,
            ]);
            return response()->json($event);
        }
        return response()->json(['error' => 'Appointment not found'], 404);
    }

    private function deleteAppointment($request)
    {
        $event = Appointment::find($request->id);
        if ($event) {
            $event->delete();
            return response()->json(['message' => 'Appointment deleted']);
        }
        return response()->json(['error' => 'Appointment not found'], 404);
    }
}
