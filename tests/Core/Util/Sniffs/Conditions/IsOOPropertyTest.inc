<?php

/* testGlobalVar */
$var = false;

/* testFunctionParameter */
function something($var = false)
{
    /* testFunctionLocalVar */
    $var = false;
}

class MyClass {
    /* testClassPropPublic */
    public $var = true;

    /* testClassPropVar */
    var $varPHP4style = true;

    /* testClassPropStaticProtected */
    static protected $staticProtected = true;

    /* testMethodParameter */
    public function something($var = false)
    {
        /* testMethodLocalVar */
        $var = false;
    }
}

$a = new class {
    /* testAnonClassPropPrivate */
    private $var = true;

    /* testAnonMethodParameter */
    public function something($var = false)
    {
        /* testAnonMethodLocalVar */
        $var = false;
    }
};

interface MyInterface {
    // Intentional parse error. Properties are not allowed in interfaces.
    /* testInterfaceProp */
    public $var = false;

    /* testInterfaceMethodParameter */
    public function something($var = false);
}

trait MyTrait {
    /* testTraitProp */
    public $var = true;

    /* testTraitMethodParameter */
    function something($var = false) {}
}

// Multi-property declarations.
class MultiPropClass {
    /* testClassMultiProp1 */
    public $varA = true,
        /* testClassMultiProp2 */
        $varB = false,
        /* testClassMultiProp3 */
        $varC = 'string';
}

/* testGlobalVarObj */
$util->setLogger(
    new class {
        /* testNestedAnonClassProp */
        private $varName  = 'hello';
});

if ( has_filter( 'comments_open' ) === false ) {
    add_filter( 'comments_open', new class {
        /* testDoubleNestedAnonClassProp */
        public $year = 2017; // Ok.

        /* testDoubleNestedAnonClassMethodParameter */
        public function __construct( $open, $post_id ) {
            /* testDoubleNestedAnonClassMethodLocalVar */
            global $page;
        }
    /* testFunctionCallParameter */
    }, $priority, 2 );
}
