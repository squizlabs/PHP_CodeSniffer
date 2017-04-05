<?php
/**
 * Generic_Sniffs_WhiteSpace_DisallowSpaceIndentSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_WhiteSpace_DisallowSpaceIndentSniff.
 *
 * Throws errors if spaces are used for indentation other than precision indentation.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_WhiteSpace_DisallowSpaceIndentSniff implements PHP_CodeSniffer_Sniff
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
     * @var int
     */
    private $_tabWidth = null;


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
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($this->_tabWidth === null) {
            $cliValues = $phpcsFile->phpcs->cli->getCommandLineValues();
            if (isset($cliValues['tabWidth']) === false || $cliValues['tabWidth'] === 0) {
                // We have no idea how wide tabs are, so assume 4 spaces for fixing.
                // It shouldn't really matter because indent checks elsewhere in the
                // standard should fix things up.
                $this->_tabWidth = 4;
            } else {
                $this->_tabWidth = $cliValues['tabWidth'];
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
            }

            $hasSpaces = strpos($content, ' ');
            $hasTabs   = strpos($content, "\t");

            if ($hasSpaces === false && $hasTabs === false) {
                // Empty line.
                continue;
            }

            if ($hasSpaces === false && $hasTabs !== false) {
                // All ok, nothing to do.
                $phpcsFile->recordMetric($i, 'Line indent', 'tabs');
                continue;
            }

            if ($tokens[$i]['code'] === T_DOC_COMMENT_WHITESPACE && $content === ' ') {
                // Ignore file/class-level DocBlock, especially for recording metrics.
                continue;
            }

            // OK, by now we know there will be spaces.
            // We just don't know yet whether they need to be replaced or
            // are precision indentation, nor whether they are correctly
            // placed at the end of the whitespace.
            $trimmed        = str_replace(' ', '', $content);
            $numSpaces      = (strlen($content) - strlen($trimmed));
            $numTabs        = (int) floor($numSpaces / $this->_tabWidth);
            $tabAfterSpaces = strpos($content, "\t", $hasSpaces);

            if ($hasTabs === false) {
                $phpcsFile->recordMetric($i, 'Line indent', 'spaces');

                if ($numTabs === 0) {
                    // Ignore: precision indentation.
                    continue;
                }
            } else {
                if ($numTabs === 0) {
                    // Precision indentation.
                    $phpcsFile->recordMetric($i, 'Line indent', 'tabs');

                    if ($tabAfterSpaces === false) {
                        // Ignore: precision indentation is already at the
                        // end of the whitespace.
                        continue;
                    }
                } else {
                    $phpcsFile->recordMetric($i, 'Line indent', 'mixed');
                }
            }//end if

            $error = 'Tabs must be used to indent lines; spaces are not allowed';
            $fix   = $phpcsFile->addFixableError($error, $i, 'SpacesUsed');
            if ($fix === true) {
                $remaining = ($numSpaces % $this->_tabWidth);
                $padding   = str_repeat("\t", $numTabs);
                $padding  .= str_repeat(' ', $remaining);
                $phpcsFile->fixer->replaceToken($i, $trimmed.$padding.$nonWhitespace);
            }
        }//end for

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
