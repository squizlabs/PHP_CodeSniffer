<?php
/**
 * Utility functions for use when examining variables.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class Variables
{


    /**
     * Returns the visibility and implementation properties of the class member
     * variable found at the specified position in the stack.
     *
     * The format of the array is:
     *
     * <code>
     *   array(
     *    'scope'           => 'public', // public protected or protected.
     *    'scope_specified' => false,    // true if the scope was explicitly specified.
     *    'is_static'       => false,    // true if the static keyword was found.
     *   );
     * </code>
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the T_VARIABLE token to
     *                                               acquire the properties for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_VARIABLE token, or if the position is not
     *                                                      a class member variable.
     */
    public static function getMemberProperties(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_VARIABLE) {
            throw new RuntimeException('$stackPtr must be of type T_VARIABLE');
        }

        if (Conditions::isOOProperty($phpcsFile, $stackPtr) === false) {
            $lastCondition = Conditions::getlastCondition($phpcsFile, $stackPtr);
            if ($lastCondition !== false
                && $tokens[$lastCondition]['code'] === T_INTERFACE
            ) {
                // T_VARIABLEs in interfaces can actually be method arguments
                // but they wont be seen as being inside the method because there
                // are no scope openers and closers for abstract methods. If it is in
                // parentheses, we can be pretty sure it is a method argument.
                if (empty($tokens[$stackPtr]['nested_parenthesis']) === true) {
                    $error = 'Possible parse error: interfaces may not include member vars';
                    $phpcsFile->addWarning($error, $stackPtr, 'Internal.ParseError.InterfaceHasMemberVar');
                    return [];
                }
            } else {
                throw new RuntimeException('$stackPtr is not a class member var');
            }
        }

        $valid = [
            T_PUBLIC    => T_PUBLIC,
            T_PRIVATE   => T_PRIVATE,
            T_PROTECTED => T_PROTECTED,
            T_STATIC    => T_STATIC,
            T_VAR       => T_VAR,
        ];

        $valid += Tokens::$emptyTokens;

        $scope          = 'public';
        $scopeSpecified = false;
        $isStatic       = false;

        $startOfStatement = $phpcsFile->findPrevious(
            [
                T_SEMICOLON,
                T_OPEN_CURLY_BRACKET,
                T_CLOSE_CURLY_BRACKET,
            ],
            ($stackPtr - 1)
        );

        for ($i = ($startOfStatement + 1); $i < $stackPtr; $i++) {
            if (isset($valid[$tokens[$i]['code']]) === false) {
                break;
            }

            switch ($tokens[$i]['code']) {
            case T_PUBLIC:
                $scope          = 'public';
                $scopeSpecified = true;
                break;
            case T_PRIVATE:
                $scope          = 'private';
                $scopeSpecified = true;
                break;
            case T_PROTECTED:
                $scope          = 'protected';
                $scopeSpecified = true;
                break;
            case T_STATIC:
                $isStatic = true;
                break;
            }
        }//end for

        return [
            'scope'           => $scope,
            'scope_specified' => $scopeSpecified,
            'is_static'       => $isStatic,
        ];

    }//end getMemberProperties()


}//end class
