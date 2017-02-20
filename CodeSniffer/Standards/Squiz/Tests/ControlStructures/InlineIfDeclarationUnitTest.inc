<?php

echo 'var is '.(($var < 0) ? 'negative' : 'positive');
echo 'var is '.($var < 0 ? 'negative' : 'positive');
echo 'var is '.(($var < 0) ? 'negative' :'positive');
echo 'var is '.(($var < 0) ? 'negative': 'positive');
echo 'var is '.(($var < 0) ? 'negative' :   'positive');
echo 'var is '.(($var < 0) ? 'negative'   : 'positive');
echo 'var is '.(($var < 0) ?   'negative' : 'positive');
echo 'var is '.(($var < 0)  ? 'negative' : 'positive');

echo 'var is '.(($var < 0)
                    ? 'negative'
                    : 'positive');

$args = array(
         '"'.$this->id.'"',
         '"'.$this->stepInfo['title'].'"',
         '"'.((isset($this->stepInfo['description']) === TRUE) ? $this->stepInfo['description'] : '').'"',
         '"'.(isset($this->stepInfo['description']) === TRUE ? $this->stepInfo['description'] : '').'"',
         '"'.$this->stepInfo['title'].'"',
        );

echo (TRUE)?'Hello':'Bye';

$array = array(
    'one' => ($test == 1) ? true : false,
    'two' => (($test == 1) ? true : false),
    'three' => (($test == 1) ? true : false)
);
$var = ($test == 1) ? true : false;
$var = (myFunc(1,2,3) == 1) ? true : false;

set('config', function() {
    $foo = ($bar === "on") ? "1" : "2";
});

$config = function() {
    $foo = ($bar === "on") ? "1" : "2";
};

rand(0, 1) ? 'ěščřžýáí' : NULL;

$c = ($argv[1]) ? : "";
$filepath = realpath($argv[1]) ?: $argv[1];
$c = ($argv[1]) ? /* comment */ : "";
$c = ($argv[1]) ? 
: "";
