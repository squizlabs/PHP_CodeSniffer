<?php
function getSomeValue() {}
function parseMyDSN() {}
function get_some_value() {}
function getSomeValue_Again() {}
function _getSomeValue() {}
function _parseMyDSN() {}
function _get_some_value() {}
function _getSomeValue_Again() {}

function __construct() {}
function __destruct() {}
function __myFunction() {}
function __my_function() {}

function XMLParser() {}
function xmlParser() {}

echo preg_replace_callback('~-([a-z])~', function ($match) { return strtoupper($match[1]); }, 'hello-world');
?>
