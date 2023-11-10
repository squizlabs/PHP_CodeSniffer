--TEST--
Report: Gitblame, no error codes

--SKIPIF--
<?php
if (is_file(__DIR__.'/../../../autoload.php') === false) {
	print 'skip: Test cannot run from a PEAR install.';
}
?>
--ARGS--
./tests/EndToEnd/Fixtures/Reports/ -q --no-colors --report-width=80 --basepath=./tests/EndToEnd/Fixtures/Reports/ --standard=PSR1 --report=Gitblame

--FILE--
<?php
require_once __DIR__ . '/../../../bin/phpcs';

--EXPECTF--
#!/usr/bin/env php

PHP CODE SNIFFER GIT BLAME SUMMARY
----------------------------------------------------------------------
AUTHOR                                    (Author %) (Overall %) COUNT
----------------------------------------------------------------------
%s                                      (%f)       (%d)     %d
----------------------------------------------------------------------
A TOTAL OF 8 SNIFF VIOLATIONS WERE COMMITTED BY 1 AUTHOR
----------------------------------------------------------------------

Time: %f secs; Memory: %dMB
