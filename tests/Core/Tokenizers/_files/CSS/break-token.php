<?php
/**
 * Helper file wit some example data for css tokenizer tests.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Alexander Zimmermann <alex@azimmermann.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2013 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

$tokens = array(
    0  =>
    array(
        'code'       => T_OPEN_TAG,
        'type'       => 'T_OPEN_TAG',
        'content'    => '<?php ',
        'line'       => 1,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    1  =>
    array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => T_STRING_CONCAT,
        'content'    => '.',
        'line'       => 1,
        'column'     => 7,
        'level'      => 0,
        'conditions' => array(),
    ),
    2  =>
    array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'a',
        'line'       => 1,
        'column'     => 8,
        'level'      => 0,
        'conditions' => array(),
    ),
    3  =>
    array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 1,
        'bracket_opener' => 3,
        'bracket_closer' => 8,
        'column'         => 9,
        'level'          => 0,
        'conditions'     => array(),
    ),
    4  =>
    array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 1,
        'column'     => 10,
        'level'      => 0,
        'conditions' => array(),
    ),
    5  =>
    array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 1,
        'column'     => 17,
        'level'      => 0,
        'conditions' => array(),
    ),
    6  =>
    array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 1,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array(),
    ),
    7  =>
    array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 1,
        'column'     => 22,
        'level'      => 0,
        'conditions' => array(),
    ),
    8  =>
    array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 1,
        'bracket_opener' => 3,
        'bracket_closer' => 8,
        'column'         => 23,
        'level'          => 0,
        'conditions'     => array(),
    ),
    9  =>
    array(
        'type'       => 'T_WHITESPACE',
        'code'       => T_WHITESPACE,
        'content'    => "\n",
        'line'       => 1,
        'column'     => 24,
        'level'      => 0,
        'conditions' => array(),
    ),
    10 =>
    array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 2,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array(),
    ),
    11 =>
    array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'x',
        'line'       => 2,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array(),
    ),
    12 =>
    array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 2,
        'bracket_opener' => 12,
        'bracket_closer' => 20,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array(),
    ),
    13 =>
    array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'page',
        'line'       => 2,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array(),
    ),
    14 =>
    array(
        'type'       => 'T_MINUS',
        'code'       => 1018,
        'content'    => '-',
        'line'       => 2,
        'column'     => 8,
        'level'      => 0,
        'conditions' => array(),
    ),
    15 =>
    array(
        'code'       => 331,
        'type'       => 'T_BREAK',
        'content'    => 'break',
        'line'       => 2,
        'column'     => 9,
        'level'      => 0,
        'conditions' => array(),
    ),
    16 =>
    array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => '-style',
        'line'       => 2,
        'column'     => 14,
        'level'      => 0,
        'conditions' => array(),
    ),
    17 =>
    array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 2,
        'column'     => 20,
        'level'      => 0,
        'conditions' => array(),
    ),
    18 =>
    array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 2,
        'column'     => 21,
        'level'      => 0,
        'conditions' => array(),
    ),
    19 =>
    array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 2,
        'column'     => 25,
        'level'      => 0,
        'conditions' => array(),
    ),
    20 =>
    array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 2,
        'bracket_opener' => 12,
        'bracket_closer' => 20,
        'column'         => 26,
        'level'          => 0,
        'conditions'     => array(),
    ),
    21 =>
    array(
        'type'       => 'T_WHITESPACE',
        'code'       => T_WHITESPACE,
        'content'    => "\n",
        'line'       => 2,
        'column'     => 27,
        'level'      => 0,
        'conditions' => array(),
    ),
    22 =>
    array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'input',
        'line'       => 3,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array(),
    ),
    23 =>
    array(
        'type'           => 'T_OPEN_SQUARE_BRACKET',
        'code'           => 1002,
        'content'        => '[',
        'line'           => 3,
        'bracket_opener' => 23,
        'bracket_closer' => 27,
        'column'         => 6,
        'level'          => 0,
        'conditions'     => array(),
    ),
    24 =>
    array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'type',
        'line'       => 3,
        'column'     => 7,
        'level'      => 0,
        'conditions' => array(),
    ),
    25 =>
    array(
        'type'       => 'T_EQUAL',
        'code'       => 1014,
        'content'    => '=',
        'line'       => 3,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array(),
    ),
    26 =>
    array(
        'code'       => 315,
        'type'       => 'T_CONSTANT_ENCAPSED_STRING',
        'content'    => '"hidden"',
        'line'       => 3,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array(),
    ),
    27 =>
    array(
        'type'           => 'T_CLOSE_SQUARE_BRACKET',
        'code'           => 1003,
        'content'        => ']',
        'line'           => 3,
        'bracket_opener' => 23,
        'bracket_closer' => 27,
        'column'         => 20,
        'level'          => 0,
        'conditions'     => array(),
    ),
    28 =>
    array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 3,
        'bracket_opener' => 28,
        'bracket_closer' => 33,
        'column'         => 21,
        'level'          => 0,
        'conditions'     => array(),
    ),
    29 =>
    array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 3,
        'column'     => 22,
        'level'      => 0,
        'conditions' => array(),
    ),
    30 =>
    array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 3,
        'column'     => 29,
        'level'      => 0,
        'conditions' => array(),
    ),
    31 =>
    array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 3,
        'column'     => 30,
        'level'      => 0,
        'conditions' => array(),
    ),
    32 =>
    array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 3,
        'column'     => 34,
        'level'      => 0,
        'conditions' => array(),
    ),
    33 =>
    array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 3,
        'bracket_opener' => 28,
        'bracket_closer' => 33,
        'column'         => 35,
        'level'          => 0,
        'conditions'     => array(),
    ),
    34 =>
    array(
        'type'       => 'T_WHITESPACE',
        'code'       => T_WHITESPACE,
        'content'    => "\n",
        'line'       => 3,
        'column'     => 36,
        'level'      => 0,
        'conditions' => array(),
    ),
    35 =>
    array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 4,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array(),
    ),
    36 =>
    array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'y',
        'line'       => 4,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array(),
    ),
    37 =>
    array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 4,
        'bracket_opener' => 37,
        'bracket_closer' => 42,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array(),
    ),
    38 =>
    array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 4,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array(),
    ),
    39 =>
    array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 4,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array(),
    ),
    40 =>
    array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 4,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array(),
    ),
    41 =>
    array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 4,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array(),
    ),
    42 =>
    array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 4,
        'bracket_opener' => 37,
        'bracket_closer' => 42,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array(),
    ),
    43 =>
    array(
        'type'       => 'T_WHITESPACE',
        'code'       => T_WHITESPACE,
        'content'    => "\n",
        'line'       => 4,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array(),
    ),
    44 =>
    array(
        'type'       => 'T_WHITESPACE',
        'code'       => T_WHITESPACE,
        'content'    => "\n",
        'line'       => 5,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array(),
    ),
    45 =>
    array(
        'code'       => T_CLOSE_TAG,
        'type'       => 'T_CLOSE_TAG',
        'content'    => '?>',
        'line'       => 6,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array(),
    )
);
