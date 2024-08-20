<?php

/**
 * 
 */
function nextDate(string $additional_time, string|null $datetime=null)
{
    $datetime = is_null($datetime) ? $additional_time : $datetime . " " . $additional_time;
    return date("Y-m-d H:i:s", strtotime($datetime));
}

function time_elapsed_string($datetime, $full = false) {
    $now = now(); //new \Datetime(gmdate("Y-m-d\TH:i:s\Z"));
    $ago = new \Datetime($datetime);
    $diff = $now->diff($ago);
    
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) : '0 seconds';
}