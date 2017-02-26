<?php
/**
 * Generic_Sniffs_Classes_UnusedUseStatementSniff.
 *
 * PHP versions 5 and 7
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Klaus Purer <klaus.purer@gmail.com>
 * @author    Alex Pott <alexpott@157725.no-reply.drupal.org>
 * @copyright 2015-2016 Klaus Purer
 * @copyright 2015-2016 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
/**
 * Generic_Sniffs_Classes_UnusedUseStatementSniff
 *
 * Checks for "use" statements that are not needed in a file.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Klaus Purer <klaus.purer@gmail.com>
 * @author    Alex Pott <alexpott@157725.no-reply.drupal.org>
 * @copyright 2015-2016 Klaus Purer
 * @copyright 2015-2016 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Classes_UnusedUseStatementSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_USE);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        // Only check use statements in the global scope.
        if (empty($tokens[$stackPtr]['conditions']) === false) {
            return;
        }

        // Seek to the semicolon at the end of the statement...
        $semiColon = $phpcsFile->findEndOfStatement($stackPtr);
        if ($tokens[$semiColon]['code'] !== T_SEMICOLON) {
            return;
        }

        // ... then find the T_STRING containing the name of the class, function, or
        // constant being used by seeking backwards from the semicolon until
        // reaching a non-empty token.
        $classPtr = $phpcsFile->findPrevious(
            PHP_CodeSniffer_Tokens::$emptyTokens,
            ($semiColon - 1),
            null,
            true
        );

        // If we haven't found a T_STRING then this wasn't a syntactically valid use
        // statement so we ignore it.
        if ($tokens[$classPtr]['code'] !== T_STRING) {
            return;
        }

        // Search where the class name is used. PHP treats class names case
        // insensitive, that's why we cannot search for the exact class name string
        // and need to iterate over all T_STRING tokens in the file.
        $classUsed      = $phpcsFile->findNext(T_STRING, ($classPtr + 1));
        $className      = $tokens[$classPtr]['content'];
        $lowerClassName = strtolower($className);
        // Check if the referenced class is in the same namespace as the current
        // file. If it is then the use statement is not necessary.
        $namespacePtr = $phpcsFile->findPrevious(array(T_NAMESPACE), $stackPtr);
        // Check if the use statement does aliasing with the "as" keyword. Aliasing
        // is allowed even in the same namespace.
        $aliasUsed = $phpcsFile->findPrevious(T_AS, ($classPtr - 1), $stackPtr);
        if ($namespacePtr !== false && $aliasUsed === false) {
            $nsEnd           = $phpcsFile->findNext(
                array(
                 T_NS_SEPARATOR,
                 T_STRING,
                 T_WHITESPACE,
                ),
                ($namespacePtr + 1),
                null,
                true
            );
            $namespace       = trim(
                $phpcsFile->getTokensAsString(
                    ($namespacePtr + 1),
                    ($nsEnd - $namespacePtr - 1)
                )
            );
            $useNamespacePtr = $phpcsFile->findNext(
                array(T_STRING),
                ($stackPtr + 1)
            );
            $useNamespaceEnd = $phpcsFile->findNext(
                array(
                 T_NS_SEPARATOR,
                 T_STRING,
                ),
                ($useNamespacePtr + 1),
                null,
                true
            );
            $use_namespace   = rtrim(
                $phpcsFile->getTokensAsString(
                    $useNamespacePtr,
                    ($useNamespaceEnd - $useNamespacePtr - 1)
                ),
                '\\'
            );
            if (strcasecmp($namespace, $use_namespace) === 0) {
                $classUsed = false;
            }
        }//end if

        while ($classUsed !== false) {
            if (strtolower($tokens[$classUsed]['content']) === $lowerClassName) {
                $beforeUsage = $phpcsFile->findPrevious(
                    PHP_CodeSniffer_Tokens::$emptyTokens,
                    ($classUsed - 1),
                    null,
                    true
                );
                // If a backslash is used before the class name then this is some
                // other use statement.
                if ($tokens[$beforeUsage]['code'] !== T_USE
                    && $tokens[$beforeUsage]['code'] !== T_NS_SEPARATOR
                ) {
                    return;
                }

                // Trait use statement within a class.
                if ($tokens[$beforeUsage]['code'] === T_USE
                    && empty($tokens[$beforeUsage]['conditions']) === false
                ) {
                    return;
                }
            }//end if

            $classUsed = $phpcsFile->findNext(T_STRING, ($classUsed + 1));
        }//end while

        $error = "Unused use statement: $className";
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NotUsed');
        if ($fix === true) {
            // Remove the whole use statement line.
            $phpcsFile->fixer->beginChangeset();
            for ($i = $stackPtr; $i <= $semiColon; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }

            // Also remove whitespace after the semicolon (new lines).
            while (isset($tokens[$i]) === true
                   && $tokens[$i]['code'] === T_WHITESPACE
            ) {
                $phpcsFile->fixer->replaceToken($i, '');
                if (strpos($tokens[$i]['content'], $phpcsFile->eolChar) !== false) {
                    break;
                }

                $i++;
            }

            $phpcsFile->fixer->endChangeset();
        }//end if

    }//end process()


}//end class
