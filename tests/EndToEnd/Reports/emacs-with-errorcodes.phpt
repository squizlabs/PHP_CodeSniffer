--TEST--
Report: Emacs, with error codes

--SKIPIF--
<?php
if (is_file(__DIR__.'/../../../autoload.php') === false) {
	print 'skip: Test cannot run from a PEAR install.';
}
?>
--ARGS--
./tests/EndToEnd/Fixtures/Reports/ -qs --no-colors --report-width=80 --basepath=./tests/EndToEnd/Fixtures/Reports/ --standard=PSR1 --report=Emacs

--FILE--
<?php
require_once __DIR__ . '/../../../bin/phpcs';

--EXPECT--
#!/usr/bin/env php
Dirty.php:1:1: warning - A file should declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it should execute logic with side effects, but should not do both. The first symbol is defined on line 6 and the first side effect is on line 14. (PSR1.Files.SideEffects.FoundWithSymbols)
Dirty.php:6:1: error - Each class must be in a namespace of at least one level (a top-level vendor name) (PSR1.Classes.ClassDeclaration.MissingNamespace)
Dirty.php:6:1: error - Class name "dirty_class" is not in PascalCase format (Squiz.Classes.ValidClassName.NotCamelCaps)
Dirty.php:7:11: error - Class constants must be uppercase; expected LOWERCASE but found lowerCase (Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase)
Dirty.php:9:12: error - Method name "dirty_class::My_Method" is not in camel caps format (PSR1.Methods.CamelCapsMethodName.NotCamelCaps)
Dirty.php:12:1: error - Each class must be in a file by itself (PSR1.Classes.ClassDeclaration.MultipleClasses)
Dirty.php:12:1: error - Each class must be in a namespace of at least one level (a top-level vendor name) (PSR1.Classes.ClassDeclaration.MissingNamespace)
Dirty.php:12:1: error - Class name "Second_class" is not in PascalCase format (Squiz.Classes.ValidClassName.NotCamelCaps)
