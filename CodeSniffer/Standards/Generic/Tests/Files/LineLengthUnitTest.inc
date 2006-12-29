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
                                                                                    

// And here are some space exactly 80 chars long.
                                                                                
?>
<b>Hello</b>b>
<?php
echo 'hi';
?>
