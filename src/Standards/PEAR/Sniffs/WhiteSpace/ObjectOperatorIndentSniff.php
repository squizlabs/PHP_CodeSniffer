<?php
/**
 * Checks that object operators are indented correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ObjectOperatorIndentSniff implements Sniff
{

    /**
     * The number of spaces code should be indented.
     *
     * @var integer
     */
    public $indent = 4;


    /**
     * Should tabs be used for indenting?
     *
     * If TRUE, fixes will be made using tabs instead of spaces.
     * The size of each tab is important, so it should be specified
     * using the --tab-width CLI argument.
     *
     * @var boolean
     */
    public $tabIndent = false;


    /**
     * The --tab-width CLI value that is being used.
     *
     * @var integer
     */
    private $tabWidth = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_OBJECT_OPERATOR];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile All the tokens found in the document.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->tabWidth === null) {
            if (isset($phpcsFile->config->tabWidth) === false || $phpcsFile->config->tabWidth === 0) {
                // We have no idea how wide tabs are, so assume 4 spaces for fixing.
                // It shouldn't really matter because indent checks elsewhere in the
                // standard should fix things up.
                $this->tabWidth = 4;
            } else {
                $this->tabWidth = $phpcsFile->config->tabWidth;
            }
        }

        $tokens = $phpcsFile->getTokens();

        // Make sure this is the first object operator in a chain of them.
        $start = $phpcsFile->findStartOfStatement($stackPtr);
        $prev  = $phpcsFile->findPrevious(T_OBJECT_OPERATOR, ($stackPtr - 1), $start);
        if ($prev !== false) {
            return;
        }

        // Make sure this is a chained call.
        $end  = $phpcsFile->findEndOfStatement($stackPtr);
        $next = $phpcsFile->findNext(T_OBJECT_OPERATOR, ($stackPtr + 1), $end);
        if ($next === false) {
            // Not a chained call.
            return;
        }

        // Determine correct indent.
        for ($i = ($start - 1); $i >= 0; $i--) {
            if ($tokens[$i]['line'] !== $tokens[$start]['line']) {
                $i++;
                break;
            }
        }

        $requiredIndent = 0;
        if ($i >= 0 && $tokens[$i]['code'] === T_WHITESPACE) {
            $requiredIndent = $tokens[$i]['length'];
        }

        $requiredIndent += $this->indent;

        // Determine the scope of the original object operator.
        $origBrackets = null;
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            $origBrackets = $tokens[$stackPtr]['nested_parenthesis'];
        }

        $origConditions = null;
        if (isset($tokens[$stackPtr]['conditions']) === true) {
            $origConditions = $tokens[$stackPtr]['conditions'];
        }

        // Check indentation of each object operator in the chain.
        // If the first object operator is on a different line than
        // the variable, make sure we check its indentation too.
        if ($tokens[$stackPtr]['line'] > $tokens[$start]['line']) {
            $next = $stackPtr;
        }

        while ($next !== false) {
            // Make sure it is in the same scope, otherwise don't check indent.
            $brackets = null;
            if (isset($tokens[$next]['nested_parenthesis']) === true) {
                $brackets = $tokens[$next]['nested_parenthesis'];
            }

            $conditions = null;
            if (isset($tokens[$next]['conditions']) === true) {
                $conditions = $tokens[$next]['conditions'];
            }

            if ($origBrackets === $brackets && $origConditions === $conditions) {
                // Make sure it starts a line, otherwise dont check indent.
                $prev   = $phpcsFile->findPrevious(T_WHITESPACE, ($next - 1), $stackPtr, true);
                $indent = $tokens[($next - 1)];
                if ($tokens[$prev]['line'] !== $tokens[$next]['line']
                    && $indent['code'] === T_WHITESPACE
                ) {
                    if ($indent['line'] === $tokens[$next]['line']) {
                        $foundIndent = strlen($indent['content']);
                    } else {
                        $foundIndent = 0;
                    }

                    if ($foundIndent !== $requiredIndent) {
                        $error = 'Object operator not indented correctly; expected ';
                        if ($this->tabIndent === true) {
                            $error .= '%s tabs, found %s';
                            $data   = [
                                floor($requiredIndent / $this->tabWidth),
                                floor($foundIndent / $this->tabWidth),
                            ];
                        } else {
                            $error .= '%s spaces but found %s';
                            $data   = [
                                $requiredIndent,
                                $foundIndent,
                            ];
                        }

                        $fix = $phpcsFile->addFixableError($error, $next, 'Incorrect', $data);
                        if ($fix === true) {
                            $spaces = '';
                            if ($this->tabIndent === true) {
                                $numTabs = floor($requiredIndent / $this->tabWidth);
                                if ($numTabs > 0) {
                                    $numSpaces = ($requiredIndent - ($numTabs * $this->tabWidth));
                                    $spaces    = str_repeat("\t", $numTabs).str_repeat(' ', $numSpaces);
                                }
                            } else if ($requiredIndent > 0) {
                                $spaces = str_repeat(' ', $requiredIndent);
                            }

                            if ($foundIndent === 0) {
                                $phpcsFile->fixer->addContentBefore($next, $spaces);
                            } else {
                                $phpcsFile->fixer->replaceToken(($next - 1), $spaces);
                            }
                        }
                    }//end if
                }//end if

                // It cant be the last thing on the line either.
                $content = $phpcsFile->findNext(T_WHITESPACE, ($next + 1), null, true);
                if ($tokens[$content]['line'] !== $tokens[$next]['line']) {
                    $error = 'Object operator must be at the start of the line, not the end';
                    $fix   = $phpcsFile->addFixableError($error, $next, 'StartOfLine');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($x = ($next + 1); $x < $content; $x++) {
                            $phpcsFile->fixer->replaceToken($x, '');
                        }

                        $phpcsFile->fixer->addNewlineBefore($next);
                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }//end if

            $next = $phpcsFile->findNext(
                T_OBJECT_OPERATOR,
                ($next + 1),
                null,
                false,
                null,
                true
            );
        }//end while

    }//end process()


}//end class
