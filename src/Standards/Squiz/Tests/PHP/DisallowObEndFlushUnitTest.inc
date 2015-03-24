<?php
ob_start();
    echo 'hello';
    $contents = ob_get_contents();
ob_end_clean();

ob_start();
    echo 'hello';
ob_end_flush();
?>
