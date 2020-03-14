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

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

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
        return [T_INLINE_HTML];

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

        // Account for script open tags.
        if (preg_match('`(<script (?:[^>]+)?language=[\'"]?php[\'"]?(?:[^>]+)?>)`i', $content, $match) === 1) {
            $error   = 'Script style opening tag used; expected "<?php" but found "%s"';
            $snippet = $this->getSnippet($content, $match[1]);
            $data    = [$match[1].$snippet];

            $phpcsFile->addError($error, $stackPtr, 'ScriptOpenTagFound', $data);
            return;
        }

        // Account for ASP style tags.
        if (strpos($content, '<%=') !== false) {
            $error   = 'Possible use of ASP style short opening tags detected; found: %s';
            $snippet = $this->getSnippet($content, '<%=');
            $data    = ['<%='.$snippet];

            $phpcsFile->addWarning($error, $stackPtr, 'MaybeASPShortOpenTagFound', $data);
        } else if (strpos($content, '<%') !== false) {
            $error   = 'Possible use of ASP style opening tags detected; found: %s';
            $snippet = $this->getSnippet($content, '<%');
            $data    = ['<%'.$snippet];

            $phpcsFile->addWarning($error, $stackPtr, 'MaybeASPOpenTagFound', $data);
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


}//end class
