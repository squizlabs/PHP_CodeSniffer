<?php

/* testExplicitOctal */
$foo = 0o137041;

/* testExplicitOctalCapitalised */
$bar = 0O137041;

/* testExplicitOctalWithNumericSeparator */
$octal = 0o137_041;

/* testInvalid1 */
$foo = 0o_137;

/* testInvalid2 */
$foo = 0O_41;

/* testInvalid3 */
$foo = 0o91;

/* testInvalid4 */
$foo = 0O282;

/* testInvalid5 */
$foo = 0o28_2;

/* testInvalid6 */
$foo = 0o2_82;
