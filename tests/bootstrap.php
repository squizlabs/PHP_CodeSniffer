<?php

require_once __DIR__ . '/../vendor/autoload.php';

// init constants required for running code
define('PHP_CodeSniffer_CBF', false);
define('PHP_CodeSniffer_VERBOSITY', 0);

// init tokens constants
$tokens = new Symplify\PHP7_CodeSniffer\Util\Tokens();
