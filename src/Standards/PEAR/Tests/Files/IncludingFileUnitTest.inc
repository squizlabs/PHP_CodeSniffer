<?php

// These are statements and should not have brackets.
require_once('blank.inc');
require('blank.inc');

$test = true;

// Conditionally including a class file: use include_once
if ($test) {
    require_once 'blank.inc';
    require 'blank.inc';
}

// Unconditionally including a class file: use require_once
include_once 'blank.inc';
include 'blank.inc';


// These are ok
if ($test) {
    include_once 'blank.inc';
    include 'blank.inc';
}

require_once 'blank.inc';
require 'blank.inc';

?>
<pre>
Some content goes here.
<?php
include_once 'blank.inc';
include 'blank.inc';
require_once 'blank.inc';
require 'blank.inc';
?>
</pre>
<?php

if (include_once 'blank.inc') {
    $var = include_once 'blank.inc';
    if ($var === true) {
    }
}

if (require_once 'blank.inc') {
    $var = require_once 'blank.inc';
    if ($var === true) {
    }
}

function get_some_value()
{
    include_once 'blank.inc';
    include 'blank.inc';

    // These are ok
    if ($test) {
        include_once 'blank.inc';
        include 'blank.inc';
    }

    require_once 'blank.inc';
    require 'blank.inc';

    ?>
    <pre>
    Some content goes here.
    <?php
    include_once 'blank.inc';
    include 'blank.inc';
    require_once 'blank.inc';
    require 'blank.inc';
    ?>
    </pre>
    <?php

    if (include_once 'blank.inc') {
        $var = include_once 'blank.inc';
        if ($var === true) {
        }
    }
    
    if (require_once 'blank.inc') {
        $var = require_once 'blank.inc';
        if ($var === true) {
        }
    }
}


$var = include_once 'blank.inc';
if ($var == false) {

}

require_once ('blank.inc');
include_once ('blank.inc');
