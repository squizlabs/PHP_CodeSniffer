<?php
class myclass extends yourclass implements someint {
    function myfunc($var) {
        echo $var;
    }
}

$myvar = true;
myfunc(&$myvar);
myfunc($myvar);

$this->myfunc(&$myvar);
$this->myfunc($myvar);

myclass::myfunc(&$myvar);
myclass::myfunc($myvar);

while(testfunc($var1, &$var2, $var3, &$var4) === false) {
}

sprintf("0%o", 0777 & $p);

$foo(&$myvar);
?>
