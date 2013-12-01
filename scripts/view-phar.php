<?php

if (isset($argv[1]) === FALSE) {
    echo "Error!\n";
}

$phar = new Phar($argv[1], 0);

foreach (new RecursiveIteratorIterator($phar) as $file) {
    echo ' - '.$file->getPath().'/'.$file->getFileName()."\n";
}
