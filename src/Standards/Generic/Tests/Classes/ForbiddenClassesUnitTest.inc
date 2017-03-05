<?php
namespace Foo;

use Foo\Bar\ForbiddenClass, Foo\Bar\ForbiddenClass as ClassAlias;
use Foo as FooAlias;

/**
 * @property \Foo\Bar\ForbiddenClass $error1 blablabla
 * @property Bar\ForbiddenClass $error2
 * @property ForbiddenClass $error3
 * @property FooAlias\Bar\ForbiddenClass $error4
 * @property ClassAlias $error5
 *
 * @property Foo\Bar\ForbiddenClass $ok1
 * @property \Bar\ForbiddenClass $ok2
 * @property \Bar\ClassAlias $ok3
 */
class Test implements
    \Foo\Bar\ForbiddenClass, Bar\ForbiddenClass, ForbiddenClass, FooAlias\Bar\ForbiddenClass, ClassAlias, // Error
    Foo\Bar\ForbiddenClass,  \Bar\ForbiddenClass, \ClassAlias // Ok
{
    use \Foo\Bar\ForbiddenClass, Bar\ForbiddenClass, ForbiddenClass, FooAlias\Bar\ForbiddenClass, ClassAlias; // Error
    use Foo\Bar\ForbiddenClass, \Bar\ForbiddenClass, \ClassAlias; // Ok

    // Error
    /** @var \Foo\Bar\ForbiddenClass $error1 */
    /** @var Bar\ForbiddenClass $error2 */
    /** @var ForbiddenClass $error3 */
    /** @var FooAlias\Bar\ForbiddenClass $error4 */
    /** @var ClassAlias $error5 */

    // Ok
    /** @var Foo\Bar\ForbiddenClass $ok1 */
    /** @var \Bar\ForbiddenClass $ok2 */
    /** @var \Bar\ClassAlias $ok3 */

    // PHPDoc with multi-type and array syntax
    /** @var null|\Foo\Bar\ForbiddenClass[]|Bar\ForbiddenClass $specialError1 */

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
    /**
     * @param Foo $foo
     * @param \Foo\Bar\ForbiddenClass $crawler
     * @param $bar
     *
     * @return \Foo\Bar\ForbiddenClass
     */
    public function error1(Foo &$foo, \Foo\Bar\ForbiddenClass $crawler, $bar) {}

    /**
     * @param Foo $foo
     * @param Bar\ForbiddenClass $crawler
     * @param $bar
     *
     * @return Bar\ForbiddenClass
     */
    public function error2(Foo &$foo, Bar\ForbiddenClass $crawler, $bar) {}

    /**
     * @param Foo $foo
     * @param ForbiddenClass $crawler
     * @param $bar
     *
     * @return ForbiddenClass
     */
    public function error3(Foo &$foo, ForbiddenClass $crawler, $bar) {}

    /**
     * @param Foo $foo
     * @param FooAlias\Bar\ForbiddenClass $crawler
     * @param $bar
     *
     * @return FooAlias\Bar\ForbiddenClass
     */
    public function error4(Foo &$foo, FooAlias\Bar\ForbiddenClass $crawler, $bar) {}

    /**
     * @param Foo $foo
     * @param ClassAlias $crawler
     * @param $bar
     *
     * @return ClassAlias
     */
    public function error5(Foo &$foo, ClassAlias $crawler, $bar) {}

    // Ok
    /**
     * @param Foo $foo
     * @param Foo\Bar\ForbiddenClass $crawler
     * @param $bar
     *
     * @return Foo\Bar\ForbiddenClass
     */
    public function ok1(Foo &$foo, Foo\Bar\ForbiddenClass $crawler, $bar) {}

    /**
     * @param Foo $foo
     * @param \Bar\ForbiddenClass $crawler
     * @param $bar
     *
     * @return \Bar\ForbiddenClass
     */
    public function ok2(Foo &$foo, \Bar\ForbiddenClass $crawler, $bar) {}

    /**
     * @param Foo $foo
     * @param \ClassAlias $crawler
     * @param $bar
     *
     * @return \ClassAlias
     */
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
