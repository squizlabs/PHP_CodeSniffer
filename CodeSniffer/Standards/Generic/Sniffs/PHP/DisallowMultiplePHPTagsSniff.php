<?php
/**
 * Generic_Sniffs_PHP_DisallowMultiplePHPTags.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Julian Kleinhans <kleinhans@bergisch-media.de>
 * @copyright 2010 Julian Kleinhans
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
/**
 * Exactly one pair of opening and closing tags are allowed
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Julian Kleinhans <kleinhans@bergisch-media.de>
 * @copyright 2010 Julian Kleinhans
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_PHP_DisallowMultiplePHPTagsSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports
     *
     * @var array
     */
    public $supportedTokenizers = array('PHP');


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_OPEN_TAG,
                T_CLOSE_TAG,
               );

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
        $tokens      = $phpcsFile->getTokens();
        $disallowTag = $phpcsFile->findNext($tokens[$stackPtr]['code'], ($stackPtr + 1));
        if (false !== $disallowTag) {
            $data = $tokens[$stackPtr]['content'];
            $error = 'Exactly one "%s" tag is allowed';
            $phpcsFile->addError($error, $disallowTag, 'OnlyOnePHPTag', $data);
        }

        return;

    }//end process()


}//end class

?>