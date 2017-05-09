<?php
/**
 * Detects for-loops that use a function call in the test expression.
 *
 * This rule is based on the PMD rule catalog. Detects for-loops that use a
 * function call in the test expression.
 *
 * <code>
 * class Foo
 * {
 *     public function bar($x)
 *     {
 *         $a = array(1, 2, 3, 4);
 *         for ($i = 0; $i < count($a); $i++) {
 *              $a[$i] *= $i;
 *         }
 *     }
 * }
 * </code>
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2014 Manuel Pichler. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ForLoopWithTestFunctionCallSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return array(T_FOR);

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
        $token  = $tokens[$stackPtr];

        // Skip invalid statement.
        if (isset($token['parenthesis_opener']) === false) {
            return;
        }

        $next = ++$token['parenthesis_opener'];
        $end  = --$token['parenthesis_closer'];

        $position = 0;

        for (; $next <= $end; ++$next) {
            $code = $tokens[$next]['code'];
            if ($code === T_SEMICOLON) {
                ++$position;
            }

            if ($position < 1) {
                continue;
            } else if ($position > 1) {
                break;
            } else if ($code !== T_VARIABLE && $code !== T_STRING) {
                continue;
            }

            // Find next non empty token, if it is a open curly brace we have a
            // function call.
            $index = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);

            if ($tokens[$index]['code'] === T_OPEN_PARENTHESIS) {
                $error = 'Avoid function calls in a FOR loop test part';
                $phpcsFile->addWarning($error, $stackPtr, 'NotAllowed');
                break;
            }
        }//end for

    }//end process()


}//end class
