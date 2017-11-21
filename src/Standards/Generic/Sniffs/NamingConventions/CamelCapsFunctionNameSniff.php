<?php
/**
 * Ensures method and functions are named correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions;

use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Files\File;

class CamelCapsFunctionNameSniff extends AbstractScopeSniff
{

    /**
     * A list of all PHP magic methods.
     *
     * @var array
     */
    protected $magicMethods = [
        'construct'  => true,
        'destruct'   => true,
        'call'       => true,
        'callstatic' => true,
        'get'        => true,
        'set'        => true,
        'isset'      => true,
        'unset'      => true,
        'sleep'      => true,
        'wakeup'     => true,
        'tostring'   => true,
        'set_state'  => true,
        'clone'      => true,
        'invoke'     => true,
        'debuginfo'  => true,
    ];

    /**
     * A list of all PHP non-magic methods starting with a double underscore.
     *
     * These come from PHP modules such as SOAPClient.
     *
     * @var array
     */
    protected $methodsDoubleUnderscore = [
        'soapcall'               => true,
        'getlastrequest'         => true,
        'getlastresponse'        => true,
        'getlastrequestheaders'  => true,
        'getlastresponseheaders' => true,
        'getfunctions'           => true,
        'gettypes'               => true,
        'dorequest'              => true,
        'setcookie'              => true,
        'setlocation'            => true,
        'setsoapheaders'         => true,
    ];

    /**
     * A list of all PHP magic functions.
     *
     * @var array
     */
    protected $magicFunctions = ['autoload' => true];

    /**
     * If TRUE, the string must not have two capital letters next to each other.
     *
     * @var boolean
     */
    public $strict = true;


    /**
     * Constructs a Generic_Sniffs_NamingConventions_CamelCapsFunctionNameSniff.
     */
    public function __construct()
    {
        parent::__construct(Tokens::$ooScopeTokens, [T_FUNCTION], true);

    }//end __construct()


    /**
     * Processes the tokens within the scope.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being processed.
     * @param int                         $stackPtr  The position where this token was
     *                                               found.
     * @param int                         $currScope The position of the current scope.
     *
     * @return void
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null) {
            // Ignore closures.
            return;
        }

        $className = $phpcsFile->getDeclarationName($currScope);
        $errorData = [$className.'::'.$methodName];

        // Is this a magic method. i.e., is prefixed with "__" ?
        if (preg_match('|^__[^_]|', $methodName) !== 0) {
            $magicPart = strtolower(substr($methodName, 2));
            if (isset($this->magicMethods[$magicPart]) === false
                && isset($this->methodsDoubleUnderscore[$magicPart]) === false
            ) {
                $error = 'Method name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
                $phpcsFile->addError($error, $stackPtr, 'MethodDoubleUnderscore', $errorData);
            }

            return;
        }

        // PHP4 constructors are allowed to break our rules.
        if ($methodName === $className) {
            return;
        }

        // PHP4 destructors are allowed to break our rules.
        if ($methodName === '_'.$className) {
            return;
        }

        // Ignore first underscore in methods prefixed with "_".
        $methodName = ltrim($methodName, '_');

        $methodProps = $phpcsFile->getMethodProperties($stackPtr);
        if (Common::isCamelCaps($methodName, false, true, $this->strict) === false) {
            if ($methodProps['scope_specified'] === true) {
                $error = '%s method name "%s" is not in camel caps format';
                $data  = [
                    ucfirst($methodProps['scope']),
                    $errorData[0],
                ];
                $phpcsFile->addError($error, $stackPtr, 'ScopeNotCamelCaps', $data);
            } else {
                $error = 'Method name "%s" is not in camel caps format';
                $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $errorData);
            }

            $phpcsFile->recordMetric($stackPtr, 'CamelCase method name', 'no');
            return;
        } else {
            $phpcsFile->recordMetric($stackPtr, 'CamelCase method name', 'yes');
        }

    }//end processTokenWithinScope()


    /**
     * Processes the tokens outside the scope.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being processed.
     * @param int                         $stackPtr  The position where this token was
     *                                               found.
     *
     * @return void
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {
        $functionName = $phpcsFile->getDeclarationName($stackPtr);
        if ($functionName === null) {
            // Ignore closures.
            return;
        }

        $errorData = [$functionName];

        // Is this a magic function. i.e., it is prefixed with "__".
        if (preg_match('|^__[^_]|', $functionName) !== 0) {
            $magicPart = strtolower(substr($functionName, 2));
            if (isset($this->magicFunctions[$magicPart]) === false) {
                 $error = 'Function name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
                 $phpcsFile->addError($error, $stackPtr, 'FunctionDoubleUnderscore', $errorData);
            }

            return;
        }

        // Ignore first underscore in functions prefixed with "_".
        $functionName = ltrim($functionName, '_');

        if (Common::isCamelCaps($functionName, false, true, $this->strict) === false) {
            $error = 'Function name "%s" is not in camel caps format';
            $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $errorData);
            $phpcsFile->recordMetric($stackPtr, 'CamelCase function name', 'no');
        } else {
            $phpcsFile->recordMetric($stackPtr, 'CamelCase method name', 'yes');
        }

    }//end processTokenOutsideScope()


}//end class
