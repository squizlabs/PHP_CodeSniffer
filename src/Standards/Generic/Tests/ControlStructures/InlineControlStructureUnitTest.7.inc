<?php
// Issue #2565.
// Important note: this file intentionally does not have a ".fixed" version as
// when `short_open_tag=Off`, no fixes will be made, while when `short_open_tag=On`
// no fixes are necessary.
?>

<?php if (true) : ?>
<? endif ?>
<?php if (true) { ?>
<? } ?>

<? if (false) : ?>
<? endif ?>
<?php if (false) { ?>
<?php } ?>
