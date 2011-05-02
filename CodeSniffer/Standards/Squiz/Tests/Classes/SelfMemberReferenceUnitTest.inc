<?php


class SelfMemberReferenceUnitTestExample
{


    private $testCount = 0;


    private $testResults = array();


    public function SelfMemberReferenceUnitTestExample()
    {
        $testResults =& $this->testResults;


        // Correct call to self.
        $testResults[] = self::selfMemberReferenceUnitTestFunction();
        $testResults[] = parent::selfMemberReferenceUnitTestFunction();

        // Incorrect case.
        $testResults[] = Self::selfMemberReferenceUnitTestFunction();
        $testResults[] = SELF::selfMemberReferenceUnitTestFunction();
        $testResults[] = SelfMemberReferenceUnitTestExample::selfMemberReferenceUnitTestFunction();


        // Incorrect spacing.
        $testResults[] = self ::selfMemberReferenceUnitTestFunction();
        $testResults[] = self::  selfMemberReferenceUnitTestFunction();
        $testResults[] = self  :: selfMemberReferenceUnitTestFunction();

    }


    function selfMemberReferenceUnitTestFunction()
    {
        $this->testCount = $this->testCount + 1;
        return $this->testCount;

    }


}


class MyClass {

    public static function test($value) {
        echo "$value\n";
    }

    public static function walk() {
        $callback = function($value, $key) {
                        // This is valid because you cant use self:: in a closure
                        MyClass::test($value);
                    };

        $array = array(1,2,3);
        array_walk($array, $callback);
    }
}

MyClass::walk();

?>