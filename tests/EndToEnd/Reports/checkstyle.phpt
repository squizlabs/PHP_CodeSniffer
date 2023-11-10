--TEST--
Report: Checkstyle

--SKIPIF--
<?php
if (is_file(__DIR__.'/../../../autoload.php') === false) {
	print 'skip: Test cannot run from a PEAR install.';
}
?>
--ARGS--
./tests/EndToEnd/Fixtures/Reports/ -q --no-colors --report-width=80 --basepath=./tests/EndToEnd/Fixtures/Reports/ --standard=PSR1 --report=Checkstyle

--FILE--
<?php
require_once __DIR__ . '/../../../bin/phpcs';

--EXPECTF--
#!/usr/bin/env php
<?xml version="1.0" encoding="UTF-8"?>
<checkstyle version="%s">
<file name="Dirty.php">
 <error line="1" column="1" severity="warning" message="A file should declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it should execute logic with side effects, but should not do both. The first symbol is defined on line 6 and the first side effect is on line 14." source="PSR1.Files.SideEffects.FoundWithSymbols"/>
 <error line="6" column="1" severity="error" message="Each class must be in a namespace of at least one level (a top-level vendor name)" source="PSR1.Classes.ClassDeclaration.MissingNamespace"/>
 <error line="6" column="1" severity="error" message="Class name &quot;dirty_class&quot; is not in PascalCase format" source="Squiz.Classes.ValidClassName.NotCamelCaps"/>
 <error line="7" column="11" severity="error" message="Class constants must be uppercase; expected LOWERCASE but found lowerCase" source="Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase"/>
 <error line="9" column="12" severity="error" message="Method name &quot;dirty_class::My_Method&quot; is not in camel caps format" source="PSR1.Methods.CamelCapsMethodName.NotCamelCaps"/>
 <error line="12" column="1" severity="error" message="Each class must be in a file by itself" source="PSR1.Classes.ClassDeclaration.MultipleClasses"/>
 <error line="12" column="1" severity="error" message="Each class must be in a namespace of at least one level (a top-level vendor name)" source="PSR1.Classes.ClassDeclaration.MissingNamespace"/>
 <error line="12" column="1" severity="error" message="Class name &quot;Second_class&quot; is not in PascalCase format" source="Squiz.Classes.ValidClassName.NotCamelCaps"/>
</file>
</checkstyle>
