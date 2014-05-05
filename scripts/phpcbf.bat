@echo off
REM PHP Code Beautifier and Fixer
REM 
REM PHP version 5
REM 
REM @category  PHP
REM @package   PHP_CodeSniffer
REM @author    Greg Sherwood <gsherwood@squiz.net>
REM @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
REM @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
REM @link      http://pear.php.net/package/PHP_CodeSniffer

"@php_bin@" -d auto_append_file="" -d auto_prepend_file="" -d include_path="'@php_dir@'" -f "@bin_dir@\phpcbf" -- %*
