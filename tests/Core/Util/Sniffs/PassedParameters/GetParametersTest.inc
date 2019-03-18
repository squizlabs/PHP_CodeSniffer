<?php

/* testNoParams */
myfunction();

/* testFunctionCall */
myfunction( 1, 2, 3, 4, 5, 6, true );

/* testFunctionCallNestedFunctionCall */
dirname( dirname( __FILE__ ) ); // 1

/* testAnotherFunctionCall */
mktime($stHour, 0, 0, $arrStDt[0], $arrStDt[1], $arrStDt[2]); // 6

/* testFunctionCallTrailingComma */
json_encode( array(), );

/* testFunctionCallNestedShortArray */
json_encode(['a' => $a,] + (isset($b) ? ['b' => $b,] : []));

/* testLongArrayNestedFunctionCalls */
$foo = array(some_call(5, 1), another(1), why(5, 1, 2), 4, 5, 6); // 6

/* testSimpleLongArray */
$foo = array( 1, 2, 3, 4, 5, 6, true );

/* testLongArrayWithKeys */
$foo = array('a' => $a, 'b' => $b, 'c' => $c);

/* testShortArrayNestedFunctionCalls */
$bar = [0, 0, date('s', $timestamp), date('m'), date('d'), date('Y')]; // 6

/* testShortArrayMoreNestedFunctionCalls */
$bar = [str_replace("../", "/", trim($value))]; // 1

/* testShortArrayWithKeysAndTernary */
$bar = [0 => $a, 2 => $b, 6 => (isset($c) ? $c : null)];

/* testNestedArraysToplevel */
$array = array(
    '1' => array(
        0 => 'more nesting',
        /* testNestedArraysLevel2 */
        1 => array(1,2,3),
    ),
    /* testNestedArraysLevel1 */
    '2' => [
        0 => 'more nesting',
        1 => [1,2,3],
    ],
);

/* testFunctionCallNestedArrayNestedClosureWithCommas */
preg_replace_callback_array(
    /* testShortArrayNestedClosureWithCommas */
    [
        '~'.$dyn.'~J' => function ($match) {
            echo strlen($match[0]), ' matches for "a" found', PHP_EOL;
        },
        '~'.function_call().'~i' => function ($match) {
            echo strlen($match[0]), ' matches for "b" found', PHP_EOL;
        },
    ],
    $subject
);

/* testShortArrayNestedAnonClass */
$array = [
    'class' => new class() {
        public $prop = [1,2,3];
        public function test( $foo, $bar ) {
            echo $foo, $bar;
        }
    },
    'anotherclass' => new class() {
        public function test( $foo, $bar ) {
            echo $foo, $bar;
        }
    },
];

/* testVariableFunctionCall */
$closure($a, (1 + 20), $a & $b );

/* testStaticVariableFunctionCall */
self::$closureInStaticProperty($a->property, $b->call() );

/* testSimpleList */
list($id, $name) = $data;

/* testNestedList */
list($a, list($b, $c)) = array(1, array(2, 3));

/* testListWithKeys */
list('name' => $a, 'id' => $b, 'field' => $a) = ['name' => 1, 'id' => 2, 'field' => 3];

/* testListWithEmptyEntries */
list( , $a, , $b, , $a, ,) = [1, 2, 3, 4, 5, 6, 7, 8];

/* testMultiLineKeyedListWithTrailingComma */
        list(
            "name" => $this->name,
            "colour" => $this->colour,
            "age" => $this->age,
            "cuteness" => $this->cuteness,
        ) = $attributes;

/* testShortList */
[$a, $b, $c] = [1, 2 => 'x', 'z' => 'c'];

/* testNestedShortList */
[[$a, $b], [$b, $a]] = array(array(10, 11), array(2, 3));

/* testIsset */
if ( isset(
    $variable,
    $object->property,
    static::$property,
    $array[$name][$sub],
)) {}

/* testUnset */
unset( $variable, $object->property, static::$property, $array[$name], );
