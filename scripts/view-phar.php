#!/usr/bin/env php
<?php
/**
 * View a PHPCS phar file.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Benjamin Pearson <bpearson@squiz.com.au>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

error_reporting(E_ALL | E_STRICT);

if (isset($argv[1]) === false) {
    echo 'Usage: '.$argv[0].' <phar-file>'."\n";
    exit(1);
}

$phar = new Phar($argv[1], 0);

foreach (new RecursiveIteratorIterator($phar) as $file) {
    echo ' - '.$file->getPath().'/'.$file->getFileName()."\n";
}
