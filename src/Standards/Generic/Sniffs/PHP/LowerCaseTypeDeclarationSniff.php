<?php
/**
 * Verify that type declarations are lowercase.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class LowerCaseTypeDeclarationSniff implements Sniff
{

    /**
     * A list of parameter type declarations to examine.
     *
     * Used to be able to distinguish the target type
     * declarations from class name type declarations.
     *
     * Class based parameter type declarations were introduced in PHP 5.0.
     * All other types have become available after.
     *
     * The key is a lowercase type hint, the value is the token code
     * this type declaration will have in the token stack.
     *
     * @var array
     */
    protected $parameterTypes = [
        'self'     => T_SELF,
        'parent'   => T_PARENT,

        // PHP 5.1.
        'array'    => T_ARRAY_HINT,

        // PHP 5.4.
        'callable' => T_CALLABLE,

        // PHP 7.0.
        'bool'     => T_STRING,
        'float'    => T_STRING,
        'int'      => T_STRING,
        'string'   => T_STRING,

        // PHP 7.1.
        'iterable' => T_STRING,

        // PHP 7.2.
        'object'   => T_STRING,
    ];

    /**
     * A list of return type declarations to examine.
     *
     * Used to be able to distinguish the target type
     * declarations from class name type declarations.
     *
     * Return declarations were introduced in PHP 7.0.
     * Some additional types have become available after.
     *
     * @var array
     */
    protected $returnTypes = [
        'array'    => true,
        'bool'     => true,
        'callable' => true,
        'float'    => true,
        'int'      => true,
        'parent'   => true,
        'self'     => true,
        'string'   => true,

        // PHP 7.1.
        'iterable' => true,
        'void'     => true,

        // PHP 7.2.
        'object'   => true,
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_FUNCTION,
            T_CLOSURE,
            T_RETURN_TYPE,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_FUNCTION
            || $tokens[$stackPtr]['code'] === T_CLOSURE
        ) {
            // Get all parameters from method signature.
            $paramNames = $phpcsFile->getMethodParameters($stackPtr);
            if (empty($paramNames) === true) {
                return;
            }

            foreach ($paramNames as $param) {
                if ($param['type_hint'] === '') {
                    continue;
                }

                // Strip off potential nullable indication.
                $typeHint   = ltrim($param['type_hint'], '?');
                $typeHintLC = strtolower($typeHint);

                if (isset($this->parameterTypes[$typeHintLC]) === true && $typeHintLC !== $typeHint) {
                    $typePtr = $phpcsFile->findPrevious(
                        $this->parameterTypes[$typeHintLC],
                        ($param['token'] - 1),
                        $stackPtr,
                        false,
                        $typeHint,
                        true
                    );
                    if ($typePtr === false) {
                        continue;
                    }

                    $error = 'Parameter type declarations must be lowercase; expected "%s" but found "%s"';
                    $data  = [
                        strtolower($param['type_hint']),
                        $param['type_hint'],
                    ];

                    $fix = $phpcsFile->addFixableError($error, $typePtr, 'ParameterTypeFound', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken($typePtr, $typeHintLC);
                    }
                }//end if
            }//end foreach
        } else {
            // Return type.
            $content   = $tokens[$stackPtr]['content'];
            $contentLC = strtolower($content);

            if (isset($this->returnTypes[$contentLC]) === true && $contentLC !== $content) {
                $error = 'Return type declarations must be lowercase; expected "%s" but found "%s"';
                $data  = [
                    $contentLC,
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ReturnTypeFound', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($stackPtr, $contentLC);
                }
            }
        }//end if

    }//end process()


}//end class
