<?php
/**
 * Parses and verifies the doc comments for files.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Common;

class FileCommentSniff implements Sniff
{

    /**
     * Tags in correct order and related info.
     *
     * @var array
     */
    protected $tags = [
        '@category'   => [
            'required'       => true,
            'allow_multiple' => false,
        ],
        '@package'    => [
            'required'       => true,
            'allow_multiple' => false,
        ],
        '@subpackage' => [
            'required'       => false,
            'allow_multiple' => false,
        ],
        '@author'     => [
            'required'       => true,
            'allow_multiple' => true,
        ],
        '@copyright'  => [
            'required'       => false,
            'allow_multiple' => true,
        ],
        '@license'    => [
            'required'       => true,
            'allow_multiple' => false,
        ],
        '@version'    => [
            'required'       => false,
            'allow_multiple' => false,
        ],
        '@link'       => [
            'required'       => true,
            'allow_multiple' => true,
        ],
        '@see'        => [
            'required'       => false,
            'allow_multiple' => true,
        ],
        '@since'      => [
            'required'       => false,
            'allow_multiple' => false,
        ],
        '@deprecated' => [
            'required'       => false,
            'allow_multiple' => false,
        ],
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Find the next non whitespace token.
        $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        // Allow declare() statements at the top of the file.
        if ($tokens[$commentStart]['code'] === T_DECLARE) {
            $semicolon    = $phpcsFile->findNext(T_SEMICOLON, ($commentStart + 1));
            $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($semicolon + 1), null, true);
        }

        // Ignore vim header.
        if ($tokens[$commentStart]['code'] === T_COMMENT) {
            if (strstr($tokens[$commentStart]['content'], 'vim:') !== false) {
                $commentStart = $phpcsFile->findNext(
                    T_WHITESPACE,
                    ($commentStart + 1),
                    null,
                    true
                );
            }
        }

        $errorToken = ($stackPtr + 1);
        if (isset($tokens[$errorToken]) === false) {
            $errorToken--;
        }

        if ($tokens[$commentStart]['code'] === T_CLOSE_TAG) {
            // We are only interested if this is the first open tag.
            return ($phpcsFile->numTokens + 1);
        } else if ($tokens[$commentStart]['code'] === T_COMMENT) {
            $error = 'You must use "/**" style comments for a file comment';
            $phpcsFile->addError($error, $errorToken, 'WrongStyle');
            $phpcsFile->recordMetric($stackPtr, 'File has doc comment', 'yes');
            return ($phpcsFile->numTokens + 1);
        } else if ($commentStart === false
            || $tokens[$commentStart]['code'] !== T_DOC_COMMENT_OPEN_TAG
        ) {
            $phpcsFile->addError('Missing file doc comment', $errorToken, 'Missing');
            $phpcsFile->recordMetric($stackPtr, 'File has doc comment', 'no');
            return ($phpcsFile->numTokens + 1);
        }

        $commentEnd = $tokens[$commentStart]['comment_closer'];

        $nextToken = $phpcsFile->findNext(
            T_WHITESPACE,
            ($commentEnd + 1),
            null,
            true
        );

        $ignore = [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_FUNCTION,
            T_CLOSURE,
            T_PUBLIC,
            T_PRIVATE,
            T_PROTECTED,
            T_FINAL,
            T_STATIC,
            T_ABSTRACT,
            T_CONST,
            T_PROPERTY,
        ];

        if (in_array($tokens[$nextToken]['code'], $ignore, true) === true) {
            $phpcsFile->addError('Missing file doc comment', $stackPtr, 'Missing');
            $phpcsFile->recordMetric($stackPtr, 'File has doc comment', 'no');
            return ($phpcsFile->numTokens + 1);
        }

        $phpcsFile->recordMetric($stackPtr, 'File has doc comment', 'yes');

        // Check the PHP Version, which should be in some text before the first tag.
        $found = false;
        for ($i = ($commentStart + 1); $i < $commentEnd; $i++) {
            if ($tokens[$i]['code'] === T_DOC_COMMENT_TAG) {
                break;
            } else if ($tokens[$i]['code'] === T_DOC_COMMENT_STRING
                && strstr(strtolower($tokens[$i]['content']), 'php version') !== false
            ) {
                $found = true;
                break;
            }
        }

        if ($found === false) {
            $error = 'PHP version not specified';
            $phpcsFile->addWarning($error, $commentEnd, 'MissingVersion');
        }

        // Check each tag.
        $this->processTags($phpcsFile, $stackPtr, $commentStart);

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


    /**
     * Processes each required or optional tag.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile    The file being scanned.
     * @param int                         $stackPtr     The position of the current token
     *                                                  in the stack passed in $tokens.
     * @param int                         $commentStart Position in the stack where the comment started.
     *
     * @return void
     */
    protected function processTags($phpcsFile, $stackPtr, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        if (get_class($this) === 'PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting\FileCommentSniff') {
            $docBlock = 'file';
        } else {
            $docBlock = 'class';
        }

        $commentEnd = $tokens[$commentStart]['comment_closer'];

        $foundTags = [];
        $tagTokens = [];
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            $name = $tokens[$tag]['content'];
            if (isset($this->tags[$name]) === false) {
                continue;
            }

            if ($this->tags[$name]['allow_multiple'] === false && isset($tagTokens[$name]) === true) {
                $error = 'Only one %s tag is allowed in a %s comment';
                $data  = [
                    $name,
                    $docBlock,
                ];
                $phpcsFile->addError($error, $tag, 'Duplicate'.ucfirst(substr($name, 1)).'Tag', $data);
            }

            $foundTags[]        = $name;
            $tagTokens[$name][] = $tag;

            $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag, $commentEnd);
            if ($string === false || $tokens[$string]['line'] !== $tokens[$tag]['line']) {
                $error = 'Content missing for %s tag in %s comment';
                $data  = [
                    $name,
                    $docBlock,
                ];
                $phpcsFile->addError($error, $tag, 'Empty'.ucfirst(substr($name, 1)).'Tag', $data);
                continue;
            }
        }//end foreach

        // Check if the tags are in the correct position.
        $pos = 0;
        foreach ($this->tags as $tag => $tagData) {
            if (isset($tagTokens[$tag]) === false) {
                if ($tagData['required'] === true) {
                    $error = 'Missing %s tag in %s comment';
                    $data  = [
                        $tag,
                        $docBlock,
                    ];
                    $phpcsFile->addError($error, $commentEnd, 'Missing'.ucfirst(substr($tag, 1)).'Tag', $data);
                }

                continue;
            } else {
                $method = 'process'.substr($tag, 1);
                if (method_exists($this, $method) === true) {
                    // Process each tag if a method is defined.
                    call_user_func([$this, $method], $phpcsFile, $tagTokens[$tag]);
                }
            }

            if (isset($foundTags[$pos]) === false) {
                break;
            }

            if ($foundTags[$pos] !== $tag) {
                $error = 'The tag in position %s should be the %s tag';
                $data  = [
                    ($pos + 1),
                    $tag,
                ];
                $phpcsFile->addError($error, $tokens[$commentStart]['comment_tags'][$pos], ucfirst(substr($tag, 1)).'TagOrder', $data);
            }

            // Account for multiple tags.
            $pos++;
            while (isset($foundTags[$pos]) === true && $foundTags[$pos] === $tag) {
                $pos++;
            }
        }//end foreach

    }//end processTags()


    /**
     * Process the category tag.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param array                       $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processCategory($phpcsFile, array $tags)
    {
        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                // No content.
                continue;
            }

            $content = $tokens[($tag + 2)]['content'];
            if (Common::isUnderscoreName($content) !== true) {
                $newContent = str_replace(' ', '_', $content);
                $nameBits   = explode('_', $newContent);
                $firstBit   = array_shift($nameBits);
                $newName    = ucfirst($firstBit).'_';
                foreach ($nameBits as $bit) {
                    if ($bit !== '') {
                        $newName .= ucfirst($bit).'_';
                    }
                }

                $error     = 'Category name "%s" is not valid; consider "%s" instead';
                $validName = trim($newName, '_');
                $data      = [
                    $content,
                    $validName,
                ];
                $phpcsFile->addError($error, $tag, 'InvalidCategory', $data);
            }
        }//end foreach

    }//end processCategory()


    /**
     * Process the package tag.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param array                       $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processPackage($phpcsFile, array $tags)
    {
        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                // No content.
                continue;
            }

            $content = $tokens[($tag + 2)]['content'];
            if (Common::isUnderscoreName($content) === true) {
                continue;
            }

            $newContent = str_replace(' ', '_', $content);
            $newContent = trim($newContent, '_');
            $newContent = preg_replace('/[^A-Za-z_]/', '', $newContent);

            if ($newContent === '') {
                $error = 'Package name "%s" is not valid';
                $data  = [$content];
                $phpcsFile->addError($error, $tag, 'InvalidPackageValue', $data);
            } else {
                $nameBits = explode('_', $newContent);
                $firstBit = array_shift($nameBits);
                $newName  = strtoupper($firstBit[0]).substr($firstBit, 1).'_';
                foreach ($nameBits as $bit) {
                    if ($bit !== '') {
                        $newName .= strtoupper($bit[0]).substr($bit, 1).'_';
                    }
                }

                $error     = 'Package name "%s" is not valid; consider "%s" instead';
                $validName = trim($newName, '_');
                $data      = [
                    $content,
                    $validName,
                ];
                $phpcsFile->addError($error, $tag, 'InvalidPackage', $data);
            }//end if
        }//end foreach

    }//end processPackage()


    /**
     * Process the subpackage tag.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param array                       $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processSubpackage($phpcsFile, array $tags)
    {
        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                // No content.
                continue;
            }

            $content = $tokens[($tag + 2)]['content'];
            if (Common::isUnderscoreName($content) === true) {
                continue;
            }

            $newContent = str_replace(' ', '_', $content);
            $nameBits   = explode('_', $newContent);
            $firstBit   = array_shift($nameBits);
            $newName    = strtoupper($firstBit[0]).substr($firstBit, 1).'_';
            foreach ($nameBits as $bit) {
                if ($bit !== '') {
                    $newName .= strtoupper($bit[0]).substr($bit, 1).'_';
                }
            }

            $error     = 'Subpackage name "%s" is not valid; consider "%s" instead';
            $validName = trim($newName, '_');
            $data      = [
                $content,
                $validName,
            ];
            $phpcsFile->addError($error, $tag, 'InvalidSubpackage', $data);
        }//end foreach

    }//end processSubpackage()


    /**
     * Process the author tag(s) that this header comment has.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param array                       $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processAuthor($phpcsFile, array $tags)
    {
        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                // No content.
                continue;
            }

            $content = $tokens[($tag + 2)]['content'];
            $local   = '\da-zA-Z-_+';
            // Dot character cannot be the first or last character in the local-part.
            $localMiddle = $local.'.\w';
            if (preg_match('/^([^<]*)\s+<(['.$local.'](['.$localMiddle.']*['.$local.'])*@[\da-zA-Z][-.\w]*[\da-zA-Z]\.[a-zA-Z]{2,})>$/', $content) === 0) {
                $error = 'Content of the @author tag must be in the form "Display Name <username@example.com>"';
                $phpcsFile->addError($error, $tag, 'InvalidAuthors');
            }
        }

    }//end processAuthor()


    /**
     * Process the copyright tags.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param array                       $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processCopyright($phpcsFile, array $tags)
    {
        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                // No content.
                continue;
            }

            $content = $tokens[($tag + 2)]['content'];
            $matches = [];
            if (preg_match('/^([0-9]{4})((.{1})([0-9]{4}))? (.+)$/', $content, $matches) !== 0) {
                // Check earliest-latest year order.
                if ($matches[3] !== '' && $matches[3] !== null) {
                    if ($matches[3] !== '-') {
                        $error = 'A hyphen must be used between the earliest and latest year';
                        $phpcsFile->addError($error, $tag, 'CopyrightHyphen');
                    }

                    if ($matches[4] !== '' && $matches[4] !== null && $matches[4] < $matches[1]) {
                        $error = "Invalid year span \"$matches[1]$matches[3]$matches[4]\" found; consider \"$matches[4]-$matches[1]\" instead";
                        $phpcsFile->addWarning($error, $tag, 'InvalidCopyright');
                    }
                }
            } else {
                $error = '@copyright tag must contain a year and the name of the copyright holder';
                $phpcsFile->addError($error, $tag, 'IncompleteCopyright');
            }
        }//end foreach

    }//end processCopyright()


    /**
     * Process the license tag.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param array                       $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processLicense($phpcsFile, array $tags)
    {
        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                // No content.
                continue;
            }

            $content = $tokens[($tag + 2)]['content'];
            $matches = [];
            preg_match('/^([^\s]+)\s+(.*)/', $content, $matches);
            if (count($matches) !== 3) {
                $error = '@license tag must contain a URL and a license name';
                $phpcsFile->addError($error, $tag, 'IncompleteLicense');
            }
        }

    }//end processLicense()


    /**
     * Process the version tag.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param array                       $tags      The tokens for these tags.
     *
     * @return void
     */
    protected function processVersion($phpcsFile, array $tags)
    {
        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                // No content.
                continue;
            }

            $content = $tokens[($tag + 2)]['content'];
            if (strstr($content, 'CVS:') === false
                && strstr($content, 'SVN:') === false
                && strstr($content, 'GIT:') === false
                && strstr($content, 'HG:') === false
            ) {
                $error = 'Invalid version "%s" in file comment; consider "CVS: <cvs_id>" or "SVN: <svn_id>" or "GIT: <git_id>" or "HG: <hg_id>" instead';
                $data  = [$content];
                $phpcsFile->addWarning($error, $tag, 'InvalidVersion', $data);
            }
        }

    }//end processVersion()


}//end class
