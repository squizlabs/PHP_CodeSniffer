--TEST--
Report: Code, with error codes

--SKIPIF--
<?php
if (is_file(__DIR__.'/../../../autoload.php') === false) {
	print 'skip: Test cannot run from a PEAR install.';
}
?>
--ARGS--
./tests/EndToEnd/Fixtures/Reports/ -qs --no-colors --report-width=80 --basepath=./tests/EndToEnd/Fixtures/Reports/ --standard=PSR1 --report=Code

--FILE--
<?php
require_once __DIR__ . '/../../../bin/phpcs';

--EXPECTF--
#!/usr/bin/env php

FILE: Dirty.php
--------------------------------------------------------------------------------
FOUND 7 ERRORS AND 1 WARNING AFFECTING 5 LINES
--------------------------------------------------------------------------------
LINE  1: WARNING A file should declare new symbols (classes, functions, constants,
                 etc.) and cause no other side effects, or it should execute logic with
                 side effects, but should not do both. The first symbol is defined on
                 line 6 and the first side effect is on line 14.
                 (PSR1.Files.SideEffects.FoundWithSymbols)
--------------------------------------------------------------------------------
>>  1:  <?php
    2:  /*
    3:   * Test fixture file for End to End Report tests.
--------------------------------------------------------------------------------
LINE  6: ERROR   Each class must be in a namespace of at least one level (a
                 top-level vendor name)
                 (PSR1.Classes.ClassDeclaration.MissingNamespace)
LINE  6: ERROR   Class name "dirty_class" is not in PascalCase format
                 (Squiz.Classes.ValidClassName.NotCamelCaps)
--------------------------------------------------------------------------------
    4:   */
    5:%w
>>  6:  class dirty_class {
    7:      const lowerCase = false;
    8:
--------------------------------------------------------------------------------
LINE  7: ERROR   Class constants must be uppercase; expected LOWERCASE but found
                 lowerCase
                 (Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase)
--------------------------------------------------------------------------------
    5:%w
    6:  class dirty_class {
>>  7:      const lowerCase = false;
    8:%w
    9:      public function My_Method() {}
--------------------------------------------------------------------------------
LINE  9: ERROR   Method name "dirty_class::My_Method" is not in camel caps
                 format (PSR1.Methods.CamelCapsMethodName.NotCamelCaps)
--------------------------------------------------------------------------------
    7:      const lowerCase = false;
    8:%w
>>  9:      public function My_Method() {}
   10:  }
   11:
--------------------------------------------------------------------------------
LINE 12: ERROR   Each class must be in a file by itself
                 (PSR1.Classes.ClassDeclaration.MultipleClasses)
LINE 12: ERROR   Each class must be in a namespace of at least one level (a
                 top-level vendor name)
                 (PSR1.Classes.ClassDeclaration.MissingNamespace)
LINE 12: ERROR   Class name "Second_class" is not in PascalCase format
                 (Squiz.Classes.ValidClassName.NotCamelCaps)
--------------------------------------------------------------------------------
   10:  }
   11:%w
>> 12:  class Second_class {}
   13:%w
   14:  $obj = new dirty_class();
--------------------------------------------------------------------------------
Time: %f secs; Memory: %dMB
