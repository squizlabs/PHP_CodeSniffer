<?php
/**
 * Throws errors if spaces are used for indentation other than precision indentation.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowSpaceIndentSniff implements Sniff
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
                // We have no idea how wide tabs are, so assume 4 spaces for fixing.
                // It shouldn't really matter because indent checks elsewhere in the
                // standard should fix things up.
                $this->tabWidth = 4;
            } else {
                $this->tabWidth = $phpcsFile->config->tabWidth;
            }
        }

        $checkTokens = array(
                        T_WHITESPACE             => true,
                        T_INLINE_HTML            => true,
                        T_DOC_COMMENT_WHITESPACE => true,
                       );

        $tokens = $phpcsFile->getTokens();
        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['column'] !== 1 || isset($checkTokens[$tokens[$i]['code']]) === false) {
                continue;
            }

            // If tabs are being converted to spaces by the tokeniser, the
            // original content should be checked instead of the converted content.
            if (isset($tokens[$i]['orig_content']) === true) {
                $content = $tokens[$i]['orig_content'];
            } else {
                $content = $tokens[$i]['content'];
            }

            $recordMetrics = true;

            // If this is an inline HTML token, split the content into
            // indentation whitespace and the actual HTML/text.
            $nonWhitespace = '';
            if ($tokens[$i]['code'] === T_INLINE_HTML && preg_match('`^(\s*)(\S.*)`s', $content, $matches) > 0) {
                if (isset($matches[1]) === true) {
                    $content = $matches[1];
                }

                if (isset($matches[2]) === true) {
                    $nonWhitespace = $matches[2];
                }
            } else if (isset($tokens[($i + 1)]) === true
                && $tokens[$i]['line'] < $tokens[($i + 1)]['line']
            ) {
                // There is no content after this whitespace except for a newline.
                $content       = rtrim($content, "\r\n");
                $nonWhitespace = $phpcsFile->eolChar;

                // Don't record metrics for empty lines.
                $recordMetrics = false;
            }

            $hasSpaces = strpos($content, ' ');
            $hasTabs   = strpos($content, "\t");

            if ($hasSpaces === false && $hasTabs === false) {
                // Empty line.
                continue;
            }

            if ($hasSpaces === false && $hasTabs !== false) {
                // All ok, nothing to do.
                if ($recordMetrics === true) {
                    $phpcsFile->recordMetric($i, 'Line indent', 'tabs');
                }

                continue;
            }

            if ($tokens[$i]['code'] === T_DOC_COMMENT_WHITESPACE && $content === ' ') {
                // Ignore file/class-level docblocks, especially for recording metrics.
                continue;
            }

            // OK, by now we know there will be spaces.
            // We just don't know yet whether they need to be replaced or
            // are precision indentation, nor whether they are correctly
            // placed at the end of the whitespace.
            $trimmed        = str_replace(' ', '', $content);
            $numSpaces      = (strlen($content) - strlen($trimmed));
            $numTabs        = (int) floor($numSpaces / $this->tabWidth);
            $tabAfterSpaces = strpos($content, "\t", $hasSpaces);

            if ($hasTabs === false) {
                if ($recordMetrics === true) {
                    $phpcsFile->recordMetric($i, 'Line indent', 'spaces');
                }

                if ($numTabs === 0) {
                    // Ignore: precision indentation.
                    continue;
                }
            } else {
                if ($numTabs === 0) {
                    // Precision indentation.
                    if ($recordMetrics === true) {
                        if ($tabAfterSpaces !== false) {
                            $phpcsFile->recordMetric($i, 'Line indent', 'mixed');
                        } else {
                            $phpcsFile->recordMetric($i, 'Line indent', 'tabs');
                        }
                    }

                    if ($tabAfterSpaces === false) {
                        // Ignore: precision indentation is already at the
                        // end of the whitespace.
                        continue;
                    }
                } else if ($recordMetrics === true) {
                    $phpcsFile->recordMetric($i, 'Line indent', 'mixed');
                }
            }//end if

            $error = 'Tabs must be used to indent lines; spaces are not allowed';
            $fix   = $phpcsFile->addFixableError($error, $i, 'SpacesUsed');
            if ($fix === true) {
                $remaining = ($numSpaces % $this->tabWidth);
                $padding   = str_repeat("\t", $numTabs);
                $padding  .= str_repeat(' ', $remaining);
                $phpcsFile->fixer->replaceToken($i, $trimmed.$padding.$nonWhitespace);
            }
        }//end for

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
