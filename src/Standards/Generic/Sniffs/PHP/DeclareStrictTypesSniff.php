<?php
/**
 * Checks that strict types are declared in the PHP file.
 *
 * @author    Michał Bundyra <contact@webimpress.com>
 * @copyright 2006-2018 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class DeclareStrictTypesSniff implements Sniff
{
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
    public $spacingBefore = 1;

    /**
     * Number of blank lines after declaration.
     *
     * @var integer
     */
    public $spacingAfter = 1;


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
        $this->spacingBefore = (int) $this->spacingBefore;
        $this->spacingAfter  = (int) $this->spacingAfter;

        $tokens = $phpcsFile->getTokens();

        if ($stackPtr > 0) {
            $before = trim($phpcsFile->getTokensAsString(0, $stackPtr));

            if ($before === '') {
                $error = 'Unexpected whitespace before PHP opening tag';
                $fix   = $phpcsFile->addFixableError($error, 0, 'Whitespace');

                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = 0; $i < $stackPtr; ++$i) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            } else {
                $error = 'Missing strict type declaration as first statement in the script';
                $fix   = $phpcsFile->addFixableError($error, 0, 'Missing');

                if ($fix === true) {
                    $phpcsFile->fixer->addContentBefore(
                        0,
                        sprintf('<?php %s ?>%s', $this->format, $phpcsFile->eolChar)
                    );
                }
            }//end if

            $this->checkOtherDeclarations($phpcsFile);

            return ($phpcsFile->numTokens + 1);
        }//end if

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($tokens[$next]['code'] === T_DECLARE) {
            $string = $phpcsFile->findNext(
                T_STRING,
                ($tokens[$next]['parenthesis_opener'] + 1),
                $tokens[$next]['parenthesis_closer']
            );

            if ($string !== false
                && stripos($tokens[$string]['content'], 'strict_types') !== false
            ) {
                $eos   = $phpcsFile->findEndOfStatement($next);
                $prev  = $phpcsFile->findPrevious(T_WHITESPACE, ($next - 1), null, true);
                $after = $phpcsFile->findNext(T_WHITESPACE, ($eos + 1), null, true);

                if ($after !== false
                    && $tokens[$prev]['code'] === T_OPEN_TAG
                    && $tokens[$after]['code'] === T_CLOSE_TAG
                ) {
                    if ($tokens[$prev]['line'] !== $tokens[$next]['line']) {
                        $error = 'PHP open tag must be on the same line as strict type declaration.';
                        $fix   = $phpcsFile->addFixableError($error, $prev, 'OpenTag');

                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken($prev, '<?php ');
                            for ($i = ($prev + 1); $i < $next; ++$i) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->endChangeset();
                        }

                        $prev = false;
                    }//end if

                    if ($prev !== false && ($prev < ($next - 1) || $tokens[$prev]['content'] !== '<?php ')) {
                        $error = 'Expected single space after PHP open tag and before strict type declaration.';
                        $fix   = $phpcsFile->addFixableError($error, $prev, 'OpenTagSpace');

                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken($prev, '<?php ');
                            for ($i = ($prev + 1); $i < $next; ++$i) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->endChangeset();
                        }
                    }

                    if ($tokens[$after]['line'] !== $tokens[$eos]['line']) {
                        $error = 'PHP close tag must be on the same line as strict type declaration.';
                        $fix   = $phpcsFile->addFixableError($error, $after, 'CloseTag');

                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            for ($i = ($eos + 1); $i < $after; ++$i) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->addContentBefore($after, ' ');
                            $phpcsFile->fixer->endChangeset();
                        }

                        $after = false;
                    }//end if

                    if ($after !== false && ($after > ($eos + 2) || $tokens[($eos + 1)]['content'] !== ' ')) {
                        $error = 'Expected single space before PHP close tag and after declaration.';
                        $fix   = $phpcsFile->addFixableError($error, $after, 'CloseTagSpace');

                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            for ($i = ($eos + 1); $i < $after; ++$i) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->addContentBefore($after, ' ');
                            $phpcsFile->fixer->endChangeset();
                        }
                    }//end if

                    $prev  = false;
                    $after = false;
                }//end if

                // Check how many blank lines there are before declare statement.
                if ($prev !== false) {
                    $linesBefore = ($tokens[$next]['line'] - $tokens[$prev]['line'] - 1);
                    if ($linesBefore !== $this->spacingBefore) {
                        if ($linesBefore < 0) {
                            $error = 'Strict type declaration must be in new line';
                            $data  = [];
                        } else {
                            $error = 'Invalid number of blank lines before declare statement; expected %d, but found %d';
                            $data  = [
                                $this->spacingBefore,
                                $linesBefore,
                            ];
                        }

                        $fix = $phpcsFile->addFixableError($error, $next, 'LinesBefore', $data);

                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            if ($linesBefore > $this->spacingBefore) {
                                // Remove additional blank line(s).
                                for ($i = ($prev + 1); $i < $next; ++$i) {
                                    $phpcsFile->fixer->replaceToken($i, '');
                                    if (($tokens[$next]['line'] - $tokens[($i + 1)]['line'] - 1) === $this->spacingBefore) {
                                        break;
                                    }
                                }
                            } else {
                                // Clear whitespaces between prev and next, but no new lines.
                                if ($linesBefore < 0) {
                                    for ($i = ($prev + 1); $i < $next; ++$i) {
                                        $phpcsFile->fixer->replaceToken($i, '');
                                    }
                                }

                                // Add new blank line(s).
                                while ($linesBefore < $this->spacingBefore) {
                                    $phpcsFile->fixer->addNewlineBefore($next);
                                    ++$linesBefore;
                                }
                            }//end if

                            $phpcsFile->fixer->endChangeset();
                        }//end if
                    }//end if
                }//end if

                // Check number of blank lines after the declare statement.
                if ($after !== false) {
                    if ($tokens[$after]['code'] === T_CLOSE_TAG) {
                        $this->spacingAfter = 0;
                    }

                    $linesAfter = ($tokens[$after]['line'] - $tokens[$eos]['line'] - 1);
                    if ($linesAfter !== $this->spacingAfter) {
                        if ($linesAfter < 0) {
                            $error = 'Strict type declaration must be the only statement in the line';
                            $data  = [];
                        } else {
                            $error = 'Invalid number of blank lines after declare statement; expected %d, but found %d';
                            $data  = [
                                $this->spacingAfter,
                                $linesAfter,
                            ];
                        }

                        $fix = $phpcsFile->addFixableError($error, $eos, 'LinesAfter', $data);

                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            if ($linesAfter > $this->spacingAfter) {
                                for ($i = ($eos + 1); $i < $after; ++$i) {
                                    $phpcsFile->fixer->replaceToken($i, '');
                                    if (($tokens[$after]['line'] - $tokens[($i + 1)]['line'] - 1) === $this->spacingAfter) {
                                        break;
                                    }
                                }
                            } else {
                                // Remove whitespaces between EOS and after token.
                                if ($linesAfter < 0) {
                                    for ($i = ($eos + 1); $i < $after; ++$i) {
                                        $phpcsFile->fixer->replaceToken($i, '');
                                    }
                                }

                                // Add new lines after the statement.
                                while ($linesAfter < $this->spacingAfter) {
                                    $phpcsFile->fixer->addNewline($eos);
                                    ++$linesAfter;
                                }
                            }//end if

                            $phpcsFile->fixer->endChangeset();
                        }//end if
                    }//end if
                }//end if

                // Check if declare statement match provided format.
                $string = $phpcsFile->getTokensAsString($next, ($eos - $next + 1));
                if ($string !== $this->format) {
                    $error = 'Invalid format of strict type declaration; expected "%s", but found "%s"';
                    $data  = [
                        $this->format,
                        $string,
                    ];

                    if ($this->normalize($string) === $this->normalize($this->format)) {
                        $fix = $phpcsFile->addFixableError($error, $next, 'InvalidFormat', $data);

                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            for ($i = $next; $i < $eos; ++$i) {
                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->replaceToken($eos, $this->format);
                            $phpcsFile->fixer->endChangeset();
                        }
                    } else {
                        $phpcsFile->addError($error, $next, 'InvalidFormatNotFixable', $data);
                    }
                }//end if

                $this->checkOtherDeclarations($phpcsFile, $next);

                return ($phpcsFile->numTokens + 1);
            }//end if
        }//end if

        $this->checkOtherDeclarations($phpcsFile, $next);

        $error = 'Missing strict type declaration at the beginning of the file';
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NotFound');

        if ($fix === true) {
            $phpcsFile->fixer->addContent($stackPtr, $this->format.$phpcsFile->eolChar);
        }

        return ($phpcsFile->numTokens + 1);

    }//end process()


    /**
     * Normalize given string by removing all white characters
     * and changed to lower case.
     *
     * @param string $string String to be normalized.
     *
     * @return string
     */
    private function normalize($string)
    {
        return strtolower(preg_replace('/\s/', '', $string));

    }//end normalize()


    /**
     * Process other strict_type declaration in the file and remove them.
     * The declaration has to be the very first statement in the script.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $declare   The position of the first declaration.
     *
     * @return void
     */
    private function checkOtherDeclarations(File $phpcsFile, $declare=0)
    {
        $tokens = $phpcsFile->getTokens();

        while (($declare = $phpcsFile->findNext(T_DECLARE, ($declare + 1))) !== false) {
            $string = $phpcsFile->findNext(
                T_STRING,
                ($tokens[$declare]['parenthesis_opener'] + 1),
                $tokens[$declare]['parenthesis_closer']
            );

            if ($string !== false
                && stripos($tokens[$string]['content'], 'strict_types') !== false
            ) {
                $error = 'Strict type declaration must be the very first statement in the script';
                $fix   = $phpcsFile->addFixableError($error, $declare, 'NotFirstStatement');

                if ($fix === true) {
                    $end = $phpcsFile->findNext(
                        (Tokens::$emptyTokens + [T_SEMICOLON => T_SEMICOLON]),
                        ($tokens[$declare]['parenthesis_closer'] + 1),
                        null,
                        true
                    );

                    if ($end === false) {
                        $end = $phpcsFile->numTokens;
                    }

                    for ($i = $declare; $i < $end; ++$i) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                }
            }//end if
        }//end while

    }//end checkOtherDeclarations()


}//end class
