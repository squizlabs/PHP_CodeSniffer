<?php
/**
 * Checks the format of the file header.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class FileHeaderSniff implements Sniff
{


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
     * Processes this sniff when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current
     *                                               token in the stack.
     *
     * @return int|null
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $possibleHeaders = [];

        $searchFor = Tokens::$ooScopeTokens;
        $searchFor[T_OPEN_TAG] = T_OPEN_TAG;

        $openTag = $stackPtr;
        do {
            $headerLines = $this->getHeaderLines($phpcsFile, $openTag);
            if (empty($headerLines) === true && $openTag === $stackPtr) {
                // No content in the file.
                return;
            }

            $possibleHeaders[$openTag] = $headerLines;
            if (count($headerLines) > 1) {
                break;
            }

            $next = $phpcsFile->findNext($searchFor, ($openTag + 1));
            if (isset(Tokens::$ooScopeTokens[$tokens[$next]['code']]) === true) {
                // Once we find an OO token, the file content has
                // definitely started.
                break;
            }

            $openTag = $next;
        } while ($openTag !== false);

        if ($openTag === false) {
            // We never found a proper file header.
            // If the file has multiple PHP open tags, we know
            // that it must be a mix of PHP and HTML (or similar)
            // so the header rules do not apply.
            if (count($possibleHeaders) > 1) {
                return $phpcsFile->numTokens;
            }

            // There is only one possible header.
            // If it is the first content in the file, it technically
            // serves as the file header, and the open tag needs to
            // have a newline after it. Otherwise, ignore it.
            if ($stackPtr > 0) {
                return $phpcsFile->numTokens;
            }

            $openTag = $stackPtr;
        } else if (count($possibleHeaders) > 1) {
            // There are other PHP blocks before the file header.
            $error = 'The file header must be the first content in the file';
            $phpcsFile->addError($error, $openTag, 'HeaderPosition');
        } else {
            // The first possible header was the file header block,
            // so make sure it is the first content in the file.
            if ($openTag !== 0) {
                // Allow for hashbang lines.
                $hashbang = false;
                if ($tokens[($openTag - 1)]['code'] === T_INLINE_HTML) {
                    $content = trim($tokens[($openTag - 1)]['content']);
                    if (substr($content, 0, 2) === '#!') {
                        $hashbang = true;
                    }
                }

                if ($hashbang === false) {
                    $error = 'The file header must be the first content in the file';
                    $phpcsFile->addError($error, $openTag, 'HeaderPosition');
                }
            }
        }//end if

        $this->processHeaderLines($phpcsFile, $possibleHeaders[$openTag]);

        return $phpcsFile->numTokens;

    }//end process()


    /**
     * Gather information about the statements inside a possible file header.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current
     *                                               token in the stack.
     *
     * @return array
     */
    public function getHeaderLines(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($next === false) {
            return [];
        }

        $headerLines   = [];
        $headerLines[] = [
            'type'  => 'tag',
            'start' => $stackPtr,
            'end'   => $stackPtr,
        ];

        $foundDocblock = false;

        $commentOpeners = Tokens::$scopeOpeners;
        unset($commentOpeners[T_NAMESPACE]);
        unset($commentOpeners[T_DECLARE]);
        unset($commentOpeners[T_USE]);
        unset($commentOpeners[T_IF]);
        unset($commentOpeners[T_WHILE]);
        unset($commentOpeners[T_FOR]);
        unset($commentOpeners[T_FOREACH]);
        unset($commentOpeners[T_DO]);
        unset($commentOpeners[T_TRY]);

        do {
            switch ($tokens[$next]['code']) {
            case T_DOC_COMMENT_OPEN_TAG:
                if ($foundDocblock === true) {
                    // Found a second docblock, so start of code.
                    break(2);
                }

                // Make sure this is not a code-level docblock.
                $end      = $tokens[$next]['comment_closer'];
                $docToken = $phpcsFile->findNext(Tokens::$emptyTokens, ($end + 1), null, true);

                if (isset($commentOpeners[$tokens[$docToken]['code']]) === false
                    && isset(Tokens::$methodPrefixes[$tokens[$docToken]['code']]) === false
                ) {
                    // Check for an @var annotation.
                    $annotation = false;
                    for ($i = $next; $i < $end; $i++) {
                        if ($tokens[$i]['code'] === T_DOC_COMMENT_TAG
                            && strtolower($tokens[$i]['content']) === '@var'
                        ) {
                            $annotation = true;
                            break;
                        }
                    }

                    if ($annotation === false) {
                        $foundDocblock = true;
                        $headerLines[] = [
                            'type'  => 'docblock',
                            'start' => $next,
                            'end'   => $end,
                        ];
                    }
                }//end if

                $next = $end;
                break;
            case T_DECLARE:
            case T_NAMESPACE:
                $end = $phpcsFile->findEndOfStatement($next);

                $headerLines[] = [
                    'type'  => substr(strtolower($tokens[$next]['type']), 2),
                    'start' => $next,
                    'end'   => $end,
                ];

                $next = $end;
                break;
            case T_USE:
                $type    = 'use';
                $useType = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);
                if ($useType !== false && $tokens[$useType]['code'] === T_STRING) {
                    $content = strtolower($tokens[$useType]['content']);
                    if ($content === 'function' || $content === 'const') {
                        $type .= ' '.$content;
                    }
                }

                $end = $phpcsFile->findEndOfStatement($next);

                $headerLines[] = [
                    'type'  => $type,
                    'start' => $next,
                    'end'   => $end,
                ];

                $next = $end;
                break;
            default:
                // Skip comments as PSR-12 doesn't say if these are allowed or not.
                if (isset(Tokens::$commentTokens[$tokens[$next]['code']]) === true) {
                    $next = $phpcsFile->findNext(Tokens::$commentTokens, ($next + 1), null, true);
                    if ($next === false) {
                        // We reached the end of the file.
                        break(2);
                    }

                    $next--;
                    break;
                }

                // We found the start of the main code block.
                break(2);
            }//end switch

            $next = $phpcsFile->findNext(T_WHITESPACE, ($next + 1), null, true);
        } while ($next !== false);

        return $headerLines;

    }//end getHeaderLines()


    /**
     * Check the spacing and grouping of the statements inside each header block.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being scanned.
     * @param array                       $headerLines Header information, as sourced
     *                                                 from getHeaderLines().
     *
     * @return int|null
     */
    public function processHeaderLines(File $phpcsFile, $headerLines)
    {
        $tokens = $phpcsFile->getTokens();

        $found = [];

        foreach ($headerLines as $i => $line) {
            if (isset($headerLines[($i + 1)]) === false
                || $headerLines[($i + 1)]['type'] !== $line['type']
            ) {
                // We're at the end of the current header block.
                // Make sure there is a single blank line after
                // this block.
                $next = $phpcsFile->findNext(T_WHITESPACE, ($line['end'] + 1), null, true);
                if ($next !== false && $tokens[$next]['line'] !== ($tokens[$line['end']]['line'] + 2)) {
                    $error = 'Header blocks must be separated by a single blank line';
                    $fix   = $phpcsFile->addFixableError($error, $line['end'], 'SpacingAfterBlock');
                    if ($fix === true) {
                        if ($tokens[$next]['line'] === $tokens[$line['end']]['line']) {
                            $phpcsFile->fixer->addContentBefore($next, $phpcsFile->eolChar.$phpcsFile->eolChar);
                        } else if ($tokens[$next]['line'] === ($tokens[$line['end']]['line'] + 1)) {
                            $phpcsFile->fixer->addNewline($line['end']);
                        } else {
                            $phpcsFile->fixer->beginChangeset();
                            for ($i = ($line['end'] + 1); $i < $next; $i++) {
                                if ($tokens[$i]['line'] === ($tokens[$line['end']]['line'] + 2)) {
                                    break;
                                }

                                $phpcsFile->fixer->replaceToken($i, '');
                            }

                            $phpcsFile->fixer->endChangeset();
                        }
                    }//end if
                }//end if

                // Make sure we haven't seen this next block before.
                if (isset($headerLines[($i + 1)]) === true
                    && isset($found[$headerLines[($i + 1)]['type']]) === true
                ) {
                    $error  = 'Similar statements must be grouped together inside header blocks; ';
                    $error .= 'the first "%s" statement was found on line %s';
                    $data   = [
                        $headerLines[($i + 1)]['type'],
                        $tokens[$found[$headerLines[($i + 1)]['type']]['start']]['line'],
                    ];
                    $phpcsFile->addError($error, $headerLines[($i + 1)]['start'], 'IncorrectGrouping', $data);
                }
            } else if ($headerLines[($i + 1)]['type'] === $line['type']) {
                // Still in the same block, so make sure there is no
                // blank line after this statement.
                $next = $phpcsFile->findNext(T_WHITESPACE, ($line['end'] + 1), null, true);
                if ($tokens[$next]['line'] > ($tokens[$line['end']]['line'] + 1)) {
                    $error = 'Header blocks must not contain blank lines';
                    $fix   = $phpcsFile->addFixableError($error, $line['end'], 'SpacingInsideBlock');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($line['end'] + 1); $i < $next; $i++) {
                            if ($tokens[$i]['line'] === $tokens[$line['end']]['line']) {
                                continue;
                            }

                            if ($tokens[$i]['line'] === $tokens[$next]['line']) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }//end if

            if (isset($found[$line['type']]) === false) {
                $found[$line['type']] = $line;
            }
        }//end foreach

        /*
            Next, check that the order of the header blocks
            is correct:
                Opening php tag.
                File-level docblock.
                One or more declare statements.
                The namespace declaration of the file.
                One or more class-based use import statements.
                One or more function-based use import statements.
                One or more constant-based use import statements.
        */

        $blockOrder = [
            'tag'          => 'opening PHP tag',
            'docblock'     => 'file-level docblock',
            'declare'      => 'declare statements',
            'namespace'    => 'namespace declaration',
            'use'          => 'class-based use imports',
            'use function' => 'function-based use imports',
            'use const'    => 'constant-based use imports',
        ];

        foreach (array_keys($found) as $type) {
            if ($type === 'tag') {
                // The opening tag is always in the correct spot.
                continue;
            }

            do {
                $orderedType = next($blockOrder);
            } while ($orderedType !== false && key($blockOrder) !== $type);

            if ($orderedType === false) {
                // We didn't find the block type in the rest of the
                // ordered array, so it is out of place.
                // Error and reset the array to the correct position
                // so we can check the next block.
                reset($blockOrder);
                $prevValidType = 'tag';
                do {
                    $orderedType = next($blockOrder);
                    if (isset($found[key($blockOrder)]) === true
                        && key($blockOrder) !== $type
                    ) {
                        $prevValidType = key($blockOrder);
                    }
                } while ($orderedType !== false && key($blockOrder) !== $type);

                $error = 'The %s must follow the %s in the file header';
                $data  = [
                    $blockOrder[$type],
                    $blockOrder[$prevValidType],
                ];
                $phpcsFile->addError($error, $found[$type]['start'], 'IncorrectOrder', $data);
            }//end if
        }//end foreach

    }//end processHeaderLines()


}//end class
