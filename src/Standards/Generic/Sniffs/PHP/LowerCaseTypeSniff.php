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
         * Check function return type.
         */

        $props = $phpcsFile->getMethodProperties($stackPtr);

        // Strip off potential nullable indication.
        $returnType      = ltrim($props['return_type'], '?');
        $returnTypeLower = strtolower($returnType);

        if ($returnType !== ''
            && isset($this->phpTypes[$returnTypeLower]) === true
        ) {
            $error     = 'PHP return type declarations must be lowercase; expected "%s" but found "%s"';
            $errorCode = 'ReturnTypeFound';

            $this->processType($phpcsFile, $props['return_type_token'], $returnType, $error, $errorCode);
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
            $typeHint      = ltrim($param['type_hint'], '?');
            $typeHintLower = strtolower($typeHint);

            if ($typeHint !== ''
                && isset($this->phpTypes[$typeHintLower]) === true
            ) {
                $error     = 'PHP parameter type declarations must be lowercase; expected "%s" but found "%s"';
                $errorCode = 'ParamTypeFound';

                $this->processType($phpcsFile, $param['type_hint_token'], $typeHint, $error, $errorCode);
            }
        }

    }//end process()


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
