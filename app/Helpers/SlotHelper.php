<?php

namespace App\Helpers;

use DateInterval;
use DateTime;

class SlotHelper
{

    public static function createSlots($start_time, $end_time, $slot_duration, $prep_minute_break)
    {
        $start_time = new DateTime($start_time);
        $end_time = new DateTime($end_time);
        $slot_duration = new DateInterval('PT' . $slot_duration . 'M');
        $prep_minute_break = new DateInterval('PT' . $prep_minute_break . 'M');
        $slots = [];
        $current_time = clone $start_time;

        while ($current_time < $end_time) {
            $slot_start_time = $current_time->format('H:i');
            $current_time->add($slot_duration);
            $slot_end_time = $current_time->format('H:i');
            $slots[] = compact('slot_start_time', 'slot_end_time');
            $current_time->add($prep_minute_break);
        }

        return $slots;
    }

    public static function getDatesDiff($date_one, $date_two)
    {
        $earlier = new DateTime($date_one);
        $later = new DateTime($date_two);

        return $later->diff($earlier)->format("%a");
    }

}
