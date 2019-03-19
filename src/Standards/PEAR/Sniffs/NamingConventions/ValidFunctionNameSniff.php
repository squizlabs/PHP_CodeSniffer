<?php
/**
 * Ensures method and function names are correct.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\NamingConventions;

use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Util\Sniffs\Conditions;
use PHP_CodeSniffer\Util\Sniffs\ConstructNames;
use PHP_CodeSniffer\Util\Sniffs\FunctionDeclarations;
use PHP_CodeSniffer\Util\Tokens;

class ValidFunctionNameSniff extends AbstractScopeSniff
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
     * Constructs a PEAR_Sniffs_NamingConventions_ValidFunctionNameSniff.
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

        $methodProps    = FunctionDeclarations::getProperties($phpcsFile, $stackPtr);
        $scope          = $methodProps['scope'];
        $scopeSpecified = $methodProps['scope_specified'];

        if ($methodProps['scope'] === 'private') {
            $isPublic = false;
        } else {
            $isPublic = true;
        }

        // If it's a private method, it must have an underscore on the front.
        if ($isPublic === false) {
            if ($methodName{0} !== '_') {
                $error = 'Private method name "%s" must be prefixed with an underscore';
                $phpcsFile->addError($error, $stackPtr, 'PrivateNoUnderscore', $errorData);
                $phpcsFile->recordMetric($stackPtr, 'Private method prefixed with underscore', 'no');
            } else {
                $phpcsFile->recordMetric($stackPtr, 'Private method prefixed with underscore', 'yes');
            }
        }

        // If it's not a private method, it must not have an underscore on the front.
        if ($isPublic === true && $scopeSpecified === true && $methodName{0} === '_') {
            $error = '%s method name "%s" must not be prefixed with an underscore';
            $data  = [
                ucfirst($scope),
                $errorData[0],
            ];
            $phpcsFile->addError($error, $stackPtr, 'PublicUnderscore', $data);
        }

        $testMethodName = ltrim($methodName, '_');

        if (Common::isCamelCaps($testMethodName, false, true, false) === false) {
            if ($scopeSpecified === true) {
                $error = '%s method name "%s" is not in camel caps format';
                $data  = [
                    ucfirst($scope),
                    $errorData[0],
                ];
                $phpcsFile->addError($error, $stackPtr, 'ScopeNotCamelCaps', $data);
            } else {
                $error = 'Method name "%s" is not in camel caps format';
                $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $errorData);
            }
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

        if (ltrim($functionName, '_') === '') {
            // Ignore special functions.
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

        // Function names can be in two parts; the package name and
        // the function name.
        $packagePart   = '';
        $underscorePos = strrpos($functionName, '_');
        if ($underscorePos === false) {
            $camelCapsPart = $functionName;
        } else {
            $packagePart   = substr($functionName, 0, $underscorePos);
            $camelCapsPart = substr($functionName, ($underscorePos + 1));

            // We don't care about _'s on the front.
            $packagePart = ltrim($packagePart, '_');
        }

        // If it has a package part, make sure the first letter is a capital.
        if ($packagePart !== '') {
            if ($functionName{0} === '_') {
                $error = 'Function name "%s" is invalid; only private methods should be prefixed with an underscore';
                $phpcsFile->addError($error, $stackPtr, 'FunctionUnderscore', $errorData);
            }

            if ($functionName{0} !== strtoupper($functionName{0})) {
                $error = 'Function name "%s" is prefixed with a package name but does not begin with a capital letter';
                $phpcsFile->addError($error, $stackPtr, 'FunctionNoCapital', $errorData);
            }
        }

        // If it doesn't have a camel caps part, it's not valid.
        if (trim($camelCapsPart) === '') {
            $error = 'Function name "%s" is not valid; name appears incomplete';
            $phpcsFile->addError($error, $stackPtr, 'FunctionInvalid', $errorData);
            return;
        }

        $validName        = true;
        $newPackagePart   = $packagePart;
        $newCamelCapsPart = $camelCapsPart;

        // Every function must have a camel caps part, so check that first.
        if (Common::isCamelCaps($camelCapsPart, false, true, false) === false) {
            $validName        = false;
            $newCamelCapsPart = strtolower($camelCapsPart{0}).substr($camelCapsPart, 1);
        }

        if ($packagePart !== '') {
            // Check that each new word starts with a capital.
            $nameBits = explode('_', $packagePart);
            $nameBits = array_filter($nameBits);
            foreach ($nameBits as $bit) {
                if ($bit{0} !== strtoupper($bit{0})) {
                    $newPackagePart = '';
                    foreach ($nameBits as $bit) {
                        $newPackagePart .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }

                    $validName = false;
                    break;
                }
            }
        }

        if ($validName === false) {
            if ($newPackagePart === '') {
                $newName = $newCamelCapsPart;
            } else {
                $newName = rtrim($newPackagePart, '_').'_'.$newCamelCapsPart;
            }

            $error  = 'Function name "%s" is invalid; consider "%s" instead';
            $data   = $errorData;
            $data[] = $newName;
            $phpcsFile->addError($error, $stackPtr, 'FunctionNameInvalid', $data);
        }

    }//end processTokenOutsideScope()


}//end class
