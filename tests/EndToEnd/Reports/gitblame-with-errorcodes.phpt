--TEST--
Report: Gitblame, with error codes

--SKIPIF--
<?php
if (is_file(__DIR__.'/../../../autoload.php') === false) {
	print 'skip: Test cannot run from a PEAR install.';
}
?>
--ARGS--
./tests/EndToEnd/Fixtures/Reports/ -qs --no-colors --report-width=80 --basepath=./tests/EndToEnd/Fixtures/Reports/ --standard=PSR1 --report=Gitblame

--FILE--
<?php
require_once __DIR__ . '/../../../bin/phpcs';

--EXPECTF--
#!/usr/bin/env php

PHP CODE SNIFFER GIT BLAME SUMMARY
--------------------------------------------------------------------------------
AUTHOR   SOURCE                                     (Author %) (Overall %) COUNT
--------------------------------------------------------------------------------
%s                                         (%f)       (%d)     %d
         Squiz.Classes.ValidClassName.NotCamelCaps                             2
         PSR1.Classes.ClassDeclaration.MissingNamespace                        2
         PSR1.Classes.ClassDeclaration.MultipleClasses                         1
         PSR1.Methods.CamelCapsMethodName.NotCamelCaps                         1
         Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotU     1
         PSR1.Files.SideEffects.FoundWithSymbols                               1
--------------------------------------------------------------------------------
A TOTAL OF 8 SNIFF VIOLATIONS WERE COMMITTED BY 1 AUTHOR
--------------------------------------------------------------------------------

Time: %f secs; Memory: %dMB
