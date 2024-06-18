--TEST--
Report: Full, no error codes

--SKIPIF--
<?php
if (is_file(__DIR__.'/../../../autoload.php') === false) {
	print 'skip: Test cannot run from a PEAR install.';
}
?>
--ARGS--
./tests/EndToEnd/Fixtures/Reports/ -q --no-colors --report-width=80 --basepath=./tests/EndToEnd/Fixtures/Reports/ --standard=PSR1 --report=Full

--FILE--
<?php
require_once __DIR__ . '/../../../bin/phpcs';

--EXPECTF--
#!/usr/bin/env php

FILE: Dirty.php
--------------------------------------------------------------------------------
FOUND 7 ERRORS AND 1 WARNING AFFECTING 5 LINES
--------------------------------------------------------------------------------
  1 | WARNING | A file should declare new symbols (classes, functions,
    |         | constants, etc.) and cause no other side effects, or it should
    |         | execute logic with side effects, but should not do both. The
    |         | first symbol is defined on line 6 and the first side effect is
    |         | on line 14.
  6 | ERROR   | Each class must be in a namespace of at least one level (a
    |         | top-level vendor name)
  6 | ERROR   | Class name "dirty_class" is not in PascalCase format
  7 | ERROR   | Class constants must be uppercase; expected LOWERCASE but found
    |         | lowerCase
  9 | ERROR   | Method name "dirty_class::My_Method" is not in camel caps
    |         | format
 12 | ERROR   | Each class must be in a file by itself
 12 | ERROR   | Each class must be in a namespace of at least one level (a
    |         | top-level vendor name)
 12 | ERROR   | Class name "Second_class" is not in PascalCase format
--------------------------------------------------------------------------------

Time: %f secs; Memory: %dMB
