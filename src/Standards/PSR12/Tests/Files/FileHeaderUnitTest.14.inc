<?php if (array_key_exists('user', $_SESSION)) : ?>
    <li><a href="/user/update.php">My page</a></li>
    <li><a href="/post/write.php">Write</a></li>
    <li><a href="/auth/logout.php">Sign out</a></li>
<?php else : ?>
    <li><a href="/auth/login.php">Sign in</a></li>
<?php endif; ?>
