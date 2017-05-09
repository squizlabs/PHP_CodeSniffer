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

// No errors for these because they are needed in some cases.
$code = ' ?'.'>';
$code = '<'.'?php ';

$string = 'This is a really long string. '
        . 'It is being used for errors. '
        . 'The message is not translated.';
?>
