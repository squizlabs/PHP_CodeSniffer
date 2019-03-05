<?php
/**
 * Utility functions for use when examining T_NAMESPACE tokens and to determine the
 * namespace of arbitrary tokens.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class Namespaces
{

    /**
     * List of tokens which can end a namespace declaration statement.
     *
     * @var array
     */
    public static $statementClosers = [
        T_SEMICOLON          => T_SEMICOLON,
        T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
        T_CLOSE_TAG          => T_CLOSE_TAG,
    ];


    /**
     * Determine what a T_NAMESPACE token is used for.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the T_NAMESPACE token.
     *
     * @return string Either 'declaration', 'operator'.
     *                An empty string will be returned if it couldn't be
     *                reliably determined what the T_NAMESPACE token is used for.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is
     *                                                      not a T_NAMESPACE token.
     */
    public static function getType(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== T_NAMESPACE) {
            throw new RuntimeException('$stackPtr must be of type T_NAMESPACE');
        }

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false) {
            // Live coding or parse error.
            return '';
        }

        if ($tokens[$next]['code'] === T_STRING
            || isset(self::$statementClosers[$tokens[$next]['code']]) === true
        ) {
            return 'declaration';
        }

        if ($tokens[$next]['code'] === T_NS_SEPARATOR) {
            return 'operator';
        }

        return '';

    }//end getType()


    /**
     * Determine whether a T_NAMESPACE token is the keyword for a namespace declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of a T_NAMESPACE token.
     *
     * @return bool True if the token passed is the keyword for a namespace declaration.
     *              False if not.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is
     *                                                      not a T_NAMESPACE token.
     */
    public static function isDeclaration(File $phpcsFile, $stackPtr)
    {
        return (self::getType($phpcsFile, $stackPtr) === 'declaration');

    }//end isDeclaration()


    /**
     * Determine whether a T_NAMESPACE token is used as an operator.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of a T_NAMESPACE token.
     *
     * @return bool True if the token passed is used as an operator. False if not.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is
     *                                                      not a T_NAMESPACE token.
     */
    public static function isOperator(File $phpcsFile, $stackPtr)
    {
        return (self::getType($phpcsFile, $stackPtr) === 'operator');

    }//end isOperator()


    /**
     * Get the complete namespace name as declared.
     *
     * For hierarchical namespaces, the name will be composed of several tokens,
     * i.e. MyProject\Sub\Level which will be returned together as one string.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of a T_NAMESPACE token.
     * @param bool                        $clean     Optional. Whether to get the name stripped
     *                                               of potentially interlaced whitespace and/or
     *                                               comments. Defaults to true.
     *
     * @return string|false The namespace name, or false if the specified position is not a
     *                      T_NAMESPACE token, not the keyword for a namespace declaration
     *                      or when parse errors are encountered/during live coding.
     *                      Note: The name can be an empty string for a valid global
     *                      namespace declaration.
     */
    public static function getDeclaredName(File $phpcsFile, $stackPtr, $clean=true)
    {
        try {
            if (self::isDeclaration($phpcsFile, $stackPtr) === false) {
                // Not a namespace declaration.
                return false;
            }
        } catch (RuntimeException $e) {
            // Non-existent token or not a namespace keyword token.
            return false;
        }

        $endOfStatement = $phpcsFile->findNext(self::$statementClosers, ($stackPtr + 1));
        if ($endOfStatement === false) {
            // Live coding or parse error.
            return false;
        }

        $tokens = $phpcsFile->getTokens();
        $next   = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), ($endOfStatement + 1), true);
        if ($next === $endOfStatement) {
            // Declaration of global namespace. I.e.: namespace {}.
            // If not a scoped {} namespace declaration, no name/global declarations are invalid
            // and result in parse errors, but that's not our concern.
            return '';
        }

        if ($clean === false) {
            return trim($phpcsFile->getTokensAsString($next, ($endOfStatement - $next), true));
        }

        $name = '';
        for ($i = $next; $i < $endOfStatement; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            $name .= $tokens[$i]['content'];
        }

        return trim($name);

    }//end getDeclaredName()


    /**
     * Determine the namespace an arbitrary token lives in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The token for which to determine
     *                                               the namespace.
     *
     * @return int|false Token pointer to the applicable namespace keyword or
     *                   false if it couldn't be determined or no namespace applies.
     */
    public static function findNamespacePtr(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        // Check for scoped namespace {}.
        $namespacePtr = Conditions::getCondition($phpcsFile, $stackPtr, T_NAMESPACE);
        if ($namespacePtr !== false) {
            return $namespacePtr;
        }

        /*
         * Not in a scoped namespace, so let's see if we can find a non-scoped namespace instead.
         * Keeping in mind that:
         * - there can be multiple non-scoped namespaces in a file (bad practice, but is allowed);
         * - the namespace keyword can also be used as an operator;
         * - a non-named namespace resolves to the global namespace;
         * - and that namespace declarations can't be nested in anything, so we can skip over any
         *   nesting structures.
         */

        $previousNSToken = $stackPtr;
        $find            = [
            T_NAMESPACE,
            T_CLOSE_CURLY_BRACKET,
            T_CLOSE_PARENTHESIS,
            T_CLOSE_SHORT_ARRAY,
        ];

        do {
            $previousNSToken = $phpcsFile->findPrevious($find, ($previousNSToken - 1));
            if ($previousNSToken === false) {
                break;
            }

            if ($tokens[$previousNSToken]['code'] === T_CLOSE_CURLY_BRACKET) {
                // Stop if we encounter a scoped namespace declaration as we already know we're not in one.
                if (isset($tokens[$previousNSToken]['scope_condition']) === true
                    && $tokens[$tokens[$previousNSToken]['scope_condition']]['code'] === T_NAMESPACE
                ) {
                    break;
                }

                // Skip over other scoped structures for efficiency.
                if (isset($tokens[$previousNSToken]['scope_condition']) === true) {
                    $previousNSToken = $tokens[$previousNSToken]['scope_condition'];
                } else if (isset($tokens[$previousNSToken]['bracket_opener']) === true) {
                    $previousNSToken = $tokens[$previousNSToken]['bracket_opener'];
                }

                continue;
            }

            // Skip over other nesting structures for efficiency.
            if ($tokens[$previousNSToken]['code'] === T_CLOSE_SHORT_ARRAY) {
                if (isset($tokens[$previousNSToken]['bracket_opener']) === true) {
                    $previousNSToken = $tokens[$previousNSToken]['bracket_opener'];
                }

                continue;
            }

            if ($tokens[$previousNSToken]['code'] === T_CLOSE_PARENTHESIS) {
                if (isset($tokens[$previousNSToken]['parenthesis_owner']) === true) {
                    $previousNSToken = $tokens[$previousNSToken]['parenthesis_owner'];
                } else if (isset($tokens[$previousNSToken]['parenthesis_opener']) === true) {
                    $previousNSToken = $tokens[$previousNSToken]['parenthesis_opener'];
                }

                continue;
            }

            // So this is a namespace keyword, check if it's a declaration.
            if (self::isDeclaration($phpcsFile, $previousNSToken) === true) {
                return $previousNSToken;
            }
        } while (true);

        return false;

    }//end findNamespacePtr()


    /**
     * Determine the namespace name an arbitrary token lives in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The token for which to determine
     *                                               the namespace.
     *
     * @return string Namespace name or empty string if it couldn't be determined
     *                or no namespace applies.
     */
    public static function determineNamespace(File $phpcsFile, $stackPtr)
    {
        $namespacePtr = self::findNamespacePtr($phpcsFile, $stackPtr);
        if ($namespacePtr === false) {
            return '';
        }

        $namespace = self::getDeclaredName($phpcsFile, $namespacePtr);
        if ($namespace !== false) {
            return $namespace;
        }

        return '';

    }//end determineNamespace()


}//end class
