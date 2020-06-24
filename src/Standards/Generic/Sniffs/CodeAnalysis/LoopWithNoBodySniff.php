<?php
/**
 * Detects loops (for and foreach) that do not have a body
 * due to a semicolon
 *
 * The following scenarios most probably are typos.
 *
 * <code>
 * foreach ($array as $for => $value);
 * {
 *   doSomething(); // doSomething will not be looped
 * }
 *
 * for ($i =0; $i<10; $i++);
 * {
 *   doSomething(); // doSomething will not be looped
 * }
 * </code>
 *
 * @author    George Mponos <gmponos@gmail.com>
 * @copyright 2020 George Mponos. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class LoopWithNoBodySniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [
            T_FOR,
            T_FOREACH,
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
        if (isset($token['scope_opener']) === false) {
            $phpcsFile->addError('The `%s` loop statement does not have a body', $stackPtr, 'NoBody', [$token['content']]);
        }

    }//end process()


}//end class
