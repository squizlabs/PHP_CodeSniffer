<?php
/**
 * Checks the separation between functions and methods.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class FunctionSpacingSniff implements Sniff
{

    /**
     * The number of blank lines between functions.
     *
     * @var integer
     */
    public $spacing = 2;

    /**
     * The number of blank lines before the first function in a class.
     *
     * @var integer
     */
    public $spacingBeforeFirst = 2;

    /**
     * The number of blank lines after the last function in a class.
     *
     * @var integer
     */
    public $spacingAfterLast = 2;

    /**
     * Original properties as set in a custom ruleset (if any).
     *
     * @var array|null
     */
    private $rulesetProperties = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_FUNCTION];

    }//end register()


    /**
     * Processes this sniff when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens           = $phpcsFile->getTokens();
        $previousNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($previousNonEmpty !== false
            && $tokens[$previousNonEmpty]['code'] === T_OPEN_TAG
            && $tokens[$previousNonEmpty]['line'] !== 1
        ) {
            // Ignore functions at the start of an embedded PHP block.
            return;
        }

        // If the ruleset has only overridden the spacing property, use
        // that value for all spacing rules.
        if ($this->rulesetProperties === null) {
            $this->rulesetProperties = [];
            if (isset($phpcsFile->ruleset->ruleset['Squiz.WhiteSpace.FunctionSpacing']) === true
                && isset($phpcsFile->ruleset->ruleset['Squiz.WhiteSpace.FunctionSpacing']['properties']) === true
            ) {
                $this->rulesetProperties = $phpcsFile->ruleset->ruleset['Squiz.WhiteSpace.FunctionSpacing']['properties'];
                if (isset($this->rulesetProperties['spacing']) === true) {
                    if (isset($this->rulesetProperties['spacingBeforeFirst']) === false) {
                        $this->spacingBeforeFirst = $this->spacing;
                    }

                    if (isset($this->rulesetProperties['spacingAfterLast']) === false) {
                        $this->spacingAfterLast = $this->spacing;
                    }
                }
            }
        }

        $this->spacing            = (int) $this->spacing;
        $this->spacingBeforeFirst = (int) $this->spacingBeforeFirst;
        $this->spacingAfterLast   = (int) $this->spacingAfterLast;

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            // Must be an interface method, so the closer is the semicolon.
            $closer = $phpcsFile->findNext(T_SEMICOLON, $stackPtr);
        } else {
            $closer = $tokens[$stackPtr]['scope_closer'];
        }

        $isFirst = false;
        $isLast  = false;

        $ignore = ([T_WHITESPACE => T_WHITESPACE] + Tokens::$methodPrefixes);

        $prev = $phpcsFile->findPrevious($ignore, ($stackPtr - 1), null, true);
        if ($tokens[$prev]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            // Skip past function docblocks.
            $prev = $phpcsFile->findPrevious($ignore, ($tokens[$prev]['comment_opener'] - 1), null, true);
        }

        if ($tokens[$prev]['code'] === T_OPEN_CURLY_BRACKET) {
            $isFirst = true;
        }

        $next = $phpcsFile->findNext($ignore, ($closer + 1), null, true);
        if (isset(Tokens::$emptyTokens[$tokens[$next]['code']]) === true
            && $tokens[$next]['line'] === $tokens[$closer]['line']
        ) {
            // Skip past "end" comments.
            $next = $phpcsFile->findNext($ignore, ($next + 1), null, true);
        }

        if ($tokens[$next]['code'] === T_CLOSE_CURLY_BRACKET) {
            $isLast = true;
        }

        /*
            Check the number of blank lines
            after the function.
        */

        // Allow for comments on the same line as the closer.
        for ($nextLineToken = ($closer + 1); $nextLineToken < $phpcsFile->numTokens; $nextLineToken++) {
            if ($tokens[$nextLineToken]['line'] !== $tokens[$closer]['line']) {
                break;
            }
        }

        $requiredSpacing = $this->spacing;
        $errorCode       = 'After';
        if ($isLast === true) {
            $requiredSpacing = $this->spacingAfterLast;
            $errorCode       = 'AfterLast';
        }

        $foundLines = 0;
        if ($nextLineToken === ($phpcsFile->numTokens - 1)) {
            // We are at the end of the file.
            // Don't check spacing after the function because this
            // should be done by an EOF sniff.
            $foundLines = $requiredSpacing;
        } else {
            $nextContent = $phpcsFile->findNext(T_WHITESPACE, $nextLineToken, null, true);
            if ($nextContent === false) {
                // We are at the end of the file.
                // Don't check spacing after the function because this
                // should be done by an EOF sniff.
                $foundLines = $requiredSpacing;
            } else {
                $foundLines = ($tokens[$nextContent]['line'] - $tokens[$nextLineToken]['line']);
            }
        }

        if ($isLast === true) {
            $phpcsFile->recordMetric($stackPtr, 'Function spacing after last', $foundLines);
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Function spacing after', $foundLines);
        }

        if ($foundLines !== $requiredSpacing) {
            $error = 'Expected %s blank line';
            if ($requiredSpacing !== 1) {
                $error .= 's';
            }

            $error .= ' after function; %s found';
            $data   = [
                $requiredSpacing,
                $foundLines,
            ];

            $fix = $phpcsFile->addFixableError($error, $closer, $errorCode, $data);
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = $nextLineToken; $i <= $nextContent; $i++) {
                    if ($tokens[$i]['line'] === $tokens[$nextContent]['line']) {
                        $phpcsFile->fixer->addContentBefore($i, str_repeat($phpcsFile->eolChar, $requiredSpacing));
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }//end if
        }//end if

        /*
            Check the number of blank lines
            before the function.
        */

        $prevLineToken = null;
        for ($i = $stackPtr; $i >= 0; $i--) {
            if ($tokens[$i]['line'] === $tokens[$stackPtr]['line']) {
                continue;
            }

            $prevLineToken = $i;
            break;
        }

        if ($prevLineToken === null) {
            // Never found the previous line, which means
            // there are 0 blank lines before the function.
            $foundLines    = 0;
            $prevContent   = 0;
            $prevLineToken = 0;
        } else {
            $currentLine = $tokens[$stackPtr]['line'];

            $prevContent = $phpcsFile->findPrevious(T_WHITESPACE, $prevLineToken, null, true);

            if ($tokens[$prevContent]['code'] === T_COMMENT
                || isset(Tokens::$phpcsCommentTokens[$tokens[$prevContent]['code']]) === true
            ) {
                // Ignore comments as they can have different spacing rules, and this
                // isn't a proper function comment anyway.
                return;
            }

            if ($tokens[$prevContent]['code'] === T_DOC_COMMENT_CLOSE_TAG
                && $tokens[$prevContent]['line'] === ($currentLine - 1)
            ) {
                // Account for function comments.
                $prevContent = $phpcsFile->findPrevious(T_WHITESPACE, ($tokens[$prevContent]['comment_opener'] - 1), null, true);
            }

            $prevLineToken = $prevContent;

            // Before we throw an error, check that we are not throwing an error
            // for another function. We don't want to error for no blank lines after
            // the previous function and no blank lines before this one as well.
            $prevLine   = ($tokens[$prevContent]['line'] - 1);
            $i          = ($stackPtr - 1);
            $foundLines = 0;

            $stopAt = 0;
            if (isset($tokens[$stackPtr]['conditions']) === true) {
                $conditions = $tokens[$stackPtr]['conditions'];
                $conditions = array_keys($conditions);
                $stopAt     = array_pop($conditions);
            }

            while ($currentLine !== $prevLine && $currentLine > 1 && $i > $stopAt) {
                if ($tokens[$i]['code'] === T_FUNCTION) {
                    // Found another interface or abstract function.
                    return;
                }

                if ($tokens[$i]['code'] === T_CLOSE_CURLY_BRACKET
                    && $tokens[$tokens[$i]['scope_condition']]['code'] === T_FUNCTION
                ) {
                    // Found a previous function.
                    return;
                }

                $currentLine = $tokens[$i]['line'];
                if ($currentLine === $prevLine) {
                    break;
                }

                if ($tokens[($i - 1)]['line'] < $currentLine && $tokens[($i + 1)]['line'] > $currentLine) {
                    // This token is on a line by itself. If it is whitespace, the line is empty.
                    if ($tokens[$i]['code'] === T_WHITESPACE) {
                        $foundLines++;
                    }
                }

                $i--;
            }//end while
        }//end if

        $requiredSpacing = $this->spacing;
        $errorCode       = 'Before';
        if ($isFirst === true) {
            $requiredSpacing = $this->spacingBeforeFirst;
            $errorCode       = 'BeforeFirst';

            $phpcsFile->recordMetric($stackPtr, 'Function spacing before first', $foundLines);
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Function spacing before', $foundLines);
        }

        if ($foundLines !== $requiredSpacing) {
            $error = 'Expected %s blank line';
            if ($requiredSpacing !== 1) {
                $error .= 's';
            }

            $error .= ' before function; %s found';
            $data   = [
                $requiredSpacing,
                $foundLines,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, $errorCode, $data);
            if ($fix === true) {
                $nextSpace = $phpcsFile->findNext(T_WHITESPACE, ($prevContent + 1), $stackPtr);
                if ($nextSpace === false) {
                    $nextSpace = ($stackPtr - 1);
                }

                if ($foundLines < $requiredSpacing) {
                    $padding = str_repeat($phpcsFile->eolChar, ($requiredSpacing - $foundLines));
                    $phpcsFile->fixer->addContent($prevLineToken, $padding);
                } else {
                    $nextContent = $phpcsFile->findNext(T_WHITESPACE, ($nextSpace + 1), null, true);
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $nextSpace; $i < $nextContent; $i++) {
                        if ($tokens[$i]['line'] === $tokens[$prevContent]['line']) {
                            continue;
                        }

                        if ($tokens[$i]['line'] === $tokens[$nextContent]['line']) {
                            $phpcsFile->fixer->addContentBefore($i, str_repeat($phpcsFile->eolChar, $requiredSpacing));
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }//end if
            }//end if
        }//end if

    }//end process()


}//end class
