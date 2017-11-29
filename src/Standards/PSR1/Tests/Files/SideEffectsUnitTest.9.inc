<?php
// phpcs:disable PSR1.Files.SideEffects
define("MAXSIZE", 100);
// phpcs:enable
$defined = true;
if (defined('MINSIZE') === false) {
    $defined = false;
}
