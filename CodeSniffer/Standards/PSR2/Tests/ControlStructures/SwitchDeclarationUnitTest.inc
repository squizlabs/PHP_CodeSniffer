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

switch ($foo) {
    case 'Foo': {
        echo 'foo';
        break;
        }
}

while ($i < 10) {
    switch ($foo) {
        case '1':
        case '2':
            ++$i;
            continue 2;
        case '3':
            return $i;
    }
}

switch (true) {
    case is_resource($value):
        throw new Exception('foo');
    case is_object($value):
        return 'object';
}

switch (0) {
    case 0:
        switch (1) {
            case 1:
                echo 'a';
                break;
        }
        break;
}
?>
