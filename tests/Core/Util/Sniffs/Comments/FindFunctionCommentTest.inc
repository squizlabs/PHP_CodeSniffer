<?php

function functionNoDocblock() {
}/** end function */

function functionUnrelatedComment() {}

// Comment line,
function functionHasComment_1() {}

// Unrelated comment.

// Comment line,
// and another comment line.
function functionHasComment_2() {}

/*
 * Comment.
 */
function functionHasComment_3() {}

/**
 * Docblock.
 */
function functionHasDocblock() {}

class Test {
    public function methodNoDocblock_1() {
    }//end function

    public function methodUnrelatedComment() {
    } // Unrelated end comment.

    // Comment line,
    // and another comment line.
    public function methodHasComment_1() {}

    // Unrelated comment.

    // Comment line,
    // and another comment line.
    protected static function methodHasComment_2() {}

    /*
     * Comment.
     */
    private function methodHasComment_3() {}

    /**
     * Docblock (should not be picked up).
     */
    /* Comment. */
    abstract public function methodHasComment_4();

    /**
     * Docblock.
     */
    public function methodHasDocblock_1() {}

    /**
     * Docblock.
     */
    final public function methodHasDocblock_2() {}

    /**
     * Docblock.
     */
    final /*comment*/ /** comment */ public /*comment*/ static /* comment */ function methodHasDocblock_3() {}

    /**
     * Docblock.
     */
    protected // phpcs:ignore Standard.Cat.Sniff -- for reasons
        static // comment
        /* comment */
        function methodHasDocblock_4() {}

    public function methodNoDocblock_2() {}

}
