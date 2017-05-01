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

        $useStatementIsInGlobalScope     = empty($tokens[$stackPtr]['conditions']);
        $useStatementIsInBracedNamespace = array_values($tokens[$stackPtr]['conditions']) === [T_NAMESPACE];
        if ($useStatementIsInGlobalScope === false && $useStatementIsInBracedNamespace === false) {
            return;
        }

        $couldParseUseStatement = $this->parseUseStatement(
            $phpcsFile,
            $stackPtr,
            $useStatementNamespace,
            $namesInUseStatement,
            $useStatementSemicolonPtr
        );
        if ($couldParseUseStatement === false) {
            return;
        }
        $couldParseNamespace = $this->getContainingNamespace(
            $phpcsFile,
            $stackPtr,
            $containingNamespaceName,
            $endOfNamespacePtr
        );
        if ($couldParseNamespace === false) {
            return;
        }
        $namesToKeep = [];
        $useStatementNeedsFixing = false;
        foreach ($namesInUseStatement as $i => $usedNameAndAlias) {
            list($usedName, $alias) = $usedNameAndAlias;
            $removeName = false;
            if (
                $this->namespaceEquals($containingNamespaceName, $useStatementNamespace) === true &&
                strcasecmp($usedName, $alias) === 0 // Aliasing is allowed even in the same namespace.
            ) {
                $removeName = $phpcsFile->addFixableError(
                    "using $useStatementNamespace\\$usedName when already inside the $containingNamespaceName namespace is unnecessary",
                    $stackPtr,
                    'NotUsed'
                );
            } elseif (!$this->nameIsUsedInCode($phpcsFile, $alias, $useStatementSemicolonPtr, $endOfNamespacePtr)) {
                if ($this->nameIsUsedInPhpDoc($phpcsFile, $alias, $useStatementSemicolonPtr, $endOfNamespacePtr)) {
                    $removeName = $phpcsFile->addFixableWarning(
                        "$alias is only used in PhpDoc, not code",
                        $stackPtr,
                        'UsedInDoc'
                    );
                } else {
                    $removeName = $phpcsFile->addFixableError(
                        "$alias is unused",
                        $stackPtr,
                        'NotUsed'
                    );
                }
            }
            if ($removeName === true) {
                $useStatementNeedsFixing = true;
            } else {
                $namesToKeep[] = $usedNameAndAlias;
            }
        }
        if (count($namesToKeep) === 0) {
            // Entire use statement is unused, so delete it
            $this->deleteUseStatement($phpcsFile, $stackPtr, $useStatementSemicolonPtr);
        } elseif ($useStatementNeedsFixing) {
            $this->replaceUseStatement($phpcsFile, $stackPtr, $useStatementSemicolonPtr, $useStatementNamespace, $namesToKeep);
        }
    }//end process()

    /**
     * Attempts to parse the use statement found at $stackPtr and set $namesInUseStatement and
     * $useStatementSemicolonPtr to, respectively, a list of the fully-qualified names included in the use
     * statement, and a pointer to the semicolon at the end of the use statement.
     *
     * @param PHP_CodeSniffer_File $phpcsFile                    The file being scanned.
     * @param int                  $stackPtr                     Pointer to the use statement to parse
     * @param string               &namespace                    The namespace of the names being included
     * @param array                &$unqualifiedNamesAndAliases  An [original name, alias] pair per used name
     * @param int                  &$semicolonPtr                Pointer to semicolon at end of statement
     * @return bool  false if the use statement was definitely syntactically invalid.
     */
    private function parseUseStatement(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        &$namespace,
        &$unqualifiedNamesAndAliases,
        &$semicolonPtr
    ) {
        $semicolonPtr = $phpcsFile->findNext(T_SEMICOLON, $stackPtr);
        if ($semicolonPtr === false) {
            return false;
        }
        // Find the location of the open brace, if this is a PHP 7 Group Use Declaration
        $openBracePtr = $phpcsFile->findNext(T_OPEN_USE_GROUP, $stackPtr, $semicolonPtr);
        if ($openBracePtr === false) {
            if ($this->parseNonGroupUseStatement(
                $phpcsFile,
                $stackPtr,
                $namespace,
                $unqualifiedNamesAndAliases,
                $semicolonPtr
            ) === false) {
                return false;
            }
        } else {
            if ($this->parseGroupUseStatement(
                $phpcsFile,
                $stackPtr,
                $namespace,
                $unqualifiedNamesAndAliases,
                $semicolonPtr,
                $openBracePtr
            ) === false) {
                return false;
            }
        }
        // Some final sanity checks. Not sufficient to catch all synatically-invalid use
        // statements (nor intended to be), but sufficient to stop process() from blowing up.
        if (strlen($namespace) === 0 && $openBracePtr !== false) {
            return false;
        }
        if (count($unqualifiedNamesAndAliases) === 0) {
            return false;
        }
        return true;
    }

    private function parseNonGroupUseStatement(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        &$namespace,
        &$unqualifiedNamesAndAliases,
        $semicolonPtr
    ) {
        $tokens = $phpcsFile->getTokens();
        // Check if the AS keyword is used
        $asPtr = $phpcsFile->findNext(T_AS, $stackPtr, $semicolonPtr);
        // Gather the T_STRINGs that make up the fully-qualified name being used
        $ptr = $stackPtr;
        do {
            $ptr = $phpcsFile->findNext(T_STRING, $ptr + 1, $asPtr ? $asPtr : $semicolonPtr);
            if ($ptr !== false) {
                $fullyQualifiedNameParts[] = $tokens[$ptr]['content'];
            }
        } while ($ptr !== false);
        $usedName = array_pop($fullyQualifiedNameParts);
        $namespaceNameParts = $fullyQualifiedNameParts;
        if ($asPtr === false) {
            $alias = $usedName;
        } else {
            $aliasPtr = $phpcsFile->findNext(T_STRING, $asPtr, $semicolonPtr);
            if ($aliasPtr === false) {
                return false;
            }
            $alias = $tokens[$aliasPtr]['content'];
        }
        $namespace = implode('\\', $namespaceNameParts);
        $unqualifiedNamesAndAliases = [[$usedName, $alias]];
        return true;
    }

    private function parseGroupUseStatement(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        &$namespace,
        &$unqualifiedNamesAndAliases,
        $semicolonPtr,
        $openBracePtr
    ) {
        $tokens = $phpcsFile->getTokens();

        // Gather the T_STRINGs that make up the namespace name
        $namespaceNameParts = [];
        $ptr = $stackPtr;
        do {
            $ptr = $phpcsFile->findNext(T_STRING, $ptr + 1, $openBracePtr);
            if ($ptr !== false) {
                $namespaceNameParts[] = $tokens[$ptr]['content'];
            }
        } while ($ptr !== false);
        $namespace = implode('\\', $namespaceNameParts);
        $closeBracePtr = $phpcsFile->findNext(T_CLOSE_USE_GROUP, $openBracePtr, $semicolonPtr);
        if ($closeBracePtr === false) {
            return false;
        }
        
        $unqualifiedNamesAndAliases = [];
        $haveSeenAs = false;
        for ($i = $openBracePtr + 1; $i < $closeBracePtr; $i++) {
            $code = $tokens[$i]['code'];
            if (in_array($code, PHP_CodeSniffer_Tokens::$emptyTokens)) {
                continue;
            }
            if ($code === T_COMMA) {
                if (isset($trueName) === false) {
                    // Syntax error
                    return false;
                }
                if ($haveSeenAs === true && isset($alias) === false) {
                    // Syntax error
                    return false;
                }
                if (isset($alias) === false) {
                    $alias = $trueName;
                }
                $unqualifiedNamesAndAliases[] = [$trueName, $alias];
                unset($trueName);
                $haveSeenAs = false;
                unset($alias);
                continue;
            }
            if ($code === T_STRING) {
                if (isset($trueName) === false) {
                    $trueName = $tokens[$i]['content'];
                    continue;
                } elseif (isset($alias) === true || $haveSeenAs === false) {
                    // syntax error
                    return false;
                } else {
                    $alias = $tokens[$i]['content'];
                    continue;                    
                }
            }
            if ($code === T_AS) {
                if (isset($trueName) === false) {
                    // Syntax error
                    return false;
                }
                $haveSeenAs = true;
                continue;
            }
            // Some unexpected token; syntax error
            return false;
        }
        if ($haveSeenAs === true && isset($alias) === false) {
            // Syntax error
            return false;
        }
        if (isset($alias) === false) {
            $alias = $trueName;
        }
        $unqualifiedNamesAndAliases[] = [$trueName, $alias];
        return true;
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile         The file being scanned.
     * @param int                  $stackPtr          Pointer to the use statement
     * @param string               &namespaceName     Namespace in which use statement is contained
     * @param int|bool             &$namespaceEndPtr  Pointer to the end of the namespace, or false
     *                                                if the namespace continues until the end of
     *                                                the file.
     * @return bool  False if the namespace couldn't be parsed due to a syntax error.
     */
    private function getContainingNamespace(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        &$namespaceName,
        &$namespaceEndPtr
    ) {
        $namespaceName = '';
        $namespaceEndPtr = false;
        $namespacePtr = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
        if ($namespacePtr === false) {
            // There is no namespace declaration in the file prior to the use statement
            return true;
        }

        $tokens = $phpcsFile->getTokens();

        $endOfNamespaceNamePtr = $phpcsFile->findNext(
            array_merge(PHP_CodeSniffer_Tokens::$emptyTokens, [T_STRING, T_NS_SEPARATOR]),
            $namespacePtr + 1,
            null,
            true
        );

        // Gather the T_STRINGs that make up the namespace name
        $ptr = $namespacePtr;
        $namespaceNameParts = [];
        do {
            $ptr = $phpcsFile->findNext(T_STRING, $ptr + 1, $endOfNamespaceNamePtr);
            if ($ptr !== false) {
                $namespaceNameParts[] = $tokens[$ptr]['content'];
            }
        } while ($ptr);
        $namespaceName = implode('\\', $namespaceNameParts);

        if (isset($tokens[$namespacePtr]['scope_closer'])) {
            $namespaceEndPtr = $tokens[$namespacePtr]['scope_closer'];
        } else {
            $namespaceEndPtr = $phpcsFile->findNext(T_NAMESPACE, $stackPtr);
        }

        if ($namespaceEndPtr < $stackPtr) {
            // Only possible if the use statement is outside a namespace in a file using braced
            // namespaces, which is illegal
            return false;
        }
        return true;
    }

    /**
     * Determines if the two namespaces given are equal, ignoring case and leading backslashes.
     *
     * @param string $namespace1
     * @param string $namespace2
     * @return bool
     */
    private function namespaceEquals($namespace1, $namespace2) {
        return strcasecmp(
            ltrim($namespace1, '\\'),
            ltrim($namespace2, '\\')
        ) === 0;
    }

    /**
     * Determines whether the given class/function/constant $name is used in code anywhere between
     * the $useStatementSemicolonPtr and the $endOfNamespacePtr
     */
    private function nameIsUsedInCode(
        PHP_CodeSniffer_File $phpcsFile,
        $name,
        $useStatementSemicolonPtr,
        $endOfNamespacePtr
    ) {
        // PHP treats class names case insensitively so we cannot search for the exact class name
        // string and need to iterate over all T_STRING tokens in the file.
        // We'll accept almost any T_STRING between the use statement and the end of the namespace
        // as a legit usage, except:
        // - usage in another use statement
        // - usage prefixed by a slash
        $tokens = $phpcsFile->getTokens();
        $ptr = $phpcsFile->findNext(T_STRING, $useStatementSemicolonPtr + 1, $endOfNamespacePtr);
        while ($ptr !== false) {
            $tstringContent = $tokens[$ptr]['content'];
            if (strcasecmp($tstringContent, $name) === 0) {
                $beforeUsagePtr = $phpcsFile->findPrevious(
                    PHP_CodeSniffer_Tokens::$emptyTokens,
                    ($ptr - 1),
                    null,
                    true
                );
                $tokenBefore = $tokens[$beforeUsagePtr];
                if ($tokenBefore['code'] === T_NS_SEPARATOR) {
                    // Not a real usage
                }
                elseif ($tokenBefore['code'] === T_USE) {
                    if (array_pop($tokenBefore['conditions']) === T_CLASS) {
                        // trait usage within a class
                        return true;
                    }
                    // Otherwise, not a real usage
                } else {
                    return true;
                }
            }
            $ptr = $phpcsFile->findNext(T_STRING, $ptr + 1, $endOfNamespacePtr);
        }
        return false;
    }

    private function nameIsUsedInPhpDoc(
        PHP_CodeSniffer_File $phpcsFile,
        $alias,
        $useStatementSemicolonPtr,
        $endOfNamespacePtr
    ) {
        // We iterate over all doc comments in the namespace and use a regex to look for references
        // to $alias within them.
        $tokens = $phpcsFile->getTokens();
        $ptr = $phpcsFile->findNext(
            [T_DOC_COMMENT_STRING, T_DOC_COMMENT_TAG],
            $useStatementSemicolonPtr + 1,
            $endOfNamespacePtr
        );
        while ($ptr !== false) {
            // An extremely liberal check - treat any single-word appearance of $alias as a usage
            if (preg_match('/\b' . preg_quote($alias) . '\b/im', $tokens[$ptr]['content']) === 1) {
                return true;
            };
            $ptr = $phpcsFile->findNext(
                [T_DOC_COMMENT_STRING, T_DOC_COMMENT_TAG],
                $ptr + 1,
                $endOfNamespacePtr
            );
        }
        return false;
    }

    private function deleteUseStatement(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        $useStatementSemicolonPtr
    ) {
        $tokens = $phpcsFile->getTokens();
        // Remove the whole use statement line.
        $phpcsFile->fixer->beginChangeset();
        for ($i = $stackPtr; $i <= $useStatementSemicolonPtr; $i++) {
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
    }

    private function replaceUseStatement(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        $useStatementSemicolonPtr,
        $useStatementNamespace,
        $namesToKeep
    ) {
        $tokens = $phpcsFile->getTokens();
        $phpcsFile->fixer->beginChangeset();
        for ($i = $stackPtr + 1; $i <= $useStatementSemicolonPtr; $i++) {
            $phpcsFile->fixer->replaceToken($i, '');
        }
        
        $newUseStatement = "use $useStatementNamespace\\";
        if (count($namesToKeep) > 1) {
            $newUseStatement .= '{';
        }
        $nameAliasStrings = [];
        foreach ($namesToKeep as $nameAndAlias) {
            list($originalName, $alias) = $nameAndAlias;
            if ($originalName === $alias) {
                $nameAliasStrings[] = $originalName;
            } else {
                $nameAliasStrings[] = "$originalName as $alias";
            }
        }
        $newUseStatement .= implode(', ', $nameAliasStrings);
        if (count($namesToKeep) > 1) {
            $newUseStatement .= '};';
        }
        $phpcsFile->fixer->replaceToken($stackPtr, $newUseStatement);
        $phpcsFile->fixer->endChangeset();
    }

}//end class
