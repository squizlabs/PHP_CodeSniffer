<?php

/* testLongList */
list( $a ) = $b;

/* testArrayAssignment */
$b[] = $c;

/* testNonNestedShortArray */
$array = [$a];

/* testNoAssignment */
if( [$a, /* testNestedNoAssignment */ [$b]] === $array ) {};

/* testShortArrayInForeach */
foreach ([1, 2, 3] as $id) {}

/* testShortList */
[$b] = $c;

/* testShortListDetectOnCloseBracket */
[$a, $b] = $c;

/* testShortListWithNesting */
[$a, /* testNestedShortList */ [$b]] = array(new stdclass, array(new stdclass));

/* testShortListInForeach */
foreach ($data as [$id, $name, $info]) {}

/* testShortListInForeachWithKey */
foreach ($data as $key => [$id, $name, $info]) {}

foreach ($array as [$a, /* testShortListInForeachNested */ [$b, $c]]) {}

/* testMultiAssignShortlist */
$foo = [$baz, $bat] = [$a, $b];

/* testShortListWithKeys */
["id" => $id1, "name" => $name1] = $data[0];

/* testShortListInForeachWithKeysDetectOnCloseBracket */
foreach ($data as ["id" => $id, "name" => $name]) {}

// Invalid as empty lists are not allowed, but it is short list syntax.
[$x, /* testNestedShortListEmpty */ [], $y] = $a;

[$x, [ $y, /* testDeeplyNestedShortList */ [$z]], $q] = $a;

/* testShortListWithNestingAndKeys */
[
    /* testNestedShortListWithKeys_1 */
    ["x" => $x1, "y" => $y1],
    /* testNestedShortListWithKeys_2 */
    ["x" => $x2, "y" => $y2],
    /* testNestedShortListWithKeys_3 */
    ["x" => $x3, "y" => $y3],
] = $points;

/* testShortListWithoutVars */
// Invalid list as it doesn't contain variables, but it is short list syntax.
[42] = [1];

/* testShortListNestedLongList */
// Invalid list as mixing short list syntax with list() is not allowed, but it is short list syntax.
[list($a, $b), list($c, $d)] = [[1, 2], [3, 4]];

/* testNestedAnonClassWithTraitUseAs */
// Parse error, but not short list syntax.
array_map(function($a) {
	return new class() {
		use MyTrait {
			MyTrait::functionName as [];
		}
	};
}, $array);

/* testParseError */
// Parse error, but not short list syntax.
use Something as [$a];

/* testLiveCoding */
// This has to be the last test in the file.
[$a, [$b]
