<?php
/**
 * Checks if Control Structures Brackets are on a line by their own
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2018 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\ControlStructures;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ControlStructuresBracketsNewLineSniff implements Sniff
{

    /**
     * The number of spaces code should be indented.
     *
     * @var integer
     */
    public $indent = 4;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_IF,
            T_ELSEIF,
            T_ELSE,
            T_FOREACH,
            T_FOR,
            T_SWITCH,
            T_DO,
            T_WHILE,
            T_TRY,
            T_CATCH,
            T_FINALLY,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param integer                     $stackPtr  The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens      = $phpcsFile->getTokens();
        $errorData   = [strtolower($tokens[$stackPtr]['content'])];
        $openBrace   = $tokens[$stackPtr]['scope_opener'];
        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($openBrace - 1), $stackPtr, true);
        $controlStructureLine = $tokens[$lastContent]['line'];
        $braceLine            = $tokens[$openBrace]['line'];

        if ($braceLine === $controlStructureLine) {
            $phpcsFile->recordMetric($stackPtr, 'Control Structure opening brace placement', 'same line');
            $error = 'Opening brace of a %s must be on the line after the definition';
            $fix   = $phpcsFile->addFixableError($error, $openBrace, 'OpenBraceNewLine', $errorData);

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();

                if ($tokens[($openBrace - 1)]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken(($openBrace - 1), '');
                }

                $phpcsFile->fixer->addNewlineBefore($openBrace);
                $phpcsFile->fixer->endChangeset();
            }

            return;
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Control Structure opening brace placement', 'new line');

            if ($braceLine > ($controlStructureLine + 1)) {
                $error = 'Opening brace of a %s must be on the line following the %s declaration.; Found %s line(s).';
                $data  = [
                    $tokens[$stackPtr]['content'],
                    $tokens[$stackPtr]['content'],
                    ($braceLine - $controlStructureLine - 1),
                ];
                $fix   = $phpcsFile->addFixableError($error, $openBrace, 'OpenBraceWrongLine', $data);

                if ($fix === true) {
                          $phpcsFile->fixer->beginChangeset();

                    for ($i = ($openBrace - 1); $i > $lastContent; $i--) {
                        if ($tokens[$i]['line'] === ($tokens[$openBrace]['line'] + 1)) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                          $phpcsFile->fixer->endChangeset();
                }

                return;
            }//end if
        }//end if

        if ($tokens[($openBrace + 1)]['content'] !== $phpcsFile->eolChar) {
            $error = 'Opening %s brace must be on a line by itself.';
            $fix   = $phpcsFile->addFixableError($error, $openBrace, 'OpenBraceNotAlone', $errorData);

            if ($fix === true) {
                $phpcsFile->fixer->addNewline($openBrace);
            }
        }

        if ($tokens[($openBrace - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($openBrace - 1)]['content'];

            if ($prevContent === $phpcsFile->eolChar) {
                $spaces = 0;
            } else {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = 0;

                // A tab is only counted with strlen as 1 character but we want to count,
                // the number of spaces so add 4 characters for a tab otherwise the strlen.
                for ($i = 0; $length = strlen($blankSpace), $i < $length; $i++) {
                    if ($blankSpace[$i] === "\t") {
                        $spaces += $this->indent;
                    } else {
                        $spaces += strlen($blankSpace[$i]);
                    }
                }
            }//end if

            $nested = 0;

            // Take into account any nested parenthesis that don't contribute to the level (often required for
            // closures and anonymous classes)
            if (array_key_exists('nested_parenthesis', $tokens[$stackPtr])) {
                $nested = count($tokens[$stackPtr]['nested_parenthesis']);
            }//end if

			$expected = ($tokens[$stackPtr]['level'] + $nested) * $this->indent;

            // We need to divide by 4 here since there is a space vs tab intent in the check vs token.
            $expected /= $this->indent;
            $spaces   /= $this->indent;

            if ($spaces !== $expected) {
                $error = 'Expected %s tabs before opening brace; %s found';
                $data  = [
                    $expected,
                    $spaces,
                ];
                $fix   = $phpcsFile->addFixableError($error, $openBrace, 'SpaceBeforeBrace', $data);

                if ($fix === true) {
                    $indent = str_repeat("\t", $expected);

                    if ($spaces === 0) {
                        $phpcsFile->fixer->addContentBefore($openBrace, $indent);
                    } else {
                        $phpcsFile->fixer->replaceToken(($openBrace - 1), $indent);
                    }
                }
            }//end if
        }//end if

        // A single newline after opening brace (i.e. brace in on a line by itself), remove extra newlines.
        if (isset($tokens[$stackPtr]['scope_opener']) === true) {
            $opener = $tokens[$stackPtr]['scope_opener'];

            for ($next = ($opener + 1); $next < $phpcsFile->numTokens; $next++) {
                $code = $tokens[$next]['code'];

                if ($code === T_WHITESPACE) {
                    continue;
                }

                // Skip all empty tokens on the same line as the opener.
                if ($tokens[$next]['line'] === $tokens[$opener]['line']
                    && (isset(PHP_CodeSniffer_Tokens::$emptyTokens[$code]) === true
                    || $code === T_CLOSE_TAG)
                ) {
                    continue;
                }

                // We found the first bit of a code, or a comment on the following line.
                break;
            }

            $found = ($tokens[$next]['line'] - $tokens[$opener]['line']);

            if ($found > 1) {
                $error = 'Expected 1 newline after opening brace; %s found';
                $data  = [$found];
                $fix   = $phpcsFile->addFixableError($error, $opener, 'ExtraNewlineAfterOpenBrace', $data);

                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();

                    for ($i = ($opener + 1); $i < $next; $i++) {
                        if ($found > 0 && $tokens[$i]['line'] === $tokens[$next]['line']) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->addContent($opener, $phpcsFile->eolChar);
                    $phpcsFile->fixer->endChangeset();
                }
            }//end if
        }//end if

    }//end process()


}//end class
