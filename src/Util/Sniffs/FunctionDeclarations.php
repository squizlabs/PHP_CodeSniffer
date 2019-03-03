<?php
/**
 * Utility functions for use when examining function declaration statements.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;

class FunctionDeclarations
{


    /**
     * Returns the parameters for the specified function token.
     *
     * Each parameter is in the following format:
     *
     * <code>
     *   0 => array(
     *         'name'              => '$var',  // The variable name.
     *         'token'             => integer, // The stack pointer to the variable name.
     *         'content'           => string,  // The full content of the variable definition.
     *         'pass_by_reference' => boolean, // Is the variable passed by reference?
     *         'variable_length'   => boolean, // Is the param of variable length through use of `...` ?
     *         'type_hint'         => string,  // The type hint for the variable.
     *         'type_hint_token'   => integer, // The stack pointer to the type hint
     *                                         // or false if there is no type hint.
     *         'nullable_type'     => boolean, // Is the variable using a nullable type?
     *        )
     * </code>
     *
     * Parameters with default values have an additional array index of
     * 'default' with the value of the default as a string.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the function token
     *                                               to acquire the parameters for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is not of
     *                                                      type T_FUNCTION or T_CLOSURE.
     */
    public static function getParameters(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_FUNCTION
            && $tokens[$stackPtr]['code'] !== T_CLOSURE
        ) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE');
        }

        $opener = $tokens[$stackPtr]['parenthesis_opener'];
        $closer = $tokens[$stackPtr]['parenthesis_closer'];

        $vars            = [];
        $currVar         = null;
        $paramStart      = ($opener + 1);
        $defaultStart    = null;
        $paramCount      = 0;
        $passByReference = false;
        $variableLength  = false;
        $typeHint        = '';
        $typeHintToken   = false;
        $nullableType    = false;

        for ($i = $paramStart; $i <= $closer; $i++) {
            // Check to see if this token has a parenthesis or bracket opener. If it does
            // it's likely to be an array which might have arguments in it. This
            // could cause problems in our parsing below, so lets just skip to the
            // end of it.
            if (isset($tokens[$i]['parenthesis_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $tokens[$i]['parenthesis_closer']) {
                    $i = ($tokens[$i]['parenthesis_closer'] + 1);
                }
            }

            if (isset($tokens[$i]['bracket_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $tokens[$i]['bracket_closer']) {
                    $i = ($tokens[$i]['bracket_closer'] + 1);
                }
            }

            switch ($tokens[$i]['code']) {
            case T_BITWISE_AND:
                if ($defaultStart === null) {
                    $passByReference = true;
                }
                break;
            case T_VARIABLE:
                $currVar = $i;
                break;
            case T_ELLIPSIS:
                $variableLength = true;
                break;
            case T_CALLABLE:
                if ($typeHintToken === false) {
                    $typeHintToken = $i;
                }

                $typeHint .= $tokens[$i]['content'];
                break;
            case T_SELF:
            case T_PARENT:
            case T_STATIC:
                // Self and parent are valid, static invalid, but was probably intended as type hint.
                if (isset($defaultStart) === false) {
                    if ($typeHintToken === false) {
                        $typeHintToken = $i;
                    }

                    $typeHint .= $tokens[$i]['content'];
                }
                break;
            case T_STRING:
                // This is a string, so it may be a type hint, but it could
                // also be a constant used as a default value.
                $prevComma = false;
                for ($t = $i; $t >= $opener; $t--) {
                    if ($tokens[$t]['code'] === T_COMMA) {
                        $prevComma = $t;
                        break;
                    }
                }

                if ($prevComma !== false) {
                    $nextEquals = false;
                    for ($t = $prevComma; $t < $i; $t++) {
                        if ($tokens[$t]['code'] === T_EQUAL) {
                            $nextEquals = $t;
                            break;
                        }
                    }

                    if ($nextEquals !== false) {
                        break;
                    }
                }

                if ($defaultStart === null) {
                    if ($typeHintToken === false) {
                        $typeHintToken = $i;
                    }

                    $typeHint .= $tokens[$i]['content'];
                }
                break;
            case T_NS_SEPARATOR:
                // Part of a type hint or default value.
                if ($defaultStart === null) {
                    if ($typeHintToken === false) {
                        $typeHintToken = $i;
                    }

                    $typeHint .= $tokens[$i]['content'];
                }
                break;
            case T_NULLABLE:
                if ($defaultStart === null) {
                    $nullableType = true;
                    $typeHint    .= $tokens[$i]['content'];
                }
                break;
            case T_CLOSE_PARENTHESIS:
            case T_COMMA:
                // If it's null, then there must be no parameters for this
                // method.
                if ($currVar === null) {
                    continue 2;
                }

                $vars[$paramCount]            = [];
                $vars[$paramCount]['token']   = $currVar;
                $vars[$paramCount]['name']    = $tokens[$currVar]['content'];
                $vars[$paramCount]['content'] = trim($phpcsFile->getTokensAsString($paramStart, ($i - $paramStart)));

                if ($defaultStart !== null) {
                    $vars[$paramCount]['default'] = trim($phpcsFile->getTokensAsString($defaultStart, ($i - $defaultStart)));
                }

                $vars[$paramCount]['pass_by_reference'] = $passByReference;
                $vars[$paramCount]['variable_length']   = $variableLength;
                $vars[$paramCount]['type_hint']         = $typeHint;
                $vars[$paramCount]['type_hint_token']   = $typeHintToken;
                $vars[$paramCount]['nullable_type']     = $nullableType;

                // Reset the vars, as we are about to process the next parameter.
                $defaultStart    = null;
                $paramStart      = ($i + 1);
                $passByReference = false;
                $variableLength  = false;
                $typeHint        = '';
                $typeHintToken   = false;
                $nullableType    = false;

                $paramCount++;
                break;
            case T_EQUAL:
                $defaultStart = ($i + 1);
                break;
            }//end switch
        }//end for

        return $vars;

    }//end getParameters()


    /**
     * Returns the visibility and implementation properties of a function or method.
     *
     * The format of the array is:
     * <code>
     *   array(
     *    'scope'                => 'public', // public protected or protected
     *    'scope_specified'      => true,     // true is scope keyword was found.
     *    'return_type'          => '',       // the return type of the method.
     *    'return_type_token'    => integer,  // The stack pointer to the start of the return type
     *                                        // or false if there is no return type.
     *    'nullable_return_type' => false,    // true if the return type is nullable.
     *    'is_abstract'          => false,    // true if the abstract keyword was found.
     *    'is_final'             => false,    // true if the final keyword was found.
     *    'is_static'            => false,    // true if the static keyword was found.
     *    'has_body'             => false,    // true if the method has a body
     *   );
     * </code>
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the function token to
     *                                               acquire the properties for.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_FUNCTION token.
     */
    public static function getProperties(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_FUNCTION
            && $tokens[$stackPtr]['code'] !== T_CLOSURE
        ) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE');
        }

        if ($tokens[$stackPtr]['code'] === T_FUNCTION) {
            $valid = [
                T_PUBLIC      => T_PUBLIC,
                T_PRIVATE     => T_PRIVATE,
                T_PROTECTED   => T_PROTECTED,
                T_STATIC      => T_STATIC,
                T_FINAL       => T_FINAL,
                T_ABSTRACT    => T_ABSTRACT,
                T_WHITESPACE  => T_WHITESPACE,
                T_COMMENT     => T_COMMENT,
                T_DOC_COMMENT => T_DOC_COMMENT,
            ];
        } else {
            $valid = [
                T_STATIC      => T_STATIC,
                T_WHITESPACE  => T_WHITESPACE,
                T_COMMENT     => T_COMMENT,
                T_DOC_COMMENT => T_DOC_COMMENT,
            ];
        }

        $scope          = 'public';
        $scopeSpecified = false;
        $isAbstract     = false;
        $isFinal        = false;
        $isStatic       = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
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
            case T_ABSTRACT:
                $isAbstract = true;
                break;
            case T_FINAL:
                $isFinal = true;
                break;
            case T_STATIC:
                $isStatic = true;
                break;
            }//end switch
        }//end for

        $returnType         = '';
        $returnTypeToken    = false;
        $nullableReturnType = false;
        $hasBody            = true;

        if (isset($tokens[$stackPtr]['parenthesis_closer']) === true) {
            $scopeOpener = null;
            if (isset($tokens[$stackPtr]['scope_opener']) === true) {
                $scopeOpener = $tokens[$stackPtr]['scope_opener'];
            }

            $valid = [
                T_STRING       => T_STRING,
                T_CALLABLE     => T_CALLABLE,
                T_SELF         => T_SELF,
                T_PARENT       => T_PARENT,
                T_NS_SEPARATOR => T_NS_SEPARATOR,
            ];

            for ($i = $tokens[$stackPtr]['parenthesis_closer']; $i < $phpcsFile->numTokens; $i++) {
                if (($scopeOpener === null && $tokens[$i]['code'] === T_SEMICOLON)
                    || ($scopeOpener !== null && $i === $scopeOpener)
                ) {
                    // End of function definition.
                    break;
                }

                if ($tokens[$i]['code'] === T_NULLABLE) {
                    $nullableReturnType = true;
                }

                if (isset($valid[$tokens[$i]['code']]) === true) {
                    if ($returnTypeToken === false) {
                        $returnTypeToken = $i;
                    }

                    $returnType .= $tokens[$i]['content'];
                }
            }

            $end     = $phpcsFile->findNext([T_OPEN_CURLY_BRACKET, T_SEMICOLON], $tokens[$stackPtr]['parenthesis_closer']);
            $hasBody = $tokens[$end]['code'] === T_OPEN_CURLY_BRACKET;
        }//end if

        if ($returnType !== '' && $nullableReturnType === true) {
            $returnType = '?'.$returnType;
        }

        return [
            'scope'                => $scope,
            'scope_specified'      => $scopeSpecified,
            'return_type'          => $returnType,
            'return_type_token'    => $returnTypeToken,
            'nullable_return_type' => $nullableReturnType,
            'is_abstract'          => $isAbstract,
            'is_final'             => $isFinal,
            'is_static'            => $isStatic,
            'has_body'             => $hasBody,
        ];

    }//end getProperties()


}//end class
