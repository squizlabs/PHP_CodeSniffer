<?php
$varName  = 'hello';
$var_name = 'hello';
$varname  = 'hello';
$_varName = 'hello';

class MyClass
{
    $varName  = 'hello';
    $var_name = 'hello';
    $varname  = 'hello';
    $_varName = 'hello';

    public $varName  = 'hello';
    public bool $var_name = false;
    public $varname  = 'hello';
    public $_varName = 'hello';

    protected $varName  = 'hello';
    protected string $var_name = 'hello';
    protected $varname  = 'hello';
    protected ?string $_varName = 'hello';

    private $_varName  = 'hello';
    private $_var_name = 'hello';
    private int $_varname  = 1;
    private $varName   = 'hello';
}

echo $varName;
echo $var_name;
echo $varname;
echo $_varName;

echo "Hello $varName";
echo "Hello $var_name";
echo "Hello ${var_name}";
echo "Hello $varname";
echo "Hello $_varName";

echo 'Hello '.$varName;
echo 'Hello '.$var_name;
echo 'Hello '.$varname;
echo 'Hello '.$_varName;

echo $_SERVER['var_name'];
echo $_REQUEST['var_name'];
echo $_GET['var_name'];
echo $_POST['var_name'];
echo $GLOBALS['var_name'];

echo MyClass::$varName;
echo MyClass::$var_name;
echo MyClass::$varname;
echo MyClass::$_varName;

echo $this->varName2;
echo $this->var_name2;
echo $this->varname2;
echo $this->_varName2;
echo $object->varName2;
echo $object->var_name2;
echo $object_name->varname2;
echo $object_name->_varName2;

echo $this->myFunction($one, $two);
echo $object->myFunction($one_two);

$error = "format is \$GLOBALS['$varName']";

echo $_SESSION['var_name'];
echo $_FILES['var_name'];
echo $_ENV['var_name'];
echo $_COOKIE['var_name'];

$XML       = 'hello';
$myXML     = 'hello';
$XMLParser = 'hello';
$xmlParser = 'hello';

echo "{$_SERVER['HOSTNAME']} $var_name";

// Need to be the last thing in this test file.
$obj->$classVar = $prefix.'-'.$type;

class foo
{
    const bar = <<<BAZ
qux
BAZ;
}

$foo = <<<'BAR'
$123
"$456"
BAR;

$foo = <<<BAR
$123
"$456"
BAR;

class a
{
    protected
        $_sheet,
        $_FieldParser,
        $_key;

    private
        $varN = true,
        $varO = array( 'a', 'b' ),
        $varP = 'string',
        $varQ = 123;
}

var_dump($http_response_header);
var_dump($HTTP_RAW_POST_DATA);
var_dump($php_errormsg);

trait MyTrait
{
    public $_varName = 'hello';
    private $_varName = 'hello';
}

class TestClass
{
    /**
     * Comment goes here.
     *
     * @var array
     */
    private static $_foo = [];
}

$anonClass = new class() {
    public function foo($foo, $_foo, $foo_bar) {
        $bar = 1;
        $_bar = 2;
        $bar_foo = 3;
    }
};

echo $obj?->varName;
echo $obj?->var_name;
echo $obj?->varname;
echo $obj?->_varName;

enum SomeEnum
{
    public function foo($foo, $_foo, $foo_bar) {
        $bar = 1;
        $_bar = 2;
        $bar_foo = 3;
    }
}
