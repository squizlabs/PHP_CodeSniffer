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
     * Is the fixer enabled and fixing a file?
     *
     * Sniffs should check this value to ensure they are not
     * doing extra processing to prepare for a fix when fixing is
     * not required.
     *
     * @var boolean
     */
    public $enabled = false;

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
     * A list of tokens that have been fixed during a changeset.
     *
     * All changes in changeset must be able to be applied, or else
     * the entire changeset is rejected.
     *
     * @var array()
     */
    private $_changeset = array();

    /**
     * Is there an open changeset.
     *
     * @var boolean
     */
    private $_inChangeset = false;

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

        $tokens        = $phpcsFile->getTokens();
        $this->_tokens = array();
        foreach ($tokens as $index => $token) {
            if (isset($token['orig_content']) === true) {
                $this->_tokens[$index] = $token['orig_content'];
            } else {
                $this->_tokens[$index] = $token['content'];
            }
        }

    }//end startFile()


    /**
     * Attempt to fix the file by processing it until no fixes are made.
     *
     * @return boolean
     */
    public function fixFile()
    {
        $fixable = $this->_currentFile->getFixableCount();
        if ($fixable === 0) {
            // Nothing to fix.
            return false;
        }

        $this->enabled = true;

        $loops = 0;
        while ($loops < 50) {
            ob_start();
            // Only needed once file content has changed.
            $contents = $this->getContents();

            /*
                Useful for debugging fixed contents.
                @ob_end_clean();
                $debugContent = str_replace("\n", "\033[30;1m\\n\n\033[0m", $contents);
                $debugContent = str_replace("\t", "\033[30;1m»\t\033[0m", $debugContent);
                $debugContent = str_replace(' ', "\033[30;1m·\033[0m", $debugContent);
                echo $debugContent;
            */

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

            if ($this->_numFixes === 0) {
                // Nothing left to do.
                break;
            } else if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo "\t* fixed $this->_numFixes violations, starting over *".PHP_EOL;
            }
        }//end while

        $this->enabled = false;

        if ($this->_numFixes > 0) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                @ob_end_clean();
                echo "\t*** Reached maximum number of loops with $this->_numFixes violations left unfixed ***".PHP_EOL;
                ob_start();
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
        $cwd       = getcwd().DIRECTORY_SEPARATOR;
        $filename  = str_replace($cwd, '', $this->_currentFile->getFilename());
        $fixedFile = $cwd.'phpcs-fixed.tmp';
        $contents  = $this->getContents();

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
     * Get the current fixed content of a token.
     *
     * This function takes changesets into account so should be used
     * instead of directly accessing the token array.
     *
     * @param int $stackPtr The position of the token in the token stack.
     *
     * @return string
     */
    public function getTokenContent($stackPtr)
    {
        if ($this->_inChangeset === true
            && isset($this->_changeset[$stackPtr]) === true
        ) {
            return $this->_changeset[$stackPtr];
        } else {
            return $this->_tokens[$stackPtr];
        }

    }//end getTokenContent()


    /**
     * Start recording actions for a changeset.
     *
     * @return void
     */
    public function beginChangeset()
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $bt    = debug_backtrace();
            $sniff = $bt[1]['class'];
            $line  = $bt[0]['line'];

            @ob_end_clean();
            echo "\t=> Changeset started by $sniff (line $line)".PHP_EOL;
            ob_start();
        }

        $this->_changeset   = array();
        $this->_inChangeset = true;

    }//end beginChangeset()


    /**
     * Stop recording actions for a changeset, and apply logged changes.
     *
     * @return boolean
     */
    public function endChangeset()
    {
        $this->_inChangeset = false;

        $errors = array_intersect(array_keys($this->_changeset), array_keys($this->_fixedTokens));
        if (empty($errors) === false) {
            // At least one change cannot be applied.
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $fixes = count($this->_changeset);
                $fails = count($errors);
                @ob_end_clean();
                echo "\t=> Changeset could not be applied: $fails of $fixes changes would not apply".PHP_EOL;
                ob_start();
            }

            $this->_changeset = array();
            return false;
        }

        foreach ($this->_changeset as $stackPtr => $content) {
            $this->replaceToken($stackPtr, $content);
        }

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $fixes = count($this->_changeset);
            @ob_end_clean();
            echo "\t=> Changeset ended: $fixes changes applied".PHP_EOL;
            ob_start();
        }

        $this->_changeset = array();

    }//end endChangeset()


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
        if ($this->_inChangeset === false
            && isset($this->_fixedTokens[$stackPtr]) === true
        ) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                @ob_end_clean();
                echo "\t* token $stackPtr has already been modified, skipping *".PHP_EOL;
                ob_start();
            }

            return;
        }

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
            $oldContent = str_replace($this->_currentFile->eolChar, "\033[30;1m\\n\033[0m", $tokens[$stackPtr]['content']);
            $newContent = str_replace($this->_currentFile->eolChar, "\033[30;1m\\n\033[0m", $content);
            $oldContent = str_replace(' ', "\033[30;1m·\033[0m", $oldContent);
            $newContent = str_replace(' ', "\033[30;1m·\033[0m", $newContent);
        }

        if ($this->_inChangeset === true) {
            $this->_changeset[$stackPtr] = $content;

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                @ob_end_clean();
                echo "\t\tQ: $sniff (line $line) replaced token $stackPtr ($type) \"$oldContent\" => \"$newContent\"".PHP_EOL;
                ob_start();
            }

            return;
        }

        $this->_tokens[$stackPtr] = $content;
        $this->_numFixes++;
        $this->_fixedTokens[$stackPtr] = true;

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            $indent = "\t";
            if (empty($this->_changeset) === false) {
                $indent .= "\tA: ";
            }

            @ob_end_clean();
            echo "$indent$sniff (line $line) replaced token $stackPtr ($type) \"$oldContent\" => \"$newContent\"".PHP_EOL;
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
        $current = $this->getTokenContent($stackPtr);

        if ($length === null) {
            $newContent = substr($current, $start);
        } else {
            $newContent = substr($current, $start, $length);
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
        $current = $this->getTokenContent($stackPtr);
        $this->replaceToken($stackPtr, $current.$this->_currentFile->eolChar);

    }//end addNewline()


    /**
     * Adds a newline to the start of a token's content.
     *
     * @param int $stackPtr The position of the token in the token stack.
     *
     * @return void
     */
    public function addNewlineBefore($stackPtr)
    {
        $current = $this->getTokenContent($stackPtr);
        $this->replaceToken($stackPtr, $this->_currentFile->eolChar.$current);

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
        $current = $this->getTokenContent($stackPtr);
        $this->replaceToken($stackPtr, $current.$content);

    }//end addContent()


    /**
     * Adds content to the start of a token's current content.
     *
     * @param int    $stackPtr The position of the token in the token stack.
     * @param string $content  The content to add.
     *
     * @return void
     */
    public function addContentBefore($stackPtr, $content)
    {
        $current = $this->getTokenContent($stackPtr);
        $this->replaceToken($stackPtr, $content.$current);

    }//end addContentBefore()


}//end class
