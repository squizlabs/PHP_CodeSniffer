<?php

/* testNoParentheses */
$anonClass = new class {
    function __construct() {}
};

/* testNoParenthesesAndEmptyTokens */
$anonClass = new class // phpcs:ignore Standard.Cat
{
    function __construct() {}
};

/* testWithParentheses */
$anonClass = new class() {};

/* testWithParenthesesAndEmptyTokens */
$anonClass = new class /*comment */
() {};
