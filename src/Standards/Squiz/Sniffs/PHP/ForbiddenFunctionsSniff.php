<?php
/**
 * Discourages the use of alias functions.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP;

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff as GenericForbiddenFunctionsSniff;

class ForbiddenFunctionsSniff extends GenericForbiddenFunctionsSniff
{

    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array<string, string|null>
     */
    public $forbiddenFunctions = array(
                                  'sizeof'          => 'count',
                                  'delete'          => 'unset',
                                  'print'           => 'echo',
                                  'is_null'         => null,
                                  'create_function' => null,
                                 );

}//end class
