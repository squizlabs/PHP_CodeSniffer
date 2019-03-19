<?php

/* testInvalidTokenPassed */
echo MY_CONSTANT;

/* testClosure */
$closure = function() {};

/* testAnonClass */
$anonClass = new class() {};

/* testFunction */
function functionName() {}

/* testClass */
abstract class ClassName {
    /* testMethod */
    public function methodName() {}

    /* testAbstractMethod */
    abstract protected function abstractMethodName();
}

/* testExtendedClass */
class ExtendedClass extends Foo {}

/* testInterface */
interface InterfaceName {}

/* testTrait */
trait TraitName {}

/* testClassWithCommentsAndNewLines */
class /* comment */

// phpcs:ignore Standard.Cat.SniffName -- for reasons
    ClassWithCommentsAndNewLines {}

/* testClassWithNumber */
class ClassWith1Number extends MyClass {}

/* testInterfaceWithNumbers */
interface InterfaceWith12345Numbers implements MyClass {}

/* testTraitStartingWithNumber */
trait 5InvalidNameStartingWithNumber {}

/* testClassEndingWithNumber */
class ValidNameEndingWithNumber5{}

/* testInterfaceFullyNumeric */
interface 12345 {}

/* testMissingInterfaceName */
interface { // Intentional parse error.
    public function someFunction();
}

/* testLiveCoding */
abstract class // Intentional parse error.
