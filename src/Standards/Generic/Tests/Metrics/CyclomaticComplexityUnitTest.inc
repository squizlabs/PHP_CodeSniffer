<?php

function complexityOne() { }

function complexityFive()
{
    if ($condition) {
    }

    switch ($condition) {
        case '1':
        break;
        case '2':
        break;
        case '3':
        break;
    }
}

function complexityTen()
{
    while ($condition === true) {
        if ($condition) {
        }
    }

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
        break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
        break;
        case '3':
        break;
        default:
        break;
    }
}

function complexityEleven()
{
    while ($condition === true) {
        if ($condition) {
        } else if ($cond) {
        }
    }

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
        break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
        break;
        case '3':
        break;
        default:
        break;
    }
}


function complexityTwenty()
{
    while ($condition === true) {
        if ($condition) {
        } else if ($cond) {
        }
    }

    switch ($condition) {
        case '1':
            do {
                if ($condition) {
                } else if ($cond) {
                }
            } while ($cond);
        break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
        break;
        case '3':
            switch ($cond) {
                case '1':
                break;
                case '2':
                break;
            }
        break;
        case '4':
            do {
                if ($condition) {
                    if ($cond) {
                    } else if ($con) {
                    }
                }
            } while ($cond);
        break;
        default:
            if ($condition) {
            }
        break;
    }
}


function complexityTwentyOne()
{
    while ($condition === true) {
        do {
            if ($condition) {
            } else if ($cond) {
            }
        } while ($cond);
    }

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
        break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
        break;
        case '3':
            switch ($cond) {
                case '1':
                break;
                case '2':
                break;
            }
        break;
        case '4':
            do {
                if ($condition) {
                    if ($cond) {
                    } else if ($con) {
                    }
                }
            } while ($cond);
        break;
        default:
            if ($condition) {
            } else if ($cond) {
            }
        break;
    }
}


function complexityTenWithTernaries()
{
    $value1 = (empty($condition1)) ? $value1A : $value1B;
    $value2 = (empty($condition2)) ? $value2A : $value2B;

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
            break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
            break;
        case '3':
            break;
        default:
            break;
    }
}


function complexityElevenWithTernaries()
{
    $value1 = (empty($condition1)) ? $value1A : $value1B;
    $value2 = (empty($condition2)) ? $value2A : $value2B;
    $value3 = (empty($condition3)) ? $value3A : $value3B;

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
            break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
            break;
        case '3':
            break;
        default:
            break;
    }
}


function complexityTenWithNestedTernaries()
{
    $value1 = true ? $value1A : false ? $value1B : $value1C;

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
            break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
            break;
        case '3':
            break;
        default:
            break;
    }
}


function complexityElevenWithNestedTernaries()
{
    $value1 = (empty($condition1)) ? $value1A : $value1B;
    $value2 = true ? $value2A : false ? $value2B : $value2C;

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
            break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
            break;
        case '3':
            break;
        default:
            break;
    }
}


function complexityTenWithNullCoalescence()
{
    $value1 = $value1A ?? $value1B;
    $value2 = $value2A ?? $value2B;

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
            break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
            break;
        case '3':
            break;
        default:
            break;
    }
}


function complexityElevenWithNullCoalescence()
{
    $value1 = $value1A ?? $value1B;
    $value2 = $value2A ?? $value2B;
    $value3 = $value3A ?? $value3B;

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
            break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
            break;
        case '3':
            break;
        default:
            break;
    }
}


function complexityTenWithNestedNullCoalescence()
{
    $value1 = $value1A ?? $value1B ?? $value1C;

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
            break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
            break;
        case '3':
            break;
        default:
            break;
    }
}


function complexityElevenWithNestedNullCoalescence()
{
    $value1 = $value1A ?? $value1B;
    $value2 = $value2A ?? $value2B ?? $value2C;

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
            break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
            break;
        case '3':
            break;
        default:
            break;
    }
}


function complexityTenWithNullCoalescenceAssignment()
{
    $value1 ??= $default1;
    $value2 ??= $default2;

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
            break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
            break;
        case '3':
            break;
        default:
            break;
    }
}


function complexityElevenWithNullCoalescenceAssignment()
{
    $value1 ??= $default1;
    $value2 ??= $default2;
    $value3 ??= $default3;

    switch ($condition) {
        case '1':
            if ($condition) {
            } else if ($cond) {
            }
            break;
        case '2':
            while ($cond) {
                echo 'hi';
            }
            break;
        case '3':
            break;
        default:
            break;
    }
}


function complexityFiveWithMatch()
{
    return match(strtolower(substr($monthName, 0, 3))){
        'apr', 'jun', 'sep',  'nov'  => 30,
        'jan', 'mar', 'may', 'jul', 'aug',  'oct', 'dec'  => 31,
        'feb' => is_leap_year($year) ? 29 : 28,
        default => throw new InvalidArgumentException("Invalid month"),
    }
}


function complexityFourteenWithMatch()
{
    return match(strtolower(substr($monthName, 0, 3))) {
        'jan' => 31,
        'feb' => is_leap_year($year) ? 29 : 28,
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
        default => throw new InvalidArgumentException("Invalid month"),
    };
}

?>
