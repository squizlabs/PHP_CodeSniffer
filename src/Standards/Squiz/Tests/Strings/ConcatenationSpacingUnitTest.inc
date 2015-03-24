<?php
$string = 'Hello'.$there.'. How are'.$you.$going."today $okay";
$string = 'Hello' . $there . '. How are' . $you . $going . "today $okay";
$string = 'Hello'.$there;
$string = 'Hello'. $there;
$string = 'Hello' .$there;

$y = '1'
   . '2'
   . '3';

// @codingStandardsChangeSetting Squiz.Strings.ConcatenationSpacing spacing 1

$string = 'Hello'.$there.'. How are'.$you.$going.   "today $okay";
$string = 'Hello' . $there . '. How are' . $you . $going . "today $okay";
$string = 'Hello'.$there;
$string = 'Hello'. $there;
$string = 'Hello' .$there;

// @codingStandardsChangeSetting Squiz.Strings.ConcatenationSpacing ignoreNewlines true
$y = '1'
   . '2'
   . '3';

$y = '1' .
     '2' .
     '3';

$y = '1'
. '2'
. '3';

$y = '1'
   .'2'.
    '3'
   .  '4';