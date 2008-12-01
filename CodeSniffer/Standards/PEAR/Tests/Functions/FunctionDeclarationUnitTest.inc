<?php
public function someFunctionWithAVeryLongName($firstParameter='something',
   $secondParameter='booooo', $third=null, $fourthParameter=false,
     $fifthParameter=123.12, $sixthParam=true
){
}

function someFunctionWithAVeryLongName2($firstParameter='something',
$secondParameter='booooo', $third=null, $fourthParameter=false,
$fifthParameter=123.12, $sixthParam=true
)  {
}

function blah() {
}

function blah()
{
}

class MyClass
{

    public function someFunctionWithAVeryLongName($firstParameter='something',
        $secondParameter='booooo', $third=null, $fourthParameter=false,
        $fifthParameter=123.12, $sixthParam=true
    ) /** w00t */ {
    }

    public function someFunctionWithAVeryLongName2(
        $firstParameter='something', $secondParameter='booooo', $third=null
    ) {
    }

    protected abstract function processTokenWithinScope(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        $currScope
    );

}

function getInstalledStandards(
    $includeGeneric=false,
    $standardsDir=''
)
{
}

?>
