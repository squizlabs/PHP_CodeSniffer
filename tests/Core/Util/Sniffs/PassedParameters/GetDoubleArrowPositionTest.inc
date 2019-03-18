<?php

/* testGetDoubleArrowPosition */
$array = [
    /* testValueNoArrow */
    'value no arrow',

    /* testArrowNumericIndex */
    123 => 'arrow numeric index',

    /* testArrowStringIndex */
    'foo' => 'arrow string index',

    /* testArrowMultiTokenIndex */
    'concat' . 'index' => 'arrow multi token index',

    /* testNoArrowValueShortArray */
    [
        'value only' => 'arrow belongs to value',
    ],

    /* testNoArrowValueLongArray */
    array(
        'value only' => 'arrow belongs to value',
    ),

    /* testNoArrowValueNestedArrays */
    array(
        [
            array(
                ['key' => 'arrow belongs to nested array'],
            ),
        ],
    ),

    /* testNoArrowValueClosure */
    function() {
        echo 'closure as value arrow belongs to value';
        return array( $a => $b );
    },

    /* testArrowValueShortArray */
    'index and value short array' => [
        'index and value' => '',
    ],

    /* testArrowValueLongArray */
    'index and value long array' => array(
        'index and value' => '',
    ),

    /* testArrowValueClosure */
    'index and value closure' => function() {
        echo 'closure as value arrow belongs to value';
        return array( $a => $b );
    },
];
