<?php
try {
    // Try something.
    $variable = 'string';
} catch (Exception $e) {
    // Comment.
    echo 'something broke';
}

try {
    // Try something.
    $variable = 'string';
} catch (Exception $e) {
}

try {
    // Try something.
    $variable = 'string';
} catch (Exception $e) {
    // Dont want to do anything.
}

try {
    $variable = 'string';
} catch (MyException $e) {
    echo 'something broke';
} catch (Exception $e) {
    echo 'something broke';
}

try {
    $variable = 'string';
} catch (MyException $e) {

} catch (Exception $e) {
    echo 'something broke';
}

try {
    $variable = 'string';
} catch (MyException $e) {
    // Dont do anything.
} catch (Exception $e) {
    // Do nothing.
}

try {
    $variable = 'string';
} catch (MyException $e) {
} catch (YourException $e) {
} catch (OurException $e) {
} catch (Exception $e) {
}

?>