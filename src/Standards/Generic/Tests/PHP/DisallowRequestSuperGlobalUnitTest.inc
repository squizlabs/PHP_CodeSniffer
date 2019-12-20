<?php
echo $_REQUEST['action'];

echo '$_REQUEST';

echo $_POST['action'];

echo $_GET[$action];

echo $_COOKIE['action'];

$sample = Util::getArrayIndex($_REQUEST, 'sample', '');
$syntax = Util::getArrayIndex($_REQUEST, 'syntax', '');
$value  = Util::getArrayIndex($_FILES, $key, $default);

?>
