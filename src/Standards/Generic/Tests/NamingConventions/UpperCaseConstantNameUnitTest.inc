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

class TestClass extends MyClass implements MyInterface, YourInterface
{

    const const1 = 'hello';
    const CONST2 = 'hello';
}

$foo->define('bar');
$foo->getBar()->define('foo');
Foo::define('bar');

class ClassConstBowOutTest {
    const /* comment */ abc = 1;
    const // phpcs:ignore Standard.Category.Sniff
        some_constant = 2;
}

$foo->getBar()?->define('foo');
