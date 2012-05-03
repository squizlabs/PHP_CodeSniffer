<?php
/**
 * Generic_Sniffs_PHP_CharacterAfterPHPClosingTagSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Stefano Kowalke <blueduck@gmx.net>
 * @copyright 2010 Stefano Kowalke
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
/**
 * Checks that after php closing tag is no other char like newline.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Stefano Kowalke <blueduck@gmx.net>
 * @copyright 2010 Stefano Kowalke
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_PHP_CharacterAfterPHPClosingTagSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLOSE_TAG);

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens                      = $phpcsFile->getTokens();
        $keyword                     = $tokens[$stackPtr]['content'];
        $numberOfAllTokens           = (count($tokens) - 1);
        $diffCurrentTokenToAllTokens = ($numberOfAllTokens - $stackPtr);
        if ($keyword === true) {
            if ($keyword === '?>'.$phpcsFile->eolChar) {
                $error = 'No newline character is allowed after php closing tag; expect " ?> " but found " ?>\n " ';
                $phpcsFile->addError($error, $stackPtr, 'NoNewlineCharAfterPHPClosingTag');
            } else if ($diffCurrentTokenToAllTokens !== 0) {
                $nextToken = $tokens[($stackPtr + 1)]['content'];
                $error     = 'No character is allowed after php closing tag; expect " ?> " but found " ?>'.$nextToken.' " ';
                $phpcsFile->addError($error, $stackPtr, 'NoCharacterAfterPHPClosingTag');
            }
        }

    }//end process()


}//end class

?>