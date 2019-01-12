
<?php
echo 'hi';
echo 'hello';   
    
if ($something) {  
    
}


function myFunction()
{
    echo 'code here';

    echo 'code here';


    // Hello.

    /*
        HI
    */


}

/**
 * Doc comment with a white space at the end. 
 */
function myFunction2()
{
    echo 'code here';


    echo 'code here';

}

// phpcs:set Squiz.WhiteSpace.SuperfluousWhitespace ignoreBlankLines true

function myFunction3()
{
    echo 'code here';
    
    echo 'code here';
    
}

// Ordinary comment with extra whitespace at the end    

// phpcs:set Squiz.WhiteSpace.SuperfluousWhitespace ignoreBlankLines false
 

// @codingStandardsChangeSetting Squiz.WhiteSpace.SuperfluousWhitespace ignoreMultiLineStrings false

function myFunction4()
{
    echo "multi    
    line  
    string
    whitespace ";
    echo 'code here'; 
}

// @codingStandardsChangeSetting Squiz.WhiteSpace.SuperfluousWhitespace ignoreMultiLineStrings true

function myFunction5()
{
    echo "multi    
    line  
    string
    whitespace ";
    echo 'code here'; 
}

// Уберём из системных свойств все кроме информации об услугах

?>

