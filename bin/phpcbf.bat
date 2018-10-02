@echo off
REM PHP Code Beautifier and Fixer fixes violations of a defined coding standard.
REM 
REM @author    Greg Sherwood <gsherwood@squiz.net>
REM @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
REM @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence

if "%PHP_PEAR_PHP_BIN%" neq "" (
    set PHPBIN=%PHP_PEAR_PHP_BIN%
) else set PHPBIN=php

"%PHPBIN%" "%~dp0\phpcbf" %*
