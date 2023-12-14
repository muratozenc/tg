<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Availability;
use App\Models\Appointment;

// TODO : add try catch

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($request->ajax()) {
            $query = Availability::query()
                ->whereDate('start', '>=', $request->start)
                ->whereDate('end', '<=', $request->end)
                ->where('is_booked', '=', '0');

            if ($user->user_type == 'therapist') {
                $query->where('therapist_id', '=', auth()->id());
            }

            $availabilities = $query->get();
            $availabilities = $this->convertDatesToUserTimezone($availabilities, $user->current_timezone);

            return response()->json($availabilities);
        }

        return view('availabilities');
    }


    public function ajax(Request $request)
    {
        $user = auth()->user();

        if (in_array($request->type, ['add', 'update'])) {
            $utc_start_time = $this->convertToUtc($request->start, $request->timezone);
            $utc_end_time = $this->convertToUtc($request->end, $request->timezone);
        }

        switch ($user->user_type) {
            case 'therapist':
                return $this->handleTherapistRequest($request, $utc_start_time ?? null, $utc_end_time ?? null);
            case 'client':
                return $this->handleClientRequest($request);
            default:
                break;
        }
    }


    private function handleTherapistRequest($request, $utc_start_time, $utc_end_time)
    {
        switch ($request->type) {
            case 'add':
                $event = Availability::create([
                    'title' => $request->title,
                    'start' => $utc_start_time,
                    'end' => $utc_end_time,
                    'timezone' => $request->timezone,
                    'therapist_id' => auth()->id()
                ]);
                return response()->json($event);

            case 'update':
                $event = Availability::find($request->id)->update([
                    'title' => $request->title,
                    'start' => $utc_start_time,
                    'end' => $utc_end_time,
                ]);
                return response()->json($event);

            case 'delete':
                $event = Availability::find($request->id)->delete();
                return response()->json($event);

            default:
                break;
        }
    }

    private function handleClientRequest($request)
    {

        //print_r($request->all());
        $availability = Availability::find($request->id);

        if ($availability) {
            $event = Appointment::create([
                'start' => $availability->start,
                'end' => $availability->end,
                'client_id' => auth()->id(),
                'therapist_id' => $availability->therapist_id,
                'therapist_availability_id' => $availability->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $availability->update(['is_booked' => 1]);

            return redirect('dashboard');
        }

        // TODO : return error message if availability fails.
    }

}
