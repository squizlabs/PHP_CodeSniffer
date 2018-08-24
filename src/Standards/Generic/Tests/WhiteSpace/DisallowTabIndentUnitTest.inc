<?php

class ExampleClass
{
	function exampleFunction()	{}

}

	$o = <<<EOF
	this is some text
	this is some text
EOF;

    $correctVar = true;
	$correctVar = false;

class MyClass
{
    /**
	 * Short	description.
	 *
	 * @return	void
	 */
    public function 	myFunction() {	}
}

$str = 'hello
		there';

$foo = array(
	'Česká republika'   => 'Czech republic',
	'România'			=> 'Romania',
	'Magyarország'	   	=> 'Hungary',
);

$var = "$hello	$there";

?>
<html>
    <head>
		<title>Foo</title>
	</head>
	<body>
		<div>
    		<div>
				<div>
	    		</div>
    	    </div>
        </div>
    </body>
</html>

<?php
		    // Issue #1404
        	// This is a line with mixed tabs and spaces.
	    	echo 'And here is another line with mixed tabs and spaces.';
	        echo 'And another one.';
    	    echo 'And another one.';
        	echo 'And another one.';

/**
 * This doc block should be fine and should be ignored for metrics.
 */

/*
 This multi-line comment should be fine and should be ignored for metrics.

 Another line.
 */

    /**
     * This PHP DocBlock is indented with spaces; metrics should say spaces.
     */

    /*
     * This multi-line comment is indented with spaces; metrics should say spaces.
     */

	/**
	 * This PHP DocBlock is indented with tabs + one space and should be fixed; metrics should say tabs.
	 *
	 * @var int $x
	 */

	/*
	 * This multi-line comment is indented with spaces and should be fixed.
	 */

	/*
	 This multi-line comment is indented with spaces and should be fixed.

	 Another line.
	 */
