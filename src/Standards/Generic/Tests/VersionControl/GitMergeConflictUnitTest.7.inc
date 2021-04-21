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
	<p id="test-this"><?= 'Testing a merge conflict.'; ?></p>
=======
	<p id="test-that"><?= 'Another text string.'; ?></p>
>>>>>>> ref/heads/feature-branch
</div>

<?= $text; ?>
