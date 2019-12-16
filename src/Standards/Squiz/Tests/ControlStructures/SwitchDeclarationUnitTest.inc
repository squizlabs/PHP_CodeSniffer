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
        return '1';

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
    Case '1' :
        $case = '1';

case  '2':
    case  '3' :
        $case = '5';

    case'4':
        $case = '4';

    Default :
        $case = null;
        $something = 'hello';
        $other = 'hi';
    }

// Valid
switch ($condition) {
    case 'string':
        $varStr = 'test';

    default:
        // Ignore the default.
    break;
}

// No default comment
switch ($condition) {
    case 'string':
        $varStr = 'test';

    default:
    break;
}

// Break problems
switch ($condition) {
    case 'string':


        $varStr = 'test';

    break;


    case 'bool':
        $varStr = 'test';


    break;
    default:

        $varStr = 'test';
    break;

}

switch ($var) {
    case 'one':
    case 'two':
    break;

    case 'three':
        // Nothing to do.
    break;

    case 'four':
        echo $hi;
    break;

    default:
        // No default.
    break;
}

switch ($var) {
    case 'one':
        if ($blah) {
        }

    break;

    default:
        // No default.
    break;
}

switch ($name) {
    case "1":
        switch ($name2) {
            case "1":
                return true;
            break;

            case "2":
            return true;
            break;

            default:
                // No default.
            break;
        }
    break;

    case "2":
switch ($name2) {
    case "1":
        return true;
    break;

    case "2":
    return true;
    break;

    default:
        // No default.
    break;
}
    break;
}

switch ($name) {
    case "1":
        switch ($name2) {
            case "1":
            return true;

            default:
                // No default.
            break;
        }
    break;

    default:
        // No default.
    break;
}

switch ($name2) {
    default:
        // No default.
    break;
}

switch ($foo) {
    case "1":
    return true;

    default:
        if ($foo === FALSE) {
            break(2);
        }
    break;
}

// Valid SWITCH statement.
switch ($something) {
    case '1';
        $case = '1';
    return '1';

    case '2';
    case '3';
        $case = '5';
    return '2';

    case '4';
        $case = '4';
    return '3';

    default;
        $case = null;
    return '4';
}

switch ($something) {
    case '1':
        $case = '1';
    break;

    case '2':
        throw new Exception('message');

    default:
    throw new Exception('message');
}

switch ($something) {
    case '1';
        echo 'one';
    break;

    default:
        echo 'default';
    exit;
}

switch ($foo) {
    case '1':
        return; // comment
    break;

}

// Correct Multi line breaking statement with return.
switch ($foo) {
    case 1:
    return array(
            'whiz',
            'bang',
           );

    case 2:
    return helper_func(
        'whiz',
        'bang'
    );

    default:
    throw new Exception();
}

switch ($foo) {
    case 'bar':
    throw new \Exception(
        'bar'
    );

    default:
    throw new \Exception(
        'bar'
    );
}

$foo = $foo ?
    function () {
        switch ($a) {
            case 'a':
                break;

            case (preg_match('/foo/i', $foo) ? $a : $b):
                echo 'really?'
                break;

            default:
                break;
        }
    } :
    null;
