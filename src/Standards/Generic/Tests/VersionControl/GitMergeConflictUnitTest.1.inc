<?php

var_dump(1 << -1);
var_dump(1 >> -1);
var_dump(
1
<<
-1
);

$string =
<<<EOD
This is a heredoc.
EOD;

/**
 * This is not a merge conflict, but a comment showing what
 * a merge conflict boundary looks:
 * <<<<<<< HEAD
 */

function test()
    {
        $arr = array(
            'a' => 'a'
<<<<<<< HEAD
            'b' => 'b'
=======
            'c' => 'c'
>>>>>>> master
        );
    }

/*
 * The above tests are based on "normal" tokens.
 * The below test checks that once the tokenizer breaks down because of
 * unexpected merge conflict boundaries - i.e. after the first merge conflict
 * opener in non-comment, non-heredoc/nowdoc, non-inline HTML code -, subsequent
 * merge conflict boundaries will still be detected correctly.
 */

/*
 * Testing detecting subsequent merge conflicts.
 *
<<<<<<< HEAD
 * @var string $bar
 */
public function foo($bar){ }

/**
============
The above is not the boundary, the below is.
=======
 * @var string $bar
>>>>>>> f19f8a5... Commit message
*/

// Test that stray boundaries, i.e. an opener without closer and such, are detected.
<<<<<<< HEAD
$a = 1;
=======
