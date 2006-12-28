<?php
function outer()
{
    if (!function_exists('inner')) {
        function inner() {
        }
    }
}
?>
