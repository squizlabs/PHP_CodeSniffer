<?php
use Exception;
namespace foo;
namespace foo\bar;
namespace bar\foo\baz;

define('VALID_NAME', true);
define('invalidName', true);
define("VALID_NAME", true);
define("invalidName", true);

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

        echo constant("VALID_NAME");
        echo constant("invalidName");

        echo 'Hello', VALID_NAME;
        echo 'Hello', invalidName;

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
?>