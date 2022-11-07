<?php
/**
 * Throws errors if tabs are used for indentation.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DisallowTabIndentSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
        'CSS',
    ];

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
        return [
            T_OPEN_TAG,
            T_OPEN_TAG_WITH_ECHO,
        ];

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

        $tokens      = $phpcsFile->getTokens();
        $checkTokens = [
            T_WHITESPACE             => true,
            T_INLINE_HTML            => true,
            T_DOC_COMMENT_WHITESPACE => true,
            T_DOC_COMMENT_STRING     => true,
            T_COMMENT                => true,
            T_END_HEREDOC            => true,
            T_END_NOWDOC             => true,
        ];

        for ($i = 0; $i < $phpcsFile->numTokens; $i++) {
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

            // If this is an inline HTML token or a subsequent line of a multi-line comment,
            // split off the indentation as that is the only part to take into account for the metrics.
            $indentation = $content;
            if (($tokens[$i]['code'] === T_INLINE_HTML
                || $tokens[$i]['code'] === T_COMMENT)
                && preg_match('`^(\s*)\S.*`s', $content, $matches) > 0
            ) {
                if (isset($matches[1]) === true) {
                    $indentation = $matches[1];
                }
            }

            if (($tokens[$i]['code'] === T_DOC_COMMENT_WHITESPACE
                || $tokens[$i]['code'] === T_COMMENT)
                && $indentation === ' '
            ) {
                // Ignore all non-indented comments, especially for recording metrics.
                continue;
            }

            $recordMetrics = true;
            if ($content === $indentation
                && isset($tokens[($i + 1)]) === true
                && $tokens[$i]['line'] < $tokens[($i + 1)]['line']
            ) {
                // Don't record metrics for empty lines.
                $recordMetrics = false;
            }

            $foundTabs = substr_count($content, "\t");

            $error     = 'Spaces must be used to indent lines; tabs are not allowed';
            $errorCode = 'TabsUsed';
            if ($tokens[$i]['column'] === 1) {
                if ($recordMetrics === true) {
                    $foundIndentSpaces = substr_count($indentation, ' ');
                    $foundIndentTabs   = substr_count($indentation, "\t");

                    if ($foundIndentTabs > 0 && $foundIndentSpaces === 0) {
                        $phpcsFile->recordMetric($i, 'Line indent', 'tabs');
                    } else if ($foundIndentTabs === 0 && $foundIndentSpaces > 0) {
                        $phpcsFile->recordMetric($i, 'Line indent', 'spaces');
                    } else if ($foundIndentTabs > 0 && $foundIndentSpaces > 0) {
                        $spacePosition  = strpos($indentation, ' ');
                        $tabAfterSpaces = strpos($indentation, "\t", $spacePosition);
                        if ($tabAfterSpaces !== false) {
                            $phpcsFile->recordMetric($i, 'Line indent', 'mixed');
                        } else {
                            // Check for use of precision spaces.
                            $numTabs = (int) floor($foundIndentSpaces / $this->tabWidth);
                            if ($numTabs === 0) {
                                $phpcsFile->recordMetric($i, 'Line indent', 'tabs');
                            } else {
                                $phpcsFile->recordMetric($i, 'Line indent', 'mixed');
                            }
                        }
                    }
                }//end if
            } else {
                // Look for tabs so we can report and replace, but don't
                // record any metrics about them because they aren't
                // line indent tokens.
                if ($foundTabs > 0) {
                    $error     = 'Spaces must be used for alignment; tabs are not allowed';
                    $errorCode = 'NonIndentTabsUsed';
                }
            }//end if

            if ($foundTabs === 0) {
                continue;
            }

            $fix = $phpcsFile->addFixableError($error, $i, $errorCode);
            if ($fix === true) {
                if (isset($tokens[$i]['orig_content']) === true) {
                    // Use the replacement that PHPCS has already done.
                    $phpcsFile->fixer->replaceToken($i, $tokens[$i]['content']);
                } else {
                    // Replace tabs with spaces, using an indent of tabWidth spaces.
                    // Other sniffs can then correct the indent if they need to.
                    $newContent = str_replace("\t", str_repeat(' ', $this->tabWidth), $tokens[$i]['content']);
                    $phpcsFile->fixer->replaceToken($i, $newContent);
                }
            }
        }//end for

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
