<?php
use Exception as My_Exception, foo\bar, baz;
namespace foo;
namespace foo\bar;
namespace bar\foo\baz;

define('VALID_NAME', true);
define('invalidName', true);
define("VALID_NAME", true);
define("invalidName", true);
define('bar\foo\baz\VALID_NAME_WITH_NAMESPACE', true);
define('bar\foo\baz\invalidNameWithNamespace', true);
define("bar\foo\baz\VALID_NAME_WITH_NAMESPACE", true);
define("bar\foo\baz\invalidNameWithNamespace", true);

class TestClass extends MyClass, YourClass
{

    const const1 = 'hello';
    const CONST2 = 'hello';

    function test()
    {
        echo constant('VALID_NAME');
        echo VALID_NAME;
        print VALID_NAME;
        echo(VALID_NAME);
        print(VALID_NAME);
        echo constant('invalidName');
        echo invalidName;
        print invalidName;
        echo(invalidName);
        print(invalidName);
        echo constant('VALID_NAME_WITH_NAMESPACE');
        echo VALID_NAME_WITH_NAMESPACE;
        print VALID_NAME_WITH_NAMESPACE;
        echo(VALID_NAME_WITH_NAMESPACE);
        print(VALID_NAME_WITH_NAMESPACE);
        echo constant('invalidNameWithNamespace');
        echo invalidNameWithNamespace;
        print invalidNameWithNamespace;
        echo(invalidNameWithNamespace);
        print(invalidNameWithNamespace);

        echo constant("VALID_NAME");
        echo constant("invalidName");
        echo constant("VALID_NAME_WITH_NAMESPACE");
        echo constant("invalidNameWithNamespace");

        echo 'Hello', VALID_NAME;
        echo 'Hello', invalidName;
        echo 'Hello', VALID_NAME_WITH_NAMESPACE;
        echo 'Hello', invalidNameWithNamespace;

        // These might look like constants to
        // poorly written code.
        echo 'Hello there';
        echo "HELLO";
        echo 'HELLO';
        print 'Hello there';
        print "HELLO";
        print 'HELLO';
    }

    function myFunc(PHP_CodeSniffer &$blah) {}
    function myFunc(PHP_CodeSniffer $blah) {}

}

interface MyInterface
{
}

if (($object instanceof Some_Class) === false) {
    $var = <<<EOF
This is some heredoc text.
This is some heredoc text.
This is some heredoc text.

This is some heredoc text.
This is some heredoc text.
This is some heredoc text.
EOF;
}

$var = <<<EOF
This is some heredoc text.
This is some heredoc text.
This is some heredoc text.

This is some heredoc text.
This is some heredoc text.
This is some heredoc text.
EOF;

throw new InvalidSomethingException;

declare(ticks = 1) {
    foreach ($var as $bit) {
        echo $bit;
    }
}

$binary = (binary) $string;

$foo->define('bar');
$foo->getBar()->define('foo');

// This is allowed as it is required for PHPUnit.
if (PHPUnit_MAIN_METHOD == 'PHP_Shell_AllTests::main') {
    PHP_Shell_AllTests::main();
}

class ConstTest {
    const TESTER = '1';
    public function __construct() {
        echo constant('self::TESTER');
        echo constant('self::tester');
    }
}

class A {
    public static function constant($x) {}
}

A::constant('anything-not-in-uppercase');

// goto
goto gotolabel;

gototarget1:

function ConstTest() {
    gototarget2:
}

switch($foo)
{
    case $bar:
    gototarget3:
}

$a = 2 * ${x} - ${minus};

class Object implements IObject
{
    use TProperties;
    use TReflected {
        TReflected::reflect insteadof TProperties;
    }
    use ListenerAggregateTrait, PreLoadUserTrait, PreLoadCompanyTrait {
        PreloadUserTrait::PreLoadUser as loadUser;
        PreloadCompanyTrait::PreLoadCompany as loadCompany;
    }
}

class MyClass {
    use Something {
        delete as protected blah;
    }
}

class Foo
{
    public function bar()
    {
        return Bar::class;
    }
}

?>
