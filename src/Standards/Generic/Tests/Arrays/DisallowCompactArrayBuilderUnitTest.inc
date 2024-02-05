<?php
$var = compact();
$var = compact('a','b','c');
$foo = compact($var[1],$var[2]);
$foo = compact(
        'a',
        "b",
        'c'
       );
$var = compact/*comment*/('a', 'b', "c");
$var = compact(['aa', 'bb' => 'cc']);
$var = compact(array('aa', 'bb' => 'cc'));

function foo($compact) {}
$compact = function ($a, $b, $c) use ($foo): array {};
$compact('a', 'b', 'c');

view('some.view', compact("a", 'b', 'c'));
view('some.view', compact(
    'a',
    'b',
    'c'
));

$var = compact('aa', 'invalid-var.name');
COMPACT('a');
Compact('a');
$var = Bazz::compact('a', 'b');
$ver = $foo->compact('a', 'b');
$obj?->compact('a');
class compact {
    public function compact( $param = 'a' ) {}
    public function &compact( $param = 'a' ) {}
}
new compact('a');
MyNamespace\compact('a');
namespace\compact('a');
\compact('a');
compact(...$names);
compact( 'prefix' . $name, '$name' . 'suffix', "some$name");
compact(...get_names('category1', 'category2'));
$bar = @compact('a', 'b');
$foo = true && compact('a', 'b');
$baz = &compact('a', 'b');
func(compact('a', 'b'));
// Live coding/parse error.
compact( 'a', 'b'
