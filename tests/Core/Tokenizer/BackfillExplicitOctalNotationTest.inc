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
