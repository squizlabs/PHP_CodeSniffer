<?php
/**
 * Checks that all PHP types are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class LowerCaseTypeSniff implements Sniff
{


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
            if (strtolower($tokens[$stackPtr]['content']) !== $tokens[$stackPtr]['content']) {
                if ($tokens[$stackPtr]['content'] === strtoupper($tokens[$stackPtr]['content'])) {
                    $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'upper');
                } else {
                    $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'mixed');
                }

                $error = 'PHP type casts must be lowercase; expected "%s" but found "%s"';
                $data  = [
                    strtolower($tokens[$stackPtr]['content']),
                    $tokens[$stackPtr]['content'],
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'TypeCastFound', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($stackPtr, strtolower($tokens[$stackPtr]['content']));
                }
            } else {
                $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'lower');
            }//end if

            return;
        }//end if

        $phpTypes = [
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
        ];

        $props = $phpcsFile->getMethodProperties($stackPtr);

        // Strip off potential nullable indication.
        $returnType      = ltrim($props['return_type'], '?');
        $returnTypeLower = strtolower($returnType);

        if ($returnType !== ''
            && isset($phpTypes[$returnTypeLower]) === true
        ) {
            // A function return type.
            if ($returnTypeLower !== $returnType) {
                if ($returnType === strtoupper($returnType)) {
                    $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'upper');
                } else {
                    $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'mixed');
                }

                $error = 'PHP return type declarations must be lowercase; expected "%s" but found "%s"';
                $token = $props['return_type_token'];
                $data  = [
                    $returnTypeLower,
                    $returnType,
                ];

                $fix = $phpcsFile->addFixableError($error, $token, 'ReturnTypeFound', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($token, $returnTypeLower);
                }
            } else {
                $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'lower');
            }//end if
        }//end if

        $params = $phpcsFile->getMethodParameters($stackPtr);
        if (empty($params) === true) {
            return;
        }

        foreach ($params as $param) {
            // Strip off potential nullable indication.
            $typeHint      = ltrim($param['type_hint'], '?');
            $typeHintLower = strtolower($typeHint);

            if ($typeHint !== ''
                && isset($phpTypes[$typeHintLower]) === true
            ) {
                // A function return type.
                if ($typeHintLower !== $typeHint) {
                    if ($typeHint === strtoupper($typeHint)) {
                        $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'upper');
                    } else {
                        $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'mixed');
                    }

                    $error = 'PHP parameter type declarations must be lowercase; expected "%s" but found "%s"';
                    $token = $param['type_hint_token'];
                    $data  = [
                        $typeHintLower,
                        $typeHint,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $token, 'ParamTypeFound', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken($token, $typeHintLower);
                    }
                } else {
                    $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'lower');
                }//end if
            }//end if
        }//end foreach

    }//end process()


}//end class
