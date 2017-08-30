<?php
$str = <<<EOD
Example of string
spanning multiple lines
using heredoc syntax.
EOD;

echo <<<'EOT'
My name is "$name". I am printing some $foo->foo.
Now, I am printing some {$foo->bar[1]}.
This should not print a capital 'A': \x41
EOT;

// The following function has a simulated git conflict for testing.
// This is not a merge conflict - it is a valid test case.
// Please do not remove.
function test()
    {
        $arr = array(
            'a' => 'a'
<<<<<<< HEAD
            'b' => 'b'
=======
            'c' => 'c'
>>>>>>> master
        );
    }
