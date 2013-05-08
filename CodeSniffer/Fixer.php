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
    private $_fixedTokens = array();
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
    }

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
        if (in_array($stackPtr, $this->_fixedTokens) === true) {
            return;
        }

        #echo "replace token $stackPtr with \"$content\"\n";
        $this->_tokens[$stackPtr] = $content;
        $this->_numFixes++;
        $this->_fixedTokens[] = $stackPtr;
    }

    public function substrToken($stackPtr, $start, $length=null)
    {
        if ($length === null) {
            $newContent = substr($this->_tokens[$stackPtr], $start);
        } else {
            $newContent = substr($this->_tokens[$stackPtr], $start, $length);
        }

        $this->replaceToken($stackPtr, $newContent);
    }

    public function addNewline($stackPtr)
    {
        if (in_array($stackPtr, $this->_fixedTokens) === true) {
            return;
        }

        #echo "add newline after $stackPtr\n";
        $this->_tokens[$stackPtr] .= $this->_currentFile->eolChar;
        $this->_numFixes++;
        $this->_fixedTokens[] = $stackPtr;
    }

    public function addNewlineBefore($stackPtr)
    {
        $this->addNewline($stackPtr - 1);
    }

    public function addContent($stackPtr, $content)
    {
        if (in_array($stackPtr, $this->_fixedTokens) === true) {
            return;
        }

        #echo "add content \"$content\" after $stackPtr\n";
        $this->_tokens[$stackPtr] .= $content;
        $this->_numFixes++;
        $this->_fixedTokens[] = $stackPtr;
    }

    public function addContentBefore($stackPtr, $content)
    {
        $this->addContent(($stackPtr - 1), $content);
    }

}//end class

?>
