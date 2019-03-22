<?php

$notAProperty = 123;

class Test {
    public $propertyNoDocblock = true;

    /**
     * Docblock.
     */
    static $propertyStaticHadDocblock = true; // Unrelated trailing comment.

    private static $propertyNoDocblockTrailingComment = true;

    /*
     * Multi-line comment.
     */
    var $propertyMultiLineComment = true;

    // Inline comment.
    protected $propertyInlineComment = true;

    /**
     * Docblock with whitespace between property and docblock.
     */


    static private $propertyHadDocblockAndWhitespace = true; // Unrelated trailing comment.

    /**
     * Docblock.
     */
    public
    /* comment */
    static
    // phpcs:ignore Standard.Category.Sniff -- For reasons.
    $propertyHasDocblockInterlacedComments = true;

    /**
     * Docblock.
     */
    public function test($paramNotProperty) {}
}
