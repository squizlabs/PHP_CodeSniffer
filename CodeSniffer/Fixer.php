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


    /**
     * Starts fixing a new file.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being fixed.
     *
     * @return void
     */
    public function startFile($phpcsFile)
    {
        $this->_currentFile = $phpcsFile;
        $this->_numFixes    = 0;
        $this->_fixedTokens = array();

        $tokens = $phpcsFile->getTokens();
        $this->_tokens = array();
        foreach ($tokens as $index => $token) {
            $this->_tokens[$index] = $token['content'];
        }

    }//end startFile()


    /**
     * Attempt to fix the file by processing it until no fixes are made.
     *
     * @return void
     */
    public function fixFile()
    {
        if ($this->_numFixes === 0) {
            return false;
        }

        $loops = 0;
        while ($this->_numFixes > 0 && $loops < 50) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo "\tFixed $this->_numFixes violations, starting over".PHP_EOL;
            }

            $contents = $this->getContents();
            //ob_end_clean();
            //print_r(str_replace("\n", '\n', $contents)."\n\n");
            ob_start();
            $this->_currentFile->refreshTokenListeners();
            $this->_currentFile->start($contents);
            ob_end_clean();
            /*
            Possibly useful as a fail-safe, but may mask problems with the actual
            fixes being performed.
            $newContents = $this->getContents();
            if ($newContents === $contents) {
                break;
            }
            */
            $loops++;
        }

        if ($this->_numFixes > 0) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo "\tReached maximum number of loops with $this->_numFixes violations left unfixed".PHP_EOL;
            }

            return false;
        }

        return true;

    }//end fixFile()


    /**
     * Generates a text diff of the original file and the new content.
     *
     * @return string
     */
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


    /**
     * Get a count of fixes that have been performed on the file.
     *
     * This value is reset every time a new file is started, or an existing
     * file is restarted.
     *
     * @return int
     */
    public function getFixCount()
    {
        return $this->_numFixes;

    }//end getFixCount()


    /**
     * Determine if a given token has been already been fixed.
     *
     * Tokens can only be fixed once per cycle, so some sniffs may need to
     * abort a series of fixes if one cannot be applied due to the token
     * content already being modified.
     *
     * @param int $stackPtr The position of the token in the token stack.
     *
     * @return void
     */
    public function isTokenFixed($stackPtr)
    {
        return in_array($stackPtr, $this->_fixedTokens);

    }//end isTokenFixed()


    /**
     * Get the current content of the file, as a string.
     *
     * @return string
     */
    public function getContents()
    {
        $contents = implode($this->_tokens);
        return $contents;

    }//end getContents()


    /**
     * Replace the entire contents of a token.
     *
     * @param int    $stackPtr The position of the token in the token stack.
     * @param string $content  The new content of the token.
     *
     * @return void
     */
    public function replaceToken($stackPtr, $content)
    {
        if ($this->isTokenFixed($stackPtr) === true) {
            return;
        }

        $this->_tokens[$stackPtr] = $content;
        $this->_numFixes++;
        $this->_fixedTokens[] = $stackPtr;

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $bt = debug_backtrace();
            if ($bt[1]['class'] === 'PHP_CodeSniffer_Fixer') {
                $sniff = $bt[2]['class'];
                $line  = $bt[1]['line'];
            } else {
                $sniff = $bt[1]['class'];
                $line  = $bt[0]['line'];
            }

            $tokens     = $this->_currentFile->getTokens();
            $type       = $tokens[$stackPtr]['type'];
            $oldContent = str_replace($this->_currentFile->eolChar, '\n', $tokens[$stackPtr]['content']);
            $newContent = str_replace($this->_currentFile->eolChar, '\n', $content);
            ob_end_clean();
            echo "\t$sniff (line $line) replaced token $stackPtr: $type => \"$newContent\"".PHP_EOL;
            echo "\t\t=> old content was: \"$oldContent\"".PHP_EOL;
            ob_start();
        }

    }//end replaceToken()


    /**
     * Replace the content of a token with a part of its current content.
     *
     * @param int $stackPtr The position of the token in the token stack.
     * @param int $start    The first character to keep.
     * @param int $length   The number of chacters to keep. If NULL, the content of
     *                      the token from $start to the end of the content is kept.
     *
     * @return void
     */
    public function substrToken($stackPtr, $start, $length=null)
    {
        if ($length === null) {
            $newContent = substr($this->_tokens[$stackPtr], $start);
        } else {
            $newContent = substr($this->_tokens[$stackPtr], $start, $length);
        }

        $this->replaceToken($stackPtr, $newContent);

    }//end substrToken()


    /**
     * Adds a newline to end of a token's content.
     *
     * @param int $stackPtr The position of the token in the token stack.
     *
     * @return void
     */
    public function addNewline($stackPtr)
    {
        if ($this->isTokenFixed($stackPtr) === true) {
            return;
        }

        $this->_tokens[$stackPtr] .= $this->_currentFile->eolChar;
        $this->_numFixes++;
        $this->_fixedTokens[] = $stackPtr;

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $bt = debug_backtrace();
            if ($bt[1]['class'] === 'PHP_CodeSniffer_Fixer') {
                $sniff = $bt[2]['class'];
                $line  = $bt[1]['line'];
            } else {
                $sniff = $bt[1]['class'];
                $line  = $bt[0]['line'];
            }

            $tokens  = $this->_currentFile->getTokens();
            $type    = $tokens[$stackPtr]['type'];
            $content = str_replace($this->_currentFile->eolChar, '\n', $this->_tokens[$stackPtr]);
            ob_end_clean();
            echo "\t$sniff (line $line) added newline after token $stackPtr: $type => $content".PHP_EOL;
            ob_start();
        }

    }//end addNewline()


    /**
     * Adds a newline to the start of a token's content.
     *
     * The token before the one passed is modified rather than the passed token.
     *
     * @param int $stackPtr The position of the token in the token stack.
     *
     * @return void
     */
    public function addNewlineBefore($stackPtr)
    {
        $this->addNewline($stackPtr - 1);

    }//end addNewlineBefore()


    /**
     * Adds content to the end of a token's current content.
     *
     * @param int    $stackPtr The position of the token in the token stack.
     * @param string $content  The content to add.
     *
     * @return void
     */
    public function addContent($stackPtr, $content)
    {
        if ($this->isTokenFixed($stackPtr) === true) {
            return;
        }

        $this->_tokens[$stackPtr] .= $content;
        $this->_numFixes++;
        $this->_fixedTokens[] = $stackPtr;

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $bt = debug_backtrace();
            if ($bt[1]['class'] === 'PHP_CodeSniffer_Fixer') {
                $sniff = $bt[2]['class'];
                $line  = $bt[1]['line'];
            } else {
                $sniff = $bt[1]['class'];
                $line  = $bt[0]['line'];
            }

            $tokens     = $this->_currentFile->getTokens();
            $type       = $tokens[$stackPtr]['type'];
            $oldContent = str_replace($this->_currentFile->eolChar, '\n', $tokens[$stackPtr]['content']);
            $newContent = str_replace($this->_currentFile->eolChar, '\n', $content);
            ob_end_clean();
            echo "\t$sniff (line $line) added content after token $stackPtr: $type => \"$oldContent\"".PHP_EOL;
            echo "\t\t=> additional content is: \"$newContent\"".PHP_EOL;
            ob_start();
        }

    }//end addContent()


    /**
     * Adds content to the start of a token's current content.
     *
     * The token before the one passed is modified rather than the passed token.
     *
     * @param int    $stackPtr The position of the token in the token stack.
     * @param string $content  The content to add.
     *
     * @return void
     */
    public function addContentBefore($stackPtr, $content)
    {
        $this->addContent(($stackPtr - 1), $content);

    }//end addContentBefore()


}//end class

?>
