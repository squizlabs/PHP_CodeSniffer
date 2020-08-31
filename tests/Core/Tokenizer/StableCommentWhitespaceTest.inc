<?php

/* testSingleLineSlashComment */
// Comment

/* testSingleLineSlashCommentTrailing */
echo 'a'; // Comment

/* testSingleLineSlashAnnotation */
// phpcs:disable Stnd.Cat

/* testMultiLineSlashComment */
// Comment1
// Comment2
// Comment3

/* testMultiLineSlashCommentWithIndent */
    // Comment1
    // Comment2
    // Comment3

/* testMultiLineSlashCommentWithAnnotationStart */
// phpcs:ignore Stnd.Cat
// Comment2
// Comment3

/* testMultiLineSlashCommentWithAnnotationMiddle */
// Comment1
// @phpcs:ignore Stnd.Cat
// Comment3

/* testMultiLineSlashCommentWithAnnotationEnd */
// Comment1
// Comment2
// phpcs:ignore Stnd.Cat


/* testSingleLineStarComment */
/* Single line star comment */

/* testSingleLineStarCommentTrailing */
echo 'a'; /* Comment */

/* testSingleLineStarAnnotation */
/* phpcs:ignore Stnd.Cat */

/* testMultiLineStarComment */
/* Comment1
 * Comment2
 * Comment3 */

/* testMultiLineStarCommentWithIndent */
        /* Comment1
         * Comment2
         * Comment3 */

/* testMultiLineStarCommentWithAnnotationStart */
/* @phpcs:ignore Stnd.Cat
 * Comment2
 * Comment3 */

/* testMultiLineStarCommentWithAnnotationMiddle */
/* Comment1
 * phpcs:ignore Stnd.Cat
 * Comment3 */

/* testMultiLineStarCommentWithAnnotationEnd */
/* Comment1
 * Comment2
 * phpcs:ignore Stnd.Cat */


/* testSingleLineDocblockComment */
/** Comment */

/* testSingleLineDocblockCommentTrailing */
$prop = 123; /** Comment */

/* testSingleLineDocblockAnnotation */
/** phpcs:ignore Stnd.Cat.Sniff */

/* testMultiLineDocblockComment */
/**
 * Comment1
 * Comment2
 *
 * @tag Comment
 */

/* testMultiLineDocblockCommentWithIndent */
    /**
     * Comment1
     * Comment2
     *
     * @tag Comment
     */

/* testMultiLineDocblockCommentWithAnnotation */
/**
 * Comment
 *
 * phpcs:ignore Stnd.Cat
 * @tag Comment
 */

/* testMultiLineDocblockCommentWithTagAnnotation */
/**
 * Comment
 *
 * @phpcs:ignore Stnd.Cat
 * @tag Comment
 */

/* testSingleLineHashComment */
# Comment

/* testSingleLineHashCommentTrailing */
echo 'a'; # Comment

/* testMultiLineHashComment */
# Comment1
# Comment2
# Comment3

/* testMultiLineHashCommentWithIndent */
    # Comment1
    # Comment2
    # Comment3

/* testSingleLineSlashCommentNoNewLineAtEnd */
// Slash ?>
<?php

/* testSingleLineHashCommentNoNewLineAtEnd */
# Hash ?>
<?php

/* testCommentAtEndOfFile */
/* Comment