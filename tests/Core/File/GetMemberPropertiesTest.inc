<?php

class TestMemberProperties
{
    /* testVar */
    var $varA = true;

    /* testVarType */
    var ?int $varA = true;

    /* testPublic */
    public $varB = true;

    /* testPublicType */
    public string $varB = true;

    /* testProtected */
    protected $varC = true;

    /* testProtectedType */
    protected bool $varC = true;

    /* testPrivate */
    private $varD = true;

    /* testPrivateType */
    private array $varD = true;

    /* testStatic */
    static $varE = true;

    /* testStaticType */
    static ?string $varE = true;

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

    public float
    /* testGroupType 1 */
    $x,
    /* testGroupType 2 */
    $y;

    public static ?string
    /* testGroupNullableType 1 */
    $x = null,
    /* testGroupNullableType 2 */
    $y = null;

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

    /* testMessyNullableType */
    public /* comment
         */ ? //comment
        array $foo = [];

    /* testNamespaceType */
    public \MyNamespace\MyClass $foo;

    /* testNullableNamespaceType 1 */
    private ?ClassName $nullableClassType;

    /* testNullableNamespaceType 2 */
    protected ?Folder\ClassName $nullableClassType2;

    /* testMultilineNamespaceType */
    public \MyNamespace /** comment *\/ comment */
           \MyClass /* comment */
           \Foo $foo;

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

class PHP8Mixed {
    /* testPHP8MixedTypeHint */
    public static miXed $mixed;

    /* testPHP8MixedTypeHintNullable */
    // Intentional fatal error - nullability is not allowed with mixed, but that's not the concern of the method.
    private ?mixed $nullableMixed;
}
