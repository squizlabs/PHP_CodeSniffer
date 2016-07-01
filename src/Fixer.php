<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Symplify\PHP7_CodeSniffer\Files\File;

final class Fixer
{
    /**
     * @var bool
     */
    public $enabled = false;

    /**
     * @var int
     */
    public $loops = 0;

    /**
     * @var File
     */
    private $currentFile;

    /**
     * The list of tokens that make up the file contents.
     *
     * This is a simplified list which just contains the token content and nothing
     * else. This is the array that is updated as fixes are made, not the file's
     * token array. Imploding this array will give you the file content back.
     *
     * @var array<int, string>
     */
    private $tokens = array();

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
    private $oldTokenValues = array();

    /**
     * A list of tokens that have been fixed during a changeset.
     *
     * All changes in changeset must be able to be applied, or else
     * the entire changeset is rejected.
     *
     * @var array
     */
    private $changeset = [];

    /**
     * Is there an open changeset.
     *
     * @var boolean
     */
    private $inChangeset = false;

    /**
     * Is the current fixing loop in conflict?
     *
     * @var boolean
     */
    private $inConflict = false;

    /**
     * The number of fixes that have been performed.
     *
     * @var integer
     */
    private $numFixes = 0;

    public function startFile(File $file)
    {
        $this->currentFile  = $file;
        $this->numFixes    = 0;
        $this->_fixedTokens = array();

        $tokens        = $file->getTokens();
        $this->tokens = array();
        foreach ($tokens as $index => $token) {
            if (isset($token['orig_content']) === true) {
                $this->tokens[$index] = $token['orig_content'];
            } else {
                $this->tokens[$index] = $token['content'];
            }
        }
    }//end startFile()


    public function fixFile() : bool
    {
        $fixable = $this->currentFile->getFixableCount();
        if ($fixable === 0) {
            // Nothing to fix.
            return false;
        }

        $this->enabled = true;

        $this->loops = 0;
        while ($this->loops < 50) {
            ob_start();

            // Only needed once file content has changed.
            $contents = $this->getContents();

            $this->inConflict = false;
            $this->currentFile->ruleset->populateTokenListeners();
            $this->currentFile->setContent($contents);
            $this->currentFile->process();
            ob_end_clean();

            $this->loops++;

            if (PHP_CodeSniffer_CBF === true) {
                echo "\r".str_repeat(' ', 80)."\r";
                echo "\t=> Fixing file: $this->numFixes/$fixable violations remaining [made $this->loops pass";
                if ($this->loops > 1) {
                    echo 'es';
                }

                echo ']... ';
            }

            if ($this->numFixes === 0 && $this->inConflict === false) {
                // Nothing left to do.
                break;
            }
        }

        $this->enabled = false;

        if ($this->numFixes > 0) {
            return false;
        }

        return true;
    }

    /**
     * Generates a text diff of the original file and the new content.
     *
     * @param string  $filePath Optional file path to diff the file against.
     *                          If not specified, the original version of the
     *                          file will be used.
     */
    public function generateDiff(string $filePath = null) : string
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
        $cmd  = "diff -u -L\"$filename\" -LSymplify\\PHP7_CodeSniffer \"$filename\" \"$tempName\"";
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
    }

    /**
     * Get a count of fixes that have been performed on the file.
     */
    public function getFixCount() : int
    {
        return $this->numFixes;
    }

    /**
     * Get the current content of the file, as a string.
     */
    public function getContents() : string
    {
        $contents = implode($this->tokens);
        return $contents;
    }

    public function getTokenContent(int $stackPtr) : string
    {
        if ($this->inChangeset === true
            && isset($this->changeset[$stackPtr]) === true
        ) {
            return $this->changeset[$stackPtr];
        } else {
            return $this->tokens[$stackPtr];
        }

    }//end getTokenContent()


    public function beginChangeset()
    {
        if ($this->inConflict === true) {
            return false;
        }

        $this->changeset   = array();
        $this->inChangeset = true;
    }

    public function endChangeset() : bool
    {
        if ($this->inConflict === true) {
            return false;
        }

        $this->inChangeset = false;

        $success = true;
        $applied = array();
        foreach ($this->changeset as $stackPtr => $content) {
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

        $this->changeset = array();
    }

    /**
     * Stop recording actions for a changeset, and discard logged changes.
     *
     * @return void
     */
    public function rollbackChangeset()
    {
        $this->inChangeset = false;
        $this->inConflict  = false;

        if (empty($this->changeset) === false) {
            $this->changeset = array();
        }//end if

    }//end rollbackChangeset()


    /**
     * Replace the entire contents of a token.
     *
     * @return bool If the change was accepted.
     */
    public function replaceToken(int $stackPtr, string $content) : bool
    {
        if ($this->inConflict === true) {
            return false;
        }

        if ($this->inChangeset === false
            && isset($this->_fixedTokens[$stackPtr]) === true
        ) {
            $indent = "\t";
            if (empty($this->changeset) === false) {
                $indent .= "\t";
            }

            return false;
        }

        if ($this->inChangeset === true) {
            $this->changeset[$stackPtr] = $content;
            return true;
        }

        if (isset($this->oldTokenValues[$stackPtr]) === false) {
            $this->oldTokenValues[$stackPtr] = array(
                                                 'curr' => $content,
                                                 'prev' => $this->tokens[$stackPtr],
                                                 'loop' => $this->loops,
                                                );
        } else {
            if ($this->oldTokenValues[$stackPtr]['prev'] === $content
                && $this->oldTokenValues[$stackPtr]['loop'] === ($this->loops - 1)
            ) {
                if ($this->oldTokenValues[$stackPtr]['loop'] >= ($this->loops - 1)) {
                    $this->inConflict = true;
                }

                return false;
            }//end if

            $this->oldTokenValues[$stackPtr]['prev'] = $this->oldTokenValues[$stackPtr]['curr'];
            $this->oldTokenValues[$stackPtr]['curr'] = $content;
            $this->oldTokenValues[$stackPtr]['loop'] = $this->loops;
        }//end if

        $this->_fixedTokens[$stackPtr] = $this->tokens[$stackPtr];
        $this->tokens[$stackPtr]      = $content;
        $this->numFixes++;

        return true;

    }//end replaceToken()


    /**
     * @return bool If a change was reverted.
     */
    public function revertToken(int $stackPtr) : bool
    {
        if (isset($this->_fixedTokens[$stackPtr]) === false) {
            return false;
        }

        $this->tokens[$stackPtr] = $this->_fixedTokens[$stackPtr];
        unset($this->_fixedTokens[$stackPtr]);
        $this->numFixes--;

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
    }
}
