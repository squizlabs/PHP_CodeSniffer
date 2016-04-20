<?php
/**
 * The Tokens class contains weightings for tokens based on their
 * probability of occurrence in a file.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

define('T_NONE', 'PHPCS_T_NONE');
define('T_OPEN_CURLY_BRACKET', 'PHPCS_T_OPEN_CURLY_BRACKET');
define('T_CLOSE_CURLY_BRACKET', 'PHPCS_T_CLOSE_CURLY_BRACKET');
define('T_OPEN_SQUARE_BRACKET', 'PHPCS_T_OPEN_SQUARE_BRACKET');
define('T_CLOSE_SQUARE_BRACKET', 'PHPCS_T_CLOSE_SQUARE_BRACKET');
define('T_OPEN_PARENTHESIS', 'PHPCS_T_OPEN_PARENTHESIS');
define('T_CLOSE_PARENTHESIS', 'PHPCS_T_CLOSE_PARENTHESIS');
define('T_COLON', 'PHPCS_T_COLON');
define('T_STRING_CONCAT', 'PHPCS_T_STRING_CONCAT');
define('T_INLINE_THEN', 'PHPCS_T_INLINE_THEN');
define('T_INLINE_ELSE', 'PHPCS_T_INLINE_ELSE');
define('T_NULL', 'PHPCS_T_NULL');
define('T_FALSE', 'PHPCS_T_FALSE');
define('T_TRUE', 'PHPCS_T_TRUE');
define('T_SEMICOLON', 'PHPCS_T_SEMICOLON');
define('T_EQUAL', 'PHPCS_T_EQUAL');
define('T_MULTIPLY', 'PHPCS_T_MULTIPLY');
define('T_DIVIDE', 'PHPCS_T_DIVIDE');
define('T_PLUS', 'PHPCS_T_PLUS');
define('T_MINUS', 'PHPCS_T_MINUS');
define('T_MODULUS', 'PHPCS_T_MODULUS');
define('T_BITWISE_AND', 'PHPCS_T_BITWISE_AND');
define('T_BITWISE_OR', 'PHPCS_T_BITWISE_OR');
define('T_BITWISE_XOR', 'PHPCS_T_BITWISE_XOR');
define('T_ARRAY_HINT', 'PHPCS_T_ARRAY_HINT');
define('T_GREATER_THAN', 'PHPCS_T_GREATER_THAN');
define('T_LESS_THAN', 'PHPCS_T_LESS_THAN');
define('T_BOOLEAN_NOT', 'PHPCS_T_BOOLEAN_NOT');
define('T_SELF', 'PHPCS_T_SELF');
define('T_PARENT', 'PHPCS_T_PARENT');
define('T_DOUBLE_QUOTED_STRING', 'PHPCS_T_DOUBLE_QUOTED_STRING');
define('T_COMMA', 'PHPCS_T_COMMA');
define('T_HEREDOC', 'PHPCS_T_HEREDOC');
define('T_PROTOTYPE', 'PHPCS_T_PROTOTYPE');
define('T_THIS', 'PHPCS_T_THIS');
define('T_REGULAR_EXPRESSION', 'PHPCS_T_REGULAR_EXPRESSION');
define('T_PROPERTY', 'PHPCS_T_PROPERTY');
define('T_LABEL', 'PHPCS_T_LABEL');
define('T_OBJECT', 'PHPCS_T_OBJECT');
define('T_CLOSE_OBJECT', 'PHPCS_T_CLOSE_OBJECT');
define('T_COLOUR', 'PHPCS_T_COLOUR');
define('T_HASH', 'PHPCS_T_HASH');
define('T_URL', 'PHPCS_T_URL');
define('T_STYLE', 'PHPCS_T_STYLE');
define('T_ASPERAND', 'PHPCS_T_ASPERAND');
define('T_DOLLAR', 'PHPCS_T_DOLLAR');
define('T_TYPEOF', 'PHPCS_T_TYPEOF');
define('T_CLOSURE', 'PHPCS_T_CLOSURE');
define('T_ANON_CLASS', 'PHPCS_T_ANON_CLASS');
define('T_BACKTICK', 'PHPCS_T_BACKTICK');
define('T_START_NOWDOC', 'PHPCS_T_START_NOWDOC');
define('T_NOWDOC', 'PHPCS_T_NOWDOC');
define('T_END_NOWDOC', 'PHPCS_T_END_NOWDOC');
define('T_OPEN_SHORT_ARRAY', 'PHPCS_T_OPEN_SHORT_ARRAY');
define('T_CLOSE_SHORT_ARRAY', 'PHPCS_T_CLOSE_SHORT_ARRAY');
define('T_GOTO_LABEL', 'PHPCS_T_GOTO_LABEL');
define('T_BINARY_CAST', 'PHPCS_T_BINARY_CAST');
define('T_EMBEDDED_PHP', 'PHPCS_T_EMBEDDED_PHP');
define('T_RETURN_TYPE', 'PHPCS_T_RETURN_TYPE');
define('T_OPEN_USE_GROUP', 'PHPCS_T_OPEN_USE_GROUP');
define('T_CLOSE_USE_GROUP', 'PHPCS_T_CLOSE_USE_GROUP');

// Some PHP 5.3 tokens, replicated for lower versions.
if (defined('T_NAMESPACE') === false) {
    define('T_NAMESPACE', 'PHPCS_T_NAMESPACE');
}

if (defined('T_NS_C') === false) {
    define('T_NS_C', 'PHPCS_T_NS_C');
}

if (defined('T_NS_SEPARATOR') === false) {
    define('T_NS_SEPARATOR', 'PHPCS_T_NS_SEPARATOR');
}

if (defined('T_GOTO') === false) {
    define('T_GOTO', 'PHPCS_T_GOTO');
}

if (defined('T_DIR') === false) {
    define('T_DIR', 'PHPCS_T_DIR');
}

// Some PHP 5.4 tokens, replicated for lower versions.
if (defined('T_TRAIT') === false) {
    define('T_TRAIT', 'PHPCS_T_TRAIT');
}

if (defined('T_TRAIT_C') === false) {
    define('T_TRAIT_C', 'PHPCS_T_TRAIT_C');
}

if (defined('T_INSTEADOF') === false) {
    define('T_INSTEADOF', 'PHPCS_T_INSTEADOF');
}

if (defined('T_CALLABLE') === false) {
    define('T_CALLABLE', 'PHPCS_T_CALLABLE');
}

// Some PHP 5.5 tokens, replicated for lower versions.
if (defined('T_FINALLY') === false) {
    define('T_FINALLY', 'PHPCS_T_FINALLY');
}

if (defined('T_YIELD') === false) {
    define('T_YIELD', 'PHPCS_T_YIELD');
}

// Some PHP 5.6 tokens, replicated for lower versions.
if (defined('T_ELLIPSIS') === false) {
    define('T_ELLIPSIS', 'PHPCS_T_ELLIPSIS');
}

if (defined('T_POW') === false) {
    define('T_POW', 'PHPCS_T_POW');
}

if (defined('T_POW_EQUAL') === false) {
    define('T_POW_EQUAL', 'PHPCS_T_POW_EQUAL');
}

// Some PHP 7 tokens, replicated for lower versions.
if (defined('T_SPACESHIP') === false) {
    define('T_SPACESHIP', 'PHPCS_T_SPACESHIP');
}

if (defined('T_COALESCE') === false) {
    define('T_COALESCE', 'PHPCS_T_COALESCE');
}

// Tokens used for parsing doc blocks.
define('T_DOC_COMMENT_STAR', 'PHPCS_T_DOC_COMMENT_STAR');
define('T_DOC_COMMENT_WHITESPACE', 'PHPCS_T_DOC_COMMENT_WHITESPACE');
define('T_DOC_COMMENT_TAG', 'PHPCS_T_DOC_COMMENT_TAG');
define('T_DOC_COMMENT_OPEN_TAG', 'PHPCS_T_DOC_COMMENT_OPEN_TAG');
define('T_DOC_COMMENT_CLOSE_TAG', 'PHPCS_T_DOC_COMMENT_CLOSE_TAG');
define('T_DOC_COMMENT_STRING', 'PHPCS_T_DOC_COMMENT_STRING');

/**
 * The Tokens class contains weightings for tokens based on their
 * probability of occurrence in a file.
 *
 * The less the chance of a high occurrence of an arbitrary token, the higher
 * the weighting.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
final class PHP_CodeSniffer_Tokens
{

    /**
     * The token weightings.
     *
     * @var array(int => int)
     */
    public static $weightings = array(
                                 T_CLASS               => 1000,
                                 T_INTERFACE           => 1000,
                                 T_TRAIT               => 1000,
                                 T_NAMESPACE           => 1000,
                                 T_FUNCTION            => 100,
                                 T_CLOSURE             => 100,

                                 /*
                                     Conditions.
                                 */

                                 T_WHILE               => 50,
                                 T_FOR                 => 50,
                                 T_FOREACH             => 50,
                                 T_IF                  => 50,
                                 T_ELSE                => 50,
                                 T_ELSEIF              => 50,
                                 T_DO                  => 50,
                                 T_TRY                 => 50,
                                 T_CATCH               => 50,
                                 T_FINALLY             => 50,
                                 T_SWITCH              => 50,

                                 T_SELF                => 25,
                                 T_PARENT              => 25,

                                 /*
                                     Operators and arithmetic.
                                 */

                                 T_BITWISE_AND         => 8,
                                 T_BITWISE_OR          => 8,
                                 T_BITWISE_XOR         => 8,

                                 T_MULTIPLY            => 5,
                                 T_DIVIDE              => 5,
                                 T_PLUS                => 5,
                                 T_MINUS               => 5,
                                 T_MODULUS             => 5,
                                 T_POW                 => 5,
                                 T_SPACESHIP           => 5,
                                 T_COALESCE            => 5,

                                 T_SL                  => 5,
                                 T_SR                  => 5,
                                 T_SL_EQUAL            => 5,
                                 T_SR_EQUAL            => 5,

                                 T_EQUAL               => 5,
                                 T_AND_EQUAL           => 5,
                                 T_CONCAT_EQUAL        => 5,
                                 T_DIV_EQUAL           => 5,
                                 T_MINUS_EQUAL         => 5,
                                 T_MOD_EQUAL           => 5,
                                 T_MUL_EQUAL           => 5,
                                 T_OR_EQUAL            => 5,
                                 T_PLUS_EQUAL          => 5,
                                 T_XOR_EQUAL           => 5,

                                 T_BOOLEAN_AND         => 5,
                                 T_BOOLEAN_OR          => 5,

                                 /*
                                     Equality.
                                 */

                                 T_IS_EQUAL            => 5,
                                 T_IS_NOT_EQUAL        => 5,
                                 T_IS_IDENTICAL        => 5,
                                 T_IS_NOT_IDENTICAL    => 5,
                                 T_IS_SMALLER_OR_EQUAL => 5,
                                 T_IS_GREATER_OR_EQUAL => 5,
                                );

    /**
     * The token weightings.
     *
     * @var array(int => int)
     */
    public static $knownLengths = array(
                                   T_ABSTRACT                 => 8,
                                   T_AND_EQUAL                => 2,
                                   T_ARRAY                    => 5,
                                   T_AS                       => 2,
                                   T_BOOLEAN_AND              => 2,
                                   T_BOOLEAN_OR               => 2,
                                   T_BREAK                    => 5,
                                   T_CALLABLE                 => 8,
                                   T_CASE                     => 4,
                                   T_CATCH                    => 5,
                                   T_CLASS                    => 5,
                                   T_CLASS_C                  => 9,
                                   T_CLONE                    => 5,
                                   T_CONCAT_EQUAL             => 2,
                                   T_CONST                    => 5,
                                   T_CONTINUE                 => 8,
                                   T_CURLY_OPEN               => 2,
                                   T_DEC                      => 2,
                                   T_DECLARE                  => 7,
                                   T_DEFAULT                  => 7,
                                   T_DIR                      => 7,
                                   T_DIV_EQUAL                => 2,
                                   T_DO                       => 2,
                                   T_DOLLAR_OPEN_CURLY_BRACES => 2,
                                   T_DOUBLE_ARROW             => 2,
                                   T_DOUBLE_COLON             => 2,
                                   T_ECHO                     => 4,
                                   T_ELSE                     => 4,
                                   T_ELSEIF                   => 6,
                                   T_EMPTY                    => 5,
                                   T_ENDDECLARE               => 10,
                                   T_ENDFOR                   => 6,
                                   T_ENDFOREACH               => 10,
                                   T_ENDIF                    => 5,
                                   T_ENDSWITCH                => 9,
                                   T_ENDWHILE                 => 8,
                                   T_EVAL                     => 4,
                                   T_EXTENDS                  => 7,
                                   T_FILE                     => 8,
                                   T_FINAL                    => 5,
                                   T_FINALLY                  => 7,
                                   T_FOR                      => 3,
                                   T_FOREACH                  => 7,
                                   T_FUNCTION                 => 8,
                                   T_FUNC_C                   => 12,
                                   T_GLOBAL                   => 6,
                                   T_GOTO                     => 4,
                                   T_HALT_COMPILER            => 15,
                                   T_IF                       => 2,
                                   T_IMPLEMENTS               => 10,
                                   T_INC                      => 2,
                                   T_INCLUDE                  => 7,
                                   T_INCLUDE_ONCE             => 12,
                                   T_INSTANCEOF               => 10,
                                   T_INSTEADOF                => 9,
                                   T_INTERFACE                => 9,
                                   T_ISSET                    => 5,
                                   T_IS_EQUAL                 => 2,
                                   T_IS_GREATER_OR_EQUAL      => 2,
                                   T_IS_IDENTICAL             => 3,
                                   T_IS_NOT_EQUAL             => 2,
                                   T_IS_NOT_IDENTICAL         => 3,
                                   T_IS_SMALLER_OR_EQUAL      => 2,
                                   T_LINE                     => 8,
                                   T_LIST                     => 4,
                                   T_LOGICAL_AND              => 3,
                                   T_LOGICAL_OR               => 2,
                                   T_LOGICAL_XOR              => 3,
                                   T_METHOD_C                 => 10,
                                   T_MINUS_EQUAL              => 2,
                                   T_POW_EQUAL                => 3,
                                   T_MOD_EQUAL                => 2,
                                   T_MUL_EQUAL                => 2,
                                   T_NAMESPACE                => 9,
                                   T_NS_C                     => 13,
                                   T_NS_SEPARATOR             => 1,
                                   T_NEW                      => 3,
                                   T_OBJECT_OPERATOR          => 2,
                                   T_OPEN_TAG_WITH_ECHO       => 3,
                                   T_OR_EQUAL                 => 2,
                                   T_PLUS_EQUAL               => 2,
                                   T_PRINT                    => 5,
                                   T_PRIVATE                  => 7,
                                   T_PUBLIC                   => 6,
                                   T_PROTECTED                => 9,
                                   T_REQUIRE                  => 7,
                                   T_REQUIRE_ONCE             => 12,
                                   T_RETURN                   => 6,
                                   T_STATIC                   => 6,
                                   T_SWITCH                   => 6,
                                   T_THROW                    => 5,
                                   T_TRAIT                    => 5,
                                   T_TRAIT_C                  => 9,
                                   T_TRY                      => 3,
                                   T_UNSET                    => 5,
                                   T_USE                      => 3,
                                   T_VAR                      => 3,
                                   T_WHILE                    => 5,
                                   T_XOR_EQUAL                => 2,
                                   T_YIELD                    => 5,
                                   T_OPEN_CURLY_BRACKET       => 1,
                                   T_CLOSE_CURLY_BRACKET      => 1,
                                   T_OPEN_SQUARE_BRACKET      => 1,
                                   T_CLOSE_SQUARE_BRACKET     => 1,
                                   T_OPEN_PARENTHESIS         => 1,
                                   T_CLOSE_PARENTHESIS        => 1,
                                   T_COLON                    => 1,
                                   T_STRING_CONCAT            => 1,
                                   T_INLINE_THEN              => 1,
                                   T_INLINE_ELSE              => 1,
                                   T_NULL                     => 4,
                                   T_FALSE                    => 5,
                                   T_TRUE                     => 4,
                                   T_SEMICOLON                => 1,
                                   T_EQUAL                    => 1,
                                   T_MULTIPLY                 => 1,
                                   T_DIVIDE                   => 1,
                                   T_PLUS                     => 1,
                                   T_MINUS                    => 1,
                                   T_MODULUS                  => 1,
                                   T_POW                      => 2,
                                   T_SPACESHIP                => 3,
                                   T_COALESCE                 => 2,
                                   T_BITWISE_AND              => 1,
                                   T_BITWISE_OR               => 1,
                                   T_BITWISE_XOR              => 1,
                                   T_SL                       => 2,
                                   T_SR                       => 2,
                                   T_SL_EQUAL                 => 3,
                                   T_SR_EQUAL                 => 3,
                                   T_ARRAY_HINT               => 5,
                                   T_GREATER_THAN             => 1,
                                   T_LESS_THAN                => 1,
                                   T_BOOLEAN_NOT              => 1,
                                   T_SELF                     => 4,
                                   T_PARENT                   => 6,
                                   T_COMMA                    => 1,
                                   T_THIS                     => 4,
                                   T_CLOSURE                  => 8,
                                   T_BACKTICK                 => 1,
                                   T_OPEN_SHORT_ARRAY         => 1,
                                   T_CLOSE_SHORT_ARRAY        => 1,
                                  );

    /**
     * Tokens that represent assignments.
     *
     * @var array(int)
     */
    public static $assignmentTokens = array(
                                       T_EQUAL        => T_EQUAL,
                                       T_AND_EQUAL    => T_AND_EQUAL,
                                       T_OR_EQUAL     => T_OR_EQUAL,
                                       T_CONCAT_EQUAL => T_CONCAT_EQUAL,
                                       T_DIV_EQUAL    => T_DIV_EQUAL,
                                       T_MINUS_EQUAL  => T_MINUS_EQUAL,
                                       T_POW_EQUAL    => T_POW_EQUAL,
                                       T_MOD_EQUAL    => T_MOD_EQUAL,
                                       T_MUL_EQUAL    => T_MUL_EQUAL,
                                       T_PLUS_EQUAL   => T_PLUS_EQUAL,
                                       T_XOR_EQUAL    => T_XOR_EQUAL,
                                       T_DOUBLE_ARROW => T_DOUBLE_ARROW,
                                       T_SL_EQUAL     => T_SL_EQUAL,
                                       T_SR_EQUAL     => T_SR_EQUAL,
                                      );

    /**
     * Tokens that represent equality comparisons.
     *
     * @var array(int)
     */
    public static $equalityTokens = array(
                                     T_IS_EQUAL            => T_IS_EQUAL,
                                     T_IS_NOT_EQUAL        => T_IS_NOT_EQUAL,
                                     T_IS_IDENTICAL        => T_IS_IDENTICAL,
                                     T_IS_NOT_IDENTICAL    => T_IS_NOT_IDENTICAL,
                                     T_IS_SMALLER_OR_EQUAL => T_IS_SMALLER_OR_EQUAL,
                                     T_IS_GREATER_OR_EQUAL => T_IS_GREATER_OR_EQUAL,
                                    );

    /**
     * Tokens that represent comparison operator.
     *
     * @var array(int)
     */
    public static $comparisonTokens = array(
                                       T_IS_EQUAL            => T_IS_EQUAL,
                                       T_IS_IDENTICAL        => T_IS_IDENTICAL,
                                       T_IS_NOT_EQUAL        => T_IS_NOT_EQUAL,
                                       T_IS_NOT_IDENTICAL    => T_IS_NOT_IDENTICAL,
                                       T_LESS_THAN           => T_LESS_THAN,
                                       T_GREATER_THAN        => T_GREATER_THAN,
                                       T_IS_SMALLER_OR_EQUAL => T_IS_SMALLER_OR_EQUAL,
                                       T_IS_GREATER_OR_EQUAL => T_IS_GREATER_OR_EQUAL,
                                      );

    /**
     * Tokens that represent arithmetic operators.
     *
     * @var array(int)
     */
    public static $arithmeticTokens = array(
                                       T_PLUS     => T_PLUS,
                                       T_MINUS    => T_MINUS,
                                       T_MULTIPLY => T_MULTIPLY,
                                       T_DIVIDE   => T_DIVIDE,
                                       T_MODULUS  => T_MODULUS,
                                      );

    /**
     * Tokens that represent casting.
     *
     * @var array(int)
     */
    public static $castTokens = array(
                                 T_INT_CAST    => T_INT_CAST,
                                 T_STRING_CAST => T_STRING_CAST,
                                 T_DOUBLE_CAST => T_DOUBLE_CAST,
                                 T_ARRAY_CAST  => T_ARRAY_CAST,
                                 T_BOOL_CAST   => T_BOOL_CAST,
                                 T_OBJECT_CAST => T_OBJECT_CAST,
                                 T_UNSET_CAST  => T_UNSET_CAST,
                                 T_BINARY_CAST => T_BINARY_CAST,
                                );

    /**
     * Token types that open parenthesis.
     *
     * @var array(int)
     */
    public static $parenthesisOpeners = array(
                                         T_ARRAY    => T_ARRAY,
                                         T_FUNCTION => T_FUNCTION,
                                         T_CLOSURE  => T_CLOSURE,
                                         T_WHILE    => T_WHILE,
                                         T_FOR      => T_FOR,
                                         T_FOREACH  => T_FOREACH,
                                         T_SWITCH   => T_SWITCH,
                                         T_IF       => T_IF,
                                         T_ELSEIF   => T_ELSEIF,
                                         T_CATCH    => T_CATCH,
                                         T_DECLARE  => T_DECLARE,
                                        );

    /**
     * Tokens that are allowed to open scopes.
     *
     * @var array(int)
     */
    public static $scopeOpeners = array(
                                   T_CLASS      => T_CLASS,
                                   T_ANON_CLASS => T_ANON_CLASS,
                                   T_INTERFACE  => T_INTERFACE,
                                   T_TRAIT      => T_TRAIT,
                                   T_NAMESPACE  => T_NAMESPACE,
                                   T_FUNCTION   => T_FUNCTION,
                                   T_CLOSURE    => T_CLOSURE,
                                   T_IF         => T_IF,
                                   T_SWITCH     => T_SWITCH,
                                   T_CASE       => T_CASE,
                                   T_DECLARE    => T_DECLARE,
                                   T_DEFAULT    => T_DEFAULT,
                                   T_WHILE      => T_WHILE,
                                   T_ELSE       => T_ELSE,
                                   T_ELSEIF     => T_ELSEIF,
                                   T_FOR        => T_FOR,
                                   T_FOREACH    => T_FOREACH,
                                   T_DO         => T_DO,
                                   T_TRY        => T_TRY,
                                   T_CATCH      => T_CATCH,
                                   T_FINALLY    => T_FINALLY,
                                   T_PROPERTY   => T_PROPERTY,
                                   T_OBJECT     => T_OBJECT,
                                   T_USE        => T_USE,
                                  );

    /**
     * Tokens that represent scope modifiers.
     *
     * @var array(int)
     */
    public static $scopeModifiers = array(
                                     T_PRIVATE   => T_PRIVATE,
                                     T_PUBLIC    => T_PUBLIC,
                                     T_PROTECTED => T_PROTECTED,
                                    );

    /**
     * Tokens that can prefix a method name
     *
     * @var array(int)
     */
    public static $methodPrefixes = array(
                                     T_PRIVATE   => T_PRIVATE,
                                     T_PUBLIC    => T_PUBLIC,
                                     T_PROTECTED => T_PROTECTED,
                                     T_ABSTRACT  => T_ABSTRACT,
                                     T_STATIC    => T_STATIC,
                                     T_FINAL     => T_FINAL,
                                    );

    /**
     * Tokens that perform operations.
     *
     * @var array(int)
     */
    public static $operators = array(
                                T_MINUS       => T_MINUS,
                                T_PLUS        => T_PLUS,
                                T_MULTIPLY    => T_MULTIPLY,
                                T_DIVIDE      => T_DIVIDE,
                                T_MODULUS     => T_MODULUS,
                                T_POW         => T_POW,
                                T_SPACESHIP   => T_SPACESHIP,
                                T_COALESCE    => T_COALESCE,
                                T_BITWISE_AND => T_BITWISE_AND,
                                T_BITWISE_OR  => T_BITWISE_OR,
                                T_BITWISE_XOR => T_BITWISE_XOR,
                                T_SL          => T_SL,
                                T_SR          => T_SR,
                               );

    /**
     * Tokens that perform boolean operations.
     *
     * @var array(int)
     */
    public static $booleanOperators = array(
                                       T_BOOLEAN_AND => T_BOOLEAN_AND,
                                       T_BOOLEAN_OR  => T_BOOLEAN_OR,
                                       T_LOGICAL_AND => T_LOGICAL_AND,
                                       T_LOGICAL_OR  => T_LOGICAL_OR,
                                       T_LOGICAL_XOR => T_LOGICAL_XOR,
                                      );

    /**
     * Tokens that open code blocks.
     *
     * @var array(int)
     */
    public static $blockOpeners = array(
                                   T_OPEN_CURLY_BRACKET  => T_OPEN_CURLY_BRACKET,
                                   T_OPEN_SQUARE_BRACKET => T_OPEN_SQUARE_BRACKET,
                                   T_OPEN_PARENTHESIS    => T_OPEN_PARENTHESIS,
                                   T_OBJECT              => T_OBJECT,
                                  );

    /**
     * Tokens that don't represent code.
     *
     * @var array(int)
     */
    public static $emptyTokens = array(
                                  T_WHITESPACE             => T_WHITESPACE,
                                  T_COMMENT                => T_COMMENT,
                                  T_DOC_COMMENT            => T_DOC_COMMENT,
                                  T_DOC_COMMENT_STAR       => T_DOC_COMMENT_STAR,
                                  T_DOC_COMMENT_WHITESPACE => T_DOC_COMMENT_WHITESPACE,
                                  T_DOC_COMMENT_TAG        => T_DOC_COMMENT_TAG,
                                  T_DOC_COMMENT_OPEN_TAG   => T_DOC_COMMENT_OPEN_TAG,
                                  T_DOC_COMMENT_CLOSE_TAG  => T_DOC_COMMENT_CLOSE_TAG,
                                  T_DOC_COMMENT_STRING     => T_DOC_COMMENT_STRING,
                                 );

    /**
     * Tokens that are comments.
     *
     * @var array(int)
     */
    public static $commentTokens = array(
                                    T_COMMENT                => T_COMMENT,
                                    T_DOC_COMMENT            => T_DOC_COMMENT,
                                    T_DOC_COMMENT_STAR       => T_DOC_COMMENT_STAR,
                                    T_DOC_COMMENT_WHITESPACE => T_DOC_COMMENT_WHITESPACE,
                                    T_DOC_COMMENT_TAG        => T_DOC_COMMENT_TAG,
                                    T_DOC_COMMENT_OPEN_TAG   => T_DOC_COMMENT_OPEN_TAG,
                                    T_DOC_COMMENT_CLOSE_TAG  => T_DOC_COMMENT_CLOSE_TAG,
                                    T_DOC_COMMENT_STRING     => T_DOC_COMMENT_STRING,
                                   );

    /**
     * Tokens that represent strings.
     *
     * Note that T_STRINGS are NOT represented in this list.
     *
     * @var array(int)
     */
    public static $stringTokens = array(
                                   T_CONSTANT_ENCAPSED_STRING => T_CONSTANT_ENCAPSED_STRING,
                                   T_DOUBLE_QUOTED_STRING     => T_DOUBLE_QUOTED_STRING,
                                  );

    /**
     * Tokens that represent brackets and parenthesis.
     *
     * @var array(int)
     */
    public static $bracketTokens = array(
                                    T_OPEN_CURLY_BRACKET   => T_OPEN_CURLY_BRACKET,
                                    T_CLOSE_CURLY_BRACKET  => T_CLOSE_CURLY_BRACKET,
                                    T_OPEN_SQUARE_BRACKET  => T_OPEN_SQUARE_BRACKET,
                                    T_CLOSE_SQUARE_BRACKET => T_CLOSE_SQUARE_BRACKET,
                                    T_OPEN_PARENTHESIS     => T_OPEN_PARENTHESIS,
                                    T_CLOSE_PARENTHESIS    => T_CLOSE_PARENTHESIS,
                                   );

    /**
     * Tokens that include files.
     *
     * @var array(int)
     */
    public static $includeTokens = array(
                                    T_REQUIRE_ONCE => T_REQUIRE_ONCE,
                                    T_REQUIRE      => T_REQUIRE,
                                    T_INCLUDE_ONCE => T_INCLUDE_ONCE,
                                    T_INCLUDE      => T_INCLUDE,
                                   );

    /**
     * Tokens that make up a heredoc string.
     *
     * @var array(int)
     */
    public static $heredocTokens = array(
                                    T_START_HEREDOC => T_START_HEREDOC,
                                    T_END_HEREDOC   => T_END_HEREDOC,
                                    T_HEREDOC       => T_HEREDOC,
                                    T_START_NOWDOC  => T_START_NOWDOC,
                                    T_END_NOWDOC    => T_END_NOWDOC,
                                    T_NOWDOC        => T_NOWDOC,
                                   );

    /**
     * Tokens that represent the names of called functions.
     *
     * Mostly, these are just strings. But PHP tokeizes some language
     * constructs and functions using their own tokens.
     *
     * @var array(int)
     */
    public static $functionNameTokens = array(
                                         T_STRING       => T_STRING,
                                         T_EVAL         => T_EVAL,
                                         T_EXIT         => T_EXIT,
                                         T_INCLUDE      => T_INCLUDE,
                                         T_INCLUDE_ONCE => T_INCLUDE_ONCE,
                                         T_REQUIRE      => T_REQUIRE,
                                         T_REQUIRE_ONCE => T_REQUIRE_ONCE,
                                         T_ISSET        => T_ISSET,
                                         T_UNSET        => T_UNSET,
                                         T_EMPTY        => T_EMPTY,
                                        );


    /**
     * A PHP_CodeSniffer_Tokens class cannot be constructed.
     *
     * Only static calls are allowed.
     */
    private function __construct()
    {

    }//end __construct()


    /**
     * Returns the highest weighted token type.
     *
     * Tokens are weighted by their approximate frequency of appearance in code
     * - the less frequently they appear in the code, the higher the weighting.
     * For example T_CLASS tokens appear very infrequently in a file, and
     * therefore have a high weighting.
     *
     * Returns false if there are no weightings for any of the specified tokens.
     *
     * @param array(int) $tokens The token types to get the highest weighted
     *                           type for.
     *
     * @return int The highest weighted token.
     */
    public static function getHighestWeightedToken(array $tokens)
    {
        $highest     = -1;
        $highestType = false;

        $weights = self::$weightings;

        foreach ($tokens as $token) {
            if (isset($weights[$token]) === true) {
                $weight = $weights[$token];
            } else {
                $weight = 0;
            }

            if ($weight > $highest) {
                $highest     = $weight;
                $highestType = $token;
            }
        }

        return $highestType;

    }//end getHighestWeightedToken()


}//end class
