<?php
/**
 * Checks for "use" statements that are not needed in a file.
 *
 * @author    Michał Bundyra <contact@webimpress.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2007-2014 Mayflower GmbH
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class UnusedUseSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_USE];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack passed in $tokens.
     *
     * @return int|void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Only check use statements in the global scope.
        if ($this->isGlobalUse($phpcsFile, $stackPtr) === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $semiColon = $phpcsFile->findEndOfStatement($stackPtr);
        $prev      = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($semiColon - 1), null, true);

        if ($tokens[$prev]['code'] === T_CLOSE_USE_GROUP) {
            $to   = $prev;
            $from = $phpcsFile->findPrevious(T_OPEN_USE_GROUP, ($prev - 1));

            // Empty group is invalid syntax.
            if ($phpcsFile->findNext(Tokens::$emptyTokens, ($from + 1), null, true) === $to) {
                $error = 'Empty use group';

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'EmptyUseGroup');
                if ($fix === true) {
                    $this->removeUse($phpcsFile, $stackPtr, $semiColon);
                }

                return;
            }

            $comma = $phpcsFile->findNext(T_COMMA, ($from + 1), $to);
            if ($comma === false
                || $phpcsFile->findNext(Tokens::$emptyTokens, ($comma + 1), $to, true) === false
            ) {
                $error = 'Redundant use group for one declaration';

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'RedundantUseGroup');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->replaceToken($from, '');
                    $i = ($from + 1);

                    while ($tokens[$i]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($i, '');
                        ++$i;
                    }

                    if ($comma !== false) {
                        $phpcsFile->fixer->replaceToken($comma, '');
                    }

                    $phpcsFile->fixer->replaceToken($to, '');
                    $i = ($to - 1);
                    while ($tokens[$i]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken($i, '');
                        --$i;
                    }

                    $phpcsFile->fixer->endChangeset();
                }//end if

                return;
            }//end if

            $skip = (Tokens::$emptyTokens + [T_COMMA => T_COMMA]);

            $classPtr = $phpcsFile->findPrevious($skip, ($to - 1), ($from + 1), true);
            while ($classPtr !== false) {
                $to = $phpcsFile->findPrevious(T_COMMA, ($classPtr - 1), ($from + 1));

                if ($this->isClassUsed($phpcsFile, $stackPtr, $classPtr) === false) {
                    $error = 'Unused use statement "%s"';
                    $data  = [$tokens[$classPtr]['content']];

                    $fix = $phpcsFile->addFixableError($error, $classPtr, 'UnusedUseInGroup', $data);
                    if ($fix === true) {
                        if ($to === false) {
                            $first = ($from + 1);
                        } else {
                            $first = $to;
                        }

                        $last = $classPtr;
                        if ($to === false) {
                            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($classPtr + 1), null, true);
                            if ($tokens[$next]['code'] === T_COMMA) {
                                $last = $next;
                            }
                        }

                        $phpcsFile->fixer->beginChangeset();
                        for ($i = $first; $i <= $last; ++$i) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }//end if
                }//end if

                if ($to === false) {
                    break;
                }

                $classPtr = $phpcsFile->findPrevious($skip, ($to - 1), ($from + 1), true);
            }//end while

            return;
        }//end if

        do {
            $classPtr = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($semiColon - 1), null, true);
            if ($this->isClassUsed($phpcsFile, $stackPtr, $classPtr) === false) {
                $warning = 'Unused use statement "%s"';
                $data    = [$tokens[$classPtr]['content']];
                $fix     = $phpcsFile->addFixableError($warning, $stackPtr, 'UnusedUse', $data);

                if ($fix === true) {
                    $prev = $phpcsFile->findPrevious(
                        (Tokens::$emptyTokens + [
                            T_STRING       => T_STRING,
                            T_NS_SEPARATOR => T_NS_SEPARATOR,
                            T_AS           => T_AS,
                        ]),
                        $classPtr,
                        null,
                        true
                    );

                    $to = $semiColon;
                    if ($tokens[$prev]['code'] === T_COMMA) {
                        $from = $prev;
                        $to   = $classPtr;
                    } else if ($tokens[$semiColon]['code'] === T_SEMICOLON) {
                        $from = $stackPtr;
                    } else {
                        $from = $phpcsFile->findNext(Tokens::$emptyTokens, ($prev + 1), null, true);
                        if ($tokens[$from]['code'] === T_STRING
                            && in_array(strtolower($tokens[$from]['content']), ['const', 'function'], true) === true
                        ) {
                            $from = $phpcsFile->findNext(Tokens::$emptyTokens, ($from + 1), null, true);
                        }
                    }

                    $this->removeUse($phpcsFile, $from, $to);
                }//end if
            }//end if

            if ($tokens[$semiColon]['code'] === T_SEMICOLON) {
                break;
            }

            $semiColon = $phpcsFile->findEndOfStatement($semiColon + 1);
        } while ($semiColon !== false);

    }//end process()


    /**
     * Check if the use is global.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack passed in $tokens.
     *
     * @return bool
     */
    private function isGlobalUse(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return false;
        }

        // Ignore USE keywords for traits.
        if ($phpcsFile->hasCondition($stackPtr, [T_CLASS, T_TRAIT, T_ANON_CLASS]) === true) {
            return false;
        }

        return true;

    }//end isGlobalUse()


    /**
     * Remove the use.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $from      The start of the use to remove.
     * @param int  $to        The end of the use to remove.
     *
     * @return void
     */
    private function removeUse(File $phpcsFile, $from, $to)
    {
        $tokens = $phpcsFile->getTokens();

        $phpcsFile->fixer->beginChangeset();

        // Remote whitespaces before in the same line.
        if ($tokens[($from - 1)]['code'] === T_WHITESPACE
            && $tokens[($from - 1)]['line'] === $tokens[$from]['line']
            && $tokens[($from - 2)]['line'] !== $tokens[$from]['line']
        ) {
            $phpcsFile->fixer->replaceToken(($from - 1), '');
        }

        for ($i = $from; $i <= $to; ++$i) {
            $phpcsFile->fixer->replaceToken($i, '');
        }

        // Also remove whitespace after the semicolon (new lines).
        if (isset($tokens[($to + 1)]) === true && $tokens[($to + 1)]['code'] === T_WHITESPACE) {
            $phpcsFile->fixer->replaceToken(($to + 1), '');
        }

        $phpcsFile->fixer->endChangeset();

    }//end removeUse()


    /**
     * Check if the class is used.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $usePtr    The position of the current use.
     * @param int  $classPtr  The position of the class to check.
     *
     * @return bool
     */
    private function isClassUsed(File $phpcsFile, $usePtr, $classPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Search where the class name is used. PHP treats class names case
        // insensitive, that's why we cannot search for the exact class name string
        // and need to iterate over all T_STRING tokens in the file.
        $classUsed = $phpcsFile->findNext(
            [
                T_STRING,
                T_DOC_COMMENT_STRING,
                T_DOC_COMMENT_TAG,
                T_NAMESPACE,
            ],
            ($classPtr + 1)
        );
        $className = $tokens[$classPtr]['content'];

        // Check if the referenced class is in the same namespace as the current
        // file. If it is then the use statement is not necessary.
        $namespacePtr = $phpcsFile->findPrevious(T_NAMESPACE, $usePtr);
        while ($namespacePtr !== false && $this->isNamespace($phpcsFile, $namespacePtr) === false) {
            $phpcsFile->findPrevious(T_NAMESPACE, ($namespacePtr - 1));
        }

        $namespaceEnd = null;
        if ($namespacePtr !== false && isset($tokens[$namespacePtr]['scope_closer']) === true) {
            $namespaceEnd = $tokens[$namespacePtr]['scope_closer'];
        }

        $type = 'class';
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($usePtr + 1), null, true);
        if ($tokens[$next]['code'] === T_STRING
            && in_array(strtolower($tokens[$next]['content']), ['const', 'function'], true) === true
        ) {
            $type = strtolower($tokens[$next]['content']);
        }

        if ($type === 'const') {
            $searchName = $className;
        } else {
            $searchName = strtolower($className);
        }

        $prev = $phpcsFile->findPrevious(
            (Tokens::$emptyTokens + [
                T_STRING       => T_STRING,
                T_NS_SEPARATOR => T_NS_SEPARATOR,
            ]),
            ($classPtr - 1),
            null,
            $usePtr
        );

        // Only if alias is not used.
        if ($tokens[$prev]['code'] !== T_AS) {
            $isGroup = $tokens[$prev]['code'] === T_OPEN_USE_GROUP
                || $phpcsFile->findPrevious(T_OPEN_USE_GROUP, $prev, $usePtr) !== false;

            $useNamespace = '';
            if ($isGroup === true || $tokens[$prev]['code'] !== T_COMMA) {
                if ($type === 'class') {
                    $useNamespacePtr = $next;
                } else {
                    $useNamespacePtr = ($next + 1);
                }

                $useNamespace = $this->getNamespace(
                    $phpcsFile,
                    $useNamespacePtr,
                    [
                        T_OPEN_USE_GROUP,
                        T_COMMA,
                        T_AS,
                        T_SEMICOLON,
                    ]
                );

                if ($isGroup === true) {
                    $useNamespace .= '\\';
                }
            }//end if

            if ($tokens[$prev]['code'] === T_COMMA || $tokens[$prev]['code'] === T_OPEN_USE_GROUP) {
                $useNamespace .= $this->getNamespace(
                    $phpcsFile,
                    ($prev + 1),
                    [
                        T_CLOSE_USE_GROUP,
                        T_COMMA,
                        T_AS,
                        T_SEMICOLON,
                    ]
                );
            }

            $pos = strrpos($useNamespace, '\\');
            if ($pos === false) {
                $pos = 0;
            }

            $useNamespace = substr($useNamespace, 0, $pos);

            if ($namespacePtr !== false) {
                $namespace = $this->getNamespace($phpcsFile, ($namespacePtr + 1), [T_CURLY_OPEN, T_SEMICOLON]);

                if (strcasecmp($namespace, $useNamespace) === 0) {
                    $classUsed = false;
                }
            } else if ($namespacePtr === false && $useNamespace === '') {
                $classUsed = false;
            }
        }//end if

        $emptyTokens = Tokens::$emptyTokens;
        unset($emptyTokens[T_DOC_COMMENT_TAG]);

        while ($classUsed !== false && $this->isNamespace($phpcsFile, $classUsed) === false) {
            $isStringToken = $tokens[$classUsed]['code'] === T_STRING;

            $match = null;

            if (($isStringToken === true
                && (($type !== 'const' && strtolower($tokens[$classUsed]['content']) === $searchName)
                || ($type === 'const' && $tokens[$classUsed]['content'] === $searchName)))
                || ($type === 'class'
                && (($tokens[$classUsed]['code'] === T_DOC_COMMENT_STRING
                && preg_match(
                    '/(\s|\||\(|^)'.preg_quote($searchName, '/').'(\s|\||\\\\|$|\[\])/i',
                    $tokens[$classUsed]['content']
                ) === 1)
                || ($tokens[$classUsed]['code'] === T_DOC_COMMENT_TAG
                && preg_match(
                    '/@'.preg_quote($searchName, '/').'(\(|\\\\|$)/i',
                    $tokens[$classUsed]['content']
                ) === 1)
                || ($isStringToken === false
                && preg_match(
                    '/"[^"]*'.preg_quote($searchName, '/').'\b[^"]*"/i',
                    $tokens[$classUsed]['content']
                ) !== 1
                && preg_match(
                    '/(?<!")@'.preg_quote($searchName, '/').'\b/i',
                    $tokens[$classUsed]['content'],
                    $match
                ) === 1)))
            ) {
                $emptyTokensToUse = $emptyTokens;
                if ($isStringToken === true) {
                    $emptyTokensToUse = Tokens::$emptyTokens;
                }

                $beforeUsage = $phpcsFile->findPrevious(
                    $emptyTokensToUse,
                    ($classUsed - 1),
                    null,
                    true
                );

                if ($isStringToken === true) {
                    if ($this->determineType($phpcsFile, $beforeUsage, $classUsed) === $type) {
                        return true;
                    }
                } else if (T_DOC_COMMENT_STRING === $tokens[$classUsed]['code']) {
                    if (T_DOC_COMMENT_TAG === $tokens[$beforeUsage]['code']
                        && in_array(
                            $tokens[$beforeUsage]['content'],
                            [
                                '@var',
                                '@param',
                                '@return',
                                '@throws',
                                '@method',
                                '@property',
                                '@property-read',
                                '@property-write',
                            ],
                            true
                        ) === true
                    ) {
                        return true;
                    }

                    if ($match !== null) {
                        return true;
                    }
                } else {
                    return true;
                }//end if
            }//end if

            $classUsed = $phpcsFile->findNext(
                [
                    T_STRING,
                    T_DOC_COMMENT_STRING,
                    T_DOC_COMMENT_TAG,
                    T_NAMESPACE,
                ],
                ($classUsed + 1),
                $namespaceEnd
            );
        }//end while

        return false;

    }//end isClassUsed()


    /**
     * Check if the stackPtr is a namespace construct.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack passed in $tokens.
     *
     * @return bool
     */
    private function isNamespace(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_NAMESPACE) {
            return false;
        }

        $nextNonEmpty = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        return $nextNonEmpty === false || $tokens[$nextNonEmpty]['code'] !== T_NS_SEPARATOR;

    }//end isNamespace()


    /**
     * Return the nasmespace of the class.
     *
     * @param File  $phpcsFile The file being scanned.
     * @param int   $ptr       The position of the current token.
     * @param array $stop      List of token to end the namespace.
     *
     * @return string
     */
    private function getNamespace(File $phpcsFile, $ptr, array $stop)
    {
        $tokens = $phpcsFile->getTokens();

        $result = '';
        while (in_array($tokens[$ptr]['code'], $stop, true) === false) {
            if (in_array($tokens[$ptr]['code'], [T_STRING, T_NS_SEPARATOR], true) === true) {
                $result .= $tokens[$ptr]['content'];
            }

            ++$ptr;
        }

        return trim(trim($result), '\\');

    }//end getNamespace()


    /**
     * Return the type of the current token.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $beforePtr The position of the previous token.
     * @param int  $ptr       The position of the current token.
     *
     * @return string|null
     */
    private function determineType(File $phpcsFile, $beforePtr, $ptr)
    {
        $tokens = $phpcsFile->getTokens();

        $beforeCode = $tokens[$beforePtr]['code'];

        if (in_array(
            $beforeCode,
            [
                T_NS_SEPARATOR,
                T_OBJECT_OPERATOR,
                T_DOUBLE_COLON,
                T_FUNCTION,
                T_CONST,
                T_AS,
                T_INSTEADOF,
            ],
            true
        ) === true
        ) {
            return null;
        }

        if (in_array(
            $beforeCode,
            [
                T_NEW,
                T_NULLABLE,
                T_EXTENDS,
                T_IMPLEMENTS,
                T_INSTANCEOF,
            ],
            true
        ) === true
        ) {
            return 'class';
        }

        // Trait usage.
        if ($beforeCode === T_USE) {
            if ($this->isTraitUse($phpcsFile, $beforePtr) === true) {
                return 'class';
            }

            return null;
        }

        if ($beforeCode === T_COMMA) {
            $prev = $phpcsFile->findPrevious(
                (Tokens::$emptyTokens + [
                    T_STRING       => T_STRING,
                    T_NS_SEPARATOR => T_NS_SEPARATOR,
                    T_COMMA        => T_COMMA,
                ]),
                ($beforePtr - 1),
                null,
                true
            );

            if ($tokens[$prev]['code'] === T_IMPLEMENTS || $tokens[$prev]['code'] === T_EXTENDS) {
                return 'class';
            }
        }

        $afterPtr  = $phpcsFile->findNext(Tokens::$emptyTokens, ($ptr + 1), null, true);
        $afterCode = $tokens[$afterPtr]['code'];

        if ($afterCode === T_AS) {
            return null;
        }

        if ($afterCode === T_OPEN_PARENTHESIS) {
            return 'function';
        }

        if (in_array(
            $afterCode,
            [
                T_DOUBLE_COLON,
                T_VARIABLE,
                T_ELLIPSIS,
                T_NS_SEPARATOR,
                T_OPEN_CURLY_BRACKET,
            ],
            true
        ) === true
        ) {
            return 'class';
        }

        if ($beforeCode === T_COLON) {
            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($beforePtr - 1), null, true);
            if ($prev !== false
                && $tokens[$prev]['code'] === T_CLOSE_PARENTHESIS
                && isset($tokens[$prev]['parenthesis_owner']) === true
                && $tokens[$tokens[$prev]['parenthesis_owner']]['code'] === T_FUNCTION
            ) {
                return 'class';
            }
        }

        if ($afterCode === T_BITWISE_OR) {
            $next = $phpcsFile->findNext(
                (Tokens::$emptyTokens + [
                    T_BITWISE_OR   => T_BITWISE_OR,
                    T_STRING       => T_STRING,
                    T_NS_SEPARATOR => T_NS_SEPARATOR,
                ]),
                $afterPtr,
                null,
                true
            );

            if ($tokens[$next]['code'] === T_VARIABLE) {
                return 'class';
            }
        }

        return 'const';

    }//end determineType()


    /**
     * Check if using a trait.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack passed in $tokens.
     *
     * @return bool
     */
    private function isTraitUse(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return false;
        }

        // Ignore global USE keywords.
        if ($phpcsFile->hasCondition($stackPtr, [T_CLASS, T_TRAIT, T_ANON_CLASS]) === false) {
            return false;
        }

        return true;

    }//end isTraitUse()


}//end class
