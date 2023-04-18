--TEST--
Report: Xml

--SKIPIF--
<?php
if (is_file(__DIR__.'/../../../autoload.php') === false) {
	print 'skip: Test cannot run from a PEAR install.';
}
?>
--ARGS--
./tests/EndToEnd/Fixtures/Reports/ -q --no-colors --report-width=80 --basepath=./tests/EndToEnd/Fixtures/Reports/ --standard=PSR1 --report=Xml

--FILE--
<?php
require_once __DIR__ . '/../../../bin/phpcs';

--EXPECTF--
#!/usr/bin/env php
<?xml version="1.0" encoding="UTF-8"?>
<phpcs version="%s">
<file name="Dirty.php" errors="7" warnings="1" fixable="0">
    <warning line="1" column="1" source="PSR1.Files.SideEffects.FoundWithSymbols" severity="5" fixable="0">A file should declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it should execute logic with side effects, but should not do both. The first symbol is defined on line 6 and the first side effect is on line 14.</warning>
    <error line="6" column="1" source="PSR1.Classes.ClassDeclaration.MissingNamespace" severity="5" fixable="0">Each class must be in a namespace of at least one level (a top-level vendor name)</error>
    <error line="6" column="1" source="Squiz.Classes.ValidClassName.NotCamelCaps" severity="5" fixable="0">Class name &quot;dirty_class&quot; is not in PascalCase format</error>
    <error line="7" column="11" source="Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase" severity="5" fixable="0">Class constants must be uppercase; expected LOWERCASE but found lowerCase</error>
    <error line="9" column="12" source="PSR1.Methods.CamelCapsMethodName.NotCamelCaps" severity="5" fixable="0">Method name &quot;dirty_class::My_Method&quot; is not in camel caps format</error>
    <error line="12" column="1" source="PSR1.Classes.ClassDeclaration.MultipleClasses" severity="5" fixable="0">Each class must be in a file by itself</error>
    <error line="12" column="1" source="PSR1.Classes.ClassDeclaration.MissingNamespace" severity="5" fixable="0">Each class must be in a namespace of at least one level (a top-level vendor name)</error>
    <error line="12" column="1" source="Squiz.Classes.ValidClassName.NotCamelCaps" severity="5" fixable="0">Class name &quot;Second_class&quot; is not in PascalCase format</error>
</file>
</phpcs>
