<?php

function matchWithDefault($i) {
    return match ($i) {
        1 => 1,
        2 => 2,
        /* testSimpleMatchDefault */
        default => 'default',
    };
}

function switchWithDefault($i) {
    switch ($i) {
        case 1:
            return 1;
        case 2:
            return 2;
        /* testSimpleSwitchDefault */
        default:
            return 'default';
    }
}

function switchWithDefaultAndCurlies($i) {
    switch ($i) {
        case 1:
            return 1;
        case 2:
            return 2;
        /* testSimpleSwitchDefaultWithCurlies */
        default: {
            return 'default';
        }
    }
}

function matchWithDefaultInSwitch() {
    switch ($something) {
        case 'foo':
            $var = [1, 2, 3];
            $var = match ($i) {
                1 => 1,
                /* testMatchDefaultNestedInSwitchCase1 */
                default => 'default',
            };
            continue;

        case 'bar' :
            $i = callMe($a, $b);
            return match ($i) {
                1 => 1,
                /* testMatchDefaultNestedInSwitchCase2 */
                default => 'default',
            };

        /* testSwitchDefault */
        default;
            echo 'something', match ($i) {
                1, => 1,
                /* testMatchDefaultNestedInSwitchDefault */
                default, => 'default',
            };
            break;
    }
}

function switchWithDefaultInMatch() {
    $x = match ($y) {
        5, 8 => function($z) {
            switch($z) {
                case 'a';
                    $var = [1, 2, 3];
                    return 'a';
                /* testSwitchDefaultNestedInMatchCase */
                default:
                    $var = [1, 2, 3];
                    return 'default1';
            }
        },
        /* testMatchDefault */
        default => function($z) {
            switch($z) {
                case 'a':
                    $i = callMe($a, $b);
                    return 'b';
                /* testSwitchDefaultNestedInMatchDefault */
                default:
                    $i = callMe($a, $b);
                    return 'default2';
            }
        }
    };
}
