<?php
/**
 * Ensures a file declares new symbols and causes no other side effects, or executes logic with side effects, but not both.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR1\Sniffs\Files;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class SideEffectsSniff implements Sniff
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
     *                                               the token stack.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $result = $this->searchForConflict($phpcsFile, 0, ($phpcsFile->numTokens - 1), $tokens);

        if ($result['symbol'] !== null && $result['effect'] !== null) {
            $error = 'A file should declare new symbols (classes, functions, constants, etc.) and cause no other side effects, or it should execute logic with side effects, but should not do both. The first symbol is defined on line %s and the first side effect is on line %s.';
            $data  = [
                $tokens[$result['symbol']]['line'],
                $tokens[$result['effect']]['line'],
            ];
            $phpcsFile->addWarning($error, 0, 'FoundWithSymbols', $data);
            $phpcsFile->recordMetric($stackPtr, 'Declarations and side effects mixed', 'yes');
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Declarations and side effects mixed', 'no');
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


    /**
     * Searches for symbol declarations and side effects.
     *
     * Returns the positions of both the first symbol declared and the first
     * side effect in the file. A NULL value for either indicates nothing was
     * found.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $start     The token to start searching from.
     * @param int                         $end       The token to search to.
     * @param array                       $tokens    The stack of tokens that make up
     *                                               the file.
     *
     * @return array
     */
    private function searchForConflict($phpcsFile, $start, $end, $tokens)
    {
        $symbols = [
            T_CLASS     => T_CLASS,
            T_INTERFACE => T_INTERFACE,
            T_TRAIT     => T_TRAIT,
            T_FUNCTION  => T_FUNCTION,
        ];

        $conditions = [
            T_IF     => T_IF,
            T_ELSE   => T_ELSE,
            T_ELSEIF => T_ELSEIF,
        ];

        $firstSymbol = null;
        $firstEffect = null;
        for ($i = $start; $i <= $end; $i++) {
            // Respect phpcs:disable comments.
            if ($tokens[$i]['code'] === T_PHPCS_DISABLE
                && (empty($tokens[$i]['sniffCodes']) === true
                || isset($tokens[$i]['sniffCodes']['PSR1']) === true
                || isset($tokens[$i]['sniffCodes']['PSR1.Files']) === true
                || isset($tokens[$i]['sniffCodes']['PSR1.Files.SideEffects']) === true)
            ) {
                do {
                    $i = $phpcsFile->findNext(T_PHPCS_ENABLE, ($i + 1));
                } while ($i !== false
                    && empty($tokens[$i]['sniffCodes']) === false
                    && isset($tokens[$i]['sniffCodes']['PSR1']) === false
                    && isset($tokens[$i]['sniffCodes']['PSR1.Files']) === false
                    && isset($tokens[$i]['sniffCodes']['PSR1.Files.SideEffects']) === false);

                if ($i === false) {
                    // The entire rest of the file is disabled,
                    // so return what we have so far.
                    break;
                }

                continue;
            }

            // Ignore whitespace and comments.
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            // Ignore PHP tags.
            if ($tokens[$i]['code'] === T_OPEN_TAG
                || $tokens[$i]['code'] === T_CLOSE_TAG
            ) {
                continue;
            }

            // Ignore shebang.
            if (substr($tokens[$i]['content'], 0, 2) === '#!') {
                continue;
            }

            // Ignore entire namespace, declare, const and use statements.
            if ($tokens[$i]['code'] === T_NAMESPACE
                || $tokens[$i]['code'] === T_USE
                || $tokens[$i]['code'] === T_DECLARE
                || $tokens[$i]['code'] === T_CONST
            ) {
                if (isset($tokens[$i]['scope_opener']) === true) {
                    $i = $tokens[$i]['scope_closer'];
                } else {
                    $semicolon = $phpcsFile->findNext(T_SEMICOLON, ($i + 1));
                    if ($semicolon !== false) {
                        $i = $semicolon;
                    }
                }

                continue;
            }

            // Ignore function/class prefixes.
            if (isset(Tokens::$methodPrefixes[$tokens[$i]['code']]) === true) {
                continue;
            }

            // Ignore anon classes.
            if ($tokens[$i]['code'] === T_ANON_CLASS) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            }

            // Detect and skip over symbols.
            if (isset($symbols[$tokens[$i]['code']]) === true
                && isset($tokens[$i]['scope_closer']) === true
            ) {
                if ($firstSymbol === null) {
                    $firstSymbol = $i;
                }

                $i = $tokens[$i]['scope_closer'];
                continue;
            } else if ($tokens[$i]['code'] === T_STRING
                && strtolower($tokens[$i]['content']) === 'define'
            ) {
                $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($i - 1), null, true);
                if ($tokens[$prev]['code'] !== T_OBJECT_OPERATOR
                    && $tokens[$prev]['code'] !== T_DOUBLE_COLON
                ) {
                    if ($firstSymbol === null) {
                        $firstSymbol = $i;
                    }

                    $semicolon = $phpcsFile->findNext(T_SEMICOLON, ($i + 1));
                    if ($semicolon !== false) {
                        $i = $semicolon;
                    }

                    continue;
                }
            }//end if

            // Conditional statements are allowed in symbol files as long as the
            // contents is only a symbol definition. So don't count these as effects
            // in this case.
            if (isset($conditions[$tokens[$i]['code']]) === true) {
                if (isset($tokens[$i]['scope_opener']) === false) {
                    // Probably an "else if", so just ignore.
                    continue;
                }

                $result = $this->searchForConflict(
                    $phpcsFile,
                    ($tokens[$i]['scope_opener'] + 1),
                    ($tokens[$i]['scope_closer'] - 1),
                    $tokens
                );

                if ($result['symbol'] !== null) {
                    if ($firstSymbol === null) {
                        $firstSymbol = $result['symbol'];
                    }

                    if ($result['effect'] !== null) {
                        // Found a conflict.
                        $firstEffect = $result['effect'];
                        break;
                    }
                }

                if ($firstEffect === null) {
                    $firstEffect = $result['effect'];
                }

                $i = $tokens[$i]['scope_closer'];
                continue;
            }//end if

            if ($firstEffect === null) {
                $firstEffect = $i;
            }

            if ($firstSymbol !== null) {
                // We have a conflict we have to report, so no point continuing.
                break;
            }
        }//end for

        return [
            'symbol' => $firstSymbol,
            'effect' => $firstEffect,
        ];

    }//end searchForConflict()


}//end class
