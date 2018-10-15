<?php

$value = '';
// Check booleans
if ($value === true) {}
if ($value == true) {}
if (true === $value) {}
if(true == $value){}

if($value === true){}
if($value == true){}
if(false === $value){}
if(false == $value){}

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

if(($otherValue) === $value){}
if($otherValue === ($value)){}
if(($value) === true){}
if((true) === $value){}