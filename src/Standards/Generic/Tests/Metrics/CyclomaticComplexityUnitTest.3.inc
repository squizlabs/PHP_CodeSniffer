<?php

function complexityFiveWithMatch()
{
    return match(strtolower(substr($monthName, 0, 3))){
        'apr', 'jun', 'sep',  'nov'  => 30,
        'jan', 'mar', 'may', 'jul', 'aug',  'oct', 'dec'  => 31,
        'feb' => is_leap($year) ? 29 : 28,
        default => throw new InvalidArgumentException("Bogus month"),
    }
}

function complexityFourteenWithMatch()
{
    return match(strtolower(substr($monthName, 0, 3))) {
        'jan' => 31,
        'feb' => is_leap($year) ? 29 : 28,
        'mar' => 31,
        'apr' => 30,
        'may' => 31,
        'jun' => 30,
        'jul' => 31,
        'aug' => 31,
        'sep' => 30,
        'oct' => 31,
        'nov' => 30,
        'dec' => 31,
        default => throw new InvalidArgumentException("Bogus month"),
    };
}

?>
