<?php
namespace Foo;

use Foo\Bar\ForbiddenClass, Foo\Bar\ForbiddenClass as ClassAlias;
use Foo as FooAlias;

class Test implements
    \Foo\Bar\ForbiddenClass, Bar\ForbiddenClass, ForbiddenClass, FooAlias\Bar\ForbiddenClass, ClassAlias, // Error
    Foo\Bar\ForbiddenClass,  \Bar\ForbiddenClass, \ClassAlias // Ok
{
    use \Foo\Bar\ForbiddenClass, Bar\ForbiddenClass, ForbiddenClass, FooAlias\Bar\ForbiddenClass, ClassAlias; // Error
    use Foo\Bar\ForbiddenClass, \Bar\ForbiddenClass, \ClassAlias; // Ok

    public static function test()
    {
        // Error
        new \Foo\Bar\ForbiddenClass();
        new Bar\ForbiddenClass();
        new ForbiddenClass();
        new FooAlias\Bar\ForbiddenClass();
        new ClassAlias();

        \Foo\Bar\ForbiddenClass::method();
        Bar\ForbiddenClass::method();
        ForbiddenClass::method();
        FooAlias\Bar\ForbiddenClass::method();
        ClassAlias::method();

        function (Foo &$foo, \Foo\Bar\ForbiddenClass $crawler, $bar) {};
        function (Foo &$foo, Bar\ForbiddenClass $crawler, $bar) {};
        function (Foo &$foo, ForbiddenClass $crawler, $bar) {};
        function (Foo &$foo, FooAlias\Bar\ForbiddenClass $foo, $bar) {};
        function (Foo &$foo, ClassAlias $foo, $bar) {};

        // Ok
        new Foo\Bar\ForbiddenClass();
        new \Bar\ForbiddenClass();
        new \ClassAlias();

        Foo\Bar\ForbiddenClass::method();
        \Bar\ForbiddenClass::method();
        \ClassAlias::method();

        function (Foo &$foo, Foo\Bar\ForbiddenClass $foo, $bar) {};
        function (Foo &$foo, \Bar\ForbiddenClass $foo, $bar) {};
        function (Foo &$foo, \ClassAlias $foo, $bar) {};
    }

    // Error
    public function error1(Foo &$foo, \Foo\Bar\ForbiddenClass $crawler, $bar) {}
    public function error2(Foo &$foo, Bar\ForbiddenClass $crawler, $bar) {}
    public function error3(Foo &$foo, ForbiddenClass $crawler, $bar) {}
    public function error4(Foo &$foo, FooAlias\Bar\ForbiddenClass $crawler, $bar) {}
    public function error5(Foo &$foo, ClassAlias $crawler, $bar) {}

    // Ok
    public function ok1(Foo &$foo, Foo\Bar\ForbiddenClass $crawler, $bar) {}
    public function ok2(Foo &$foo, \Bar\ForbiddenClass $crawler, $bar) {}
    public function ok3(Foo &$foo, \ClassAlias $crawler, $bar) {}
}

// Error
class Error1 extends \Foo\Bar\ForbiddenClass {};
class Error2 extends Bar\ForbiddenClass {};
class Error3 extends ForbiddenClass {};
class Error4 extends FooAlias\Bar\ForbiddenClass {};
class Error5 extends ClassAlias {};

// Failure
class Ok1 extends Foo\Bar\ForbiddenClass {};
class Ok2 extends \Bar\ForbiddenClass {};
class Ok3 extends \ClassAlias {};
