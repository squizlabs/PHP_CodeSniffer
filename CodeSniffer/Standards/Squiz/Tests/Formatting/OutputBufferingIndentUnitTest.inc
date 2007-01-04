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

ob_start();
    echo $hello;
?>
