<?php
if (Function_Exists('myFunction') === TRUE) {
    $retval = MyFunction(true);
    $keys = Array_Keys($array);
}

function getType() {}

$obj = new Date();

$count = $object->Count();
$count = $object::Count();
$count = $object->count();
$count = $object::count();
class MyClass {
    public function Count() {}
}

function &Sort() {

}

$connection = new Db\Adapter\Pdo\Mysql($config);

namespace Strtolower\Silly;

use function strToUpper as somethingElse;
use function MyClass\WordsToUpper as UCWords; // Intentional redeclared function error.
use function strToUpper\NotTheFunction;

class ArrayUnique {}

$sillyComments = strToLower /*comment*/ ($string);

$callToGlobalFunction = \STR_REPEAT($a, 2);
$callToGlobalFunction = \ /*comment*/ str_Repeat($a, 2);

$callToNamespacedFunction = MyNamespace /* phpcs:ignore Standard */ \STR_REPEAT($a, 2);
$callToNamespacedFunction = namespace\STR_REPEAT($a, 2); // Could potentially be false negative.
