--TEST--
Report: Json

--SKIPIF--
<?php
if (is_file(__DIR__.'/../../../autoload.php') === false) {
	print 'skip: Test cannot run from a PEAR install.';
}
?>
--ARGS--
./tests/EndToEnd/Fixtures/Reports/ -q --no-colors --report-width=80 --basepath=./tests/EndToEnd/Fixtures/Reports/ --standard=PSR1 --report=Json

--FILE--
<?php
require_once __DIR__ . '/../../../bin/phpcs';

--EXPECT--
#!/usr/bin/env php
{"totals":{"errors":7,"warnings":1,"fixable":0},"files":{"CleanClass.php":{"errors":0,"warnings":0,"messages":[]},"Dirty.php":{"errors":7,"warnings":1,"messages":[{"message":"A file should declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it should execute logic with side effects, but should not do both. The first symbol is defined on line 6 and the first side effect is on line 14.","source":"PSR1.Files.SideEffects.FoundWithSymbols","severity":5,"fixable":false,"type":"WARNING","line":1,"column":1},{"message":"Each class must be in a namespace of at least one level (a top-level vendor name)","source":"PSR1.Classes.ClassDeclaration.MissingNamespace","severity":5,"fixable":false,"type":"ERROR","line":6,"column":1},{"message":"Class name \"dirty_class\" is not in PascalCase format","source":"Squiz.Classes.ValidClassName.NotCamelCaps","severity":5,"fixable":false,"type":"ERROR","line":6,"column":1},{"message":"Class constants must be uppercase; expected LOWERCASE but found lowerCase","source":"Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase","severity":5,"fixable":false,"type":"ERROR","line":7,"column":11},{"message":"Method name \"dirty_class::My_Method\" is not in camel caps format","source":"PSR1.Methods.CamelCapsMethodName.NotCamelCaps","severity":5,"fixable":false,"type":"ERROR","line":9,"column":12},{"message":"Each class must be in a file by itself","source":"PSR1.Classes.ClassDeclaration.MultipleClasses","severity":5,"fixable":false,"type":"ERROR","line":12,"column":1},{"message":"Each class must be in a namespace of at least one level (a top-level vendor name)","source":"PSR1.Classes.ClassDeclaration.MissingNamespace","severity":5,"fixable":false,"type":"ERROR","line":12,"column":1},{"message":"Class name \"Second_class\" is not in PascalCase format","source":"Squiz.Classes.ValidClassName.NotCamelCaps","severity":5,"fixable":false,"type":"ERROR","line":12,"column":1}]}}}
