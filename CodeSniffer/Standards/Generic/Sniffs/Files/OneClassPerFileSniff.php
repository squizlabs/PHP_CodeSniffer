<?php
/**
 * Generic_Sniffs_Files_OneClassPerFileSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Andy Grunwald <andygrunwald@gmail.com>
 * @copyright 2010 Andy Grunwald
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
/**
 * Checks that only one class is declared per file.
 * The base of this sniff was "Squiz_Sniffs_Classes_ClassDeclarationSniff".
 * Thanks for this!
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Andy Grunwald <andygrunwald@gmail.com>
 * @copyright 2010 Andy Grunwald
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Files_OneClassPerFileSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports
     *
     * @var array
     */
    public $supportedTokenizes = array('PHP');


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLASS);

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
        $nextClass = $phpcsFile->findNext($this->register(), ($stackPtr + 1));
        if ($nextClass !== false) {
            $error = 'Only one class is allowed in a file.';
            $phpcsFile->addError($error, $nextClass, 'OnlyOneClassPerFile');
        }

    }//end process()


}//end class

?>