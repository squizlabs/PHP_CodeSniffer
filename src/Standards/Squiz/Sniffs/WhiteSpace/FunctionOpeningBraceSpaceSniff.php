<?php
/**
 * Checks that there is no empty line after the opening brace of a function.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class FunctionOpeningBraceSpaceSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
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

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            // Probably an interface or abstract method.
            return;
        }

        $openBrace   = $tokens[$stackPtr]['scope_opener'];
        $nextContent = $phpcsFile->findNext(T_WHITESPACE, ($openBrace + 1), null, true);

        if ($nextContent === $tokens[$stackPtr]['scope_closer']) {
             // The next bit of content is the closing brace, so this
             // is an empty function and should have a blank line
             // between the opening and closing braces.
            return;
        }

        $braceLine = $tokens[$openBrace]['line'];
        $nextLine  = $tokens[$nextContent]['line'];

        $found = ($nextLine - $braceLine - 1);
        if ($found > 0) {
            $error = 'Expected 0 blank lines after opening function brace; %s found';
            $data  = [$found];
            $fix   = $phpcsFile->addFixableError($error, $openBrace, 'SpacingAfter', $data);
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($openBrace + 1); $i < $nextContent; $i++) {
                    if ($tokens[$i]['line'] === $nextLine) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->addNewline($openBrace);
                $phpcsFile->fixer->endChangeset();
            }
        }

    }//end process()


}//end class
