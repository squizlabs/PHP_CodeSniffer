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
     * An array of variable types for param/var we will check.
     *
     * Keys are short-form, values long-form.
     *
     * @var string[]
     */
    public static $allowedTypes = [
        'array'    => 'array',
        'bool'     => 'boolean',
        'float'    => 'float',
        'int'      => 'integer',
        'mixed'    => 'mixed',
        'object'   => 'object',
        'string'   => 'string',
        'resource' => 'resource',
        'callable' => 'callable',
    ];


    /**
     * Returns a valid variable type for param/var tags.
     *
     * If type is not one of the standard types, it must be a custom type.
     * Returns the correct type name suggestion if type name is invalid.
     *
     * @param string $varType The variable type to process.
     * @param string $form    Optional. Whether to prefer long-form or short-form
     *                        types. This only affects the integer and boolean types.
     *                        Accepted values: 'long', 'short'. Defaults to `short`.
     *
     * @return string
     */
    public static function suggestType($varType, $form='short')
    {
        if ($varType === '') {
            return '';
        }

        $lowerVarType = strtolower(trim($varType));

        if (($form === 'short' && isset(self::$allowedTypes[$lowerVarType]) === true)
            || ($form === 'long' && in_array($lowerVarType, self::$allowedTypes, true) === true)
        ) {
            return $lowerVarType;
        }

        // Not listed in allowed types, check for a limited set of known variations.
        switch ($lowerVarType) {
        case 'bool':
        case 'boolean':
            if ($form === 'long') {
                return 'boolean';
            }
            return 'bool';

        case 'double':
        case 'real':
            return 'float';

        case 'int':
        case 'integer':
            if ($form === 'long') {
                return 'integer';
            }
            return 'int';

        case 'array()':
            return 'array';
        }//end switch

        // Handle more complex types, like arrays.
        if (strpos($lowerVarType, 'array(') !== false) {
            // Valid array declaration:
            // array, array(type), array(type1 => type2).
            $matches = [];
            $pattern = '/^array\(\s*([^\s=>]*)(?:\s*=>\s*+(.*))?\s*\)/i';
            if (preg_match($pattern, $varType, $matches) === 1) {
                $type1 = '';
                if (isset($matches[1]) === true) {
                    $type1 = self::suggestType($matches[1], $form);
                }

                $type2 = '';
                if (isset($matches[2]) === true) {
                    $type2 = self::suggestType($matches[2], $form);
                    if ($type2 !== '') {
                        $type2 = ' => '.$type2;
                    }
                }

                return "array($type1$type2)";
            }//end if

            return 'array';
        }//end if

        // Must be a custom type name.
        return $varType;

    }//end suggestType()


}//end class
