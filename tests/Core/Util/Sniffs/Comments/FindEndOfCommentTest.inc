<?php

echo 'testDocblockSequence';

    /**
     * Short description.
     */

echo 'testInlineCommentSequence_1';

    // line 1
    echo 'string';

echo 'testInlineCommentSequence_2';

    // line 1
    //line 2
    // line 3
    // line 4
    echo 'string';

echo 'testInlineCommentSequence_3';

    // line 1
    //line 2
    // line 3

    // Comment line not belonging to the sequence.

echo 'testInlineCommentSequence_4';

    // line 1
    //line 2
    // line 3
    /* Block comment not belonging to the sequence. */

echo 'testInlineCommentSequence_5';

    // line 1
    // phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within a block.
    // line 3

echo 'testInlineCommentSequence_6';

    // phpcs:ignore Standard.Cat.Sniff -- blocks do not start on an annotation.
    // line 1
    // line 2
    // line 3
    // phpcs:ignore Standard.Cat.Sniff -- blocks can not end on an annotation.

echo 'testInlineCommentSequence_7'; // Stand-alone inline trailing comment.
    // This starts a new inline comment

echo 'testInlineCommentSequence_8';

    /* Stand-alone block comment.*/ // Stand-alone inline trailing comment.
    // This starts a new inline comment

echo 'testInlineCommentSequence_9';

    // Stand alone inline comment.



    // Another stand alone inline comment.

echo 'testInlineCommentSequence_10';

    // line 1
    // line 2
    # Perl-style comment not belonging to the sequence.

echo 'testInlineCommentSequence_11';

    # line 1
    # line 2
    // Slash-style comment not belonging to the sequence.

echo 'testInlineCommentSequence_12';

    // line 1
    // This inline comment shows /* block comment format */
    // line 3

echo 'testBlockCommentSequence_1';

    /* line 1 */
    echo 'string';

echo 'testBlockCommentSequence_2';

    /* line 1 */
    // inline comment not related to the sequence.
    echo 'string';

echo 'testBlockCommentSequence_3';

    /* line 1
       line 2
       line 3 */
    /* Comment line not belonging to the sequence. */

echo 'testBlockCommentSequence_4';

    /*
       line 1
       line 2
       line 3
     */
    /* Comment line not belonging to the sequence. */

echo 'testBlockCommentSequence_5';

    /*
     * line 1
     * line 2
     * line 3
     */
    /* Comment line not belonging to the sequence. */

echo 'testBlockCommentSequence_6';

    /*
       comment with a couple of blank lines


       another comment line
     */

echo 'testBlockCommentSequence_7';

    /*
     * line 1
     * phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
     * line 3
     */

echo 'testBlockCommentSequence_8';

    /* line 1
     * and line 2
     * phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
     * phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them. */
    /* Comment line not belonging to the sequence. */

echo 'testBlockCommentSequence_9';

    /* line 1

       phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
       phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them. */

echo 'testBlockCommentSequence_10';

    /* phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
     * phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
     * ... and some more reasons for ignoring the sniff
     * ... and yet more reasons.
     */
    /* Comment line not belonging to the sequence. */

echo 'testBlockCommentSequence_11';

    /* phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
       phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.

       ... and yet more reasons.
     */

echo 'testBlockCommentSequence_12';

    /* line 1 *//* unrelated comment */
    echo 'string';

echo 'testBlockCommentSequence_13';

    /* ****************
    // line 1
    // line 2
    // line 3
    // *************** */

echo 'testBlockCommentSequence_last';
    /*
     * Intentional parse error.
     * This must be the last test in the file.
     *
