<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class TimeZoneController extends Controller
{
    public function updateUserTimeZone(Request $request){
        $timezone = $request->get('timezone');

        $user = auth()->user();

        $user->current_timezone = $timezone;
        $user->save();

        return response()->json([
            'message' => 'Timezone updated successfully',
            'timezone' => $timezone,
        ]);
    }
}

