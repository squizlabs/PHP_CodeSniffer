<?php

/*
 * Null safe operator.
 */

/* testObjectOperator */
echo $obj->foo;

/* testNullsafeObjectOperator */
echo $obj?->foo;

/* testNullsafeObjectOperatorWriteContext */
// Intentional parse error, but not the concern of the tokenizer.
$foo?->bar->baz = 'baz';

/* testTernaryThen */
echo $obj ? $obj->prop : $other->prop;

/* testParseErrorWhitespaceNotAllowed */
echo $obj ?
    -> foo;

/* testParseErrorCommentNotAllowed */
echo $obj ?/*comment*/-> foo;

/* testLiveCoding */
// Intentional parse error. This has to be the last test in the file.
echo $obj?
