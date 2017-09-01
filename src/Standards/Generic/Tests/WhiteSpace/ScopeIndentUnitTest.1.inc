@codingStandardsChangeSetting Generic.WhiteSpace.ScopeIndent tabIndent false
<meta name="twitter:site" content="<?php echo $metaTagsData['twitter:site']; ?>">
<?php
class Test {
    function __construct()
    {
       $this->hello();
    }

   function hello()
    {
        echo 'hello';
}//end hello()

    function hello2()
    {
       if (TRUE) {
            echo 'hello'; // no error here as its more than 4 spaces.
        } else {
        echo 'bye';
        }

        while (TRUE) {
           echo 'hello';
         }

       do {
         echo 'hello';
       } while (TRUE);
   }

    function hello3()
    {
        switch ($hello) {
            case 'hello':
            break;
        }
    }

}

?>
<pre>
</head>
<body>
<?php
if ($form->validate()) {
    $safe = $form->getSubmitValues();
}
?>
</pre>
<?php

class Test2
{
    function __construct()
    {
    //    $this->open(); // error here
    }

    public function open()
    {
        // Some inline stuff that shouldn't error
        if (TRUE) echo 'hello';
        foreach ($tokens as $token) echo $token;
    }

    /**
     * This is a comment 1.
     * This is a comment 2.
     * This is a comment 3.
     * This is a comment 4.
     */
    public function close()
    {
        // All ok.
        if (TRUE) {
            if (TRUE) {
            } else if (FALSE) {
                foreach ($tokens as $token) {
                    switch ($token) {
                        case '1':
                        case '2':
                            if (true) {
                                if (false) {
                                    if (false) {
                                        if (false) {
                                            echo 'hello';
                                        }
                                    }
                                }
                            }
                        break;
                        case '5':
                        break;
                    }
                    do {
                        while (true) {
                            foreach ($tokens as $token) {
                                for ($i = 0; $i < $token; $i++) {
                                    echo 'hello';
                                }
                            }
                        }
                    } while (true);
                }
            }
        }
    }

    /*
      This is another c style comment 1.
      This is another c style comment 2.
      This is another c style comment 3.
      This is another c style comment 4.
      This is another c style comment 5.
    */

    /* This is a T_COMMENT
    *
    *
    *
   */

    /** This is a T_DOC_COMMENT
   */

    /*
      This T_COMMENT has a newline in it.

    */

    public function read()
    {
        echo 'hello';

        // no errors below.
        $array = array(
                  'this',
                  'that' => array(
                             'hello',
                             'hello again' => array(
                                               'hello',
                                              ),
                            ),
                 );
    }
}

abstract class Test3
{
    public function parse()
    {

        foreach ($t as $ndx => $token) {
            if (is_array($token)) {
                echo 'here';
            } else {
                $ts[] = array("token" => $token, "value" => '');

                $last = count($ts) - 1;

                switch ($token) {
                    case '(':

                        if ($last >= 3 &&
                            $ts[0]['token'] != T_CLASS &&
                            $ts[$last - 2]['token'] == T_OBJECT_OPERATOR &&
                            $ts[$last - 3]['token'] == T_VARIABLE ) {


                            if (true) {
                                echo 'hello';
                            }
                        }
                        array_push($braces, $token);
                    break;
                }
            }
        }
    }
}

public function test()
{
    $o = <<<EOF
this is some text
this is some text
this is some text
this is some text
this is some text
this is some text
EOF;

    return $o;
}

if ($a === true || $a === true || $a === true || $a === true ||
    $a === true || $a === true || $a === true || $a === true) {

    echo 'hello';
}

if ($true) {
    /* First comment line
    *
    * Comment test here
    * Comment test here
    *
    */

    /* First comment line
    *
    * Comment test here
    * Comment test here
    *
    this si something */
}

