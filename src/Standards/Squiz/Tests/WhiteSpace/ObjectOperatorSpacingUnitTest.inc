<?php
$this->testThis();
$this-> testThis();
$this    ->  testThis();
$this-> /* comment here */testThis();
$this/* comment here */ -> testThis();
$this
    ->testThis();
$this->
    testThis();

// phpcs:set Squiz.WhiteSpace.ObjectOperatorSpacing ignoreNewlines true

$this->testThis();
$this-> testThis();
$this    ->  testThis();
$this->/* comment here */testThis();
$this/* comment here */ -> testThis();
$this
    ->testThis();
$this->
    testThis();

// phpcs:set Squiz.WhiteSpace.ObjectOperatorSpacing ignoreNewlines false

thisObject::testThis();
thisObject:: testThis();
thisObject    ::  testThis();
thisObject::/* comment here */testThis();
thisObject/* comment here */ :: testThis();
thisObject
    ::testThis();
thisObject::
    testThis();
	
// phpcs:set Squiz.WhiteSpace.ObjectOperatorSpacing ignoreNewlines true

thisObject::testThis();
thisObject:: testThis();
thisObject    ::  testThis();
thisObject::/* comment here */testThis();
thisObject/* comment here */ :: testThis();
thisObject
    ::testThis();
thisObject::
    testThis();

// phpcs:set Squiz.WhiteSpace.ObjectOperatorSpacing ignoreNewlines false
