<?php
/**
 * Detects for-loops that can be simplified to a while-loop.
 *
 * This rule is based on the PMD rule catalog. Detects for-loops that can be
 * simplified as a while-loop.
 *
 * <code>
 * class Foo
 * {
 *     public function bar($x)
 *     {
 *         for (;true;) true; // No Init or Update part, may as well be: while (true)
 *     }
 * }
 * </code>
 *
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2014 Manuel Pichler. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ForLoopShouldBeWhileLoopSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_FOR];

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

        $parts = [
            0,
            0,
            0,
        ];
        $index = 0;

        for (; $next <= $end; ++$next) {
            $code = $tokens[$next]['code'];
            if ($code === T_SEMICOLON) {
                ++$index;
            } else if (isset(Tokens::$emptyTokens[$code]) === false) {
                ++$parts[$index];
            }
        }

        if ($parts[0] === 0 && $parts[2] === 0 && $parts[1] > 0) {
            $error = 'This FOR loop can be simplified to a WHILE loop';
            $phpcsFile->addWarning($error, $stackPtr, 'CanSimplify');
        }

    }//end process()


}//end class