function test()
{
    /* taken from http://de3.php.net/manual/en/reserved.php */
    # $m[] = 'declare';
   /* taken from http://de3.php.net/manual/en/reserved.php */
   # $m[] = 'declare';
}

foreach ($elements as $element) {
    if ($something) {
        // Do IF.
    } else if ($somethingElse) {
        // Do ELSE.
    }
}

if ($condition) {
    echo "This is a long
string that spans $numLines lines
without indenting.
";
}

if ($condition) {
    echo 'This is a long
    string that spans multiple lines
    with indenting.
    ';
}

if ($condition) {
    echo 'This is a long
          string that spans multiple lines
          with indenting.';
}

switch ($foo) {
    case 1:
        switch ($bar) {
            default:
                echo $string{1};
        }
    break;
}

function temp($foo, $bar) {
    switch ($foo) {
        case 1:
            switch ($bar) {
                default:
                return $foo;
            }
        break;
    }
}

switch ($foo) {
    case 1:
        switch ($bar) {
        default:
            if ($something) {
                echo $string{1};
            } else if ($else) {
                switch ($else) {
                default:
                }
            }
        }
    break;
}

switch ($name) {
    case "1":
    case "2":
    case "3":
        return true;
}

switch ($name) {
    case "1":
    case "2":
    case "3":
    default :
        return true;
}

function myFunction()
{
    ?>
    <dynamic_content>

   </dynamic_content>
    <?php

}

switch ($name) {
    case "1":
        switch ($name2) {
            case "1":
            break;
            case "2":
            break;
        }
    break;
    case "2":
    break;
}

switch (true) {
    case true: {
    }
    echo 'hi';
    break;
    case false:
    case null:{
    echo 'hi';
    }
    break;
}

switch (true) {
    case true: {
        echo 'hi';
    }
    // No break here.
    case false:
    case true:
    case null:{
    echo 'hi';
        echo 'hi';
    }
    break;
}

switch (true) {
    case true: {
        if (true) {
            echo 'hi';
        }
    }
    break;
}

// Testing anon function.
class a {
    function c()
    {
        $this->doSomething(
            function () {
                echo 123;
            }
        );
    }
}

some_function(
    function() {
        $a = 403;
        if ($a === 404) {
        $a = 403;
        }
    }
);

some_function(
   function() {
      $a = 403;
       if ($a === 404) {
           $a = 403;
       }
    }
);

$myFunction = function() {
    $a = 403;
    if ($a === 404) {
       $a = 403;
    }
}

class Whatever
{
    protected $_protectedArray = array(
        'normalString' => 'That email address is already in use!',
        'offendingString' => <<<'STRING'
Each line of this string is always said to be at column 0, 
    no matter how many spaces are placed
                at the beginning of each line
and the ending STRING on the next line is reported as having to be indented.
STRING
    );
}

class MyClass
{
    public static function myFunction()
    {
        if (empty($keywords) === FALSE) {
    	    $keywords = 'foo';
    	    $existing = 'foo';
        }

        return $keywords;

    }//end myFunction()

}//end class

$var = call_user_func(
    $new_var = function () use (&$a) {
        if ($a > 0) {
            return $a++;
        } else {
            return $a--;
        }
    }
);

class AnonymousFn
{
    public function getAnonFn()
    {
        return array(
            'functions' => Array(
                'function1' => function ($a, $b, $c) {
                    $a = $b + $c;
                    $b = $c / 2;
                    return Array($a, $b, $c);
                },
            ),
        );
    }
}
?>

<div>
    <?php
    if ($myvar == 'test') {
        echo 'something';
    }
    ?>
<div>
<div>
    <?php
        if ($myvar == 'test') {
            echo 'something';
        }
    ?>
<div>
<div>
    <?php
if ($myvar == 'test') {
    echo 'something';
}
    ?>
<div>
<body>
    <?php
    if (isset($_GET["test"])) {
        if ($_GET["test"] <> "") {
            $test = true;
        } else {
            $test = true;
        }
    }
    ?>
