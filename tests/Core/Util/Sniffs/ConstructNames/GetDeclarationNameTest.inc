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
