<?php

// do ... while
$i = 0;
do {
    echo $i;
} while ($i > 0);

do
{
    echo $i;
} while ($i > 0);

do
{
    echo $i;
}
while ($i > 0);

do { echo $i; } while ($i > 0);

do{
    echo $i;
}while($i > 0);


// while
while ($i < 1) {
    echo $i;
}

while($i < 1){
    echo $i;
}

while ($i < 1) { echo $i; }


// for
for ($i = 1; $i < 1; $i++) {
    echo $i;
}

for($i = 1; $i < 1; $i++){
    echo $i;
}

for ($i = 1; $i < 1; $i++) { echo $i; }


// foreach
foreach ($items as $item) {
    echo $item;
}

foreach($items as $item){
    echo $item;
}

foreach ($items as $item) { echo $item; }


// if
if ($i == 0) {
    $i = 1;
}

if($i == 0){
    $i = 1;
}

if ($i == 0) { $i = 1; }


// else
if ($i == 0) {
    $i = 1;
} else {
    $i = 0;
}

if ($i == 0) {
    $i = 1;
}else{
    $i = 0;
}

if ($i == 0) { $i = 1; } else { $i = 0; }


// else
if ($i == 0) {
    $i = 1;
} else {
    $i = 0;
}

if ($i == 0) {
    $i = 1;
}else{
    $i = 0;
}

if ($i == 0) { $i = 1; } else { $i = 0; }


// else if
if ($i == 0) {
    $i = 1;
} else if ($i == 2) {
    $i = 0;
}

if ($i == 0) {
    $i = 1;
} elseif ($i == 2) {
    $i = 0;
}

if ($i == 0) {
    $i = 1;
}else if($i == 2){
    $i = 0;
}

if ($i == 0) {
    $i = 1;
}elseif($i == 2){
    $i = 0;
}

if ($i == 0) { $i = 1; } else if ($i == 2) { $i = 0; }
if ($i == 0) { $i = 1; } elseif ($i == 2) { $i = 0; }

if ($i == 0) { // this is ok because comments are allowed
    $i = 1;
}

if ($i == 0) {// this is ok because comments are allowed
    $i = 1;
}

if ($i == 0) { /* this is ok because comments are allowed*/
    $i = 1;
}

if ($i == 0)
{ // this is not ok
    $i = 1;
}

if ($i == 0) /* this is ok */ {
}

if ($i == 0) {
}
else {
}

// match
$r = match ($x) {
    1 => 1,
};

$r = match( $x ){ 1 => 1 };
