<?php
$myArray['key']                = $value;
$myArray[/* key start */'key'] = $value;
$myArray[/* key start */'key'/* key end */] = $value;
$myArray  [  'key'  ]            = $value;
if ($array[($index + 1)] === true) {
} else if ($array  [ ($index + 1) ] === null) {
}
$deepArray['a']['b']   = $value;
$deepArray ['a']['b']  = $value;
$deepArray['a'] ['b']  = $value;
$deepArray ['a'] ['b'] = $value;
$deepArray['a'][ 'b']  = $value;
$ob->arrayProp['a']    = $value;
$ob->arrayProp ['a']   = $value;
$ob->arrayProp[ 'a' ]  = $value;
$array = ['value1', 'value2'];
$array = [ 'value1', 'value2' ];
?>