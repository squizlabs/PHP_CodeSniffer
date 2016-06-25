<?php

require_once __DIR__ . '/../vendor/autoload.php';

// init constants required for running code
define('PHP_CODESNIFFER_CBF', false);
define('PHP_CODESNIFFER_VERBOSITY', 0);

// init tokens constants
$tokens = new PHP_CodeSniffer\Util\Tokens();
