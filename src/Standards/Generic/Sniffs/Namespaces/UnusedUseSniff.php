<?php
/**
 * Checks for "use" statements that are not needed in a file.
 *
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
     * @return array
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
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Only check use statements in the global scope.
        if (empty($tokens[$stackPtr]['conditions']) === false) {
            return;
        }

        $namespacePrefix = null;
        $start           = $phpcsFile->findNext([T_STRING], $stackPtr);
        $startOfGroup    = $start;
        $end = $phpcsFile->findNext(
            [
                T_COMMA,
                T_SEMICOLON,
                T_OPEN_USE_GROUP,
                T_CLOSE_USE_GROUP,
            ],
            $start
        );

        while ($end !== false) {
            $classPtr = $phpcsFile->findPrevious([T_STRING], ($end - 1));

            switch ($tokens[$end]['code']) {
            case T_SEMICOLON:
                if ($this->isSameNamespace($phpcsFile, $start, $end, $namespacePrefix) === true
                    || $this->isUsed($phpcsFile, $classPtr) === false
                ) {
                    $this->removeUse($phpcsFile, $stackPtr, $start, $end);
                }

                $end = false;

                break;
            case T_CLOSE_USE_GROUP:
                if ($this->isSameNamespace($phpcsFile, $start, $end, $namespacePrefix) === true
                    || $this->isUsed($phpcsFile, $classPtr) === false
                ) {
                    $this->removeUse($phpcsFile, $stackPtr, $startOfGroup, $end);
                }

                $end = false;

                break;
            case T_COMMA:
                if ($this->isSameNamespace($phpcsFile, $start, $end, $namespacePrefix) === true
                    || $this->isUsed($phpcsFile, $classPtr) === false
                ) {
                    $this->removeUse($phpcsFile, $stackPtr, $start, $end);
                }

                $start = $phpcsFile->findNext([T_STRING], $end);
                $end   = $phpcsFile->findNext(
                    [
                        T_COMMA,
                        T_SEMICOLON,
                        T_OPEN_USE_GROUP,
                        T_CLOSE_USE_GROUP,
                    ],
                    $start
                );

                break;
            default:
                // Case T_OPEN_USE_GROUP.
                $namespacePrefix = $this->getUseNamespace($phpcsFile, $start, $end);
                $startOfGroup    = $start;

                $start = $phpcsFile->findNext([T_STRING], $end);
                $end   = $phpcsFile->findNext(
                    [
                        T_COMMA,
                        T_SEMICOLON,
                        T_OPEN_USE_GROUP,
                        T_CLOSE_USE_GROUP,
                    ],
                    $start
                );
            }//end switch
        }//end while

    }//end process()


    /**
     * Add fixable error for the unused use
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the use token in the stack passed in $tokens.
     * @param int  $start     The start of the use statement.
     * @param int  $end       The end of the use statement.
     *
     * @return void
     */
    private function removeUse(File $phpcsFile, $stackPtr, $start, $end)
    {
        $tokens = $phpcsFile->getTokens();
        $fix    = $phpcsFile->addFixableError('Unused use statement', $start, 'UnusedUse');

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();

            $nextEnd = $phpcsFile->findNext([T_COMMA, T_SEMICOLON], $end);
            $next    = $nextEnd;

            // Try to remove the whole use statement line only if it's the last one.
            if (T_SEMICOLON === $tokens[$nextEnd]['code']) {
                $start = $stackPtr;
            }

            // Remove empty space after comma.
            if (T_COMMA === $tokens[$nextEnd]['code']) {
                $next = ($phpcsFile->findNext([T_WHITESPACE], ($nextEnd + 1), null, true) - 1);
            }

            for ($i = $next; $i >= $start; $i--) {
                $phpcsFile->fixer->replaceToken($i, '');

                if (T_COMMA === $tokens[($i - 1)]['code'] && ($i - 1) !== $nextEnd) {
                    $phpcsFile->fixer->replaceToken(($i - 1), $tokens[$end]['content']);

                    // Case of T_CLOSE_USE_GROUP, we have to add the comma or the semicolon again.
                    if ($tokens[$end]['content'] !== $tokens[$nextEnd]['content']) {
                        $phpcsFile->fixer->addContent(($i - 1), $tokens[$nextEnd]['content']);
                    }

                    break;
                }
            }

            // Also remove the empty line.
            if (T_WHITESPACE === $tokens[$i]['code']) {
                if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) !== false) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
            }

            $phpcsFile->fixer->endChangeset();
        }//end if

    }//end removeUse()


    /**
     * Check if the use is from the same namespace than the file.
     *
     * @param File        $phpcsFile       The file being scanned.
     * @param int         $start           The start of the use statement.
     * @param int         $end             The end of the use statement.
     * @param string|null $namespacePrefix Possible namespacePrefix for group use.
     *
     * @return bool
     */
    private function isSameNamespace(File $phpcsFile, $start, $end, $namespacePrefix)
    {
        // Check if the use statement does aliasing with the "as" keyword.
        // Aliasing is allowed even in the same namespace.
        $aliasUsed = $phpcsFile->findPrevious(T_AS, $end, $start);
        if ($aliasUsed !== false) {
            return false;
        }

        $namespace    = $this->getNamespace($phpcsFile, $start);
        $useNamespace = $this->getUseNamespace($phpcsFile, $start, $end);

        if ($namespace === false || $useNamespace === false) {
            return false;
        }

        if ($namespacePrefix !== null) {
            $useNamespace = rtrim("$namespacePrefix\\$useNamespace", '\\');
        }

        return strcasecmp($namespace, $useNamespace) === 0;

    }//end isSameNamespace()


    /**
     * Return the namespace of the file.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the use token in the stack passed in $tokens.
     *
     * @return bool|string
     */
    private function getNamespace(File $phpcsFile, $stackPtr)
    {
        $namespacePtr = $phpcsFile->findPrevious([T_NAMESPACE], $stackPtr);

        $namespaceEnd = $phpcsFile->findNext(
            [
                T_NS_SEPARATOR,
                T_STRING,
                T_WHITESPACE,
            ],
            ($namespacePtr + 1),
            null,
            true
        );

        if ($namespacePtr === false || $namespaceEnd === false) {
            return false;
        }

        return trim(
            $phpcsFile->getTokensAsString(
                ($namespacePtr + 1),
                ($namespaceEnd - $namespacePtr - 1)
            )
        );

    }//end getNamespace()


    /**
     * Return the namespace of the use statement.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $start     The start of the use statement.
     * @param int  $end       The end of the use statement.
     *
     * @return bool|string
     */
    private function getUseNamespace(File $phpcsFile, $start, $end)
    {
        $tokens = $phpcsFile->getTokens();

        $useNamespacePtr = $phpcsFile->findNext([T_STRING], $start);

        if ($tokens[$useNamespacePtr]['content'] === 'const'
            || $tokens[$useNamespacePtr]['content'] === 'function'
        ) {
            $useNamespacePtr = $phpcsFile->findNext([T_STRING], ($useNamespacePtr + 1));
        }

        if ($useNamespacePtr === false) {
            return false;
        }

        return trim(
            rtrim(
                $phpcsFile->getTokensAsString(
                    $useNamespacePtr,
                    ($end - $useNamespacePtr - 1)
                ),
                '\\'
            )
        );

    }//end getUseNamespace()


    /**
     * Check if the use statement is used in the code.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $classPtr  The position of the use token in the stack passed in $tokens.
     *
     * @return bool
     */
    private function isUsed(File $phpcsFile, $classPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // PHP treats class names case insensitive.
        $lowerClassName = strtolower($tokens[$classPtr]['content']);

        // Search where the class name is used.
        $classUsed = $phpcsFile->findNext([T_STRING, T_NAMESPACE], ($classPtr + 1));
        while ($classUsed !== false && $tokens[$classUsed]['code'] !== T_NAMESPACE) {
            if (strtolower($tokens[$classUsed]['content']) === $lowerClassName) {
                $beforeUsage = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens,
                    ($classUsed - 1),
                    null,
                    true
                );

                if (in_array(
                    $tokens[$beforeUsage]['code'],
                    [
                        T_USE,
                        // If a backslash is used before the class name then this is some other use statement.
                        T_NS_SEPARATOR,
                        // If an object operator is used before the class name then is a class property.
                        T_OBJECT_OPERATOR,
                    ]
                ) === false
                ) {
                    return true;
                }

                // Trait use statement within a class.
                if ($tokens[$beforeUsage]['code'] === T_USE
                    && empty($tokens[$beforeUsage]['conditions']) === false
                ) {
                    return true;
                }
            }//end if

            $classUsed = $phpcsFile->findNext([T_STRING, T_NAMESPACE], ($classUsed + 1));
        }//end while

        // More checks.
        $i = $classPtr;
        while (isset($tokens[$i]) && T_NAMESPACE !== $tokens[$i]['code']) {
            // Check for doc params @...
            if (T_DOC_COMMENT_TAG === $tokens[$i]['code']) {
                // Handle comment tag as @Route(..) or @ORM\Id.
                if (preg_match('/^@'.$lowerClassName.'(?![a-zA-Z])/i', $tokens[$i]['content']) === 1) {
                    return true;
                }
            }

            // Check for @param Truc or @return Machin.
            if (T_DOC_COMMENT_STRING === $tokens[$i]['code']) {
                // Handle comment tag inside a string like @UniqueConstraint
                if (preg_match('/@'.$lowerClassName.'(?![a-zA-Z])/i', $tokens[$i]['content']) === 1) {
                    return true;
                }

                if (trim(strtolower($tokens[$i]['content'])) === $lowerClassName
                    // Handle @var Machin[]|Machine|AnotherMachin $machin.
                    || preg_match('/^'.$lowerClassName.'(\|| |\[)/i', trim($tokens[$i]['content'])) === 1
                    || preg_match('/(\|| )'.$lowerClassName.'(\|| |\[)/i', trim($tokens[$i]['content'])) === 1
                    || preg_match('/(\|| )'.$lowerClassName.'$/i', trim($tokens[$i]['content'])) === 1
                ) {
                    $beforeUsage = $phpcsFile->findPrevious(
                        Tokens::$emptyTokens,
                        ($classUsed - 1),
                        null,
                        true
                    );

                    // If a backslash is used before the class name then this is some other use statement.
                    if (T_USE !== $tokens[$beforeUsage]['code'] && T_NS_SEPARATOR !== $tokens[$beforeUsage]['code']) {
                        return true;
                    }
                }
            }

            $i++;
        }//end while

        return false;

    }//end isUsed()


}//end class
