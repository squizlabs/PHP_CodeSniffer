<?php
$x = 'My '.'string';
$x = 'My '.1234;
$x = 'My '.$y.' test';

echo $data['my'.'index'];
echo $data['my'.4];
echo $data['my'.$x];
echo $data[$x.$y.'My'.'String'];

$code = '$actions = array();'."\n";
$code = "$actions = array();"."\n";
?>
