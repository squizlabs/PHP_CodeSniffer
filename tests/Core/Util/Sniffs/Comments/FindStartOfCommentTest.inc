<?php

    /**
     * Short description.
     */

echo 'testDocblockSequence';

    echo 'string';
    // line 1

echo 'testInlineCommentSequence_1';

    echo 'string';
    // line 1
    //line 2
    // line 3
    // line 4

echo 'testInlineCommentSequence_2';

    // Comment line not belonging to the sequence.

    // line 1
    //line 2
    // line 3

echo 'testInlineCommentSequence_3';

    /* Block comment not belonging to the sequence. */
    // line 1
    //line 2
    // line 3

echo 'testInlineCommentSequence_4';

    // line 1
    // phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within a block.
    // line 3

echo 'testInlineCommentSequence_5';

    // phpcs:ignore Standard.Cat.Sniff -- blocks do not start on an annotation.
    // line 1
    // line 2
    // line 3
    // phpcs:ignore Standard.Cat.Sniff -- blocks can not end on an annotation.

echo 'testInlineCommentSequence_6';

echo 'string'; // Stand-alone inline trailing comment.
    // This starts (and ends) a new inline comment

echo 'testInlineCommentSequence_7';

    /* Stand-alone block comment.*/ // Stand-alone inline trailing comment.
    // This starts (and ends) a new inline comment

echo 'testInlineCommentSequence_8';

    // Stand alone inline comment.



    // Another stand alone inline comment.

echo 'testInlineCommentSequence_9';

    # Perl-style comment not belonging to the sequence.
    // line 1
    // line 2

echo 'testInlineCommentSequence_10';

    // Slash-style comment not belonging to the sequence.
    # line 1
    # line 2

echo 'testInlineCommentSequence_11';

    // line 1
    // This inline comment shows /* block comment format */
    // line 3

echo 'testInlineCommentSequence_12';

    echo 'string';
    /* line 1 */

echo 'testBlockCommentSequence_1';

    echo 'string';
    // inline comment not related to the sequence.
    /* line 1 */

echo 'testBlockCommentSequence_2';

    /* Comment line not belonging to the sequence. */
    /* line 1
       line 2
       line 3 */

echo 'testBlockCommentSequence_3';

    /* Comment line not belonging to the sequence. */
    /*
       line 1
       line 2
       line 3
     */

echo 'testBlockCommentSequence_4';

    /* Comment line not belonging to the sequence. */
    /*
     * line 1
     * line 2
     * line 3
     */

echo 'testBlockCommentSequence_5';

    /*
       comment with a couple of blank lines


       another comment line
     */

echo 'testBlockCommentSequence_6';

    /*
     * line 1
     * phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
     * line 3
     */

echo 'testBlockCommentSequence_7';

    /* Comment line not belonging to the sequence. */
    /* line 1
     * and line 2
     * phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
     * phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them. */

echo 'testBlockCommentSequence_8';

    /* line 1

       phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
       phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them. */

echo 'testBlockCommentSequence_9';

    /* Comment line not belonging to the sequence. */
    /* phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
     * phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
     * ... and some more reasons for ignoring the sniff
     * ... and yet more reasons.
     */

echo 'testBlockCommentSequence_10';

    /* phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.
       phpcs:ignore Standard.Cat.Sniff -- blocks can have annotations within them.

       ... and yet more reasons.
     */

echo 'testBlockCommentSequence_11';

    /* line 1 *//* unrelated comment */
    echo 'string';

echo 'testBlockCommentSequence_12';

    /* ****************
    // line 1
    // line 2
    // line 3
    // *************** */

echo 'testBlockCommentSequence_13';
