<?php

/*
 * Numbers with PHP 7.4 underscore separators.
 */

/* testSimpleLNumber */
$foo = 1_000_000_000;

/* testSimpleDNumber */
$foo = 107_925_284.88;

/* testFloat */
$foo = 6.674_083e-11;

/* testFloat2 */
$foo = 6.674_083e+11;

/* testFloat3 */
$foo = 1_2.3_4e1_23;

/* testHex */
$foo = 0xCAFE_F00D;

/* testHexMultiple */
$foo = 0x42_72_6F_77_6E;

/* testHexInt */
$foo = 0x42_72_6F;

/* testBinary */
$foo = 0b0101_1111;

/* testOctal */
$foo = 0137_041;

/* testIntMoreThanMax */
$foo = 10_223_372_036_854_775_807;

/*
 * Invalid use of numeric separators. These should not be touched by the backfill.
 */

/* testInvalid1 */
$a = 100_;   // trailing

/* testInvalid2 */
$a = 1__1;   // next to underscore

/* testInvalid3 */
$a = 1_.0;   // next to decimal point

/* testInvalid4 */
$a = 1._0;   // next to decimal point

/* testInvalid5 */
$a = 0x_123; // next to x

/* testInvalid6 */
$a = 0b_101; // next to b

/* testInvalid7 */
$a = 1_e2;   // next to e

/* testInvalid8 */
$a = 1e_2;   // next to e

/* testInvalid9 */
$testValue = 107_925_284 .88;

/* testInvalid10 */
$testValue = 107_925_284/*comment*/.88;

/*
 * Ensure that legitimate calculations are not touched by the backfill.
 */

/* testCalc1 */
$a = 667_083 - 11; // Calculation.

/* test Calc2 */
$a = 6.674_08e3 + 11; // Calculation.
