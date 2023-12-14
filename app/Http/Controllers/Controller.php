<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

// TODO : add try catch
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function convertToUtc($time, $timezone)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $time, $timezone)
            ->setTimezone('UTC');
    }

    public function convertDatesToUserTimezone($availabilities, $timezone)
    {
        foreach ($availabilities as $availability) {
            $availability->start = $this->convertToTimezone($availability->start, $timezone);
            $availability->end = $this->convertToTimezone($availability->end, $timezone);
        }

        return $availabilities;
    }

    public function convertToTimezone($date, $timezone)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date, 'UTC')
            ->setTimezone($timezone)
            ->format('Y-m-d H:i:s');
    }
}
