<?php

$value = '';
// Check booleans
if ($value === true) {}
if ($value == true) {}
if (true === $value) {}
if (true == $value) {}

if($value === true){}
if($value == true){}
if(false === $value){}
if(!false == $value || true !== $value){}

// check integer comparison
if($value === 5){}
if($value == 5){}
if(5 === $value){}
if(5 == $value){}

// check float comparison
if($value === 5.2){}
if($value == 5.2){}
if(5.2 === $value){}
if(5.2 == $value){}

// check null comparison
if($value === null){}
if($value == null){}
if(null === $value){}
if(null == $value){}

if(
    $value
    ===
    null
){}
if(
    null
    ===
    $value
){}

// check string comparison
if($value === 'string'){}
if($value == 'string'){}
if('string' === $value){}
if('string' == $value){}

if([] === $value){}
if($value === [] ){}
if([] == $value){}
if($value == [] ){}

if($value === array()){}
if($value == array()){}
if(array() === $value){}
if(array() == $value){}

// check string comparison
$assigned = $value === 'string';
$assigned = 'string' == $value;

if(($value) === $otherValue){}
if($value === ($otherValue)){}

if(($value) === true){}
if((true) === $value){}

if(($value + 1 + 1) === $value){}
if(($value + $value) === $value){}

if($value == self::CONSTANT_1){}

const CONSTANT1 = 1;
if($value === CONSTANT1){}
if(CONSTANT1 === $value){}

if($value === ($value1 | $value2)){}
if(($value1 | $value2) === $value){}

// Check with objects
if($object->myVar === $value){}
if($value === $object->myVar){}

if($object->function() === $value){}
if($value === $object->function()){}

// Check with functions
if(myFunction() === $value){}
if($value === myFunction()){}

// check with multiple operations
if($value === true && $value === 1 && $value === null){}
if(($value === true && $value === 1) == ($value === null && $value === new stdClass())){}

if(true === $value && 1 === $value && null === $value){}
if((true === $value && 1 === $value) == (null === $value && new stdClass() === $value)){}

// Add comments in the middle
if(
    //comment
    true
    // comment
    ===
    // comment
    $value
){}

if(
    //comment
    $value
    // comment
    ===
    // comment
    true
){}

if(array($key => $val) === $value){}
if(array($key => $val) == $value){}

if([$key => $val] === $value){}
if([$key => $val] == $value){}

$config['checkAuthIn'] !== $event->getName();

if ($var === "ab" || 'cd') {}
if ("ab" || 'cd' === $var) {}
if (2 > $value || 3 < $var) {}
if ($value == true && (/* comment */ 2 > test())) {}
if ((int) 5 > $var) {}
if ((int) $var > (int) 5) {}
if (true == function() { return false;}){}
if (function() { return false;} == true){}

if (is_array($val)
    && array($foo) === array($bar)
    && [$foo] === [$bar]
    && array('foo', 'bar') === array($foo, $bar)
    && ['foo', 'bar'] === [$foo, $bar]
    && array('foo' => true, 'bar' => false) === array(getContents())
    && ['foo' => true, 'bar' => false] === array(getContents())
    && array(getContents()) === ['foo' => true, 'bar' => false]
) {
}

if ($this->cfg['some_closure']() == 2) {
}

if (is_array($val)
    && array(get_class($val[0]), $val[1]) == array('someNamespace\\className', 'method')
) {
}

if (is_array($val)
    && array('someNamespace\\className', 'method') == array(get_class($val[0]), $val[1])
) {
}

if ([function() { echo 'hi'; }] === [$foo]
    && [$foo] === [function() { echo 'hi'; }]
    && [function() { echo 'hi'; }, $bar] === [$foo]
    && [$foo] === [function() { echo 'hi'; }, $bar]
) {
}

