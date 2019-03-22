<?php

$anonClass = new class() {}

class ClassNoDocblock {
}// Trailing comment.

class ClassNoDocblockTrailingCommentPrevious {}

// Multi-line comment.
// Single-line style.
class ClassComment_1 {}

/*
 * Multi-line comment.
 */
class ClassComment_2 {}

/**
 * Docblock.
 */
class ClassDocblock_1 {}

    /**
     * Docblock with whitespace between property and docblock and incorrect indent.
     */



abstract class ClassDocblock_2 {}

/**
 * Docblock.
 */
final
    /* comment */
    /* phpcs:ignore Standard.Category.Sniff -- For reasons. */
    class ClassDocblock_3 {}


final /* This is not the docblock. */ class ClassNoDocblockWithInterlacedComments {}

interface InterfaceNoDocblock {}

/*
 * Multi-line comment.
 */
interface InterfaceComment {}

/**
 * Docblock.
 */
interface InterfaceDocblock {} /** Trailing comment */

trait TraitNoDocblock {}

// Single-line comment.
trait TraitComment {}

/**
 * Docblock.
 */
trait TraitDocblock {}

/**
 * Traits can not have the `abstract` keyword, only trait methods can.
 */
abstract trait TraitParseError {}
