<?php
/**
 * Checks that strict types are declared in the PHP file.
 *
 * @author    Michał Bundyra <contact@webimpress.com>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class DeclareStrictTypesSniff implements Sniff
{
    /**
     * Comment with one of these tags will be omitted. The strict_types
     * declaration will be placed the next line below the comment.
     * Otherwise it will be placed line below PHP opening tag.
     *
     * @var array
     */
    public $omitCommentWithTags = [
        '@author',
        '@copyright',
        '@license',
    ];

    /**
     * How declaration should be formatted.
     *
     * @var string
     */
    public $format = 'declare(strict_types=1);';

    /**
     * Number of blank lines before declaration.
     *
     * @var integer
     */
    public $linesBefore = 0;

    /**
     * Number of blank lines after declaration.
     *
     * @var integer
     */
    public $linesAfter = 1;


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
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $this->linesBefore = (int) $this->linesBefore;
        $this->linesAfter  = (int) $this->linesAfter;

        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($tokens[$next]['code'] === T_DECLARE) {
            $eos    = $phpcsFile->findEndOfStatement($next);
            $string = $phpcsFile->getTokensAsString($next, ($eos - $next + 1));

            if (stripos($string, 'strict_types') !== false) {
                // Check how many blank lines is before declare statement.
                $prev        = $phpcsFile->findPrevious(T_WHITESPACE, ($next - 1), null, true);
                $linesBefore = ($tokens[$next]['line'] - $tokens[$prev]['line'] - 1);
                if ($linesBefore !== $this->linesBefore) {
                    $error = 'Invalid number of blank lines before declare statement; expected %d, but found %d';
                    $data  = [
                        $this->linesBefore,
                        $linesBefore,
                    ];
                    $fix   = $phpcsFile->addFixableError($error, $next, 'LinesBefore', $data);

                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        if ($linesBefore > $this->linesBefore) {
                            // Remove additional blank line(s).
                            for ($i = ($prev + 1); $i < $next; ++$i) {
                                $phpcsFile->fixer->replaceToken($i, '');
                                if (($tokens[$next]['line'] - $tokens[($i + 1)]['line'] - 1) === $this->linesBefore) {
                                    break;
                                }
                            }
                        } else {
                            // Add new blank line(s).
                            while ($linesBefore < $this->linesBefore) {
                                $phpcsFile->fixer->addNewlineBefore($next);
                                ++$linesBefore;
                            }
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }//end if

                // Check number of blank lines after the declare statement.
                $after = $phpcsFile->findNext(T_WHITESPACE, ($eos + 1), null, true);
                if ($after !== false) {
                    $linesAfter = ($tokens[$after]['line'] - $tokens[$eos]['line'] - 1);
                    if ($linesAfter !== $this->linesAfter) {
                        $error = 'Invalid number of blank lines after declare statement; expected %d, but found %d';
                        $data  = [
                            $this->linesAfter,
                            $linesAfter,
                        ];
                        $fix   = $phpcsFile->addFixableError($error, $eos, 'LinesAfter', $data);

                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            if ($linesAfter > $this->linesAfter) {
                                for ($i = ($eos + 1); $i < $after; ++$i) {
                                    $phpcsFile->fixer->replaceToken($i, '');
                                    if (($tokens[($i + 1)]['line'] - $tokens[$after]['line'] - 1) === $this->linesAfter) {
                                        break;
                                    }
                                }
                            } else {
                                while ($linesAfter < $this->linesAfter) {
                                    $phpcsFile->fixer->addNewline($eos);
                                    ++$linesAfter;
                                }
                            }

                            $phpcsFile->fixer->endChangeset();
                        }
                    }//end if
                }//end if

                // Check if declare statement match provided format.
                if ($string !== $this->format) {
                    $error = 'Invalid format of declaration; expected "%s", but found "%s"';
                    $data  = [
                        $this->format,
                        $string,
                    ];
                    $fix   = $phpcsFile->addFixableError($error, $next, 'InvalidFormat', $data);

                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = $next; $i < $eos; ++$i) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->replaceToken($eos, $this->format);
                        $phpcsFile->fixer->endChangeset();
                    }
                }

                return (count($tokens) + 1);
            }//end if
        }//end if

        $error = 'Missing declaration of strict types at the beginning of the file';
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NotFound');

        if ($fix === true) {
            $after = $stackPtr;
            $first = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($first !== null && $tokens[$first]['code'] === T_DOC_COMMENT_OPEN_TAG) {
                foreach ($tokens[$first]['comment_tags'] as $tag) {
                    if (in_array(strtolower($tokens[$tag]['content']), $this->omitCommentWithTags, true)) {
                        $after = $tokens[$first]['comment_closer'];
                        break;
                    }
                }
            }

            $phpcsFile->fixer->beginChangeset();
            if ($after > $stackPtr) {
                $phpcsFile->fixer->addNewline($after);
            }

            $phpcsFile->fixer->addContent($after, 'declare(strict_types=1);');
            if ($after === $stackPtr) {
                $phpcsFile->fixer->addNewline($after);
            }

            $phpcsFile->fixer->endChangeset();
        }//end if

        return (count($tokens) + 1);

    }//end process()


}//end class
