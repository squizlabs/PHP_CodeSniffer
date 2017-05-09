@codingStandardsChangeSetting Generic.WhiteSpace.ScopeIndent tabIndent false
@codingStandardsChangeSetting Generic.WhiteSpace.ScopeIndent exact true
<?php
function test()
{
     echo 'test';
     echo 'test2';
    echo 'test3';
    if (true) {
         echo 'test3';
    }
    echo 'test3';
    $x = f1(
        'test1', 'test2',
        'test3'
    );
}
