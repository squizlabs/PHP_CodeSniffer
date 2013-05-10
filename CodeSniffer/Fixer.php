<?php
/**
 * A helper class for fixing errors.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * A helper class for fixing errors.
 *
 * Provides helper functions that act upon a token array and modify the file
 * content.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Fixer
{

    /**
     * The file being fixed.
     *
     * @var PHP_CodeSniffer_File
     */
    private $_currentFile = null;

    /**
     * The list of tokens that make up the file contents.
     *
     * This is a simplified list which just contains the token content and nothing
     * else. This is the array that is updated as fixes are made, not the file's
     * token array. Imploding this array will give you the file content back.
     *
     * @var array(int => string)
     */
    private $_tokens = array();

    /**
     * A list of tokens that have already been fixed.
     *
     * We don't allow the same token to be fixed more than once each time
     * through a file as this can easily cause conflicts between sniffs.
     *
     * @var array(int)
     */
    private $_fixedTokens = array();

    /**
     * The number of fixes that have been performed.
     *
     * @var int
     */
    private $_numFixes = 0;


    public function startFile($file)
    {
        $this->_currentFile = $file;
        $this->_numFixes    = 0;
        $this->_fixedTokens = array();

        $tokens = $file->getTokens();
        $this->_tokens = array();
        foreach ($tokens as $index => $token) {
            $this->_tokens[$index] = $token['content'];
        }
    }//end startFile()

    public function generateDiff()
    {
        $contents  = $this->getContents();
        $filename  = $this->_currentFile->getFilename();
        $fixedFile = getcwd().'/phpcs-fixed.tmp';

        file_put_contents($fixedFile, $contents);

        // We must use something like shell_exec() because whitespace at the end
        // of lines is critical to diff files.
        $cmd  = "diff -u -L\"$filename\" -LPHP_CodeSniffer \"$filename\" \"$fixedFile\"";
        $diff = shell_exec($cmd);
        unlink($fixedFile);

        return $diff;
    }//end generateDiff()

    public function getFixCount()
    {
        return $this->_numFixes;

    }//end getFixCount()

    public function getContents()
    {
        $contents = implode($this->_tokens);
        return $contents;

    }//end getContents()

    public function replaceToken($stackPtr, $content)
    {
        if (in_array($stackPtr, $this->_fixedTokens) === true) {
            return;
        }

        $this->_tokens[$stackPtr] = $content;
        $this->_numFixes++;
        $this->_fixedTokens[] = $stackPtr;

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $bt      = debug_backtrace();
            $sniff   = $bt[1]['class'];
            $line    = $bt[0]['line'];

            $tokens  = $this->_currentFile->getTokens();
            $type    = $tokens[$stackPtr]['type'];
            $content = str_replace($this->_currentFile->eolChar, '\n', $content);
            echo "\t$sniff (line $line) replaced token $stackPtr: $type => $content".PHP_EOL;
        }

    }//end replaceToken()

    public function substrToken($stackPtr, $start, $length=null)
    {
        if ($length === null) {
            $newContent = substr($this->_tokens[$stackPtr], $start);
        } else {
            $newContent = substr($this->_tokens[$stackPtr], $start, $length);
        }

        $this->replaceToken($stackPtr, $newContent);
    }//end substrToken()

    public function addNewline($stackPtr)
    {
        if (in_array($stackPtr, $this->_fixedTokens) === true) {
            return;
        }

        $this->_tokens[$stackPtr] .= $this->_currentFile->eolChar;
        $this->_numFixes++;
        $this->_fixedTokens[] = $stackPtr;

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $bt      = debug_backtrace();
            $sniff   = $bt[1]['class'];
            $line    = $bt[0]['line'];

            $tokens  = $this->_currentFile->getTokens();
            $type    = $tokens[$stackPtr]['type'];
            $content = str_replace($this->_currentFile->eolChar, '\n', $this->_tokens[$stackPtr]);
            echo "\t$sniff (line $line) added newline after token $stackPtr: $type => $content".PHP_EOL;
        }
    }//end addNewline()

    public function addNewlineBefore($stackPtr)
    {
        $this->addNewline($stackPtr - 1);
    }//end addNewlineBefore()

    public function addContent($stackPtr, $content)
    {
        if (in_array($stackPtr, $this->_fixedTokens) === true) {
            return;
        }

        $this->_tokens[$stackPtr] .= $content;
        $this->_numFixes++;
        $this->_fixedTokens[] = $stackPtr;

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $bt      = debug_backtrace();
            $sniff   = $bt[1]['class'];
            $line    = $bt[0]['line'];

            $tokens  = $this->_currentFile->getTokens();
            $type    = $tokens[$stackPtr]['type'];
            $content = str_replace($this->_currentFile->eolChar, '\n', $this->_tokens[$stackPtr]);
            echo "\t$sniff (line $line) added content after token $stackPtr: $type => $content".PHP_EOL;
        }
    }//end addContent()

    public function addContentBefore($stackPtr, $content)
    {
        $this->addContent(($stackPtr - 1), $content);
    }//end addContentBefore()

}//end class

?>
