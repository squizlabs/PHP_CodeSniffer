<?php
echo $blah;
echo    $blah;
echo($blah);

print $blah;
print     $blah;
print($blah);

include $blah;
include     $blah;
include($blah);

include_once $blah;
include_once     $blah;
include_once($blah);

require $blah;
require     $blah;
require($blah);

require_once $blah;
require_once     $blah;
require_once($blah);

$obj = new MyClass();
$obj = new    MyClass();

yield $blah;
yield   $blah;

yield from $test();
yield FROM   $test();
YIELD   from   $test();
yield   from $test();
yield
from $test();
yield


From $test();

throw new Exception();
throw  new Exception();
throw new   Exception();
throw   new   Exception();

namespace MyClass;
namespace    MyClass;
namespace MyNamespace\MyClass;
namespace    MyNamespace\MyClass;
namespace \MyNamespace\MyClass;
namespace    \MyNamespace\MyClass;

use MyClass;
use    MyClass;
use MyNamespace\MyClass;
use    MyNamespace\MyClass;
use \MyNamespace\MyClass;
use    \MyNamespace\MyClass;

return;
return $blah;
return   $blah;
return($blah);

return	$tab;
return
$newLine;

// The following line must have a single space at the end (after return)
return 
$spaceAndNewLine;

// The following line must be the last line in the file
return
