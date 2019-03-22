<?php

const GLOBAL_CONST_NO_DOCBLOCK = true;

function() {};/* end closure */

const GLOBAL_CONST_UNRELATED_COMMENT = true;

// Comment line,
const GLOBAL_CONST_HAS_COMMENT_1 = true;

// Unrelated comment.

// Comment line,
// and another comment line.
const GLOBAL_CONST_HAS_COMMENT_2 = true;

/*
 * Comment.
 */
const GLOBAL_CONST_HAS_COMMENT_3 = true;

/**
 * Docblock.
 */
const GLOBAL_CONST_HAS_DOCBLOCK = true;

class Test {
    const CLASS_CONST_NO_DOCBLOCK = true;

    public function fooBar() {
    }// end function

    const CLASS_CONST_UNRELATED_COMMENT = true; /* Unrelated end comment. */

    // Comment line,
    // and another comment line.
    const CLASS_CONST_HAS_COMMENT_1 = true;

    // Unrelated comment.

    // Comment line,
    // and another comment line.
    const CLASS_CONST_HAS_COMMENT_2 = true;

    /*
     * Comment.
     */
    const CLASS_CONST_HAS_COMMENT_3 = true;

    /**
     * Docblock (should not be picked up).
     */
    /* Comment. */
    const CLASS_CONST_HAS_COMMENT_4 = true;

    /**
     * Docblock.
     */
    const CLASS_CONST_HAS_DOCBLOCK_1 = true;

    /**
     * Docblock.
     */


    public const CLASS_CONST_HAS_DOCBLOCK_2 = true;

    /**
     * Docblock.
     */
    private /* comment */ const CLASS_CONST_HAS_DOCBLOCK_3 = true;

    /**
     * Docblock.
     */
    protected // phpcs:ignore Standard.Cat.Sniff -- for reasons
        const CLASS_CONST_HAS_DOCBLOCK_4 = true;

    /**
     * Docblock.
     */
    protected /** Multi-line docblock style
        comment, don't ask me why someone would do this. */
        const CLASS_CONST_HAS_DOCBLOCK_5 = true;
}
