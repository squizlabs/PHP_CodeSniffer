<?php
/**
 * Generic_Sniffs_PHP_DisallowAlternativePHPTagsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_PHP_DisallowAlternativePHPTagsSniff.
 *
 * Verifies that no alternative PHP tags are used.
 *
 * If alternative PHP open tags are found, this sniff can fix both the open and close tags.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_PHP_DisallowAlternativePHPTagsSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Whether ASP tags are enabled or not.
     *
     * @var bool
     */
    private $_aspTags = false;

    /**
     * The current PHP version.
     *
     * @var integer
     */
    private $_phpVersion = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        if ($this->_phpVersion === null) {
            $this->_phpVersion = PHP_CodeSniffer::getConfigData('php_version');
            if ($this->_phpVersion === null) {
                $this->_phpVersion = PHP_VERSION_ID;
            }
        }

        if ($this->_phpVersion < 70000) {
            $this->_aspTags = (boolean) ini_get('asp_tags');
        }

        return array(
                T_OPEN_TAG,
                T_OPEN_TAG_WITH_ECHO,
                T_INLINE_HTML,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $openTag = $tokens[$stackPtr];
        $content = $openTag['content'];

        if (trim($content) === '') {
            return;
        }

        if ($openTag['code'] === T_OPEN_TAG) {
            if ($content === '<%') {
                $error     = 'ASP style opening tag used; expected "<?php" but found "%s"';
                $closer    = $this->findClosingTag($phpcsFile, $tokens, $stackPtr, '%>');
                $errorCode = 'ASPOpenTagFound';
            } else if (strpos($content, '<script ') !== false) {
                $error     = 'Script style opening tag used; expected "<?php" but found "%s"';
                $closer    = $this->findClosingTag($phpcsFile, $tokens, $stackPtr, '</script>');
                $errorCode = 'ScriptOpenTagFound';
            }

            if (isset($error, $closer, $errorCode) === true) {
                $data = array($content);

                if ($closer === false) {
                    $phpcsFile->addError($error, $stackPtr, $errorCode, $data);
                } else {
                    $fix = $phpcsFile->addFixableError($error, $stackPtr, $errorCode, $data);
                    if ($fix === true) {
                        $this->addChangeset($phpcsFile, $tokens, $stackPtr, $closer);
                    }
                }
            }

            return;
        }//end if

        if ($openTag['code'] === T_OPEN_TAG_WITH_ECHO && $content === '<%=') {
            $error   = 'ASP style opening tag used with echo; expected "<?php echo %s ..." but found "%s %s ..."';
            $nextVar = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            $snippet = $this->getSnippet($tokens[$nextVar]['content']);
            $data    = array(
                        $snippet,
                        $content,
                        $snippet,
                       );

            $closer = $this->findClosingTag($phpcsFile, $tokens, $stackPtr, '%>');

            if ($closer === false) {
                $phpcsFile->addError($error, $stackPtr, 'ASPShortOpenTagFound', $data);
            } else {
                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'ASPShortOpenTagFound', $data);
                if ($fix === true) {
                    $this->addChangeset($phpcsFile, $tokens, $stackPtr, $closer, true);
                }
            }

            return;
        }//end if

        // Account for incorrect script open tags.
        // The "(?:<s)?" in the regex is to work-around a bug in PHP 5.2.
        if ($openTag['code'] === T_INLINE_HTML
            && preg_match('`((?:<s)?cript (?:[^>]+)?language=[\'"]?php[\'"]?(?:[^>]+)?>)`i', $content, $match) === 1
        ) {
            $error   = 'Script style opening tag used; expected "<?php" but found "%s"';
            $snippet = $this->getSnippet($content, $match[1]);
            $data    = array($match[1].$snippet);

            $phpcsFile->addError($error, $stackPtr, 'ScriptOpenTagFound', $data);
            return;
        }

        if ($openTag['code'] === T_INLINE_HTML && $this->_aspTags === false) {
            if (strpos($content, '<%=') !== false) {
                $error   = 'Possible use of ASP style short opening tags detected; found: %s';
                $snippet = $this->getSnippet($content, '<%=');
                $data    = array('<%='.$snippet);

                $phpcsFile->addWarning($error, $stackPtr, 'MaybeASPShortOpenTagFound', $data);
            } else if (strpos($content, '<%') !== false) {
                $error   = 'Possible use of ASP style opening tags detected; found: %s';
                $snippet = $this->getSnippet($content, '<%');
                $data    = array('<%'.$snippet);

                $phpcsFile->addWarning($error, $stackPtr, 'MaybeASPOpenTagFound', $data);
            }
        }

    }//end process()


    /**
     * Get a snippet from a HTML token.
     *
     * @param string $content The content of the HTML token.
     * @param string $start   Partial string to use as a starting point for the snippet.
     * @param int    $length  The target length of the snippet to get. Defaults to 40.
     *
     * @return string
     */
    protected function getSnippet($content, $start='', $length=40)
    {
        $startPos = 0;

        if ($start !== '') {
            $startPos = strpos($content, $start);
            if ($startPos !== false) {
                $startPos += strlen($start);
            }
        }

        $snippet = substr($content, $startPos, $length);
        if ((strlen($content) - $startPos) > $length) {
            $snippet .= '...';
        }

        return $snippet;

    }//end getSnippet()


    /**
     * Try and find a matching PHP closing tag.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param array                $tokens    The token stack.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     * @param string               $content   The expected content of the closing tag to match the opener.
     *
     * @return int|false Pointer to the position in the stack for the closing tag or false if not found.
     */
    protected function findClosingTag(PHP_CodeSniffer_File $phpcsFile, $tokens, $stackPtr, $content)
    {
        $closer = $phpcsFile->findNext(T_CLOSE_TAG, ($stackPtr + 1));

        if ($closer !== false && $content === trim($tokens[$closer]['content'])) {
            return $closer;
        }

        return false;

    }//end findClosingTag()


    /**
     * Add a changeset to replace the alternative PHP tags.
     *
     * @param PHP_CodeSniffer_File $phpcsFile         The file being scanned.
     * @param array                $tokens            The token stack.
     * @param int                  $open_tag_pointer  Stack pointer to the PHP open tag.
     * @param int                  $close_tag_pointer Stack pointer to the PHP close tag.
     * @param bool                 $echo              Whether to add 'echo' or not.
     *
     * @return void
     */
    protected function addChangeset(PHP_CodeSniffer_File $phpcsFile, $tokens, $open_tag_pointer, $close_tag_pointer, $echo = false)
    {
        // Build up the open tag replacement and make sure there's always whitespace behind it.
        $open_replacement = '<?php';
        if ($echo === true) {
            $open_replacement .= ' echo';
        }

        if ($tokens[($open_tag_pointer + 1)]['code'] !== T_WHITESPACE) {
            $open_replacement .= ' ';
        }

        // Make sure we don't remove any line breaks after the closing tag.
        $regex = '`'.preg_quote(trim($tokens[$close_tag_pointer]['content'])).'`';
        $close_replacement = preg_replace($regex, '?>', $tokens[$close_tag_pointer]['content']);

        $phpcsFile->fixer->beginChangeset();
        $phpcsFile->fixer->replaceToken($open_tag_pointer, $open_replacement);
        $phpcsFile->fixer->replaceToken($close_tag_pointer, $close_replacement);
        $phpcsFile->fixer->endChangeset();

    }//end addChangeset()


}//end class
