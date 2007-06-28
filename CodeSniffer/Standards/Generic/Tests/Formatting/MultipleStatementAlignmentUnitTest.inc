<?php

// Valid
$var1    = 'var1';
$var10   = 'var1';
$var100  = 'var1';
$var1000 = 'var1';

// Invalid
$var1    = 'var1';
$var10 = 'var1';
$var100 = 'var1';
$var1000 = 'var1';

// Valid
$var1  = 'var1';
$var10 = 'var1';

$var100  = 'var1';
$var1000 = 'var1';

// Invalid
$var1     = 'var1';
$var10    = 'var1';

$var100   = 'var1';
$var1000  = 'var1';

// Valid
$var1    .= 'var1';
$var10   .= 'var1';
$var100  .= 'var1';
$var1000 .= 'var1';

// Invalid
$var1    .= 'var1';
$var10 .= 'var1';
$var100 .= 'var1';
$var1000 .= 'var1';

// Valid
$var1     = 'var1';
$var10   .= 'var1';
$var100   = 'var1';
$var1000 .= 'var1';

// Invalid
$var1    = 'var1';
$var10   .= 'var1';
$var100  = 'var1';
$var1000 .= 'var1';

// Valid
$var1  .= 'var1';
$var10 .= 'var1';

$var100  .= 'var1';
$var1000 .= 'var1';

// Invalid
$var1     .= 'var1';
$var10    .= 'var1';

$var100   .= 'var1';
$var1000  .= 'var1';

// Valid
$var = 100;

// InValid
$var  = 100;

$commentStart = $phpcsFile->findPrevious();
$commentEnd   = $this->_phpcsFile;
$expected    .= '...';

// Invalid
$this->okButton    = new Button();
$content = new MyClass();


$GLOBALS['_PEAR_ERRORSTACK_SINGLETON'] = array();

class MyClass
{
    const MODE_DEBUG = 'debug';
    const MODE_DEBUG2 = 'debug';

    $array[$test] = 'anything';
    $var          = 'anything';

    const MODE_DEBUG2  = 'debug';
    $array[$test]    = 'anything';
    $var             = 'anything';
    $array[($test + 1)] = 'anything';
    $array[($blah + (10 - $test))] = 'anything';
}

function myFunction($var=true)
{
    if ($strict === true) {
        $length          = strlen($string);
        $lastCharWasCaps = ($classFormat === false) ? false : true;

        for ($i = 1; $i < $length; $i++) {
            $isCaps = (strtoupper($string{$i}) === $string{$i}) ? true : false;
            if ($isCaps === true && $lastCharWasCaps === true) {
                return false;
            }

            $lastCharWasCaps = $isCaps;
        }
    }
}

// Valid
for ($i = 0; $i < 10; $i += 2) {
    $i = ($i - 1);
}

?>
