<?php
/**
 * Detects unconditional if- and elseif-statements.
 *
 * This rule is based on the PMD rule catalogue. The Unconditional If Statement
 * sniff detects statement conditions that are only set to one of the constant
 * values <b>true</b> or <b>false</b>
 *
 * <code>
 * class Foo
 * {
 *     public function close()
 *     {
 *         if (true)
 *         {
 *             // ...
 *         }
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

class UnconditionalIfStatementSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [
            T_IF,
            T_ELSEIF,
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
        $token  = $tokens[$stackPtr];

        // Skip if statement without body.
        if (isset($token['parenthesis_opener']) === false) {
            return;
        }

        $next = ++$token['parenthesis_opener'];
        $end  = --$token['parenthesis_closer'];

        $goodCondition = false;
        for (; $next <= $end; ++$next) {
            $code = $tokens[$next]['code'];

            if (isset(Tokens::$emptyTokens[$code]) === true) {
                continue;
            } else if ($code !== T_TRUE && $code !== T_FALSE) {
                $goodCondition = true;
            }
        }

        if ($goodCondition === false) {
            $error = 'Avoid IF statements that are always true or false';
            $phpcsFile->addWarning($error, $stackPtr, 'Found');
        }

    }//end process()


}//end class
