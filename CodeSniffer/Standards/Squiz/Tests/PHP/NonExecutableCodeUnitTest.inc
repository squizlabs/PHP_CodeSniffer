<?php
switch ($var) {
    case '1':
        return;
        echo 'hi';
    break;
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
    break;
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

exit();

// Errors are thrown from here down from the exit() above.
// No lines before should have duplicate errors.
foreach ($vars as $var) {
    if ($something === TRUE) {
        break;
        break;
    }
}

exit();

?>
