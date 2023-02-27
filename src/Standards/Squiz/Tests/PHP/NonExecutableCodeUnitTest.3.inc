<!-- no problem here -->
<?php if (true): ?>
    <?php foreach ([] as $item): ?>
        <?php continue; ?>
    <?php endforeach; ?>
<?php endif; ?>

<!-- no problem here -->
<?php if (true) { ?>
    <?php foreach ([] as $item) { ?>
        <?php continue; ?>
    <?php } ?>
<?php } ?>

<!-- should detect an error here -->
<?php if (true): ?>
    <?php foreach ([] as $item): ?>
        <?php continue; ?>
        <div>non-executable</div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- should detect an error here -->
<?php if (true): ?>
    <?php foreach ([] as $item): ?>
        <?php continue; ?>

        <div>non-executable</div>

    <?php endforeach; ?>
<?php endif; ?>
