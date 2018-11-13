<?php
/* phpcs:ignoreFile */

class TestMemberProperties
{
    /* testVar */
    var $varA = true;

    /* testPublic */
    public $varB = true;

    /* testProtected */
    protected $varC = true;

    /* testPrivate */
    private $varD = true;

    /* testStatic */
    static $varE = true;

    /* testStaticVar */
    static var $varF = true;

    /* testVarStatic */
    var static $varG = true;

    /* testPublicStatic */
    public static $varH = true;

    /* testProtectedStatic */
    static protected $varI = true;

    /* testPrivateStatic */
    private static $varJ = true;

    /* testNoPrefix */
    $varK = true;

    /* testPublicStaticWithDocblock */
    /**
     * Comment here.
     *
     * @phpcs:ignore Standard.Category.Sniff -- because
     * @var boolean
     */
    public static $varH = true;

    /* testProtectedStaticWithDocblock */
    /**
     * Comment here.
     *
     * @phpcs:ignore Standard.Category.Sniff -- because
     * @var boolean
     */
    static protected $varI = true;

    /* testPrivateStaticWithDocblock */
    /**
     * Comment here.
     *
     * @phpcs:ignore Standard.Category.Sniff -- because
     * @var boolean
     */
    private static $varJ = true;


    protected static
        /* testGroupProtectedStatic 1 */
        $varL,
        /* testGroupProtectedStatic 2 */
        $varM,
        /* testGroupProtectedStatic 3 */
        $varN;

    private
        /* testGroupPrivate 1 */
        $varO = true,
        /* testGroupPrivate 2 */
        $varP = array( 'a' => 'a', 'b' => 'b' ),
        /* testGroupPrivate 3 */
        $varQ = 'string',
        /* testGroupPrivate 4 */
        $varR = 123,
        /* testGroupPrivate 5 */
        $varS = ONE / self::THREE,
        /* testGroupPrivate 6 */
        $varT = [
			'a' => 'a',
			'b' => 'b'
		],
        /* testGroupPrivate 7 */
        $varU = __DIR__ . "/base";


    /* testMethodParam */
    public function methodName($param) {
        /* testImportedGlobal */
        global $importedGlobal = true;

        /* testLocalVariable */
        $localVariable = true;
    }

    /* testPropertyAfterMethod */
    private static $varV = true;

}

interface Base
{
    /* testInterfaceProperty */
    protected $anonymous;
}

/* testGlobalVariable */
$globalVariable = true;

/* testNotAVariable */
return;

$a = ( $foo == $bar ? new stdClass() :
	new class() {
		/* testNestedProperty 1 */
		public $var = true;

		/* testNestedMethodParam 1 */
		public function something($var = false) {}
	}
);

function_call( 'param', new class {
	/* testNestedProperty 2 */
	public $year = 2017;

	/* testNestedMethodParam 2 */
	public function __construct( $open, $post_id ) {}
}, 10, 2 );
