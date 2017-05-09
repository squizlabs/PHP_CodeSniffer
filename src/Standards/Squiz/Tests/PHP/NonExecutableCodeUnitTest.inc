<?php
switch ($var) {
    case '1':
        return;
        echo 'hi';

    case '2':
    case '3':
        if ($something === true) {
            break;
            echo 'hi';
        }
    break;
    default:
        return;

        if ($something === true) {
            break;
            echo 'hi';
        }

}

function myFunction($var)
{
    if ($something === TRUE) {
        return;
        echo 'hi';
    }

    return;
    return FALSE;
    if ($something === TRUE) {
        return TRUE;
    }

}//end myFunction()

foreach ($vars as $var) {
    if ($something === TRUE) {
        continue;
        break;
    } else {
        continue;
        echo 'hi';
    }

    echo $var."\n";
}

switch ($lowerVarType) {
    case 'bool':
        return 'boolean';
        echo 'hi';
    case 'double':
    case 'real':
        return 'float';
        echo 'hi';
}

while ($line=fgets($fp,2*1024*1024))
{
    if (!preg_match("/^<([a-z0-9_]+)/",$line,$matches))
    continue;
    print $line;
}

switch ($var) {
    case 1:
        echo '1';
    break;

        echo 'non-executable';
    default:
        echo '2';
    break;
}

switch (0) {
    case 1:
        return '1';

        echo 'non-executable';
    default:
    break;
}

function myFunction()
{
    if ($something === TRUE) {
        return;
    }

    echo 'foo';
    return;

}//end myFunction()

function myFunction()
{
    return uksort(
        $array,
        function() {
            return mt_rand(-1, 1);
            echo 'done';
        }
    );

}//end myFunction()

public static function thisCausesAnError() {
return new foo(function() {return $foo;}
);
}

function myFunction()
{
    if ($something === TRUE) {
        throw new Exception('exception');
    }

    throw new Exception('exception');
    echo 'non-executable';
}//end myFunction()

switch ($var) {
    case 1: {
        return '1';
    }

    case 2: {
        return '2';
    }
}

defined('FOO') or die('error');
defined('FOO') || die('error');
interface myInterface {
    function myFunc();
}
echo 'hello';

function foo($color) {
    switch ($color) {
        case 'red':
            return 'yuck';
            break;
        case 'blue':
            return 'yuck';
            break;
        case 'orange':
            return 'yay';
            break;
        default:
            return 'boring';
    }
}

function returnOverMultipleLines($color) {
    switch ($color) {
        case 'red':
            return someFunction(
                'multiple',
                'arguments'
            );
            echo $foo;
        default:
            return array(
                'multiline',
                'array'
            );
    }
}

function test() {
    return array(
                'multiline',
                'array'
            );
    echo $foo;
}

function test(){
    switch($a) {
        case 1:
            if (empty($b))
                return 0;
            break;
        default:
            return 2;
    }

    if (empty($a))
        echo '1';
    elseif ($empty($b))
        return 0;
    else
        return 1;

    echo "oi";
    return 1;
}

switch ($foo) {
    case 'foo':
        if ($foo)
            return $foo;
        return $bar;
    default:
        return $bar;
}

function foo()
{
    return $bar->{$action . 'JsonAction'}();
}

switch (true) {
    case 1:
        return foo(
            function () {
                $foo = $bar;  // when this is removed it works ok
                return false; // from here on it reports unreachable
            }
        );
}

exit();


// Errors are thrown from here down from the exit() above.
foreach ($vars as $var) {
    if ($something === TRUE) {
        break;
        break;
    }
}

exit();

function test() {
    echo 'no error';
}

class myClass {
    function myFunc() {
        echo 'no error';
    }
}

function bar() {
    return function() {
        echo "1";
    };
}

class HttpStatus
{
    const CONTINUE = 100;
    const SWITCHING_PROTOCOLS = 101;
}
