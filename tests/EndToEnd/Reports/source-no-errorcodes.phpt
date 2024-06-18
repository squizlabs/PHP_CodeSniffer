--TEST--
Report: Source, no error codes

--SKIPIF--
<?php
if (is_file(__DIR__.'/../../../autoload.php') === false) {
	print 'skip: Test cannot run from a PEAR install.';
}
?>
--ARGS--
./tests/EndToEnd/Fixtures/Reports/ -q --no-colors --report-width=80 --basepath=./tests/EndToEnd/Fixtures/Reports/ --standard=PSR1 --report=Source

--FILE--
<?php
require_once __DIR__ . '/../../../bin/phpcs';

--EXPECTF--
#!/usr/bin/env php

PHP CODE SNIFFER VIOLATION SOURCE SUMMARY
--------------------------------------------------------------------------------
STANDARD  CATEGORY            SNIFF                                        COUNT
--------------------------------------------------------------------------------
PSR1      Classes             Class declaration missing namespace          2
Squiz     Classes             Valid class name not camel caps              2
Generic   Naming conventions  Upper case constant name class constant not  1
PSR1      Classes             Class declaration multiple classes           1
PSR1      Files               Side effects found with symbols              1
PSR1      Methods             Camel caps method name not camel caps        1
--------------------------------------------------------------------------------
A TOTAL OF 8 SNIFF VIOLATIONS WERE FOUND IN 6 SOURCES
--------------------------------------------------------------------------------

Time: %f secs; Memory: %dMB
