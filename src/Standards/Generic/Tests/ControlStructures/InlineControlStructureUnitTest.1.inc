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

function test()
{
    if ($a)
        $a.=' '.($b ? 'b' : ($c ? ($d ? 'd' : 'c') : ''));
}

if ($a)
    foreach ($b as $c) {
        if ($d) {
            $e=$f;
            $g=$h;
        } elseif ($i==0) {
            $j=$k;
        }
    }

?>
<div style="text-align: right;">
    <?php if ($model->scenario == 'simple') $widget->renderPager() ?>
</div>

<?php
switch ($this->error):
    case Shop_Customer :: ERROR_INVALID_GENDER: ?>
        Ung&uuml;ltiges Geschlecht!
    <?php break;
    case Shop_Customer :: ERROR_EMAIL_IN_USE: ?>
        Die eingetragene E-Mail-Adresse ist bereits registriert.
    <?php break;
endswitch;

if ($this->allowShopping !== true):
    if ($this->status != Shop_Cart :: OK):
        switch ($this->status):
            case Shop_Cart :: NOT_FOUND:
            echo 'foo';
        endswitch;
    endif;
else:
    echo 'foo';
endif;

// ELSE IF split over multiple lines (not inline)
if ($test) {
} else
    if ($test) {
    } else {
    }

switch($response = \Bar::baz('bat', function ($foo) {
    return 'bar';
})) {
    case 1:
        return 'test';

    case 2:
        return 'other';
}

$stuff = [1,2,3];
foreach($stuff as $num)
    if ($num %2 ) {
        echo "even";
    } else {
        echo "odd";
    }

$i = 0;
foreach($stuff as $num)
    do {
        echo $i;
        $i++;
    } while ($i < 5);

foreach($stuff as $num)
    if (true) {
        echo "true1\n";
    }
    if (true) {
        echo "true2\n";
    }

if ($foo) echo 'foo';
elseif ($bar) echo 'bar';
else echo 'baz';

switch ($type) {
    case 1:
        if ($foo) {
            return true;
        } elseif ($baz)
            return true;
        else {
            echo 'else';
        }
    break;
}

foreach ($sql as $s)
        if (!$this->execute) echo "<pre>",$s.";\n</pre>";
        else {
            $ok = $this->connDest->Execute($s);
            if (!$ok)
                if ($this->neverAbort) $ret = false;
                else return false;
        }

if ($bar)
    if ($foo) echo 'hi'; // lol

if ($level == 'district')
    \DB::update(<<<EOD
some
text
here
EOD
    );

if ($level == 'district')
    $var = <<<EOD
some
text
here
EOD;

if ($a && $a === Foo::VARIABLE && ($a === Foo::METHOD || $a === Foo::FUNCTION))
    echo 'hi';

$out = array_map(function ($test) { if ($test) return 1; else return 2; }, $input); // comment

for ($x=0;$x<5;$x++):
    if ($x) continue;
endfor;

for ($x=0;$x<5;$x++):
    if ($x) continue ?> <?php
endfor;

if (true)
    try {
    }
    catch(Exception $e) {
    }

switch ($num) {
    case 0:
        if (1 > $num)
            return bar(
                baz(
                    "foobarbaz"
                )
            );
        break;
}

do {
	$i++;
}
// Comment
while ($i < 10);

if ($this) {
    if ($that)
        foo(${$a[$b]});
}

while (!$this->readLine($tokens, $tag)); //phpcs:ignore Standard.Category.Sniff

while (!$this->readLine($tokens, $tag)); // comment

while (!$this->readLine($tokens, $tag)); /* comment */

foreach ($stringParade as $hit)
    $hitParade[] = $hit + 0; // phpcs:ignore Standard.Category.Sniff

if ($bar)
    if ($foo) echo 'hi'; /* @phpcs:ignore Standard.Category.Sniff */

if (true) $callable = function () {
    return true;
};

foreach ([] as $a)
echo 'bar';
{
    echo 'baz';
}

// Issue 2822.
$i = 10;
while ($i > 0 && --$i);

for ($i = 1, $j = 0; $i <= 10; $j += $i, print $i, $i++);

if ($this->valid(fn(): bool => 2 > 1)) {
}

// Issue 3345.
function testMultiCatch()
{
    if (true)
        try {
        } catch (\LogicException $e) {
        } catch (\Exception $e) {
        }
}

function testFinally()
{
    if (true)
        try {
        } catch (\LogicException $e) {
        } finally {
        }
}
