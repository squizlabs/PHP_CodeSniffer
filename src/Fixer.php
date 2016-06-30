<?php
/**
 * A helper class for fixing errors.
 *
 * Provides helper functions that act upon a token array and modify the file
 * content.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/Symplify\PHP7_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Symplify\PHP7_CodeSniffer;

use Symplify\PHP7_CodeSniffer\Files\File;
use Symplify\PHP7_CodeSniffer\Util\Common;

class Fixer
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
     * The number of times we have looped over a file.
     *
     * @var integer
     */
    public $loops = 0;

    /**
     * The file being fixed.
     *
     * @var \Symplify\PHP7_CodeSniffer\Files\File
     */
    private $currentFile = null;

    /**
     * The list of tokens that make up the file contents.
     *
     * This is a simplified list which just contains the token content and nothing
     * else. This is the array that is updated as fixes are made, not the file's
     * token array. Imploding this array will give you the file content back.
     *
     * @var array<int, string>
     */
    private $_tokens = array();

    /**
     * A list of tokens that have already been fixed.
     *
     * We don't allow the same token to be fixed more than once each time
     * through a file as this can easily cause conflicts between sniffs.
     *
     * @var int[]
     */
    private $_fixedTokens = array();

    /**
     * The last value of each fixed token.
     *
     * If a token is being "fixed" back to its last value, the fix is
     * probably conflicting with another.
     *
     * @var array<int, string>
     */
    private $_oldTokenValues = array();

    /**
     * A list of tokens that have been fixed during a changeset.
     *
     * All changes in changeset must be able to be applied, or else
     * the entire changeset is rejected.
     *
     * @var array
     */
    private $_changeset = array();

    /**
     * Is there an open changeset.
     *
     * @var boolean
     */
    private $_inChangeset = false;

    /**
     * Is the current fixing loop in conflict?
     *
     * @var boolean
     */
    private $_inConflict = false;

    /**
     * The number of fixes that have been performed.
     *
     * @var integer
     */
    private $_numFixes = 0;


    /**
     * Starts fixing a new file.
     *
     * @param \Symplify\PHP7_CodeSniffer\Files\File $phpcsFile The file being fixed.
     *
     * @return void
     */
    public function startFile(File $phpcsFile)
    {
        $this->currentFile  = $phpcsFile;
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
        $fixable = $this->currentFile->getFixableCount();
        if ($fixable === 0) {
            // Nothing to fix.
            return false;
        }

        $stdin = false;
        if (empty($this->currentFile->config->files) === true) {
            $stdin = true;
        }

        $this->enabled = true;

        $this->loops = 0;
        while ($this->loops < 50) {
            ob_start();

            // Only needed once file content has changed.
            $contents = $this->getContents();

            $this->_inConflict = false;
            $this->currentFile->ruleset->populateTokenListeners();
            $this->currentFile->setContent($contents);
            $this->currentFile->process();
            ob_end_clean();

            $this->loops++;

            if (PHP_CodeSniffer_CBF === true && $stdin === false) {
                echo "\r".str_repeat(' ', 80)."\r";
                echo "\t=> Fixing file: $this->_numFixes/$fixable violations remaining [made $this->loops pass";
                if ($this->loops > 1) {
                    echo 'es';
                }

                echo ']... ';
            }

            if ($this->_numFixes === 0 && $this->_inConflict === false) {
                // Nothing left to do.
                break;
            }
        }//end while

        $this->enabled = false;

        if ($this->_numFixes > 0) {
            return false;
        }

        return true;

    }//end fixFile()


    /**
     * Generates a text diff of the original file and the new content.
     *
     * @param string  $filePath Optional file path to diff the file against.
     *                          If not specified, the original version of the
     *                          file will be used.
     *
     * @return string
     */
    public function generateDiff($filePath=null)
    {
        if ($filePath === null) {
            $filePath = $this->currentFile->getFilename();
        }

        $cwd      = getcwd().DIRECTORY_SEPARATOR;
        $filename = str_replace($cwd, '', $filePath);
        $contents = $this->getContents();

        $tempName  = tempnam(sys_get_temp_dir(), 'phpcs-fixer');
        $fixedFile = fopen($tempName, 'w');
        fwrite($fixedFile, $contents);

        // We must use something like shell_exec() because whitespace at the end
        // of lines is critical to diff files.
        $cmd  = "diff -u -L\"$filename\" -LSymplify\PHP7_CodeSniffer \"$filename\" \"$tempName\"";
        $diff = shell_exec($cmd);

        fclose($fixedFile);
        if (is_file($tempName) === true) {
            unlink($tempName);
        }

        $diffLines = explode(PHP_EOL, $diff);
        if (count($diffLines) === 1) {
            // Seems to be required for cygwin.
            $diffLines = explode("\n", $diff);
        }

        $diff = array();
        foreach ($diffLines as $line) {
            if (isset($line[0]) === true) {
                switch ($line[0]) {
                case '-':
                    $diff[] = "\033[31m$line\033[0m";
                    break;
                case '+':
                    $diff[] = "\033[32m$line\033[0m";
                    break;
                default:
                    $diff[] = $line;
                }
            }
        }

        $diff = implode(PHP_EOL, $diff);

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
        if ($this->_inConflict === true) {
            return false;
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
        if ($this->_inConflict === true) {
            return false;
        }

        $this->_inChangeset = false;

        $success = true;
        $applied = array();
        foreach ($this->_changeset as $stackPtr => $content) {
            $success = $this->replaceToken($stackPtr, $content);
            if ($success === false) {
                break;
            } else {
                $applied[] = $stackPtr;
            }
        }

        if ($success === false) {
            // Rolling back all changes.
            foreach ($applied as $stackPtr) {
                $this->revertToken($stackPtr);
            }
        }

        $this->_changeset = array();

    }//end endChangeset()


    /**
     * Stop recording actions for a changeset, and discard logged changes.
     *
     * @return void
     */
    public function rollbackChangeset()
    {
        $this->_inChangeset = false;
        $this->_inConflict  = false;

        if (empty($this->_changeset) === false) {
            $this->_changeset = array();
        }//end if

    }//end rollbackChangeset()


    /**
     * Replace the entire contents of a token.
     *
     * @param int    $stackPtr The position of the token in the token stack.
     * @param string $content  The new content of the token.
     *
     * @return bool If the change was accepted.
     */
    public function replaceToken($stackPtr, $content)
    {
        if ($this->_inConflict === true) {
            return false;
        }

        if ($this->_inChangeset === false
            && isset($this->_fixedTokens[$stackPtr]) === true
        ) {
            $indent = "\t";
            if (empty($this->_changeset) === false) {
                $indent .= "\t";
            }

            return false;
        }

        if ($this->_inChangeset === true) {
            $this->_changeset[$stackPtr] = $content;
            return true;
        }

        if (isset($this->_oldTokenValues[$stackPtr]) === false) {
            $this->_oldTokenValues[$stackPtr] = array(
                                                 'curr' => $content,
                                                 'prev' => $this->_tokens[$stackPtr],
                                                 'loop' => $this->loops,
                                                );
        } else {
            if ($this->_oldTokenValues[$stackPtr]['prev'] === $content
                && $this->_oldTokenValues[$stackPtr]['loop'] === ($this->loops - 1)
            ) {
                if ($this->_oldTokenValues[$stackPtr]['loop'] >= ($this->loops - 1)) {
                    $this->_inConflict = true;
                }

                return false;
            }//end if

            $this->_oldTokenValues[$stackPtr]['prev'] = $this->_oldTokenValues[$stackPtr]['curr'];
            $this->_oldTokenValues[$stackPtr]['curr'] = $content;
            $this->_oldTokenValues[$stackPtr]['loop'] = $this->loops;
        }//end if

        $this->_fixedTokens[$stackPtr] = $this->_tokens[$stackPtr];
        $this->_tokens[$stackPtr]      = $content;
        $this->_numFixes++;

        return true;

    }//end replaceToken()


    /**
     * Reverts the previous fix made to a token.
     *
     * @param int $stackPtr The position of the token in the token stack.
     *
     * @return bool If a change was reverted.
     */
    public function revertToken($stackPtr)
    {
        if (isset($this->_fixedTokens[$stackPtr]) === false) {
            return false;
        }

        $this->_tokens[$stackPtr] = $this->_fixedTokens[$stackPtr];
        unset($this->_fixedTokens[$stackPtr]);
        $this->_numFixes--;

        return true;

    }//end revertToken()


    /**
     * Replace the content of a token with a part of its current content.
     *
     * @param int $stackPtr The position of the token in the token stack.
     * @param int $start    The first character to keep.
     * @param int $length   The number of chacters to keep. If NULL, the content of
     *                      the token from $start to the end of the content is kept.
     *
     * @return bool If the change was accepted.
     */
    public function substrToken($stackPtr, $start, $length=null)
    {
        $current = $this->getTokenContent($stackPtr);

        if ($length === null) {
            $newContent = substr($current, $start);
        } else {
            $newContent = substr($current, $start, $length);
        }

        return $this->replaceToken($stackPtr, $newContent);

    }//end substrToken()


    /**
     * Adds a newline to end of a token's content.
     *
     * @param int $stackPtr The position of the token in the token stack.
     *
     * @return bool If the change was accepted.
     */
    public function addNewline($stackPtr)
    {
        $current = $this->getTokenContent($stackPtr);
        return $this->replaceToken($stackPtr, $current.$this->currentFile->eolChar);

    }//end addNewline()


    /**
     * Adds a newline to the start of a token's content.
     *
     * @param int $stackPtr The position of the token in the token stack.
     *
     * @return bool If the change was accepted.
     */
    public function addNewlineBefore($stackPtr)
    {
        $current = $this->getTokenContent($stackPtr);
        return $this->replaceToken($stackPtr, $this->currentFile->eolChar.$current);

    }//end addNewlineBefore()


    /**
     * Adds content to the end of a token's current content.
     *
     * @param int    $stackPtr The position of the token in the token stack.
     * @param string $content  The content to add.
     *
     * @return bool If the change was accepted.
     */
    public function addContent($stackPtr, $content)
    {
        $current = $this->getTokenContent($stackPtr);
        return $this->replaceToken($stackPtr, $current.$content);

    }//end addContent()


    /**
     * Adds content to the start of a token's current content.
     *
     * @param int    $stackPtr The position of the token in the token stack.
     * @param string $content  The content to add.
     *
     * @return bool If the change was accepted.
     */
    public function addContentBefore($stackPtr, $content)
    {
        $current = $this->getTokenContent($stackPtr);
        return $this->replaceToken($stackPtr, $content.$current);

    }//end addContentBefore()


}//end class
