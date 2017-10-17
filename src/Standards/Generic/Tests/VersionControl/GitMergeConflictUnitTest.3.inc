<!-- Test detecting merge conflicts in inline HTML. -->
<div class="abc">
<<<<<<< HEAD
	<p id="test-this">Testing a merge conflict.</p>
=======
	<p id="test-that">Another text string.</p>
>>>>>>> ref/heads/feature-branch
</div>

<!-- Test detecting merge conflicts in inline HTML. -->
<div class="abc">
<<<<<<< HEAD
	<p id="test-this"><?php echo 'Testing a merge conflict.'; ?></p>
=======
	<p id="test-that"><?php echo 'Another text string.'; ?></p>
>>>>>>> ref/heads/feature-branch
</div>

<?php

// Break the tokenizer.
<<<<<<< HEAD
$a = 1;
=======
$a = 2;
>>>>>>> master

/*
 * The above tests are based on "normal" tokens.
 * The below test checks that once the tokenizer breaks down because of
 * unexpected merge conflict boundaries - i.e. after the first merge conflict
 * opener in non-comment, non-heredoc/nowdoc, non-inline HTML code -, subsequent
 * merge conflict boundaries will still be detected correctly.
 */
?>

<div class="abc">
<<<<<<< HEAD
	<p id="test-this">Testing a merge conflict.</p>
=======
	<p id="test-that">Another text string.</p>
>>>>>>> ref/heads/feature-branch
</div>
