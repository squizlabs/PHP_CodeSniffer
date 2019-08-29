#!/usr/bin/env php
<?php
/**
 * Validate the PHP_CodeSniffer PEAR package.xml file.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

require_once dirname(__DIR__).'/tests/FileList.php';
require_once __DIR__.'/ValidatePEAR/ValidatePEARPackageXML.php';

$validate = new ValidatePEARPackageXML();
$validate->validate();
