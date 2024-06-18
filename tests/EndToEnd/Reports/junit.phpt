--TEST--
Report: Junit

--SKIPIF--
<?php
if (is_file(__DIR__.'/../../../autoload.php') === false) {
	print 'skip: Test cannot run from a PEAR install.';
}
?>
--ARGS--
./tests/EndToEnd/Fixtures/Reports/ -q --no-colors --report-width=80 --basepath=./tests/EndToEnd/Fixtures/Reports/ --standard=PSR1 --report=Junit

--FILE--
<?php
require_once __DIR__ . '/../../../bin/phpcs';

--EXPECTF--
#!/usr/bin/env php
<?xml version="1.0" encoding="UTF-8"?>
<testsuites name="PHP_CodeSniffer %s" errors="0" tests="9" failures="8">
<testsuite name="CleanClass.php" errors="0" tests="1" failures="0">
 <testcase name="CleanClass.php"/>
</testsuite>
<testsuite name="Dirty.php" errors="0" tests="8" failures="8">
 <testcase name="PSR1.Files.SideEffects.FoundWithSymbols at Dirty.php (1:1)">
  <failure type="warning" message="A file should declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it should execute logic with side effects, but should not do both. The first symbol is defined on line 6 and the first side effect is on line 14."/>
 </testcase>
 <testcase name="PSR1.Classes.ClassDeclaration.MissingNamespace at Dirty.php (6:1)">
  <failure type="error" message="Each class must be in a namespace of at least one level (a top-level vendor name)"/>
 </testcase>
 <testcase name="Squiz.Classes.ValidClassName.NotCamelCaps at Dirty.php (6:1)">
  <failure type="error" message="Class name &quot;dirty_class&quot; is not in PascalCase format"/>
 </testcase>
 <testcase name="Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase at Dirty.php (7:11)">
  <failure type="error" message="Class constants must be uppercase; expected LOWERCASE but found lowerCase"/>
 </testcase>
 <testcase name="PSR1.Methods.CamelCapsMethodName.NotCamelCaps at Dirty.php (9:12)">
  <failure type="error" message="Method name &quot;dirty_class::My_Method&quot; is not in camel caps format"/>
 </testcase>
 <testcase name="PSR1.Classes.ClassDeclaration.MultipleClasses at Dirty.php (12:1)">
  <failure type="error" message="Each class must be in a file by itself"/>
 </testcase>
 <testcase name="PSR1.Classes.ClassDeclaration.MissingNamespace at Dirty.php (12:1)">
  <failure type="error" message="Each class must be in a namespace of at least one level (a top-level vendor name)"/>
 </testcase>
 <testcase name="Squiz.Classes.ValidClassName.NotCamelCaps at Dirty.php (12:1)">
  <failure type="error" message="Class name &quot;Second_class&quot; is not in PascalCase format"/>
 </testcase>
</testsuite>
</testsuites>
