<?php
ob_start();
    // Do something here.
    $code = 'hello';

    $contents = ob_get_contents();
ob_end_clean();

ob_start();
// Do something here.
$code     = 'hello';
$contents = ob_get_contents();
ob_end_clean();

ob_start();
    // Do something here.
        $code     = 'hello';
        $contents = ob_get_contents();
ob_end_clean();

ob_start();
    // Do something here.

   $code = 'hello';
   // Hello.
    $contents = ob_get_contents();
    // Hi.
ob_end_clean();

function myFunc1()
{
    ob_start();
       echo 'hello';
       $content = ob_get_clean();

    if ($blah) {
        echo 'blah';
    }
}

function myFunc2()
{
    ob_start();
        echo 'hello';
        $content = ob_get_contents();
    ob_end_clean();
}

ob_start();
    ?><h2>Sign up for a new account</h2>
    <form action="<?php echo $submitTo; ?>" method="post">
    <?php
ob_end_clean();

ob_start();
    ?><h2>Sign up for a new account</h2>
<form action="<?php echo $submitTo; ?>" method="post">
   <?php
ob_end_clean();

ob_start();

    $a = 1;
    $b = 2;

    /*
        This is a multi-line comment.
    */

    $a = ($a + $b);

ob_end_clean();

ob_start();
    echo $hello;
?>