</body>

<?php
if (true) {
    ?>
    <div>
        <div>
            <div>
            <?php
                if (true) {
                    echo 'hello';
                } else {
                    echo 'goodbye';
                }
            ?>
            </div>
        </div>
    </div>
    <?php
} else {
    echo 'else';
}
?>
<?php if (isset($param)) { ?>
   <h3>some text</h3>
<?php }

$list = [
    'fn' => function ($a) {
        if ($a === true) {
            echo 'hi';
        }
    }
];

$list = [
    'fn' => function ($a) {
                if ($a === true) {
                    echo 'hi';
                }
    }
];

if ($foo) {
    foreach ($bar as $baz) {
        if ($baz) {
            ?>
            <div>
                <div>
                    <div>
            <?php
            if ($baz > 1) {
                echo '1';
            }
            ?>
                    </div>
                    <?php
                if ($baz > 1) {
                    echo '1';
                }
                    ?>
                </div>
                <?php
                if ($baz > 1) {
                    echo '1';
                }
                ?>
            </div>
            <?php
        }
    }
}

?>
<title><?= CHtml::encode($this->pageTitle); ?></title>

<?php
if ($foo) {
    echo '1';
        echo '2';
            echo '3';
}

$array = array();
array_map(
    function($x)
    {
        return trim($x);
    },
    $array
);

function foo(){return function(){};}

$mockedDatabase->expects($this->at(2))
    ->with($this->callback(
            function ($subject)
            {
            }
        )
    );

/** @var Database $mockedDatabase */
/** @var Container $mockedContainer */

echo $string->append('foo')
            ->appaend('bar')
            ->appaend('baz')
            ->outputUsing(
                function ()
                {
                }
            );

echo PHP_EOL;

switch ($arg) {
    case 1:
        break;
    case 2:
        if ($arg2 == 'foo') {
        }
    case 3:
    default:
        echo 'default';
}

if ($tokens[$stackPtr]['content']{0} === '#') {
} else if ($tokens[$stackPtr]['content']{0} === '/'
    && $tokens[$stackPtr]['content']{1} === '/'
) {
}

$var = call_user_func(
    function() {
        if ($foo) {
            $new_var = function() {
                if ($a > 0) {
                    return $a++;
                } else {
                    return $a--;
                }
            }
        }
    }
);

a(
    function() {
        $a = function() {
            $b = false;
        };
    true
    }
);

$var = [
    [
        '1' =>
            function () {
                return true;
            },
    ],
    [
        '1' =>
            function () {
                return true;
            },
        '2' => true,
    ]
];

if ($foo) {
 ?>
 </p>
  <?php
}

function foo()
{
    $failingTests[$testName][] = array(
                                  'comment'    => self::_replaceKeywords($failingComment, $result),
                                  'screenshot' => Test::getScreenshotPath(
                                      $projectid,
                                      $result['class_name'],
                                  ),
                                 );

}

$this->mockedDatabase
    ->with(
        $this->callback(
            function () {
                return;
            }
        )
    );

$this->subject->recordLogin();

function a()
{
    if (true) {
        static::$a[$b] =
            static::where($c)
                ->where($c)
                ->where(
                    function ($d) {
                        $d->whereNull();
                        $d->orWhere();
                    }
                )
                ->first();

        if (static::$a[$b] === null) {
            static::$a[$b] = new static(
                array(
                    'a' => $a->id,
                    'a' => $a->id,
                )
            );
        }
    }

    return static::$a[$b];
}

$foo->load(
    array(
        'bar' => function ($baz) {
            $baz->call();
        }
    )
);

hello();

$foo = array_unique(
    array_map(
        function ($entry) {
            return $entry * 2;
        },
        array()
    )
);
bar($foo);

class PHP_CodeSniffer_Tokenizers_JS
{

