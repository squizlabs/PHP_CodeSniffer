<?php
/**
 * Parses and verifies the file doc comment.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Parses and verifies the file doc comment.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

class Squiz_Sniffs_Commenting_FileCommentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The expected name of the author.
     *
     * @var string
     */
    public $authorName = 'Squiz Pty Ltd';

    /**
     * The expected email address of the author.
     *
     * @var string
     */
    public $authorEmail = 'products@squiz.net';

    /**
     * The expected full content of the author tag.
     *
     * @var string
     */
    private $_expectedAuthor;

    /**
     * The exxpected minimum copyright year.
     *
     * @var string
     */
    public $copyrightMinYear = 2006;

    /**
     * The expected name for who has copyright to the code.
     *
     * @var string
     */
    public $copyrightName = 'Squiz Pty Ltd (ABN 77 084 670 600)';

    /**
     * The expected full content of the copyright tag.
     *
     * @var string
     */
    private $_expectedCopyright;

    /**
     * The regular expression used to check the copyright tag
     *
     * @var string
     */
    private $_copyrightRegex;

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );


    /**
     * Constructs the test with the tokens content it wishes to listen for.
     */
    public function __construct()
    {
        // Define the pattern of the author tag.
        // Authors do not need to specify an email address.
        $this->authorEmail = trim($this->authorEmail);
        if (empty($this->authorEmail) === false) {
            $this->_expectedAuthor = "{$this->authorName} <{$this->authorEmail}>";
        } else {
            $this->_expectedAuthor = $this->authorName;
        }//end if

        // Define the pattern of the copyright tag.
        $this->copyrightMinYear = trim($this->copyrightMinYear);
        if (empty($this->copyrightMinYear) === false) {
            $currentYear = date('Y');
            $this->_expectedCopyright = "{$this->copyrightMinYear}-{$currentYear} $this->copyrightName";

            $this->_copyrightRegex = '/^([0-9]{4})(-[0-9]{4})? '.preg_quote($this->copyrightName).'$/';
        } else {
            $this->_expectedCopyright = $this->copyrightName;
            $this->_copyrightRegex    = '/^'.preg_quote($this->copyrightName).'$/';
        }//end if

    }//end __construct()


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return int
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;

        $tokens       = $phpcsFile->getTokens();
        $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        if ($tokens[$commentStart]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a file comment', $commentStart, 'WrongStyle');
            $phpcsFile->recordMetric($stackPtr, 'File has doc comment', 'yes');
            return ($phpcsFile->numTokens + 1);
        } else if ($commentStart === false || $tokens[$commentStart]['code'] !== T_DOC_COMMENT_OPEN_TAG) {
            $phpcsFile->addError('Missing file doc comment', $stackPtr, 'Missing');
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

        $ignore = array(
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
                   T_INCLUDE,
                   T_INCLUDE_ONCE,
                   T_REQUIRE,
                   T_REQUIRE_ONCE,
                  );

        if (in_array($tokens[$nextToken]['code'], $ignore) === true) {
            $phpcsFile->addError('Missing file doc comment', $stackPtr, 'Missing');
            $phpcsFile->recordMetric($stackPtr, 'File has doc comment', 'no');
            return ($phpcsFile->numTokens + 1);
        }

        $phpcsFile->recordMetric($stackPtr, 'File has doc comment', 'yes');

        // No blank line between the open tag and the file comment.
        if ($tokens[$commentStart]['line'] > ($tokens[$stackPtr]['line'] + 1)) {
            $error = 'There must be no blank lines before the file comment';
            $phpcsFile->addError($error, $stackPtr, 'SpacingAfterOpen');
        }

        // Exactly one blank line after the file comment.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($commentEnd + 1), null, true);
        if ($tokens[$next]['line'] !== ($tokens[$commentEnd]['line'] + 2)) {
            $error = 'There must be exactly one blank line after the file comment';
            $phpcsFile->addError($error, $commentEnd, 'SpacingAfterComment');
        }

        // Required tags in correct order.
        $required = array(
                     '@package'    => true,
                     '@subpackage' => true,
                     '@author'     => true,
                     '@copyright'  => true,
                    );

        $foundTags = array();
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            $name       = $tokens[$tag]['content'];
            $isRequired = isset($required[$name]);

            if ($isRequired === true && in_array($name, $foundTags) === true) {
                $error = 'Only one %s tag is allowed in a file comment';
                $data  = array($name);
                $phpcsFile->addError($error, $tag, 'Duplicate'.ucfirst(substr($name, 1)).'Tag', $data);
            }

            $foundTags[] = $name;

            if ($isRequired === false) {
                continue;
            }

            $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag, $commentEnd);
            if ($string === false || $tokens[$string]['line'] !== $tokens[$tag]['line']) {
                $error = 'Content missing for %s tag in file comment';
                $data  = array($name);
                $phpcsFile->addError($error, $tag, 'Empty'.ucfirst(substr($name, 1)).'Tag', $data);
                continue;
            }

            if ($name === '@author') {
                if ($tokens[$string]['content'] !== $this->_expectedAuthor) {
                    $error = 'Expected "'.$this->_expectedAuthor.'" for author tag';
                    $fix   = $phpcsFile->addFixableError($error, $tag, 'IncorrectAuthor');
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken($string, $this->_expectedAuthor);
                    }
                }
            } else if ($name === '@copyright') {
                if (preg_match($this->_copyrightRegex, $tokens[$string]['content']) === 0) {
                    $error = 'Expected "'.$this->_expectedCopyright.'" for copyright declaration';
                    $fix   = $phpcsFile->addFixableError($error, $tag, 'IncorrectCopyright');
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken($string, $this->_expectedCopyright);
                    }
                }
            }//end if
        }//end foreach

        // Check if the tags are in the correct position.
        $pos = 0;
        foreach ($required as $tag => $true) {
            if (in_array($tag, $foundTags) === false) {
                $error = 'Missing %s tag in file comment';
                $data  = array($tag);
                $phpcsFile->addError($error, $commentEnd, 'Missing'.ucfirst(substr($tag, 1)).'Tag', $data);
            }

            if (isset($foundTags[$pos]) === false) {
                break;
            }

            if ($foundTags[$pos] !== $tag) {
                $error = 'The tag in position %s should be the %s tag';
                $data  = array(
                          ($pos + 1),
                          $tag,
                         );
                $phpcsFile->addError($error, $tokens[$commentStart]['comment_tags'][$pos], ucfirst(substr($tag, 1)).'TagOrder', $data);
            }

            $pos++;
        }//end foreach

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
