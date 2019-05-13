<?php

$tests = [
    [
        'original' => '$no_index_value_scalar = TRUE;',
        'settings' => [
            'no_index_value_scalar' => (object) [
                'value' => FALSE,
                'comment' => 'comment',
            ],
        ],
        'expected' => '$no_index_value_scalar = false; // comment',
    ],
    [
        'original' => '$no_index_value_scalar = TRUE;',
        'settings' => [
            'no_index_value_foo' => [
                'foo' => [
                    'value' => (object) [
                        'value' => NULL,
                        'required' => TRUE,
                        'comment' => 'comment',
                    ],
                ],
            ],
        ],
        'expected' => <<<'EXPECTED'
$no_index_value_scalar = TRUE;
$no_index_value_foo['foo']['value'] = NULL; // comment
EXPECTED
    ],
    [
        'original' => '$no_index_value_array = array("old" => "value");',
        'settings' => [
            'no_index_value_array' => (object) [
                'value' => FALSE,
                'required' => TRUE,
                'comment' => 'comment',
            ],
        ],
        'expected' => '$no_index_value_array = array("old" => "value");
$no_index_value_array = false; // comment',
    ],
];
