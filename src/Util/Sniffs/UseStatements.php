<?php
/**
 * Utility functions for examining use statements.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class UseStatements
{


    /**
     * Determine what a T_USE token is used for.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the T_USE token.
     *
     * @return string Either 'closure', 'import' or 'trait'.
     *                An empty string will be returned if the token is used in an
     *                invalid context or if it couldn't be reliably determined
     *                what the T_USE token is used for.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_USE token.
     */
    public static function getType(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || $tokens[$stackPtr]['code'] !== T_USE
        ) {
            throw new RuntimeException('$stackPtr must be of type T_USE');
        }

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false) {
            // Live coding or parse error.
            return '';
        }

        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($prev !== false && $tokens[$prev]['code'] === T_CLOSE_PARENTHESIS
            && Parentheses::isOwnerIn($phpcsFile, $prev, T_CLOSURE) === true
        ) {
            return 'closure';
        }

        $lastCondition = Conditions::getLastCondition($phpcsFile, $stackPtr);

        if ($lastCondition === false || $tokens[$lastCondition]['code'] === T_NAMESPACE) {
            // Global or scoped namespace and not a closure use statement.
            return 'import';
        }

        $traitScopes = Tokens::$ooScopeTokens;
        // Only classes and traits can import traits.
        unset($traitScopes[T_INTERFACE]);

        if (isset($traitScopes[$tokens[$lastCondition]['code']]) === true) {
            return 'trait';
        }

        return '';

    }//end getType()


    /**
     * Determine whether a T_USE token represents a closure use statement.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the T_USE token.
     *
     * @return bool True if the token passed is a closure use statement.
     *              False if it's not.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_USE token.
     */
    public static function isClosureUse(File $phpcsFile, $stackPtr)
    {
        return (self::getType($phpcsFile, $stackPtr) === 'closure');

    }//end isClosureUse()


    /**
     * Determine whether a T_USE token represents a class/function/constant import use statement.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the T_USE token.
     *
     * @return bool True if the token passed is an import use statement.
     *              False if it's not.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_USE token.
     */
    public static function isImportUse(File $phpcsFile, $stackPtr)
    {
        return (self::getType($phpcsFile, $stackPtr) === 'import');

    }//end isImportUse()


    /**
     * Determine whether a T_USE token represents a trait use statement.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the T_USE token.
     *
     * @return bool True if the token passed is a trait use statement.
     *              False if it's not.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_USE token.
     */
    public static function isTraitUse(File $phpcsFile, $stackPtr)
    {
        return (self::getType($phpcsFile, $stackPtr) === 'trait');

    }//end isTraitUse()


    /**
     * Split an import use statement into individual imports.
     *
     * Handles single import, multi-import and group-import statements.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the T_USE token.
     *
     * @return array A multi-level array containing information about the use statement.
     *               The first level is 'name', 'function' and 'const'. These keys will always exist.
     *               If any statements are found for any of these categories, the second level
     *               will contain the alias/name as the key and the full original use name as the
     *               value for each of the found imports or an empty array if no imports were found
     *               in this use statement for this category.
     *
     *               For example, for this function group use statement:
     *               `use function Vendor\Package\{LevelA\Name as Alias, LevelB\Another_Name}`
     *               the return value would look like this:
     *               `[
     *                 'name'     => [],
     *                 'function' => [
     *                   'Alias'        => 'Vendor\Package\LevelA\Name',
     *                   'Another_Name' => 'Vendor\Package\LevelB\Another_Name',
     *                 ],
     *                 'const'    => [],
     *               ]`
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_USE token or not an import use statement.
     */
    public static function splitImportUseStatement(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_USE) {
            throw new RuntimeException('$stackPtr must be of type T_USE');
        }

        if (self::isImportUse($phpcsFile, $stackPtr) === false) {
            throw new RuntimeException('$stackPtr must be an import use statement');
        }

        $statements = [
            'name'     => [],
            'function' => [],
            'const'    => [],
        ];

        $endOfStatement = $phpcsFile->findNext([T_SEMICOLON, T_CLOSE_TAG], ($stackPtr + 1));
        if ($endOfStatement === false) {
            // Live coding or parse error.
            return $statements;
        }

        $endOfStatement++;

        $start     = true;
        $useGroup  = false;
        $hasAlias  = false;
        $baseName  = '';
        $name      = '';
        $type      = '';
        $fixedType = false;
        $alias     = '';

        for ($i = ($stackPtr + 1); $i < $endOfStatement; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            switch ($tokens[$i]['code']) {
            case T_STRING:
                // Only when either at the start of the statement or at the start of a new sub within a group.
                if ($start === true && $fixedType === false) {
                    $content = strtolower($tokens[$i]['content']);
                    if ($content === 'function'
                        || $content === 'const'
                    ) {
                        $type  = $content;
                        $start = false;
                        if ($useGroup === false) {
                            $fixedType = true;
                        }

                        break;
                    } else {
                        $type = 'name';
                    }
                }

                $start = false;

                if ($hasAlias === false) {
                    $name .= $tokens[$i]['content'];
                }

                $alias = $tokens[$i]['content'];
                break;

            case T_AS:
                $hasAlias = true;
                break;

            case T_OPEN_USE_GROUP:
                $start    = true;
                $useGroup = true;
                $baseName = $name;
                $name     = '';
                break;

            case T_SEMICOLON:
            case T_CLOSE_TAG:
            case T_CLOSE_USE_GROUP:
            case T_COMMA:
                if ($name !== '') {
                    if ($useGroup === true) {
                        $statements[$type][$alias] = $baseName.$name;
                    } else {
                        $statements[$type][$alias] = $name;
                    }
                }

                if ($tokens[$i]['code'] !== T_COMMA) {
                    return $statements;
                }

                // Reset.
                $start    = true;
                $name     = '';
                $hasAlias = false;
                if ($fixedType === false) {
                    $type = '';
                }
                break;

            case T_NS_SEPARATOR:
                $name .= $tokens[$i]['content'];
                break;

            // Fall back in case reserved keyword is (illegally) used in name.
            // Parse error, but not our concern.
            default:
                $name .= $tokens[$i]['content'];
                break;
            }//end switch
        }//end for

    }//end splitImportUseStatement()


}//end class
