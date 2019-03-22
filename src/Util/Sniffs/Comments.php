<?php
/**
 * Utility functions for use when examining comments and docblocks.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util\Sniffs;

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


}//end class
