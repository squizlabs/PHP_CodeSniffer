<?php
/**
 * Utility functions for use when examining comments and docblocks.
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

class Comments
{

    /**
     * Regex to split unioned type strings without splitting multi-type PSR-5
     * array types or "old-style" array types.
     *
     * @var string
     */
    const SPLIT_UNION_TYPES = '`(?:^(?P<type>array\(\s*([^\s=>]*)(?:\s*=>\s*+(.*))?\s*\)|array<\s*([^\s,]*)(?:\s*,\s*+(.*))?\s*>|\([^)]+\)\[\]|[^|]+)(?=|)|(?<=|)(?P>type)$|(?<=|)(?P>type)(?=|))`i';

    /**
     * Regex to match array(type), array(type1 => type2) types.
     *
     * @var string
     */
    const MATCH_ARRAY = '`^array\(\s*([^\s=>]*)(?:\s*=>\s*+(.*))?\s*\)`i';

    /**
     * Regex to match array<type1, type2> types.
     *
     * @var string
     */
    const MATCH_ARRAY_SQUARE = '`^array<\s*([^\s,]*)(?:\s*,\s*+(.*))?\s*>`i';


    /**
     * Valid variable types for param/var/return tags.
     *
     * Keys are short-form, values long-form.
     *
     * @var string[]
     */
    public static $allowedTypes = [
        'array'    => 'array',
        'bool'     => 'boolean',
        'callable' => 'callable',
        'false'    => 'false',
        'float'    => 'float',
        'int'      => 'integer',
        'iterable' => 'iterable',
        'mixed'    => 'mixed',
        'null'     => 'null',
        'object'   => 'object',
        'resource' => 'resource',
        'self'     => 'self',
        'static'   => 'static',
        'string'   => 'string',
        'true'     => 'true',
        'void'     => 'void',
        '$this'    => '$this',
    ];


    /**
     * Examine a complete variable type string for param/var tags.
     *
     * Examines the individual parts of unioned and intersectioned types.
     * - Where relevant, will unduplicate types.
     * - Where relevant, will combine multiple single/multi-types array types into one.
     * - Where relevant, will remove duplicate union/intersect separators.
     *
     * @param string     $typeString   The complete variable type string to process.
     * @param string     $form         Optional. Whether to prefer long-form or short-form
     *                                 types. By default, this only affects the integer and
     *                                 boolean types.
     *                                 Accepted values: 'long', 'short'. Defaults to `short`.
     * @param array|null $allowedTypes Optional. Array of allowed variable types.
     *                                 Keys are short form types, values long form.
     *                                 Both lowercase.
     *                                 If for a particular standard, long/short form does
     *                                 not apply, keys and values should be the same.
     *
     * @return string Valid variable type string.
     */
    public static function suggestTypeString($typeString, $form='short', $allowedTypes=null)
    {
        // Check for PSR-5 Union types, like `int|null`.
        if (strpos($typeString, '|') !== false && $typeString !== '|') {
            $arrayCount = substr_count($typeString, '[]');
            $typeCount  = preg_match_all(self::SPLIT_UNION_TYPES, $typeString, $matches);
            $types      = $matches[0];
            if ($typeCount > 0) {
                if ($arrayCount < 2) {
                    // No or only one array type found, process like normal.
                    $formArray    = array_fill(0, $typeCount, $form);
                    $allowedArray = array_fill(0, $typeCount, $allowedTypes);
                    $types        = array_map('self::suggestType', $types, $formArray, $allowedArray);
                    $types        = array_unique($types);
                } else {
                    // Ok, so there were two or more array types in this type string. Let's combine them.
                    $newTypes       = [];
                    $arrayTypes     = [];
                    $firstArrayType = null;
                    foreach ($types as $order => $type) {
                        if (substr($type, 0, 1) === '(' && substr($type, -3) === ')[]') {
                            if ($firstArrayType === null) {
                                $firstArrayType = $order;
                            }

                            $subTypes = explode('|', substr($type, 1, -3));
                            // Remove empty entries.
                            $subTypes = array_filter($subTypes);
                            foreach ($subTypes as $subType) {
                                $arrayTypes[] = self::suggestType($subType, $form, $allowedTypes);
                            }
                        } else if (substr($type, -2) === '[]') {
                            if ($firstArrayType === null) {
                                $firstArrayType = $order;
                            }

                            $arrayTypes[] = self::suggestType(substr($type, 0, -2), $form, $allowedTypes);
                        } else {
                            $newTypes[$order] = self::suggestTypeString($type, $form, $allowedTypes);
                        }
                    }//end foreach

                    $newTypes       = array_unique($newTypes);
                    $arrayTypes     = array_unique($arrayTypes);
                    $arrayTypeCount = count($arrayTypes);
                    if ($arrayTypeCount > 1) {
                        $newTypes[$firstArrayType] = '('.implode('|', $arrayTypes).')[]';
                    } else if ($arrayTypeCount === 1) {
                        $newTypes[$firstArrayType] = implode('', $arrayTypes).'[]';
                    }

                    $types = $newTypes;
                    ksort($types);
                }//end if
            }//end if

            // Check if both null as well as nullable types are used and if so, remove nullable indicator.
            if (array_search('null', $types, true) !== false) {
                foreach ($types as $key => $type) {
                    if (strpos($type, '?') === 0) {
                        $types[$key] = ltrim($type, '?');
                    }
                }
            }

            return implode('|', $types);
        }//end if

        // Check for PSR-5 Intersection types, like `\MyClass&\PHPUnit\Framework\MockObject\MockObject`.
        if (strpos($typeString, '&') !== false && $typeString !== '&') {
            $types = explode('&', $typeString);
            // Remove empty entries.
            $types        = array_filter($types);
            $typeCount    = count($types);
            $formArray    = array_fill(0, $typeCount, $form);
            $allowedArray = array_fill(0, $typeCount, $allowedTypes);
            $types        = array_map('self::suggestType', $types, $formArray, $allowedArray);
            $types        = array_unique($types);
            return implode('&', $types);
        }

        // Simple type.
        return self::suggestType($typeString, $form, $allowedTypes);

    }//end suggestTypeString()


    /**
     * Returns a valid variable type for param/var tags.
     *
     * If type is not one of the standard types, it must be a custom type.
     * Returns the correct type name suggestion if type name is invalid.
     *
     * @param string     $varType      The variable type to process.
     * @param string     $form         Optional. Whether to prefer long-form or short-form
     *                                 types. By default, this only affects the integer and
     *                                 boolean types.
     *                                 Accepted values: 'long', 'short'. Defaults to `short`.
     * @param array|null $allowedTypes Optional. Array of allowed variable types.
     *                                 Keys are short form types, values long form.
     *                                 Both lowercase.
     *                                 If for a particular standard, long/short form does
     *                                 not apply, keys and values should be the same.
     *
     * @return string
     */
    public static function suggestType($varType, $form='short', $allowedTypes=null)
    {
        if ($allowedTypes === null) {
            $allowedTypes = self::$allowedTypes;
        }

        if ($varType === '') {
            return '';
        }

        $lowerVarType = strtolower(trim($varType));

        if (($form === 'short' && isset($allowedTypes[$lowerVarType]) === true)
            || ($form === 'long' && in_array($lowerVarType, $allowedTypes, true) === true)
        ) {
            return $lowerVarType;
        }

        // Check for short form use when long form is expected and visa versa.
        if ($form === 'long' && isset($allowedTypes[$lowerVarType]) === true) {
            return $allowedTypes[$lowerVarType];
        }

        if ($form === 'short' && in_array($lowerVarType, $allowedTypes, true) === true) {
            return array_search($lowerVarType, $allowedTypes, true);
        }

        // Not listed in allowed types, check for a limited set of known variations.
        switch ($lowerVarType) {
        case 'double':
        case 'real':
            return 'float';

        case 'array()':
        case 'array<>':
            return 'array';
        }//end switch

        // Handle more complex types, like arrays.
        if (strpos($lowerVarType, 'array(') !== false || strpos($lowerVarType, 'array<') !== false) {
            // Valid array declarations:
            // array, array(type), array(type1 => type2), array<type1, type2>.
            $open    = '(';
            $close   = ')';
            $sep     = ' =>';
            $pattern = self::MATCH_ARRAY;
            if (strpos($lowerVarType, 'array<') !== false) {
                $open    = '<';
                $close   = '>';
                $sep     = ',';
                $pattern = self::MATCH_ARRAY_SQUARE;
            }

            $matches = [];
            if (preg_match($pattern, $varType, $matches) === 1) {
                $type1 = '';
                if (isset($matches[1]) === true) {
                    $type1 = self::suggestTypeString($matches[1], $form, $allowedTypes);
                }

                $type2 = '';
                if (isset($matches[2]) === true) {
                    $type2 = self::suggestTypeString($matches[2], $form, $allowedTypes);
                    if ($type2 !== '') {
                        $type2 = $sep.' '.$type2;
                    }
                }

                return 'array'.$open.$type1.$type2.$close;
            }//end if

            return 'array';
        }//end if

        // Check for PSR-5 multiple type array format, like `(int|string)[]`.
        if (strpos($varType, '(') === 0 && substr($varType, -3) === ')[]' && $varType !== '()[]') {
            return '('.self::suggestTypeString(substr($varType, 1, -3), $form, $allowedTypes).')[]';
        }

        // Check for PSR-5 single type array format, like `int[]`.
        if (strpos($varType, '|') === false && substr($varType, -2) === '[]' && $varType !== '[]') {
            return self::suggestType(substr($varType, 0, -2), $form, $allowedTypes).'[]';
        }

        // Allow for nullable type format, like `?string`.
        if (strpos($varType, '?') === 0) {
            return '?'.self::suggestType(substr($varType, 1), $form, $allowedTypes);
        }

        // Must be a custom type name.
        return $varType;

    }//end suggestType()


    /**
     * Find the end of a docblock, inline or block comment sequence.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the
     *                                               start of the comment.
     *
     * @return int Stack pointer to the end of the comment.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is
     *                                                      not of type T_COMMENT or
     *                                                      T_DOC_COMMENT_OPEN_TAG or if it
     *                                                      is not the start of a comment.
     */
    public static function findEndOfComment(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_COMMENT
            && $tokens[$stackPtr]['code'] !== T_DOC_COMMENT_OPEN_TAG
        ) {
            throw new RuntimeException('$stackPtr must be of type T_COMMENT or T_DOC_COMMENT_OPEN_TAG');
        }

        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT_OPEN_TAG) {
            return $tokens[$stackPtr]['comment_closer'];
        }

        // Find the end of inline comment blocks.
        if (strpos($tokens[$stackPtr]['content'], '//') === 0
            || strpos($tokens[$stackPtr]['content'], '#') === 0
        ) {
            $commentPrefix = '//';
            if (strpos($tokens[$stackPtr]['content'], '#') === 0) {
                $commentPrefix = '#';
            }

            $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
            if ($prev !== false) {
                if ($tokens[$prev]['line'] === $tokens[$stackPtr]['line']) {
                    // Stand-alone trailing comment.
                    return $stackPtr;
                } else if ($tokens[$prev]['line'] === ($tokens[$stackPtr]['line'] - 1)) {
                    // Previous token was on the previous line.
                    // Now make sure it wasn't a stand-alone trailing comment.
                    if ($tokens[$prev]['code'] === T_COMMENT
                        && strpos($tokens[$prev]['content'], $commentPrefix) === 0
                    ) {
                        $pprev = $phpcsFile->findPrevious(T_WHITESPACE, ($prev - 1), null, true);
                        if ($pprev === false
                            || $tokens[$pprev]['line'] !== $tokens[$prev]['line']
                        ) {
                            throw new RuntimeException('$stackPtr must point to the start of a comment');
                        }
                    }
                }
            }

            $commentEnd = $stackPtr;
            for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
                if ($tokens[$i]['code'] === T_WHITESPACE) {
                    continue;
                }

                if ($tokens[$i]['code'] !== T_COMMENT
                    && isset(Tokens::$phpcsCommentTokens[$tokens[$i]['code']]) === false
                ) {
                    break;
                }

                if (strpos($tokens[$i]['content'], $commentPrefix) !== 0) {
                    // Not an inline comment or not same style comment, so not part of this comment sequence.
                    break;
                }

                if ($tokens[$i]['line'] !== ($tokens[$commentEnd]['line'] + 1)) {
                    // There must have been a blank line between these comments.
                    break;
                }

                $commentEnd = $i;
            }//end for

            if (isset(Tokens::$phpcsCommentTokens[$tokens[$commentEnd]['code']]) === true) {
                // Inline comment blocks can't end on a PHPCS annotation, so move one back.
                // We already know that the previous token must exist and be a comment token,
                // so no extra validation needed.
                $commentEnd = $phpcsFile->findPrevious(T_WHITESPACE, ($commentEnd - 1), null, true);
            }

            return $commentEnd;
        }//end if

        // Deal with block comments which start with a PHPCS annotation.
        if (strpos($tokens[$stackPtr]['content'], '/*') !== 0) {
            do {
                $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
                if (isset(Tokens::$phpcsCommentTokens[$tokens[$prev]['code']]) === false) {
                    throw new RuntimeException('$stackPtr must point to the start of a comment');
                }

                $stackPtr = $prev;

                if (strpos($tokens[$prev]['content'], '/*') === 0) {
                    break;
                }
            } while ($stackPtr >= 0);
        }

        // Find the end of block comments.
        if (strpos($tokens[$stackPtr]['content'], '/*') === 0) {
            if (substr($tokens[$stackPtr]['content'], -2) === '*/') {
                // Single line block comment.
                return $stackPtr;
            }

            $valid            = Tokens::$phpcsCommentTokens;
            $valid[T_COMMENT] = T_COMMENT;

            $commentEnd = $stackPtr;
            $i          = ($stackPtr + 1);
            while ($i < $phpcsFile->numTokens && isset($valid[$tokens[$i]['code']]) === true) {
                $commentEnd = $i;
                if (substr($tokens[$i]['content'], -2) === '*/') {
                    // Found end of the comment.
                    break;
                }

                ++$i;
            }

            return $commentEnd;
        }//end if

    }//end findEndOfComment()


    /**
     * Find the start of a docblock, inline or block comment sequence.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the
     *                                               end of the comment.
     *
     * @return int Stack pointer to the start of the comment.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is
     *                                                      not of type T_COMMENT or
     *                                                      T_DOC_COMMENT_CLOSE_TAG or if it
     *                                                      is not the end of a comment.
     */
    public static function findStartOfComment(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_COMMENT
            && $tokens[$stackPtr]['code'] !== T_DOC_COMMENT_CLOSE_TAG
        ) {
            throw new RuntimeException('$stackPtr must be of type T_COMMENT or T_DOC_COMMENT_CLOSE_TAG');
        }

        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            return $tokens[$stackPtr]['comment_opener'];
        }

        // Find the start of inline comment blocks.
        if (strpos($tokens[$stackPtr]['content'], '//') === 0
            || strpos($tokens[$stackPtr]['content'], '#') === 0
        ) {
            $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
            if ($prev !== false && $tokens[$prev]['line'] === $tokens[$stackPtr]['line']) {
                // Stand-alone trailing comment.
                return $stackPtr;
            }

            $commentPrefix = '//';
            if (strpos($tokens[$stackPtr]['content'], '#') === 0) {
                $commentPrefix = '#';
            }

            $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($next !== false
                && $tokens[$next]['code'] === T_COMMENT
                && strpos($tokens[$next]['content'], $commentPrefix) === 0
                && $tokens[$next]['line'] === ($tokens[$stackPtr]['line'] + 1)
            ) {
                throw new RuntimeException('$stackPtr must point to the end of a comment');
            }

            $commentStart = $stackPtr;
            for ($i = ($stackPtr - 1); $i >= 0; $i--) {
                if ($tokens[$i]['code'] === T_WHITESPACE) {
                    continue;
                }

                if ($tokens[$i]['code'] !== T_COMMENT
                    && isset(Tokens::$phpcsCommentTokens[$tokens[$i]['code']]) === false
                ) {
                    break;
                }

                if (strpos($tokens[$i]['content'], $commentPrefix) !== 0) {
                    // Not an inline comment or not same style comment, so not part of this comment sequence.
                    break;
                }

                if ($tokens[$i]['line'] !== ($tokens[$commentStart]['line'] - 1)) {
                    // There must have been a blank line between these comments.
                    break;
                }

                $commentStart = $i;
            }//end for

            if (isset(Tokens::$phpcsCommentTokens[$tokens[$commentStart]['code']]) === true) {
                // Inline comment blocks can't start on a PHPCS annotation, so move one forward.
                // We already know that the next token must exist and be a comment token,
                // so no extra validation needed.
                $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($commentStart + 1), null, true);
            } else {
                // Check that the current token we are at isn't a trailing comment.
                $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($commentStart - 1), null, true);
                if ($prev !== false && $tokens[$prev]['line'] === $tokens[$commentStart]['line']) {
                    // Trailing comment, so move one forward.
                    $commentStart = $phpcsFile->findNext(T_WHITESPACE, ($commentStart + 1), null, true);
                }
            }

            return $commentStart;
        }//end if

        // Deal with block comments which end with a PHPCS annotation.
        if (substr($tokens[$stackPtr]['content'], -2) !== '*/') {
            do {
                $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
                if (isset(Tokens::$phpcsCommentTokens[$tokens[$next]['code']]) === false) {
                    throw new RuntimeException('$stackPtr must point to the end of a comment');
                }

                $stackPtr = $next;

                if (substr($tokens[$next]['content'], -2) === '*/') {
                    break;
                }
            } while ($stackPtr >= 0);
        }

        // Find the start of block comments.
        if (substr($tokens[$stackPtr]['content'], -2) === '*/') {
            if (strpos($tokens[$stackPtr]['content'], '/*') === 0) {
                // Single line block comment.
                return $stackPtr;
            }

            $valid            = Tokens::$phpcsCommentTokens;
            $valid[T_COMMENT] = T_COMMENT;

            $commentStart = $stackPtr;
            $i            = ($stackPtr - 1);
            while ($i >= 0 && isset($valid[$tokens[$i]['code']]) === true) {
                $commentStart = $i;
                if (strpos($tokens[$i]['content'], '/*') === 0) {
                    // Found start of the comment.
                    break;
                }

                --$i;
            }

            return $commentStart;
        }//end if

    }//end findStartOfComment()


    /**
     * Find the related docblock/comment based on a T_CONST token.
     *
     * Note: As this function is based on the `T_CONST` token, it can not find
     * individual docblocks for each constant in a multi-constant declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the
     *                                               T_CONST token.
     *
     * @return int|false Integer stack pointer to the docblock/comment end (close) token;
     *                   or false if no docblock or comment was found.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is
     *                                                      not of type T_CONST.
     */
    public static function findConstantComment(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if ($tokens[$stackPtr]['code'] !== T_CONST) {
            throw new RuntimeException('$stackPtr must be of type T_CONST');
        }

        $ignore = Tokens::$scopeModifiers;

        return self::findCommentAbove($phpcsFile, $stackPtr, $ignore);

    }//end findConstantComment()


    /**
     * Find the related docblock/comment based on a T_FUNCTION token.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the
     *                                               T_FUNCTION token.
     *
     * @return int|false Integer stack pointer to the docblock/comment end (close) token;
     *                   or false if no docblock or comment was found.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is
     *                                                      not of type T_FUNCTION.
     */
    public static function findFunctionComment(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if ($tokens[$stackPtr]['code'] !== T_FUNCTION) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION');
        }

        $ignore = Tokens::$methodPrefixes;

        return self::findCommentAbove($phpcsFile, $stackPtr, $ignore);

    }//end findFunctionComment()


    /**
     * Find the related docblock/comment based on a class/interface/trait token.
     *
     * Note: anonymous classes are not supported by this method as what tokens should
     * be allowed to precede them is too arbitrary.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the
     *                                               OO construct to find the comment for.
     *
     * @return int|false Integer stack pointer to the docblock/comment end (close) token;
     *                   or false if no docblock or comment was found.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is
     *                                                      not a class, interface or trait
     *                                                      token.
     */
    public static function findOOStructureComment(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset(Tokens::$ooScopeTokens[$tokens[$stackPtr]['code']]) === false
            || $tokens[$stackPtr]['code'] === T_ANON_CLASS
        ) {
            throw new RuntimeException('$stackPtr must be a class, interface or trait token');
        }

        $ignore = [];
        if ($tokens[$stackPtr]['code'] === T_CLASS) {
            // Only classes can be abstract/final.
            $ignore = [
                T_ABSTRACT => T_ABSTRACT,
                T_FINAL    => T_FINAL,
            ];
        }

        return self::findCommentAbove($phpcsFile, $stackPtr, $ignore);

    }//end findOOStructureComment()


    /**
     * Find the related docblock/comment based on the T_VARIABLE token for a class/trait property.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the
     *                                               T_VARIABLE token.
     *
     * @return int|false Integer stack pointer to the docblock/comment end (close) token;
     *                   or false if no docblock or comment was found.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is
     *                                                      not an OO property.
     */
    public static function findPropertyComment(File $phpcsFile, $stackPtr)
    {
        if (Conditions::isOOProperty($phpcsFile, $stackPtr) === false) {
            throw new RuntimeException('$stackPtr must be an OO property');
        }

        $ignore   = Tokens::$scopeModifiers;
        $ignore[] = T_STATIC;
        $ignore[] = T_VAR;

        return self::findCommentAbove($phpcsFile, $stackPtr, $ignore);

    }//end findPropertyComment()


    /**
     * Find docblock/comment based on construct token.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack of the
     *                                               construct to find the comment for.
     * @param array                       $ignore    Array of tokens to ignore if found
     *                                               before the construct token while looking
     *                                               for the comment/docblock.
     *                                               Note: T_WHITESPACE tokens and PHPCS
     *                                               native annotations will always be
     *                                               ignored.
     *
     * @return int|false Integer stack pointer to the docblock/comment end (close) token;
     *                   or false if no docblock or comment was found.
     */
    public static function findCommentAbove(File $phpcsFile, $stackPtr, $ignore=[])
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        $customIgnore = $ignore;

        $ignore[]   = T_WHITESPACE;
        $ignore    += Tokens::$phpcsCommentTokens;
        $commentEnd = $stackPtr;

        // Find the right comment.
        do {
            $commentEnd = $phpcsFile->findPrevious($ignore, ($commentEnd - 1), null, true);

            if ($commentEnd === false
                || ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
                && $tokens[$commentEnd]['code'] !== T_COMMENT)
            ) {
                return false;
            }

            $prevNonEmpty = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($commentEnd - 1), null, true);

            // Handle structures interlaced with inline comments where we need an earlier comment.
            if (in_array($tokens[$prevNonEmpty]['code'], $customIgnore, true) === true) {
                $commentEnd = $prevNonEmpty;
                continue;
            }

            // Handle end comments for preceeding structures, such as control structures
            // or function declarations. Assume the end comment belongs to the preceeding structure.
            if ($tokens[$prevNonEmpty]['line'] === $tokens[$commentEnd]['line']) {
                return false;
            }

            return $commentEnd;
        } while (true);

    }//end findCommentAbove()


}//end class
