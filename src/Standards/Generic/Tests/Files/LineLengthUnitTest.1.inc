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
// This is another really long comment that is going to go well over 100 characters, with no closing php tag after it.

/**
 * Does something cool, blah blah, not so long line.
 *
 * We're using Wilson scoring because blah blah, see:
 * http://en.wikipedia.org/wiki/Binomial_proportion_confidence_interval#Wilson_score_interval
 */

// Go here:
// http://en.wikipedia.org/wiki/Binomial_proportion_confidence_interval#Wilson_score_interval

# http://en.wikipedia.org/wiki/Binomial_proportion_confidence_interval#Wilson_score_interval

if ($foo) {
    if ($bar) {
        if ($baz) {
            // Next line can be broken:
            // foo bar baz http://en.wikipedia.org/wiki/Binomial_proportion#blahblahblahblah
            // But this one is just too long to break with this indent:
            // foo bar baz http://en.wikipedia.org/wiki/Binomial_proportion#blahblahblahblahblah
        }
    }
}

/* -------------------------------- 80 chars -------------------------------- */
$ab	= function () { /* comment */ if ($foo === true) { return (int) $foo; } };
$ab	= function ()	{ /* comment */ if ($foo === true){ return	(int)$foo; }};
$ab	= function ()	{ /* comment */ if ($foo === true) { return	(int) $foo; }  };
	$ab	= function () { /* comment */ if ($foo === true) { return(int) $foo; }};
	$ab	= function () { /* comment */ if ($foo === true) {	return(int) $foo; }};
$ab	=  	$ab	=  	$ab	=  	$ab	=  	$ab	=  	$ab	=  	$ab	=  	$ab	=  	$ab	=  	$ab;

// PHPCS annotations on a line by themselves should be ignored for the metrics.
// phpcs:enable Standard.Category.Sniff.ErrorCode1,Standard.Category.Sniff.ErrorCode2 -- for reasons ...

// ... but not when combined with statements.
$a = $b; // phpcs:ignore Standard.Category.Sniff.ErrorCode1,Standard.Category.Sniff.ErrorCode2 -- for reasons ...

if (($anotherReallyLongVarName === true) {} // This comment makes the line too long.
