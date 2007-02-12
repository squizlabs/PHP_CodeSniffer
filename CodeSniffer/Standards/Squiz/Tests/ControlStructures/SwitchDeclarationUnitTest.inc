<?php

// Valid SWITCH statement.
switch ($something) {
    case '1':
        $case = '1';
    break;
    case '2':
    case '3':
        $case = '5';
    break;
    case '4':
        $case = '4';
    break;
    default:
        $case = null;
    break;
}

// Alignment wrong.
switch ($something) {
    case '1':
        $case = '1';
        break;
case '2':
    case '3':
        $case = '5';
    break;
case '4':
    $case = '4';
break;
    default:
        $case = null;
    break;
}

// Closing brace wrong.
switch ($something) {
    case '1':
        $case = '1';
    break;
    }

// PEAR style.
switch ($something) {
case '1':
    $case = '1';
    break;
case '2':
case '3':
    $case = '5';
    break;
case '4':
    $case = '4';
    break;
default:
    $case = null;
    break;
}

// Code indent wrong.
switch ($something) {
    case '1':
    $case = '1';

    case '4':
        $case = '4';
        break;
    default:
    $case = null;
    $something = 'hello';
        $other = 'hi';
    break;
}

// Valid, but missing BREAKS.
switch ($something) {
    case '1':
        $case = '1';
    case '2':
    case '3':
        $case = '5';
    case '4':
        $case = '4';
    default:
        $case = null;
}

// Invalid, and missing BREAKS.
switch ($something) {
    case '1':
        $case = '1';
case '2':
    case '3':
    $case = '5';
    case '4':
        $case = '4';
    default:
    $case = null;
    $something = 'hello';
        $other = 'hi';
    }

// Valid
switch ($condition) {
    case 'string':
        $varStr = 'test';
}

?>