    public $scopeOpeners = array(
                            T_CASE => array(
                                       'end'    => array(
                                                    T_BREAK    => T_BREAK,
                                                    T_RETURN   => T_RETURN,
                                                   ),
                                       'strict' => true,
                                      ),
                           );
}

echo $string->
        append('foo')->
        appaend('bar')->
        appaend('baz')->
        outputUsing(
            function ()
            {
            }
        );

$str = 'the items I want to show are: ' .
    implode(
        ', ',
        array('a', 'b', 'c')
    );

echo $str;

$str = 'foo'
        . '1'
        . '2';

echo $str;

bar([
    'foo' => foo(function () {
        return 'foo';
    })
]);

$domains = array_unique(
    array_map(
        function ($url) {
            $urlObject = new \Purl\Url($url);
            return $urlObject->registerableDomain;
        },
        $sites
    )
);

return $domains;

if ($a == 5) :
    echo "a equals 5";
    echo "...";
elseif ($a == 6) :
    echo "a equals 6";
    echo "!!!";
else :
    echo "a is neither 5 nor 6";
endif;

if ($foo):
if ($bar) $foo = 1;
elseif ($baz) $foo = 2;
endif;

$this
    ->method(array(
        'foo' => 'bar',
    ), 'arg', array(
        'foo' => 'bar',
    ));

class Foo
{
    use Bar {
        myMethod as renamedMethod;
    }
}

class Foo
{
    use Bar {
    myMethod as renamedMethod;
    }
}

foo();

array(
 'key1' => function ($bar) {
    return $bar;
 },
 'key2' => function ($foo) {
    return $foo;
 },
);

?>
<script>
    var foo = <?php echo bar(
        'baz'
    ); ?>;
</script>
<?php

try {
    echo 'foo';
} catch (\Exception $e) {
    echo 'catch';
} finally {
    if (false) {
        echo 'finally false';
    }
}

class C0
{
     public function m0()
    {
    }

    public function m1()
    {
    }
}

namespace Foo {

    use \Foo\Bar;
    use \Foo\Baz;

    function test() {
        return true;
    }

}

declare(ticks=1) {
echo 'foo';
}

$a =
    (
        $b = 1
    );
$c = 2;

$a =
    [
        $b => 1,
    ];
$c = 2;

class foo
{
    public function get()
    {
        $foo = ['b' => 'c',
                'd' => [
                    ['e' => 'f']
                    ]];
        echo '42';

        $foo = array('b' => 'c',
                'd' => array(
                    array('e' => 'f')
                    ));
        echo '42';
    }
}

switch ($foo) {
    case 1:
        return array();
    case 2:
        return '';
    case 3:
        return $function();
    case 4:
        return $functionCall($param[0]);
    case 5:
        return array() + array(); // Array Merge
    case 6:
        // String connect
        return $functionReturningString('') . $functionReturningString(array());
    case 7:
        return functionCall(
            $withMultiLineParam[0],
            array(),
            $functionReturningString(
                $withMultiLineParam[1]
            )
        );
    case 8:
        return $param[0][0];
}

class Test {

    public
        $foo
        ,$bar
        ,$baz = [ ]
        ;

    public function wtfindent() {
    }
}

switch ($x) {
    case 1:
        return [1];
    default:
        return [2];
}

switch ($foo) {
    case self::FOO:
        return $this->bar($gfoo, function ($id) {
            return FOO::bar($id);
        }, $values);
    case self::BAR:
        $values = $this->bar($foo, $values);
        break;
}

$var = array(
    'long description' =>
        array(0, 'something'),
    'another long description' =>
        array(1, "something else")
);

$services = array(
    'service 1' =>
        Mockery::mock('class 1')
            ->shouldReceive('setFilter')->once()
            ->shouldReceive('getNbResults')->atLeast()->once()
            ->shouldReceive('getSlice')->once()->andReturn(array())
            ->getMock(),
    'service 2' =>
        Mockery::mock('class 2')
            ->shouldReceive('__invoke')->once()
            ->getMock()
);

