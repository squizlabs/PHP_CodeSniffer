--TEST--
Report: Summary

--SKIPIF--
<?php
if (is_file(__DIR__.'/../../../autoload.php') === false) {
	print 'skip: Test cannot run from a PEAR install.';
}
?>
--ARGS--
./tests/EndToEnd/Fixtures/Reports/ -q --no-colors --report-width=80 --basepath=./tests/EndToEnd/Fixtures/Reports/ --standard=PSR1 --report=Summary

--FILE--
<?php
require_once __DIR__ . '/../../../bin/phpcs';

--EXPECTF--
#!/usr/bin/env php

PHP CODE SNIFFER REPORT SUMMARY
----------------------------------------------------------------------
FILE                                                  ERRORS  WARNINGS
----------------------------------------------------------------------
Dirty.php                                             7       1
----------------------------------------------------------------------
A TOTAL OF 7 ERRORS AND 1 WARNING WERE FOUND IN 1 FILE
----------------------------------------------------------------------

Time: %f secs; Memory: %dMB
