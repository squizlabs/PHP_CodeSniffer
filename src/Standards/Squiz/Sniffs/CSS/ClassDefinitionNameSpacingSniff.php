<?php
/**
 * Ensure there are no blank lines between the names of classes/IDs.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\CSS;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ClassDefinitionNameSpacingSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['CSS'];


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_OPEN_CURLY_BRACKET];

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['bracket_closer']) === false) {
            // Syntax error or live coding, bow out.
            return;
        }

        // Do not check nested style definitions as, for example, in @media style rules.
        $nested = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, ($stackPtr + 1), $tokens[$stackPtr]['bracket_closer']);
        if ($nested !== false) {
            return;
        }

        // Find the first blank line before this opening brace, unless we get
        // to another style definition, comment or the start of the file.
        $endTokens  = [
            T_OPEN_CURLY_BRACKET  => T_OPEN_CURLY_BRACKET,
            T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
            T_OPEN_TAG            => T_OPEN_TAG,
        ];
        $endTokens += Tokens::$commentTokens;

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        $foundContent = false;
        $currentLine  = $tokens[$prev]['line'];
        for ($i = ($stackPtr - 1); $i >= 0; $i--) {
            if (isset($endTokens[$tokens[$i]['code']]) === true) {
                break;
            }

            if ($tokens[$i]['line'] === $currentLine) {
                if ($tokens[$i]['code'] !== T_WHITESPACE) {
                    $foundContent = true;
                }

                continue;
            }

            // We changed lines.
            if ($foundContent === false) {
                // Before we throw an error, make sure we are not looking
                // at a gap before the style definition.
                $prev = $phpcsFile->findPrevious(T_WHITESPACE, $i, null, true);
                if ($prev !== false
                    && isset($endTokens[$tokens[$prev]['code']]) === false
                ) {
                    $error = 'Blank lines are not allowed between class names';
                    $phpcsFile->addError($error, ($i + 1), 'BlankLinesFound');
                }

                break;
            }

            $foundContent = false;
            $currentLine  = $tokens[$i]['line'];
        }//end for

    }//end process()


}//end class
