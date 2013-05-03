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
    private $_currentFile = null;
    private $_tokens = array();
    private $_numFixes = 0;

    public function startFile($file)
    {

        $this->_currentFile = $file;
        $this->_numFixes = 0;

        $tokens = $file->getTokens();
        $this->_tokens = array();
        foreach ($tokens as $index => $token) {
            $this->_tokens[$index] = $token['content'];
        }
    }

    public function endFile()
    {
        $contents  = $this->getContents();
        $filename  = $this->_currentFile->getFilename();
        $fixedFile = getcwd().'/phpcs-fixed.tmp';

        file_put_contents($fixedFile, $contents);

        $cmd = "diff -u -L\"$filename\" -LPHP_CodeSniffer \"$filename\" \"$fixedFile\"";
        $msg = exec($cmd, $output, $retval);
        unlink($fixedFile);

        $diff     = implode(PHP_EOL, $output).PHP_EOL;
        $diffFile = getcwd().'/phpcs-fixed.diff';

        file_put_contents($diffFile, $diff, FILE_APPEND);


    }

    public function getFixCount()
    {
        return $this->_numFixes;
    }

    public function getContents()
    {
        $contents = implode($this->_tokens);
        #echo $contents."\n\n";
        return $contents;
    }

    public function replaceToken($stackPtr, $content)
    {
        $this->_tokens[$stackPtr] = $content;
        $this->_numFixes++;
    }

    public function addNewline($stackPtr)
    {
        $this->_tokens[$stackPtr] .= $this->_currentFile->eolChar;
        $this->_numFixes++;
    }

    public function addContent($stackPtr, $content)
    {
        $this->_tokens[$stackPtr] .= $content;
        $this->_numFixes++;
    }

}//end class

?>
