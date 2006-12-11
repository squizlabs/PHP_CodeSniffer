@echo off
REM PHP_CodeSniffer tokenises PHP code and detects violations of a
REM defined set of coding standards.
REM 
REM PHP version 5
REM 
REM @category  PHP
REM @package   PHP_CodeSniffer
REM @author    Greg Sherwood <gsherwood@squiz.net>
REM @author    Marc McIntyre <mmcintyre@squiz.net>
REM @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
REM @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
REM @version   CVS: $Id: phpcs.bat,v 1.2 2006-12-11 23:45:27 squiz Exp $
REM @link      http://pear.php.net/package/PHP_CodeSniffer

"@php_bin@" -d include_path="@php_dir@" "@bin_dir@\phpcs" %*
