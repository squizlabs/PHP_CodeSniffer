<?php

if ($true == true) {
    echo 'True';
} elseif ($true != true) {
    echo 'False';
} elseif (false === $true && true !== $false) {
    echo 'False';
}

$a = $b == $c;
$a = $b != $c;
