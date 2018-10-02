<?php
// phpcs:disable PSR1.Files.SideEffects
// phpcs:enable Squiz
define("MAXSIZE", 100);
$defined = true;
if (defined('MINSIZE') === false) {
    $defined = false;
}

// phpcs:enable PEAR
define("MAXSIZE", 100);
