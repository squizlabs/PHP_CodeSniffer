<?php
include 'Somefile';

const CONSTANT1 = 1;
const CONSTANT2 = 2;

if ($something) {
    echo 'hi';
}

$var = myFunction();
print_r($var);
echo $object->define();
echo $object -> define();

$c = new class extends Something{

    public function someMethod()
    {
        // ...
    }

};
