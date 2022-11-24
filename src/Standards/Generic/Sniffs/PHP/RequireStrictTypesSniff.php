<?php
/**
 * Checks that the strict_types has been declared.
 *
 * @author    Sertan Danis <sdanis@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class RequireStrictTypesSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $declare = $phpcsFile->findNext(T_DECLARE, ($stackPtr + 1));

        $found = false;

        if ($declare !== false) {
            if (isset($tokens[$declare]['parenthesis_opener'], $tokens[$declare]['parenthesis_closer']) === false) {
                // Live coding, ignore for now.
                return $phpcsFile->numTokens;
            }

            $next = $tokens[$declare]['parenthesis_opener'];

            do {
                $next = $phpcsFile->findNext(
                    Tokens::$emptyTokens,
                    ($next + 1),
                    $tokens[$declare]['parenthesis_closer'],
                    true
                );

                if ($next !== false
                    && $tokens[$next]['code'] === T_STRING
                    && strtolower($tokens[$next]['content']) === 'strict_types'
                ) {
                    // There is a strict types declaration.
                    $found = true;
                    break;
                }

                $next = $phpcsFile->findNext(T_COMMA, ($next + 1), $tokens[$declare]['parenthesis_closer']);
            } while ($next !== false && $next < $tokens[$declare]['parenthesis_closer']);
        }//end if

        if ($found === false) {
            $error = 'Missing required strict_types declaration';
            $phpcsFile->addError($error, $stackPtr, 'MissingDeclaration');
        }

        // Skip the rest of the file so we don't pick up additional
        // open tags, typically embedded in HTML.
        return $phpcsFile->numTokens;

    }//end process()


}//end class
