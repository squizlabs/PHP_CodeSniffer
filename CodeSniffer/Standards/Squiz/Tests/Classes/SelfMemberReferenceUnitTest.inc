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

class Controller
{
    public function Action()
    {
        Doctrine\Common\Util\Debug::dump();
    }
}

namespace TYPO3\CMS\Reports;

class Status {
    const NOTICE = -2;
    const INFO = -1;
    const OK = 0;
    const WARNING = 1;
    const ERROR = 2;
}

namespace TYPO3\CMS\Reports\Report\Status;

class Status implements \TYPO3\CMS\Reports\ReportInterface {
    public function getHighestSeverity(array $statusCollection) {
        $highestSeverity = \TYPO3\CMS\Reports\Status::NOTICE;
    }
}

namespace Foo;

class Bar {

    function myFunction()
    {
        \Foo\Whatever::something();
        \Foo\Bar::something();
    }
}

namespace Foo\Bar;

class Baz {

    function myFunction()
    {
        \Foo\Bar\Whatever::something();
        \Foo\Bar\Baz::something();
    }
}
