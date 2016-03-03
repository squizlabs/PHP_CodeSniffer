@codingStandardsChangeSetting Generic.WhiteSpace.ScopeIndent exact false
@codingStandardsChangeSetting Generic.WhiteSpace.ScopeIndent observePreviousPhpBlockIndentation true
<?php
$a = 10;
if ($condition) {
    $ok = true;
  $bad = true;
      $okNonExact = true;
    // Now we capture current indentation to be used in next php block
    // (because there are open scopes).
?>
<span>here we want some html</span>
<?php
    $ok = true;
  $bad = true;
      $okNonExact = true;
}
// Have closed the scope, next PHP blocks are free to have any indentation.
?>
<span>here we want some html</span>
    <?php require('ok.php'); ?>
        <?php require('ok.php'); ?>
            <?php require('ok.php'); ?>
<?php require('ok.php'); ?>
    <?php
    if ($condition) {
        $ok = true;
    ?>
    <span>here we want some html</span>
    <?php
    $bad = true;
      $bad = true;
        $ok = true;
        if ($condition) {
            $ok = true;
        }
    }
    // The close tag must be aligned, hence next one fails.
  ?>

<?php
// And, finally, it does not affect normal operations at all,
// adding some picked from ScopeIndentUnitTest.1.inc to confirm
// everything continues working ok.
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
