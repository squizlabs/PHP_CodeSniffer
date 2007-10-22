<?php
/**
 * Squiz_Sniffs_PHP_CommentedOutCodeSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_PHP_CommentedOutCodeSniff.
 *
 * Warn about commented out code.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_PHP_CommentedOutCodeSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * If a comment is more than $maxPercentage% code, a warning will be shown.
     *
     * @var int
     */
    protected $maxPercentage = 45;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$commentTokens;

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
        $tokens = $phpcsFile->getTokens();

        // Process whole comment blocks at once, so skip all but the first token.
        if ($tokens[$stackPtr]['code'] === $tokens[($stackPtr - 1)]['code']) {
            return;
        }

        $numTokens = count($tokens);

        $content = '<?php ';
        for ($i = $stackPtr; $i < $numTokens; $i++) {
            if ($tokens[$stackPtr]['code'] !== $tokens[$i]['code']) {
                break;
            }

            /*
                Trim as much off the comment as possible so we don't
                have additional whitespace tokens or comment tokens
            */

            $tokenContent = trim($tokens[$i]['content']);

            if (substr($tokenContent, 0, 2) === '//') {
                $tokenContent = substr($tokenContent, 2);
            }

            if (substr($tokenContent, 0, 1) === '#') {
                $tokenContent = substr($tokenContent, 1);
            }

            if (substr($tokenContent, 0, 2) === '/*') {
                $tokenContent = substr($tokenContent, 2);
            }

            if (substr($tokenContent, -2) === '*/') {
                $tokenContent = substr($tokenContent, 0, - 2);
            }

            $content .= ' '.$tokenContent;
        }

        $content .= ' ?>';

        $stringTokens = PHP_CodeSniffer_File::tokenizeString($content);

        $emptyTokens = array(
                        T_WHITESPACE,
                        T_STRING,
                        T_STRING_CONCAT,
                       );

        $numTokens  = count($stringTokens);
        $numComment = 0;
        $numCode    = 0;

        for ($i = 0; $i < $numTokens; $i++) {
            if (in_array($stringTokens[$i]['code'], $emptyTokens) === true) {
                // Looks like comment.
                $numComment++;
            } else {
                // Looks like code.
                $numCode++;
            }
        }

        // We subtract 4 from the token number so we ignore the start/end tokens
        // and their surrounding whitespace. We take 2 off the number of code
        // tokens so we ignore the start/end tokens.
        if ($numTokens > 4) {
            $numTokens -= 4;
        }

        if ($numCode > 2) {
            $numCode -= 2;
        }

        $percentCode = ceil((($numCode / $numTokens) * 100));
        if ($percentCode > $this->maxPercentage) {
            $error = "This comment is ${percentCode}% valid code; is this commented out code?";
            $phpcsFile->addWarning($error, $stackPtr);
        }

    }//end process()


}//end class

?>
