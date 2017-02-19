<?php
namespace Foo;

use Foo\Bar\ForbiddenClass;
use Foo\Bar\ForbiddenClass as ClassAlias;
use Foo as FooAlias;

class Test
{

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
        new \Foo\ForbiddenClass();
        new FooAlias\ClassAlias();

        Foo\Bar\ForbiddenClass::method();
        \Foo\ForbiddenClass::method();
        FooAlias\ClassAlias::method();

        function (Foo &$foo, Foo\Bar\ForbiddenClass $foo, $bar) {};
        function (Foo &$foo, \Foo\ForbiddenClass $foo, $bar) {};
        function (Foo &$foo, FooAlias\ClassAlias $foo, $bar) {};
    }

    // Error
    public function error1(Foo &$foo, \Foo\Bar\ForbiddenClass $crawler, $bar) {}
    public function error2(Foo &$foo, Bar\ForbiddenClass $crawler, $bar) {}
    public function error3(Foo &$foo, ForbiddenClass $crawler, $bar) {}
    public function error4(Foo &$foo, FooAlias\Bar\ForbiddenClass $crawler, $bar) {}
    public function error5(Foo &$foo, ClassAlias $crawler, $bar) {}

    // Ok
    public function ok1(Foo &$foo, Foo\Bar\ForbiddenClass $crawler, $bar) {}
    public function ok2(Foo &$foo, \Foo\ForbiddenClass $crawler, $bar) {}
    public function ok3(Foo &$foo, FooAlias\ClassAlias $crawler, $bar) {}
}
