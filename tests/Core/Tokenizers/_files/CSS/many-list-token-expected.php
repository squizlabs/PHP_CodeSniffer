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

$expected = array(
    0   => array(
        'code'       => T_OPEN_TAG,
        'type'       => 'T_OPEN_TAG',
        'content'    => '<?php ',
        'line'       => 1,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    1   => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 1,
        'column'     => 7,
        'level'      => 0,
        'conditions' => array()
    ),
    2   => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'a',
        'line'       => 1,
        'column'     => 8,
        'level'      => 0,
        'conditions' => array()
    ),
    3   => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 1,
        'bracket_opener' => 3,
        'bracket_closer' => 8,
        'column'         => 9,
        'level'          => 0,
        'conditions'     => array()
    ),
    4   => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 1,
        'column'     => 10,
        'level'      => 0,
        'conditions' => array()
    ),
    5   => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 1,
        'column'     => 17,
        'level'      => 0,
        'conditions' => array()
    ),
    6   => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 1,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    7   => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 1,
        'column'     => 22,
        'level'      => 0,
        'conditions' => array()
    ),
    8   => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 1,
        'bracket_opener' => 3,
        'bracket_closer' => 8,
        'column'         => 23,
        'level'          => 0,
        'conditions'     => array()
    ),
    9   => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 1,
        'column'     => 24,
        'level'      => 0,
        'conditions' => array()
    ),
    10  => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 2,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    11  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'x',
        'line'       => 2,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    12  => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 2,
        'bracket_opener' => 12,
        'bracket_closer' => 17,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    13  => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'list-style',
        'line'       => 2,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    14  => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 2,
        'column'     => 14,
        'level'      => 0,
        'conditions' => array()
    ),
    15  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 2,
        'column'     => 15,
        'level'      => 0,
        'conditions' => array()
    ),
    16  => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 2,
        'column'     => 19,
        'level'      => 0,
        'conditions' => array()
    ),
    17  => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 2,
        'bracket_opener' => 12,
        'bracket_closer' => 17,
        'column'         => 20,
        'level'          => 0,
        'conditions'     => array()
    ),
    18  => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 2,
        'column'     => 21,
        'level'      => 0,
        'conditions' => array()
    ),
    19  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'input',
        'line'       => 3,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    20  => array(
        'type'           => 'T_OPEN_SQUARE_BRACKET',
        'code'           => 1002,
        'content'        => '[',
        'line'           => 3,
        'bracket_opener' => 20,
        'bracket_closer' => 24,
        'column'         => 6,
        'level'          => 0,
        'conditions'     => array()
    ),
    21  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'type',
        'line'       => 3,
        'column'     => 7,
        'level'      => 0,
        'conditions' => array()
    ),
    22  => array(
        'type'       => 'T_EQUAL',
        'code'       => 1014,
        'content'    => '=',
        'line'       => 3,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    23  => array(
        'code'       => 315,
        'type'       => 'T_CONSTANT_ENCAPSED_STRING',
        'content'    => '"hidden"',
        'line'       => 3,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    24  => array(
        'type'           => 'T_CLOSE_SQUARE_BRACKET',
        'code'           => 1003,
        'content'        => ']',
        'line'           => 3,
        'bracket_opener' => 20,
        'bracket_closer' => 24,
        'column'         => 20,
        'level'          => 0,
        'conditions'     => array()
    ),
    25  => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 3,
        'bracket_opener' => 25,
        'bracket_closer' => 30,
        'column'         => 21,
        'level'          => 0,
        'conditions'     => array()
    ),
    26  => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 3,
        'column'     => 22,
        'level'      => 0,
        'conditions' => array()
    ),
    27  => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 3,
        'column'     => 29,
        'level'      => 0,
        'conditions' => array()
    ),
    28  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 3,
        'column'     => 30,
        'level'      => 0,
        'conditions' => array()
    ),
    29  => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 3,
        'column'     => 34,
        'level'      => 0,
        'conditions' => array()
    ),
    30  => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 3,
        'bracket_opener' => 25,
        'bracket_closer' => 30,
        'column'         => 35,
        'level'          => 0,
        'conditions'     => array()
    ),
    31  => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 3,
        'column'     => 36,
        'level'      => 0,
        'conditions' => array()
    ),
    32  => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 4,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    33  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'y',
        'line'       => 4,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    34  => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 4,
        'bracket_opener' => 34,
        'bracket_closer' => 39,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    35  => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 4,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    36  => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 4,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    37  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 4,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    38  => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 4,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array()
    ),
    39  => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 4,
        'bracket_opener' => 34,
        'bracket_closer' => 39,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array()
    ),
    40  => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 4,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    41  => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 5,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    42  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'b',
        'line'       => 5,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    43  => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 5,
        'bracket_opener' => 43,
        'bracket_closer' => 48,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    44  => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 5,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    45  => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 5,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    46  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 5,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    47  => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 5,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array()
    ),
    48  => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 5,
        'bracket_opener' => 43,
        'bracket_closer' => 48,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array()
    ),
    49  => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 5,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    50  => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 6,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    51  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'c',
        'line'       => 6,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    52  => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 6,
        'bracket_opener' => 52,
        'bracket_closer' => 57,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    53  => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'list-style',
        'line'       => 6,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    54  => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 6,
        'column'     => 14,
        'level'      => 0,
        'conditions' => array()
    ),
    55  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 6,
        'column'     => 15,
        'level'      => 0,
        'conditions' => array()
    ),
    56  => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 6,
        'column'     => 19,
        'level'      => 0,
        'conditions' => array()
    ),
    57  => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 6,
        'bracket_opener' => 53,
        'bracket_closer' => 57,
        'column'         => 20,
        'level'          => 0,
        'conditions'     => array()
    ),
    58  => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 6,
        'column'     => 21,
        'level'      => 0,
        'conditions' => array()
    ),
    59  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'select',
        'line'       => 7,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    60  => array(
        'type'           => 'T_OPEN_SQUARE_BRACKET',
        'code'           => 1002,
        'content'        => '[',
        'line'           => 7,
        'bracket_opener' => 60,
        'bracket_closer' => 64,
        'column'         => 7,
        'level'          => 0,
        'conditions'     => array()
    ),
    61  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'type',
        'line'       => 7,
        'column'     => 8,
        'level'      => 0,
        'conditions' => array()
    ),
    62  => array(
        'type'       => 'T_EQUAL',
        'code'       => 1014,
        'content'    => '=',
        'line'       => 7,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    63  => array(
        'code'       => 315,
        'type'       => 'T_CONSTANT_ENCAPSED_STRING',
        'content'    => '"hidden"',
        'line'       => 7,
        'column'     => 13,
        'level'      => 0,
        'conditions' => array()
    ),
    64  => array(
        'type'           => 'T_CLOSE_SQUARE_BRACKET',
        'code'           => 1003,
        'content'        => ']',
        'line'           => 7,
        'bracket_opener' => 60,
        'bracket_closer' => 64,
        'column'         => 21,
        'level'          => 0,
        'conditions'     => array()
    ),
    65  => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 7,
        'bracket_opener' => 65,
        'bracket_closer' => 70,
        'column'         => 22,
        'level'          => 0,
        'conditions'     => array()
    ),
    66  => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 7,
        'column'     => 23,
        'level'      => 0,
        'conditions' => array()
    ),
    67  => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 7,
        'column'     => 30,
        'level'      => 0,
        'conditions' => array()
    ),
    68  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 7,
        'column'     => 31,
        'level'      => 0,
        'conditions' => array()
    ),
    69  => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 7,
        'column'     => 35,
        'level'      => 0,
        'conditions' => array()
    ),
    70  => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 7,
        'bracket_opener' => 65,
        'bracket_closer' => 70,
        'column'         => 36,
        'level'          => 0,
        'conditions'     => array()
    ),
    71  => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 7,
        'column'     => 37,
        'level'      => 0,
        'conditions' => array()
    ),
    72  => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 8,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    73  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'd',
        'line'       => 8,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    74  => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 8,
        'bracket_opener' => 74,
        'bracket_closer' => 79,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    75  => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 8,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    76  => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 8,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    77  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 8,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    78  => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 8,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array()
    ),
    79  => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 8,
        'bracket_opener' => 74,
        'bracket_closer' => 79,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array()
    ),
    80  => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 8,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    81  => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 9,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    82  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'f',
        'line'       => 9,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    83  => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 9,
        'bracket_opener' => 83,
        'bracket_closer' => 88,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    84  => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 9,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    85  => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 9,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    86  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 9,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    87  => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 9,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array()
    ),
    88  => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 9,
        'bracket_opener' => 83,
        'bracket_closer' => 88,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array()
    ),
    89  => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 9,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    90  => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 10,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    91  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'g',
        'line'       => 10,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    92  => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 10,
        'bracket_opener' => 92,
        'bracket_closer' => 97,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    93  => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'list-style',
        'line'       => 10,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    94  => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 10,
        'column'     => 14,
        'level'      => 0,
        'conditions' => array()
    ),
    95  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 10,
        'column'     => 15,
        'level'      => 0,
        'conditions' => array()
    ),
    96  => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 10,
        'column'     => 19,
        'level'      => 0,
        'conditions' => array()
    ),
    97  => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 10,
        'bracket_opener' => 94,
        'bracket_closer' => 97,
        'column'         => 20,
        'level'          => 0,
        'conditions'     => array()
    ),
    98  => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 10,
        'column'     => 21,
        'level'      => 0,
        'conditions' => array()
    ),
    99  => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'textarea',
        'line'       => 11,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    100 => array(
        'type'           => 'T_OPEN_SQUARE_BRACKET',
        'code'           => 1002,
        'content'        => '[',
        'line'           => 11,
        'bracket_opener' => 100,
        'bracket_closer' => 104,
        'column'         => 9,
        'level'          => 0,
        'conditions'     => array()
    ),
    101 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'type',
        'line'       => 11,
        'column'     => 10,
        'level'      => 0,
        'conditions' => array()
    ),
    102 => array(
        'type'       => 'T_EQUAL',
        'code'       => 1014,
        'content'    => '=',
        'line'       => 11,
        'column'     => 14,
        'level'      => 0,
        'conditions' => array()
    ),
    103 => array(
        'code'       => 315,
        'type'       => 'T_CONSTANT_ENCAPSED_STRING',
        'content'    => '"hidden"',
        'line'       => 11,
        'column'     => 15,
        'level'      => 0,
        'conditions' => array()
    ),
    104 => array(
        'type'           => 'T_CLOSE_SQUARE_BRACKET',
        'code'           => 1003,
        'content'        => ']',
        'line'           => 11,
        'bracket_opener' => 100,
        'bracket_closer' => 104,
        'column'         => 23,
        'level'          => 0,
        'conditions'     => array()
    ),
    105 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 11,
        'bracket_opener' => 105,
        'bracket_closer' => 110,
        'column'         => 24,
        'level'          => 0,
        'conditions'     => array()
    ),
    106 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 11,
        'column'     => 25,
        'level'      => 0,
        'conditions' => array()
    ),
    107 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 11,
        'column'     => 32,
        'level'      => 0,
        'conditions' => array()
    ),
    108 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 11,
        'column'     => 33,
        'level'      => 0,
        'conditions' => array()
    ),
    109 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 11,
        'column'     => 37,
        'level'      => 0,
        'conditions' => array()
    ),
    110 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 11,
        'bracket_opener' => 105,
        'bracket_closer' => 110,
        'column'         => 38,
        'level'          => 0,
        'conditions'     => array()
    ),
    111 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 11,
        'column'     => 39,
        'level'      => 0,
        'conditions' => array()
    ),
    112 => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 12,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    113 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'h',
        'line'       => 12,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    114 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 12,
        'bracket_opener' => 114,
        'bracket_closer' => 119,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    115 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 12,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    116 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 12,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    117 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 12,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    118 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 12,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array()
    ),
    119 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 12,
        'bracket_opener' => 114,
        'bracket_closer' => 119,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array()
    ),
    120 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 12,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    121 => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 13,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    122 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'i',
        'line'       => 13,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    123 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 13,
        'bracket_opener' => 123,
        'bracket_closer' => 128,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    124 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 13,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    125 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 13,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    126 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 13,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    127 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 13,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array()
    ),
    128 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 13,
        'bracket_opener' => 123,
        'bracket_closer' => 128,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array()
    ),
    129 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 13,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    130 => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 14,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    131 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'j',
        'line'       => 14,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    132 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 14,
        'bracket_opener' => 132,
        'bracket_closer' => 137,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    133 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'list-style',
        'line'       => 14,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    134 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 14,
        'column'     => 14,
        'level'      => 0,
        'conditions' => array()
    ),
    135 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 14,
        'column'     => 15,
        'level'      => 0,
        'conditions' => array()
    ),
    136 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 14,
        'column'     => 19,
        'level'      => 0,
        'conditions' => array()
    ),
    137 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 14,
        'bracket_opener' => 135,
        'bracket_closer' => 137,
        'column'         => 20,
        'level'          => 0,
        'conditions'     => array()
    ),
    138 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 14,
        'column'     => 21,
        'level'      => 0,
        'conditions' => array()
    ),
    139 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'input',
        'line'       => 15,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    140 => array(
        'type'           => 'T_OPEN_SQUARE_BRACKET',
        'code'           => 1002,
        'content'        => '[',
        'line'           => 15,
        'bracket_opener' => 140,
        'bracket_closer' => 144,
        'column'         => 6,
        'level'          => 0,
        'conditions'     => array()
    ),
    141 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'type',
        'line'       => 15,
        'column'     => 7,
        'level'      => 0,
        'conditions' => array()
    ),
    142 => array(
        'type'       => 'T_EQUAL',
        'code'       => 1014,
        'content'    => '=',
        'line'       => 15,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    143 => array(
        'code'       => 315,
        'type'       => 'T_CONSTANT_ENCAPSED_STRING',
        'content'    => '"hidden"',
        'line'       => 15,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    144 => array(
        'type'           => 'T_CLOSE_SQUARE_BRACKET',
        'code'           => 1003,
        'content'        => ']',
        'line'           => 15,
        'bracket_opener' => 140,
        'bracket_closer' => 144,
        'column'         => 20,
        'level'          => 0,
        'conditions'     => array()
    ),
    145 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 15,
        'bracket_opener' => 145,
        'bracket_closer' => 150,
        'column'         => 21,
        'level'          => 0,
        'conditions'     => array()
    ),
    146 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 15,
        'column'     => 22,
        'level'      => 0,
        'conditions' => array()
    ),
    147 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 15,
        'column'     => 29,
        'level'      => 0,
        'conditions' => array()
    ),
    148 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 15,
        'column'     => 30,
        'level'      => 0,
        'conditions' => array()
    ),
    149 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 15,
        'column'     => 34,
        'level'      => 0,
        'conditions' => array()
    ),
    150 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 15,
        'bracket_opener' => 145,
        'bracket_closer' => 150,
        'column'         => 35,
        'level'          => 0,
        'conditions'     => array()
    ),
    151 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 15,
        'column'     => 36,
        'level'      => 0,
        'conditions' => array()
    ),
    152 => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 16,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    153 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'k',
        'line'       => 16,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    154 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 16,
        'bracket_opener' => 154,
        'bracket_closer' => 159,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    155 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 16,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    156 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 16,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    157 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 16,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    158 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 16,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array()
    ),
    159 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 16,
        'bracket_opener' => 154,
        'bracket_closer' => 159,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array()
    ),
    160 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 16,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    161 => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 17,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    162 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'l',
        'line'       => 17,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    163 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 17,
        'bracket_opener' => 163,
        'bracket_closer' => 168,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    164 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 17,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    165 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 17,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    166 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 17,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    167 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 17,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array()
    ),
    168 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 17,
        'bracket_opener' => 163,
        'bracket_closer' => 168,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array()
    ),
    169 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 17,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    170 => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 18,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    171 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'm',
        'line'       => 18,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    172 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 18,
        'bracket_opener' => 172,
        'bracket_closer' => 177,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    173 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'list-style',
        'line'       => 18,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    174 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 18,
        'column'     => 14,
        'level'      => 0,
        'conditions' => array()
    ),
    175 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 18,
        'column'     => 15,
        'level'      => 0,
        'conditions' => array()
    ),
    176 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 18,
        'column'     => 19,
        'level'      => 0,
        'conditions' => array()
    ),
    177 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 18,
        'bracket_opener' => 176,
        'bracket_closer' => 177,
        'column'         => 20,
        'level'          => 0,
        'conditions'     => array()
    ),
    178 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 18,
        'column'     => 21,
        'level'      => 0,
        'conditions' => array()
    ),
    179 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'input',
        'line'       => 19,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    180 => array(
        'type'           => 'T_OPEN_SQUARE_BRACKET',
        'code'           => 1002,
        'content'        => '[',
        'line'           => 19,
        'bracket_opener' => 180,
        'bracket_closer' => 184,
        'column'         => 6,
        'level'          => 0,
        'conditions'     => array()
    ),
    181 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'type',
        'line'       => 19,
        'column'     => 7,
        'level'      => 0,
        'conditions' => array()
    ),
    182 => array(
        'type'       => 'T_EQUAL',
        'code'       => 1014,
        'content'    => '=',
        'line'       => 19,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    183 => array(
        'code'       => 315,
        'type'       => 'T_CONSTANT_ENCAPSED_STRING',
        'content'    => '"hidden"',
        'line'       => 19,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    184 => array(
        'type'           => 'T_CLOSE_SQUARE_BRACKET',
        'code'           => 1003,
        'content'        => ']',
        'line'           => 19,
        'bracket_opener' => 180,
        'bracket_closer' => 184,
        'column'         => 20,
        'level'          => 0,
        'conditions'     => array()
    ),
    185 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 19,
        'bracket_opener' => 185,
        'bracket_closer' => 190,
        'column'         => 21,
        'level'          => 0,
        'conditions'     => array()
    ),
    186 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 19,
        'column'     => 22,
        'level'      => 0,
        'conditions' => array()
    ),
    187 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 19,
        'column'     => 29,
        'level'      => 0,
        'conditions' => array()
    ),
    188 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 19,
        'column'     => 30,
        'level'      => 0,
        'conditions' => array()
    ),
    189 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 19,
        'column'     => 34,
        'level'      => 0,
        'conditions' => array()
    ),
    190 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 19,
        'bracket_opener' => 185,
        'bracket_closer' => 190,
        'column'         => 35,
        'level'          => 0,
        'conditions'     => array()
    ),
    191 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 19,
        'column'     => 36,
        'level'      => 0,
        'conditions' => array()
    ),
    192 => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 20,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    193 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'n',
        'line'       => 20,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    194 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 20,
        'bracket_opener' => 194,
        'bracket_closer' => 199,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    195 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 20,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    196 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 20,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    197 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 20,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    198 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 20,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array()
    ),
    199 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 20,
        'bracket_opener' => 194,
        'bracket_closer' => 199,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array()
    ),
    200 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 20,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    201 => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 21,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    202 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'o',
        'line'       => 21,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    203 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 21,
        'bracket_opener' => 203,
        'bracket_closer' => 208,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    204 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 21,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    205 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 21,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    206 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 21,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    207 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 21,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array()
    ),
    208 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 21,
        'bracket_opener' => 203,
        'bracket_closer' => 208,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array()
    ),
    209 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 21,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    210 => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 22,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    211 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'p',
        'line'       => 22,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    212 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 22,
        'bracket_opener' => 212,
        'bracket_closer' => 217,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    213 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'list-style',
        'line'       => 22,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    214 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 22,
        'column'     => 14,
        'level'      => 0,
        'conditions' => array()
    ),
    215 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 22,
        'column'     => 15,
        'level'      => 0,
        'conditions' => array()
    ),
    216 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 22,
        'column'     => 19,
        'level'      => 0,
        'conditions' => array()
    ),
    217 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 22,
        'bracket_opener' => 217,
        'bracket_closer' => 217,
        'column'         => 20,
        'level'          => 0,
        'conditions'     => array()
    ),
    218 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 22,
        'column'     => 21,
        'level'      => 0,
        'conditions' => array()
    ),
    219 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'input',
        'line'       => 23,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    220 => array(
        'type'           => 'T_OPEN_SQUARE_BRACKET',
        'code'           => 1002,
        'content'        => '[',
        'line'           => 23,
        'bracket_opener' => 220,
        'bracket_closer' => 224,
        'column'         => 6,
        'level'          => 0,
        'conditions'     => array()
    ),
    221 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'type',
        'line'       => 23,
        'column'     => 7,
        'level'      => 0,
        'conditions' => array()
    ),
    222 => array(
        'type'       => 'T_EQUAL',
        'code'       => 1014,
        'content'    => '=',
        'line'       => 23,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    223 => array(
        'code'       => 315,
        'type'       => 'T_CONSTANT_ENCAPSED_STRING',
        'content'    => '"hidden"',
        'line'       => 23,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    224 => array(
        'type'           => 'T_CLOSE_SQUARE_BRACKET',
        'code'           => 1003,
        'content'        => ']',
        'line'           => 23,
        'bracket_opener' => 220,
        'bracket_closer' => 224,
        'column'         => 20,
        'level'          => 0,
        'conditions'     => array()
    ),
    225 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 23,
        'bracket_opener' => 225,
        'bracket_closer' => 230,
        'column'         => 21,
        'level'          => 0,
        'conditions'     => array()
    ),
    226 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 23,
        'column'     => 22,
        'level'      => 0,
        'conditions' => array()
    ),
    227 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 23,
        'column'     => 29,
        'level'      => 0,
        'conditions' => array()
    ),
    228 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 23,
        'column'     => 30,
        'level'      => 0,
        'conditions' => array()
    ),
    229 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 23,
        'column'     => 34,
        'level'      => 0,
        'conditions' => array()
    ),
    230 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 23,
        'bracket_opener' => 225,
        'bracket_closer' => 230,
        'column'         => 35,
        'level'          => 0,
        'conditions'     => array()
    ),
    231 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 23,
        'column'     => 36,
        'level'      => 0,
        'conditions' => array()
    ),
    232 => array(
        'type'       => 'T_STRING_CONCAT',
        'code'       => 1007,
        'content'    => '.',
        'line'       => 24,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    233 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'q',
        'line'       => 24,
        'column'     => 2,
        'level'      => 0,
        'conditions' => array()
    ),
    234 => array(
        'type'           => 'T_OPEN_CURLY_BRACKET',
        'code'           => 1000,
        'content'        => '{',
        'line'           => 24,
        'bracket_opener' => 234,
        'bracket_closer' => 239,
        'column'         => 3,
        'level'          => 0,
        'conditions'     => array()
    ),
    235 => array(
        'type'       => 'T_STYLE',
        'code'       => 1041,
        'content'    => 'display',
        'line'       => 24,
        'column'     => 4,
        'level'      => 0,
        'conditions' => array()
    ),
    236 => array(
        'type'       => 'T_COLON',
        'code'       => 1006,
        'content'    => ':',
        'line'       => 24,
        'column'     => 11,
        'level'      => 0,
        'conditions' => array()
    ),
    237 => array(
        'type'       => 'T_STRING',
        'code'       => 307,
        'content'    => 'none',
        'line'       => 24,
        'column'     => 12,
        'level'      => 0,
        'conditions' => array()
    ),
    238 => array(
        'type'       => 'T_SEMICOLON',
        'code'       => 1013,
        'content'    => ';',
        'line'       => 24,
        'column'     => 16,
        'level'      => 0,
        'conditions' => array()
    ),
    239 => array(
        'type'           => 'T_CLOSE_CURLY_BRACKET',
        'code'           => 1001,
        'content'        => '}',
        'line'           => 24,
        'bracket_opener' => 234,
        'bracket_closer' => 239,
        'column'         => 17,
        'level'          => 0,
        'conditions'     => array()
    ),
    240 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 24,
        'column'     => 18,
        'level'      => 0,
        'conditions' => array()
    ),
    241 => array(
        'type'       => 'T_WHITESPACE',
        'code'       => 371,
        'content'    => "\n",
        'line'       => 25,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
    242 => array(
        'code'       => T_CLOSE_TAG,
        'type'       => 'T_CLOSE_TAG',
        'content'    => '?>',
        'line'       => 26,
        'column'     => 1,
        'level'      => 0,
        'conditions' => array()
    ),
);
