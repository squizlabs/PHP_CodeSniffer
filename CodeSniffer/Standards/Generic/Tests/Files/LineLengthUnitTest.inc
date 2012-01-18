<?php

// This line is okay... just!
if (($reallyLongVarName === true) || (is_array($anotherLongVarName) == false)) {
    // Do something.
}

// This line is not okay... just!
if (($reallyLongVarName === true) || (is_array($anotherLongVarName) === false)) {
    // Do something.
}


// This line is is too long.
if (($anotherReallyLongVarName === true) || (is_array($anotherReallyLongVarName) === false)) {
    // Do something.
}

// This is a really really long comment that is going to go to exactly 80 chars.

// This is another really really long comment that is going to go well over 80 characters.

// And here is just a bunch of spaces that exceeds the line length.
                                                                                    

// And here are some spaces exactly 80 chars long.
                                                                                

// This is a really really really really long long comment that is going to go to exactly 100 chars.

// This is another really really really really really long comment that is going to go well over 100 characters.

// And here is just a bunch of spaces that exceeds the max line length.
                                                                                                           

// And here are some spaces exactly 100 chars long.
                                                                                                    
?>
<b>Hello</b>b>
<?php
echo 'hi';
?>

<?php
/**
 * Comments contining CVS IDs can be long, but should be ignored because
 * they cannot be changed by the developer. Same with license URLs.
 *
 * @license http://www.freebsd.org/copyright/freebsd-license.html BSD License (2 Clause)
 */
 ?>

<?php
// This is another really long comment that is going to go well over 100 characters, with no closing php tag after it.
