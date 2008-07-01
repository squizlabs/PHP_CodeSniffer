<?php

if ($something) echo 'hello';

if ($something) {
    echo 'hello';
} else echo 'hi';

if ($something) {
    echo 'hello';
} else if ($else) echo 'hi';

foreach ($something as $thing) echo 'hello';

for ($i; $i > 0; $i--) echo 'hello';

while ($something) echo 'hello';

do {
    $i--;
} while ($something);

if(true)
  $someObject->{$name};

?>