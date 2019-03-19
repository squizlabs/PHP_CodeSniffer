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
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Sniffs\Conditions;
use PHP_CodeSniffer\Util\Sniffs\ConstructNames;
use PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations;
use PHP_CodeSniffer\Util\Tokens;

class CamelCapsFunctionNameSniff extends AbstractScopeSniff
{

    /**
     * A list of all PHP magic methods.
     *
     * Set from within the constructor.
     *
     * @var array
     *
     * @deprecated 3.5.0 Use PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::$magicMethods instead.
     */
    protected $magicMethods = [];

    /**
     * A list of all PHP non-magic methods starting with a double underscore.
     *
     * These come from PHP modules such as SOAPClient.
     *
     * Set from within the constructor.
     *
     * @var array
     *
     * @deprecated 3.5.0 Use PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::$methodsDoubleUnderscore instead.
     */
    protected $methodsDoubleUnderscore = [];

    /**
     * A list of all PHP magic functions.
     *
     * Set from within the constructor.
     *
     * @var array
     *
     * @deprecated 3.5.0 Use PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations::$magicFunctions instead.
     */
    protected $magicFunctions = [];

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
        // Preserve BC without code duplication.
        $this->magicMethods   = array_combine(
            FunctionDeclarations::$magicMethods,
            array_fill(0, count(FunctionDeclarations::$magicMethods), true)
        );
        $this->magicFunctions = array_combine(
            FunctionDeclarations::$magicFunctions,
            array_fill(0, count(FunctionDeclarations::$magicFunctions), true)
        );

        $methodsDoubleUnderscore = array_keys(FunctionDeclarations::$methodsDoubleUnderscore);
        foreach ($methodsDoubleUnderscore as $method) {
            $method = ltrim($method, '_');
            $this->methodsDoubleUnderscore[$method] = true;
        }

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
        $tokens = $phpcsFile->getTokens();

        // Determine if this is a function which needs to be examined.
        $deepestScope = Conditions::getLastCondition($phpcsFile, $stackPtr);
        if ($deepestScope !== $currScope) {
            return;
        }

        $methodName = ConstructNames::getDeclarationName($phpcsFile, $stackPtr);
        if (empty($methodName) === true) {
            // Live coding or parse error.
            return;
        }

        $className = ConstructNames::getDeclarationName($phpcsFile, $currScope);
        if (isset($className) === false) {
            $className = '[Anonymous Class]';
        }

        $errorData = [$className.'::'.$methodName];

        // Check is this method is prefixed with "__" and not magic.
        if (preg_match('|^__[^_]|', $methodName) !== 0) {
            if (FunctionDeclarations::isSpecialMethodName($methodName) === true) {
                return;
            }

            $error = 'Method name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
            $phpcsFile->addError($error, $stackPtr, 'MethodDoubleUnderscore', $errorData);
        }

        $methodNameLc = strtolower($methodName);
        $classNameLc  = strtolower($className);

        // PHP4 constructors are allowed to break our rules.
        if ($methodNameLc === $classNameLc) {
            return;
        }

        // PHP4 destructors are allowed to break our rules.
        if ($methodNameLc === '_'.$classNameLc) {
            return;
        }

        // Ignore first underscore in methods prefixed with "_".
        $methodName = ltrim($methodName, '_');

        $methodProps = FunctionDeclarations::getProperties($phpcsFile, $stackPtr);
        if (ConstructNames::isCamelCaps($methodName, false, true, $this->strict) === false) {
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
        $functionName = ConstructNames::getDeclarationName($phpcsFile, $stackPtr);
        if (empty($functionName) === true) {
            // Live coding or parse error.
            return;
        }

        $errorData = [$functionName];

        // Check is this function is prefixed with "__" and not magic.
        if (preg_match('|^__[^_]|', $functionName) !== 0) {
            if (FunctionDeclarations::isMagicFunctionName($functionName) === true) {
                return;
            }

            $error = 'Function name "%s" is invalid; only PHP magic methods should be prefixed with a double underscore';
            $phpcsFile->addError($error, $stackPtr, 'FunctionDoubleUnderscore', $errorData);
        }

        // Ignore first underscore in functions prefixed with "_".
        $functionName = ltrim($functionName, '_');

        if (ConstructNames::isCamelCaps($functionName, false, true, $this->strict) === false) {
            $error = 'Function name "%s" is not in camel caps format';
            $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $errorData);
            $phpcsFile->recordMetric($stackPtr, 'CamelCase function name', 'no');
        } else {
            $phpcsFile->recordMetric($stackPtr, 'CamelCase method name', 'yes');
        }

    }//end processTokenOutsideScope()


}//end class
