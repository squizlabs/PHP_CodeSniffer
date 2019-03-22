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
                    $type1 = self::suggestType($matches[1], $form, $allowedTypes);
                }

                $type2 = '';
                if (isset($matches[2]) === true) {
                    $type2 = self::suggestType($matches[2], $form, $allowedTypes);
                    if ($type2 !== '') {
                        $type2 = $sep.' '.$type2;
                    }
                }

                return 'array'.$open.$type1.$type2.$close;
            }//end if

            return 'array';
        }//end if

        // Must be a custom type name.
        return $varType;

    }//end suggestType()


}//end class
