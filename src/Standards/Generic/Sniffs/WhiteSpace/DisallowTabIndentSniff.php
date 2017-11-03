<?php
/**
 * Throws errors if tabs are used for indentation.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowTabIndentSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                   'CSS',
                                  );

    /**
     * The --tab-width CLI value that is being used.
     *
     * @var integer
     */
    private $tabWidth = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile All the tokens found in the document.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->tabWidth === null) {
            if (isset($phpcsFile->config->tabWidth) === false || $phpcsFile->config->tabWidth === 0) {
                // We have no idea how wide tabs are, so assume 4 spaces for metrics.
                $this->tabWidth = 4;
            } else {
                $this->tabWidth = $phpcsFile->config->tabWidth;
            }
        }

        $tokens    = $phpcsFile->getTokens();
        $error     = 'Spaces must be used to indent lines; tabs are not allowed';
        $errorCode = 'TabsUsed';

        $checkTokens = array(
                        T_WHITESPACE             => true,
                        T_INLINE_HTML            => true,
                        T_DOC_COMMENT_WHITESPACE => true,
                        T_DOC_COMMENT_STRING     => true,
                       );

        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if (isset($checkTokens[$tokens[$i]['code']]) === false) {
                continue;
            }

            // If tabs are being converted to spaces by the tokeniser, the
            // original content should be checked instead of the converted content.
            if (isset($tokens[$i]['orig_content']) === true) {
                $content = $tokens[$i]['orig_content'];
            } else {
                $content = $tokens[$i]['content'];
            }

            if ($content === '') {
                continue;
            }

            if ($tokens[$i]['code'] === T_DOC_COMMENT_WHITESPACE && $content === ' ') {
                // Ignore file/class-level DocBlock, especially for recording metrics.
                continue;
            }

            $recordMetrics = true;
            if (isset($tokens[($i + 1)]) === true
                && $tokens[$i]['line'] < $tokens[($i + 1)]['line']
            ) {
                // Don't record metrics for empty lines.
                $recordMetrics = false;
            }

            $tabFound = false;
            if ($tokens[$i]['column'] === 1) {
                if ($content[0] === "\t") {
                    $tabFound = true;
                    if ($recordMetrics === true) {
                        $spacePosition  = strpos($content, ' ');
                        $tabAfterSpaces = strpos($content, "\t", $spacePosition);
                        if ($spacePosition !== false && $tabAfterSpaces !== false) {
                            $phpcsFile->recordMetric($i, 'Line indent', 'mixed');
                        } else {
                            // Check for use of precision spaces.
                            $trimmed   = str_replace(' ', '', $content);
                            $numSpaces = (strlen($content) - strlen($trimmed));
                            $numTabs   = (int) floor($numSpaces / $this->tabWidth);
                            if ($numTabs === 0) {
                                $phpcsFile->recordMetric($i, 'Line indent', 'tabs');
                            } else {
                                $phpcsFile->recordMetric($i, 'Line indent', 'mixed');
                            }
                        }
                    }
                } else if ($content[0] === ' ') {
                    if (strpos($content, "\t") !== false) {
                        if ($recordMetrics === true) {
                            $phpcsFile->recordMetric($i, 'Line indent', 'mixed');
                        }

                        $tabFound = true;
                    } else if ($recordMetrics === true) {
                        $phpcsFile->recordMetric($i, 'Line indent', 'spaces');
                    }
                }//end if
            } else {
                // Look for tabs so we can report and replace, but don't
                // record any metrics about them because they aren't
                // line indent tokens.
                if (strpos($content, "\t") !== false) {
                    $tabFound  = true;
                    $error     = 'Spaces must be used for alignment; tabs are not allowed';
                    $errorCode = 'NonIndentTabsUsed';
                }
            }//end if

            if ($tabFound === false) {
                continue;
            }

            $fix = $phpcsFile->addFixableError($error, $i, $errorCode);
            if ($fix === true) {
                if (isset($tokens[$i]['orig_content']) === true) {
                    // Use the replacement that PHPCS has already done.
                    $phpcsFile->fixer->replaceToken($i, $tokens[$i]['content']);
                } else {
                    // Replace tabs with spaces, using an indent of 4 spaces.
                    // Other sniffs can then correct the indent if they need to.
                    $newContent = str_replace("\t", '    ', $tokens[$i]['content']);
                    $phpcsFile->fixer->replaceToken($i, $newContent);
                }
            }
        }//end for

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
