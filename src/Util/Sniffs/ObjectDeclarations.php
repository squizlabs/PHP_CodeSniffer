<?php
/**
 * Utility functions for use when examining object declaration statements.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ObjectDeclarations
{


    /**
     * Returns the visibility and implementation properties of a class.
     *
     * The format of the array is:
     * <code>
     *   array(
     *    'is_abstract' => false, // true if the abstract keyword was found.
     *    'is_final'    => false, // true if the final keyword was found.
     *   );
     * </code>
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the T_CLASS
     *                                               token to acquire the properties for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_CLASS token.
     */
    public static function getClassProperties(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_CLASS) {
            throw new RuntimeException('$stackPtr must be of type T_CLASS');
        }

        $valid          = Tokens::$emptyTokens;
        $valid[T_FINAL] = T_FINAL;
        $valid[T_ABSTRACT] = T_ABSTRACT;

        $isAbstract = false;
        $isFinal    = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$tokens[$i]['code']]) === false) {
                break;
            }

            switch ($tokens[$i]['code']) {
            case T_ABSTRACT:
                $isAbstract = true;
                break;

            case T_FINAL:
                $isFinal = true;
                break;
            }
        }//end for

        return [
            'is_abstract' => $isAbstract,
            'is_final'    => $isFinal,
        ];

    }//end getClassProperties()


    /**
     * Returns the name of the class that the specified class extends.
     *
     * Works for classes, anonymous classes and interfaces, though it is
     * strongly recommended to use the findExtendedInterfaceNames() method
     * to examine interfaces as they can extend multiple parent interfaces.
     *
     * Returns FALSE on error or if there is no extended class name.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The stack position of the
     *                                               class/interface keyword.
     *
     * @return string|false
     */
    public static function findExtendedClassName(File $phpcsFile, $stackPtr)
    {
        $validStructures = [
            T_CLASS      => true,
            T_ANON_CLASS => true,
            T_INTERFACE  => true,
        ];

        $classes = self::findExtendedImplemented($phpcsFile, $stackPtr, $validStructures, T_EXTENDS);

        if (empty($classes) === true) {
            return false;
        }

        // Classes can only extend one parent class.
        return $classes[0];

    }//end findExtendedClassName()


    /**
     * Returns the names of the interfaces that the specified interface extends.
     *
     * Returns FALSE on error or if there is no extended interface name.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The stack position of the interface keyword.
     *
     * @return array|false
     */
    public static function findExtendedInterfaceNames(File $phpcsFile, $stackPtr)
    {
        $validStructures = [T_INTERFACE => true];

        $interfaces = self::findExtendedImplemented($phpcsFile, $stackPtr, $validStructures, T_EXTENDS);

        if (empty($interfaces) === true) {
            return false;
        }

        return $interfaces;

    }//end findExtendedInterfaceNames()


    /**
     * Returns the names of the interfaces that the specified class implements.
     *
     * Returns FALSE on error or if there are no implemented interface names.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The stack position of the class keyword.
     *
     * @return array|false
     */
    public static function findImplementedInterfaceNames(File $phpcsFile, $stackPtr)
    {
        $validStructures = [
            T_CLASS      => true,
            T_ANON_CLASS => true,
        ];

        $interfaces = self::findExtendedImplemented($phpcsFile, $stackPtr, $validStructures, T_IMPLEMENTS);

        if (empty($interfaces) === true) {
            return false;
        }

        return $interfaces;

    }//end findImplementedInterfaceNames()


    /**
     * Returns the names of the extended classes or interfaces or the implemented
     * interfaces that the specific class/interface declaration extends/implements.
     *
     * Returns FALSE on error or if the object does not extend/implement another object.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The stack position of the
     *                                               class/interface keyword.
     * @param array                       $OOTypes   Array of accepted token types.
     *                                               Array format <token constant> => true.
     * @param int                         $keyword   The token constant for the keyword to examine.
     *                                               Either `T_EXTENDS` or `T_IMPLEMENTS`.
     *
     * @return array|false
     */
    private static function findExtendedImplemented(File $phpcsFile, $stackPtr, array $OOTypes, $keyword)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if (isset($OOTypes[$tokens[$stackPtr]['code']]) === false) {
            return false;
        }

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return false;
        }

        $openerIndex  = $tokens[$stackPtr]['scope_opener'];
        $keywordIndex = $phpcsFile->findNext($keyword, ($stackPtr + 1), $openerIndex);
        if ($keywordIndex === false) {
            return false;
        }

        $find   = Tokens::$emptyTokens;
        $find[] = T_NS_SEPARATOR;
        $find[] = T_STRING;
        $find[] = T_COMMA;

        $end   = $phpcsFile->findNext($find, ($keywordIndex + 1), ($openerIndex + 1), true);
        $names = [];
        $name  = '';
        for ($i = ($keywordIndex + 1); $i < $end; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            if ($tokens[$i]['code'] === T_COMMA && $name !== '') {
                $names[] = $name;
                $name    = '';
                continue;
            }

            $name .= $tokens[$i]['content'];
        }

        // Add the last name.
        if ($name !== '') {
            $names[] = $name;
        }

        return $names;

    }//end findExtendedImplemented()


}//end class
