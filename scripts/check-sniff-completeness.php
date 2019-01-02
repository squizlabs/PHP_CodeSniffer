#!/usr/bin/env php
<?php
/**
 * Validate that each sniff is complete, i.e. has unit tests and documentation.
 *
 * This script should be run from the root of a PHPCS standards repo and can
 * be used by external standards as well.
 *
 * Configuration options:
 * -q: quiet, don't show warnings about missing documentation files.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

require_once __DIR__.'/ValidatePEAR/FileList.php';
require_once __DIR__.'/SniffCompleteness/CheckSniffCompleteness.php';

$validate = new CheckSniffCompleteness();
$validate->validate();
