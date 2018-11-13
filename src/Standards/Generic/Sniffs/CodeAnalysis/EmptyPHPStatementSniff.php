<?php
/**
 * Checks against empty PHP statements.
 *
 * - Check against two semi-colons with no executable code in between.
 * - Check against an empty PHP open - close tag combination.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class EmptyPHPStatementSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [
            T_SEMICOLON,
            T_CLOSE_TAG,
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

        switch ($tokens[$stackPtr]['type']) {
        // Detect `something();;`.
        case 'T_SEMICOLON':
            $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

            if ($prevNonEmpty === false
                || ($tokens[$prevNonEmpty]['code'] !== T_SEMICOLON
                && $tokens[$prevNonEmpty]['code'] !== T_OPEN_TAG
                && $tokens[$prevNonEmpty]['code'] !== T_OPEN_TAG_WITH_ECHO)
            ) {
                return;
            }

            if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
                $nested     = $tokens[$stackPtr]['nested_parenthesis'];
                $lastCloser = array_pop($nested);
                if (isset($tokens[$lastCloser]['parenthesis_owner']) === true
                    && $tokens[$tokens[$lastCloser]['parenthesis_owner']]['code'] === T_FOR
                ) {
                    // Empty for() condition.
                    return;
                }
            }

            $fix = $phpcsFile->addFixableWarning(
                'Empty PHP statement detected: superfluous semi-colon.',
                $stackPtr,
                'SemicolonWithoutCodeDetected'
            );
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();

                if ($tokens[$prevNonEmpty]['code'] === T_OPEN_TAG
                    || $tokens[$prevNonEmpty]['code'] === T_OPEN_TAG_WITH_ECHO
                ) {
                    // Check for superfluous whitespace after the semi-colon which will be
                    // removed as the `<?php ` open tag token already contains whitespace,
                    // either a space or a new line.
                    if ($tokens[($stackPtr + 1)]['code'] === T_WHITESPACE) {
                        $replacement = str_replace(' ', '', $tokens[($stackPtr + 1)]['content']);
                        $phpcsFile->fixer->replaceToken(($stackPtr + 1), $replacement);
                    }
                }

                for ($i = $stackPtr; $i > $prevNonEmpty; $i--) {
                    if ($tokens[$i]['code'] !== T_SEMICOLON
                        && $tokens[$i]['code'] !== T_WHITESPACE
                    ) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }//end if
            break;

        // Detect `<?php ? >`.
        case 'T_CLOSE_TAG':
            $prevNonEmpty = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);

            if ($prevNonEmpty === false
                || ($tokens[$prevNonEmpty]['code'] !== T_OPEN_TAG
                && $tokens[$prevNonEmpty]['code'] !== T_OPEN_TAG_WITH_ECHO)
            ) {
                return;
            }

            $fix = $phpcsFile->addFixableWarning(
                'Empty PHP open/close tag combination detected.',
                $prevNonEmpty,
                'EmptyPHPOpenCloseTagsDetected'
            );
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();

                for ($i = $prevNonEmpty; $i <= $stackPtr; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
            break;

        default:
            // Deliberately left empty.
            break;
        }//end switch

    }//end process()


}//end class
