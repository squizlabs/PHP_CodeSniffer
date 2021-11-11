<?php

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

?>
