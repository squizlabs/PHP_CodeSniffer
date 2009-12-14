<?php
echo $_REQUEST['action'];
Security::getRequestData('action');

echo $_POST['action'];
Security::getRequestData('action', 'post');

echo $_GET[$action];
Security::getRequestData($action, 'get');

class Security
{
    function getRequestData($var) {
        echo $_REQUEST[$var];
    }
}

class Insecurity
{
    function getRequestData($var) {
        unset($_REQUEST[$var]);
        echo 'OMGHAX!';
    }
}

$sample = Util::getArrayIndex($_REQUEST, 'sample', '');
$syntax = Util::getArrayIndex($_REQUEST, 'syntax', '');
$value  = Util::getArrayIndex($_FILES, $key, $default);

?>