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


/* testSingleLineSlashCommentNoNewLineAtEnd */
// Slash ?>
<?php

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

/* testSingleLineHashCommentNoNewLineAtEnd */
# Hash ?>
<?php

/* testCommentAtEndOfFile */
/* Comment