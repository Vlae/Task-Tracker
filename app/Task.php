<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    public function showNormalTime( $total_duration )
    {
        $hours = $total_duration / 3600;
        $minutes = ( $total_duration  % 3600) / 60;
        $seconds = ( $total_duration % 3600 % 60);

        $time = ' '. (int)$hours. ' h. '. (int)$minutes. ' m. '. (int)$seconds. ' sec. ';

        return $time;
    }

    public static function calculateMissedTime ( $time_session_started )
    {
        $time = abs( time() - $time_session_started );
        
        return $time;
    }

}
