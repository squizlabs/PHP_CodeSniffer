<?php
switch ($expr) {
    case 0:
        echo 'First case, with a break';
        break;
    case 1:
        echo 'Second case, which falls through';
        // no break
    case 2:
    case 3:
    case 4:
        echo 'Third case, return instead of break';
        return;
    default:
        echo 'Default case';
        break;
}

switch ($expr) {
    case 0:
        echo 'First case,';

    case 1 :
        echo 'Second case';
        // no break
case 2:
    case 3:
        echo 'Third case';
    return;

    default:
        echo 'Default case';
    break;
}
?>