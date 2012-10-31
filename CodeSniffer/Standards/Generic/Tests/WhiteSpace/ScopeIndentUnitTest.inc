<?php

class Test
{
    function __construct()
    {
       $this->hello(); // error here
    }

   function hello() // error here
    { // no error here as brackets can be put anywhere in the pear standard
        echo 'hello';
    }

    function hello2()
    {
       if (TRUE) { // error here
            echo 'hello'; // no error here as its more than 4 spaces.
        } else {
        echo 'bye'; // error here
        }

        while (TRUE) {
           echo 'hello'; // error here
         }// no error here as its handled by another test.

       do { // error here
         echo 'hello'; // error here
       } while (TRUE); // no error here as its aligned with the do.
   }// no error here as its handled by another test.

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

    /*
    *
    *
    *
   */

    /**
   */

    /*
      This comment has a newline in it.

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
    string that spans nultiple lines
    with indenting.
    ';
}

if ($condition) {
    echo 'This is a long
          string that spans nultiple lines
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
