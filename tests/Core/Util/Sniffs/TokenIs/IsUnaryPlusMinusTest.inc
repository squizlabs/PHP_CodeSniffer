<?php

/* testNonUnaryPlus */
$a = 1 + 2;

/* testNonUnaryMinus */
$a = 1-2;

/* testNonUnaryPlusArrays */
$a = [1] + [2];

/* testUnaryPlusIntAssignment */
$a = +1;

/* testUnaryMinusVariableAssignment */
$a += -$b;

/* testUnaryPlusFloatAssignment */
$a **= + 1.1;

/* testUnaryMinusBoolAssignment */
$a /= -true;

/* testUnaryPlusStringAssignmentWithComment */
$a &= + /* comment */ '1';

/* testUnaryMinusStringAssignment */
$a .= - <<<'EOD'
123
EOD;

/* testUnaryPlusNullAssignment */
$a = +null;

/* testUnaryMinusVariableVariableAssignment */
$a .= -${$var};

/* testUnaryPlusIntComparison */
$a = ($b === + 1);

/* testUnaryPlusIntComparisonYoda */
$a = (+1 == $b);

/* testUnaryMinusFloatComparison */
$a = ($b !== - 1.1);

/* testUnaryMinusStringComparisonYoda */
$a = (-'1' != $b);

/* testUnaryPlusVariableLogical */
$a = ($a && - $b);

/* testUnaryMinusVariableLogical */
$a = ($a || -$b);

/* testUnaryMinusInlineIf */
$a = $a ? -1 :
    /* testUnaryPlusInlineThen */
    + 10;

/* testUnaryPlusIntReturn */
return +1;

/* testUnaryMinusFloatReturn */
return -1.1;

/* testUnaryPlusArrayAccess */
$a = $array[ + 2 ];

/* testUnaryMinusStringArrayAccess */
if ($line{-1} === ':') {}

$array = array(
    /* testUnaryPlusLongArrayAssignment */
    +1,
    /* testUnaryMinusLongArrayAssignmentKey */
    -1 =>
        /* testUnaryPlusLongArrayAssignmentValue */
        +20,
);

$array = [
    /* testUnaryPlusShortArrayAssignment */
    +1 =>
        /* testNonUnaryMinusShortArrayAssignment */
        5-20,
];

/* testUnaryMinusCast */
$a = (bool) -2;

functionCall(
    /* testUnaryPlusFunctionCallParam */
    +2,
    /* testUnaryMinusFunctionCallParam */
    - 123.456,
);

switch ($a) {
    /* testUnaryPlusCase */
    case +20:
        // Something.
        break;

    /* testUnaryMinusCase */
    case -1.23:
        // Something.
        break;
}

// Testing `$a = -+-+10`;
$a =
    /* testSequenceNonUnary1 */
    -
    /* testSequenceNonUnary2 */
    +
    /* testSequenceNonUnary3 */
    -
    /* testSequenceUnaryEnd */
    + /*comment*/ 10;

// Intentional parse error. This has to be the last test in the file.
/* testParseError */
$a = -
