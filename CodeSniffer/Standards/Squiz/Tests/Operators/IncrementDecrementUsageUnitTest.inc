<?php
$var  = ($var + 1);
$var  = ($one + 1);
$var  = ($var + 2);
$var  = ($one + $two + 1);
$var += 1;
$var += 2;
$var += $one;
$var += (1 + 2)
$var  = myFunction($var, ($one + 1));

$var  = ($var - 1);
$var  = ($one - 1);
$var  = ($var - 2);
$var  = ($one - $two - 1);
$var -= 1;
$var -= 2;
$var -= $one;
$var -= (1 + 2)
$var  = myFunction($var, ($one - 1));

$var -= $var - 1;
$var += $var - 1;

$id = $id.'_'.($i-- - $x--).'_'.$x;
$id = $id.'_'.(++$i - $x--).'_'.$x;
$id = $id.'_'.(--$i - $x++).'_'.$x;

$id = $id.'_'.$i++;
$id = $id.'_'.($i++);
$id = $id.'_'.$i--.'_';
$id = $id.'_'.($i--).'_';

$var = (1 - $var);
$var = (1 + $var);

$expected[$i]['sort_order'] = ($i + 1);
$expected[($i + 1)]['sort_order'] = ($i + 1);

$id = $id.($this->i++).$id;
