<?php

$string =
		<<<"EOD"
		Merge conflicts in PHP 7.3 indented heredocs
<<<<<<< HEAD
		can be problematic.
=======
		should also be detected.
>>>>>>> ref/heads/other-branchname
		And now they are.
		EOD;

// Break the tokenizer.
>>>>>>> master

/*
 * The above tests are based on "normal" tokens.
 * The below test checks that once the tokenizer breaks down because of
 * unexpected merge conflict boundaries - i.e. after the first merge conflict
 * opener in non-comment, non-heredoc/nowdoc, non-inline HTML code -, subsequent
 * merge conflict boundaries will still be detected correctly.
 */

$string =
		<<<"EOD"
		Merge conflicts in PHP 7.3 indented heredocs
<<<<<<< HEAD
		can be problematic.
=======
		should also be detected.
>>>>>>> ref/heads/other-branchname
		And now they are.
		EOD;
