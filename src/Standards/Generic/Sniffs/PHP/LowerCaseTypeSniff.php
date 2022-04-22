<?php
/**
 * Checks that all PHP types are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class LowerCaseTypeSniff implements Sniff
{

    /**
     * Native types supported by PHP.
     *
     * @var array
     */
    private $phpTypes = [
        'self'     => true,
        'parent'   => true,
        'array'    => true,
        'callable' => true,
        'bool'     => true,
        'float'    => true,
        'int'      => true,
        'string'   => true,
        'iterable' => true,
        'void'     => true,
        'object'   => true,
        'mixed'    => true,
        'static'   => true,
        'false'    => true,
        'null'     => true,
        'never'    => true,
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $tokens   = Tokens::$castTokens;
        $tokens[] = T_FUNCTION;
        $tokens[] = T_CLOSURE;
        $tokens[] = T_VARIABLE;
        return $tokens;

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset(Tokens::$castTokens[$tokens[$stackPtr]['code']]) === true) {
            // A cast token.
            $this->processType(
                $phpcsFile,
                $stackPtr,
                $tokens[$stackPtr]['content'],
                'PHP type casts must be lowercase; expected "%s" but found "%s"',
                'TypeCastFound'
            );

            return;
        }

        /*
         * Check property types.
         */

        if ($tokens[$stackPtr]['code'] === T_VARIABLE) {
            try {
                $props = $phpcsFile->getMemberProperties($stackPtr);
            } catch (RuntimeException $e) {
                // Not an OO property.
                return;
            }

            // Strip off potential nullable indication.
            $type = ltrim($props['type'], '?');

            if ($type !== '') {
                $error     = 'PHP property type declarations must be lowercase; expected "%s" but found "%s"';
                $errorCode = 'PropertyTypeFound';

                if ($props['type_token'] === T_TYPE_INTERSECTION) {
                    // Intersection types don't support simple types.
                } else if (strpos($type, '|') !== false) {
                    $this->processUnionType(
                        $phpcsFile,
                        $props['type_token'],
                        $props['type_end_token'],
                        $error,
                        $errorCode
                    );
                } else if (isset($this->phpTypes[strtolower($type)]) === true) {
                    $this->processType($phpcsFile, $props['type_token'], $type, $error, $errorCode);
                }
            }

            return;
        }//end if

        /*
         * Check function return type.
         */

        $props = $phpcsFile->getMethodProperties($stackPtr);

        // Strip off potential nullable indication.
        $returnType = ltrim($props['return_type'], '?');

        if ($returnType !== '') {
            $error     = 'PHP return type declarations must be lowercase; expected "%s" but found "%s"';
            $errorCode = 'ReturnTypeFound';

            if ($props['return_type_token'] === T_TYPE_INTERSECTION) {
                // Intersection types don't support simple types.
            } else if (strpos($returnType, '|') !== false) {
                $this->processUnionType(
                    $phpcsFile,
                    $props['return_type_token'],
                    $props['return_type_end_token'],
                    $error,
                    $errorCode
                );
            } else if (isset($this->phpTypes[strtolower($returnType)]) === true) {
                $this->processType($phpcsFile, $props['return_type_token'], $returnType, $error, $errorCode);
            }
        }

        /*
         * Check function parameter types.
         */

        $params = $phpcsFile->getMethodParameters($stackPtr);
        if (empty($params) === true) {
            return;
        }

        foreach ($params as $param) {
            // Strip off potential nullable indication.
            $typeHint = ltrim($param['type_hint'], '?');

            if ($typeHint !== '') {
                $error     = 'PHP parameter type declarations must be lowercase; expected "%s" but found "%s"';
                $errorCode = 'ParamTypeFound';

                if ($param['type_hint_token'] === T_TYPE_INTERSECTION) {
                    // Intersection types don't support simple types.
                } else if (strpos($typeHint, '|') !== false) {
                    $this->processUnionType(
                        $phpcsFile,
                        $param['type_hint_token'],
                        $param['type_hint_end_token'],
                        $error,
                        $errorCode
                    );
                } else if (isset($this->phpTypes[strtolower($typeHint)]) === true) {
                    $this->processType($phpcsFile, $param['type_hint_token'], $typeHint, $error, $errorCode);
                }
            }
        }//end foreach

    }//end process()


    /**
     * Processes a union type declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile     The file being scanned.
     * @param int                         $typeDeclStart The position of the start of the type token.
     * @param int                         $typeDeclEnd   The position of the end of the type token.
     * @param string                      $error         Error message template.
     * @param string                      $errorCode     The error code.
     *
     * @return void
     */
    protected function processUnionType(File $phpcsFile, $typeDeclStart, $typeDeclEnd, $error, $errorCode)
    {
        $tokens  = $phpcsFile->getTokens();
        $current = $typeDeclStart;

        do {
            $endOfType = $phpcsFile->findNext(T_TYPE_UNION, $current, $typeDeclEnd);
            if ($endOfType === false) {
                // This must be the last type in the union.
                $endOfType = ($typeDeclEnd + 1);
            }

            $hasNsSep = $phpcsFile->findNext(T_NS_SEPARATOR, $current, $endOfType);
            if ($hasNsSep !== false) {
                // Multi-token class based type. Ignore.
                $current = ($endOfType + 1);
                continue;
            }

            // Type consisting of a single token.
            $startOfType = $phpcsFile->findNext(Tokens::$emptyTokens, $current, $endOfType, true);
            if ($startOfType === false) {
                // Parse error.
                return;
            }

            $type = $tokens[$startOfType]['content'];
            if (isset($this->phpTypes[strtolower($type)]) === true) {
                $this->processType($phpcsFile, $startOfType, $type, $error, $errorCode);
            }

            $current = ($endOfType + 1);
        } while ($current <= $typeDeclEnd);

    }//end processUnionType()


    /**
     * Processes a type cast or a singular type declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the type token.
     * @param string                      $type      The type found.
     * @param string                      $error     Error message template.
     * @param string                      $errorCode The error code.
     *
     * @return void
     */
    protected function processType(File $phpcsFile, $stackPtr, $type, $error, $errorCode)
    {
        $typeLower = strtolower($type);

        if ($typeLower === $type) {
            $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'lower');
            return;
        }

        if ($type === strtoupper($type)) {
            $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'upper');
        } else {
            $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'mixed');
        }

        $data = [
            $typeLower,
            $type,
        ];

        $fix = $phpcsFile->addFixableError($error, $stackPtr, $errorCode, $data);
        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($stackPtr, $typeLower);
        }

    }//end processType()


}//end class
