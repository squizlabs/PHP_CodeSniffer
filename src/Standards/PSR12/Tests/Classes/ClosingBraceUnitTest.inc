<?php
class Foo
{
}

class Foo1
{
    public function foo()
    {
    }
    public function foo2()
    {
    }//end foo2()
}//end class

interface Foo2
{

}echo 'hi';

trait Foo3
{

}//end

function bar()
{
}
function bar2()
{
}//end bar2()

$foo->bar(
    $arg1,
    function ($arg2) use ($var1) {
        // body
    },
    $arg3
);

$instance = new class extends \Foo implements \HandleableInterface {
    // Class content
};

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello ' . $app->escape($name);
});

enum Foo4
{

}//end
