<?php
/**
 * An exception thrown by PHP_CodeSniffer when it wants to exit from somewhere not in the main runner.
 *
 * Allows the runner to return an exit code instead of putting exit codes elsewhere
 * in the source code.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Exceptions;

class DeepExitException extends \Exception
{

}//end class
