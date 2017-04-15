<?php
error_log('test');
print_r($array);
var_dump($array);
var_export($array);
debug_backtrace();
debug_print_backtrace();
trigger_error('test', E_USER_ERROR);
set_error_handler("myErrorHandler");
?>
