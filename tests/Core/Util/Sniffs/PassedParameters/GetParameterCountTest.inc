<?php

/* testFunctionCall0 */
myfunction();

/* testFunctionCall1 */
myfunction(1);

/* testFunctionCall2 */
myfunction( 1, 2 );

/* testFunctionCall3 */
myfunction(1, 2, 3);

/* testFunctionCall4 */
myfunction(1, 2, 3, 4);

/* testFunctionCall5 */
myfunction(1, 2, 3, 4, 5);

/* testFunctionCall6 */
myfunction(1, 2, 3, 4, 5, 6);

/* testFunctionCall7 */
myfunction( 1, 2, 3, 4, 5, 6, true );

/* testFunctionCall8 */
dirname( dirname( __FILE__ ) ); // 1

/* testFunctionCall9 */
(dirname( dirname( __FILE__ ) )); // 1

/* testFunctionCall10 */
dirname( plugin_basename( __FILE__ ) ); // 1

/* testFunctionCall11 */
dirname( plugin_basename( __FILE__ ), 2 ); // 2

/* testFunctionCall12 */
unserialize(trim($value, "'")); // 1

/* testFunctionCall13 */
dirname(str_replace("../","/", $value)); // 1

/* testFunctionCall14 */
dirname(str_replace("../", "/", trim($value))); // 1

/* testFunctionCall15 */
dirname( plugin_basename( __FILE__ ), trim( 2 ) ); // 2

/* testFunctionCall16 */
mktime($stHour, 0, 0, $arrStDt[0], $arrStDt[1], $arrStDt[2]); // 6

/* testFunctionCall17 */
mktime(0, 0, 0, date('m'), date('d'), date('Y')); // 6

/* testFunctionCall18 */
mktime(0, 0, 0, date('m'), date('d') - 1, date('Y') + 1); // 6

/* testFunctionCall19 */
mktime(0, 0, 0, date('m') + 1, date('d'), date('Y')); // 6

/* testFunctionCall20 */
mktime(date('H'), 0, 0, date('m'), date('d'), date('Y')); // 6

/* testFunctionCall21 */
mktime(0, 0, date('s'), date('m'), date('d'), date('Y')); // 6

/* testFunctionCall22 */
mktime(some_call(5, 1), another(1), why(5, 1, 2), 4, 5, 6); // 6

/* testFunctionCall23 */
filter_input_array(
    INPUT_POST,
    $args,
    false
); // 3

/* testFunctionCall24 */
gettimeofday (
               true
             ); // 1

/* testFunctionCall25 */
json_encode( array(), );

/* testFunctionCall26 */
json_encode(['a' => 'b',]);

/* testFunctionCall27 */
json_encode(['a' => $a,]);

/* testFunctionCall28 */
json_encode(['a' => $a,] + (isset($b) ? ['b' => $b,] : []));

/* testFunctionCall29 */
json_encode(['a' => $a,] + (isset($b) ? ['b' => $b, 'c' => $c,] : []));

/* testFunctionCall30 */
json_encode(['a' => $a, 'b' => $b] + (isset($c) ? ['c' => $c, 'd' => $d] : []));

/* testFunctionCall31 */
json_encode(['a' => $a, 'b' => $b] + (isset($c) ? ['c' => $c, 'd' => $d,] : []));

/* testFunctionCall32 */
json_encode(['a' => $a, 'b' => $b] + (isset($c) ? ['c' => $c, 'd' => $d, $c => 'c'] : []));

/* testFunctionCall33 */
json_encode(['a' => $a,] + (isset($b) ? ['b' => $b,] : []) + ['c' => $c, 'd' => $d,]);

/* testFunctionCall34 */
json_encode(['a' => 'b', 'c' => 'd',]);

/* testFunctionCall35 */
json_encode(['a' => ['b',],]);

/* testFunctionCall36 */
json_encode(['a' => ['b' => 'c',],]);

/* testFunctionCall37 */
json_encode(['a' => ['b' => 'c',], 'd' => ['e' => 'f',],]);

/* testFunctionCall38 */
json_encode(['a' => $a, 'b' => $b,]);

/* testFunctionCall39 */
json_encode(['a' => $a,] + ['b' => $b,]);

/* testFunctionCall40 */
json_encode(['a' => $a] + ['b' => $b, 'c' => $c,]);

/* testFunctionCall41 */
json_encode(['a' => $a, 'b' => $b] + ['c' => $c, 'd' => $d]);

/* testFunctionCall42 */
json_encode(['a' => $a, 'b' => $b] + ['c' => $c, 'd' => $d,]);

/* testFunctionCall43 */
json_encode(['a' => $a, 'b' => $b] + ['c' => $c, 'd' => $d, $c => 'c']);

/* testFunctionCall44 */
json_encode(['a' => $a, 'b' => $b,] + ['c' => $c]);

/* testFunctionCall45 */
json_encode(['a' => $a, 'b' => $b,] + ['c' => $c,]);

/* testFunctionCall46 */
json_encode(['a' => $a, 'b' => $b, 'c' => $c]);

/* testFunctionCall47 */
json_encode(['a' => $a, 'b' => $b, 'c' => $c,] + ['c' => $c, 'd' => $d,]);

/* testLongArray1 */
$foo = array( 1, 2, 3, 4, 5, 6, true );

/* testLongArray2 */
$foo = array(str_replace("../", "/", trim($value))); // 1

/* testLongArray3 */
$foo = array($stHour, 0, 0, $arrStDt[0], $arrStDt[1], $arrStDt[2]); // 6

/* testLongArray4 */
$foo = array(0, 0, date('s'), date('m'), date('d'), date('Y')); // 6

/* testLongArray5 */
$foo = array(some_call(5, 1), another(1), why(5, 1, 2), 4, 5, 6); // 6

/* testLongArray6 */
$foo = array('a' => $a, 'b' => $b, 'c' => $c);

/* testLongArray7 */
$foo = array('a' => $a, 'b' => $b, 'c' => (isset($c) ? $c : null));

/* testLongArray8 */
$foo = array(0 => $a, 2 => $b, 6 => (isset($c) ? $c : null));

/* testShortArray1 */
$bar = [ 1, 2, 3, 4, 5, 6, true ];

/* testShortArray2 */
$bar = [str_replace("../", "/", trim($value))]; // 1

/* testShortArray3 */
$bar = [$stHour, 0, 0, $arrStDt[0], $arrStDt[1], $arrStDt[2]]; // 6

/* testShortArray4 */
$bar = [0, 0, date('s'), date('m'), date('d'), date('Y')]; // 6

/* testShortArray5 */
$bar = [some_call(5, 1), another(1), why(5, 1, 2), 4, 5, 6]; // 6

/* testShortArray6 */
$bar = ['a' => $a, 'b' => $b, 'c' => $c];

/* testShortArray7 */
$bar = ['a' => $a, 'b' => $b, 'c' => (isset($c) ? $c : null)];

/* testShortArray8 */
$bar = [0 => $a, 2 => $b, 6 => (isset($c) ? $c : null)];