class Foo
{
    public function setUp()
    {
        $this->foo = new class {
            public $name = 'Some value';
        };
    }
}

try {
    foo();
} catch (\Exception $e) {
    $foo = function() {
        return 'foo';
    }

    if (true) {
    }
}

if ($foo) {
    foo();
} else if (\Exception $e) {
    $foo = function() {
        return 'foo';
    }

    if (true) {
    }
} else {
    $foo = function() {
        return 'foo';
    }

    if (true) {
    }
}

switch ($parameter) {
    case null:
        return [
            'foo' => in_array(
                'foo',
                []
            ),
        ];

    default:
        return [];
}

class SomeClass
{
    public function someFunc()
    {
        a(function () {
            echo "a";
        })->b(function () {
            echo "b";
        });

        if (true) {
            echo "c";
        }
        echo "d";
    }
}

$params = self::validate_parameters(self::read_competency_framework_parameters(),
                                    array(
                                        'id' => $id,
                                    ));

$framework = api::read_framework($params['id']);
self::validate_context($framework->get_context());
$output = $PAGE->get_renderer('tool_lp');

class Test123
{
    protected static
        $prop1 = [
            'testA' => 123,
        ],
        $prop2 = [
            'testB' => 456,
        ],

    protected static
        $prop3 = array(
            'testA' => 123,
        ),
        $prop4 = array(
            'testB' => 456,
        ),

    protected static $prop5;
}

$foo = foo(
    function () {
        $foo->debug(
            $a,
            $b
        );

        if ($a) {
            $b = $a;
        }
    }
);

if (somethingIsTrue()) {
    ?>
    <div>
        <?php
        $someVar = doSomething(
            'foobar',
            true,
            'foo',
            'bar'
        );

        if (somethingElseIsTrue()) {
            doOneThing();
        } else {
            doTheOtherThing();
        }
        ?>
    </div>
    <?php
}

$foo = [
        new class() implements Bar {

            public static function foo(): string
            {
                return 'foo';
            }
        },
];

$foo = [
        function () {
            if ($foo) {
                return 'foo';
            }
        },
];

$foo
    ->bar(foo(function () {
    }), foo(function () {
    }));

echo 'foo';

class Test {

    public function a() {
        ?>a<?php
    }

    public function b(
        $a
    ) {
        echo $a;
    }
}

$foo = foo(
    function () {
        $foo->debug(
            $a,
            $b
    );

        if ($a) {
            $b = $a;
        }
    }
);

function test()
{
    $array = [];
    foreach ($array as $data) {
        [
            'key1' => $var1,
            'key2' => $var2,
        ] = $data;
        foreach ($var1 as $method) {
            echo $method . $var2;
        }
    }
}

switch ($a) {
    case 0:
        function () {
        };
    case 1:
        break;
}

class Test
{
    public function __construct()
    {
if (false) {
echo 0;
        }
    }
}

return [
    'veryLongKeySoIWantToMakeALineBreak'
        => 'veryLonValueSoIWantToMakeALineBreak',

    'someOtherKey' => [
        'someValue'
    ],

    'arrayWithArraysInThere' => [
        ['Value1', 'Value1']
    ],
];

switch ($sContext) {
    case 'SOMETHING':
    case 'CONSTANT':
        do_something();
        break;
    case 'GLOBAL':
    case 'GLOBAL1':
        do_something();
        // Fall through
    default:
    {
        do_something();
    }
}

public function foo()
{
    $foo('some
    long description', function () {
    });

    $foo('some
    long
    description', function () {
    });

    $foo(
'some long description', function () {
    });
}

    function foo()
    {
        $foo = array(
        );

        if ($foo) {
            echo 'foo';
        }

        return false;
    }

?>
<?php
    if (true) {
    }
    else if (true) {
    }

$a = 1;

/*
$a = array(
    */
);

echo ""
