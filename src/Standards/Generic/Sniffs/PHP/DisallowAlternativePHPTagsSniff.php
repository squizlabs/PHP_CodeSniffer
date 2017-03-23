<?php
/**
 * Verifies that no alternative PHP tags are used.
 *
 * If alternative PHP open tags are found, this sniff can fix both the open and close tags.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Config;

class DisallowAlternativePHPTagsSniff implements Sniff
{

    /**
     * Whether ASP tags are enabled or not.
     *
     * @var boolean
     */
    private $aspTags = false;

    /**
     * The current PHP version.
     *
     * @var integer
     */
    private $phpVersion = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        if ($this->phpVersion === null) {
            $this->phpVersion = Config::getConfigData('php_version');
            if ($this->phpVersion === null) {
                $this->phpVersion = PHP_VERSION_ID;
            }
        }

        if ($this->phpVersion < 70000) {
            $this->aspTags = (boolean) ini_get('asp_tags');
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
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
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

        if ($openTag['code'] === T_INLINE_HTML && $this->aspTags === false) {
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
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param array                       $tokens    The token stack.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     * @param string                      $content   The expected content of the closing tag to match the opener.
     *
     * @return int|false Pointer to the position in the stack for the closing tag or false if not found.
     */
    protected function findClosingTag(File $phpcsFile, $tokens, $stackPtr, $content)
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
     * @param \PHP_CodeSniffer\Files\File $phpcsFile       The file being scanned.
     * @param array                       $tokens          The token stack.
     * @param int                         $openTagPointer  Stack pointer to the PHP open tag.
     * @param int                         $closeTagPointer Stack pointer to the PHP close tag.
     * @param bool                        $echo            Whether to add 'echo' or not.
     *
     * @return void
     */
    protected function addChangeset(File $phpcsFile, $tokens, $openTagPointer, $closeTagPointer, $echo=false)
    {
        // Build up the open tag replacement and make sure there's always whitespace behind it.
        $openReplacement = '<?php';
        if ($echo === true) {
            $openReplacement .= ' echo';
        }

        if ($tokens[($openTagPointer + 1)]['code'] !== T_WHITESPACE) {
            $openReplacement .= ' ';
        }

        // Make sure we don't remove any line breaks after the closing tag.
        $regex            = '`'.preg_quote(trim($tokens[$closeTagPointer]['content'])).'`';
        $closeReplacement = preg_replace($regex, '?>', $tokens[$closeTagPointer]['content']);

        $phpcsFile->fixer->beginChangeset();
        $phpcsFile->fixer->replaceToken($openTagPointer, $openReplacement);
        $phpcsFile->fixer->replaceToken($closeTagPointer, $closeReplacement);
        $phpcsFile->fixer->endChangeset();

    }//end addChangeset()


}//end class
