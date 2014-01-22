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

if (true) :
    $foo = true;
endif;

while (true) :
    $foo = true;
endwhile;

for ($i; $i > 0; $i--) :
    echo 'hello';
endfor;

foreach ($array as $element) :
    echo 'hello';
endforeach;

while (!$this->readLine($tokens, $tag));
while (!$this->readLine($tokens, $tag)); //skip to end of file

foreach ($cookies as $cookie)
    if ($cookie->match($uri, $matchSessionCookies, $now))
        $ret[] = $cookie;

foreach ($stringParade as $hit)
    $hitParade[] = $hit + 0; //cast to integer

if ($foo) :
    echo 'true';
elseif ($something) :
    echo 'foo';
else:
    echo 'false';
endif;
