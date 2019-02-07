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
            }
        break;
    }
}


function complexityTwentyOne()
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

?>
