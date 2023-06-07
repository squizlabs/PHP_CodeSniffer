<?php

class Foo
{
    /* testReadonlyProperty */
    readonly int $readonlyProperty;
    /* testVarReadonlyProperty */
    var readonly int $varReadonlyProperty;
    /* testReadonlyVarProperty */
    readonly var int $testReadonlyVarProperty;
    /* testStaticReadonlyProperty */
    static readonly int $staticReadonlyProperty;
    /* testReadonlyStaticProperty */
    readonly static int $readonlyStaticProperty;
    /* testConstReadonlyProperty */
    public readonly const MYCONSTANT = 'foo';
    /* testReadonlyPropertyWithoutType */
    readonly $propertyWithoutType;
    /* testPublicReadonlyProperty */
    public readonly int $publicReadonlyProperty;
    /* testProtectedReadonlyProperty */
    protected readonly int $protectedReadonlyProperty;
    /* testPrivateReadonlyProperty */
    private readonly int $privateReadonlyProperty;
    /* testPublicReadonlyPropertyWithReadonlyFirst */
    readonly public int $publicReadonlyProperty;
    /* testProtectedReadonlyPropertyWithReadonlyFirst */
    readonly protected int $protectedReadonlyProperty;
    /* testPrivateReadonlyPropertyWithReadonlyFirst */
    readonly private int $privateReadonlyProperty;
    /* testReadonlyWithCommentsInDeclaration */
    private /* Comment */ readonly /* Comment */ int /* Comment */ $readonlyPropertyWithCommentsInDeclaration;
    /* testReadonlyWithNullableProperty */
    private readonly ?int $nullableProperty;
    /* testReadonlyNullablePropertyWithUnionTypeHintAndNullFirst */
    private readonly null|int $nullablePropertyWithUnionTypeHintAndNullFirst;
    /* testReadonlyNullablePropertyWithUnionTypeHintAndNullLast */
    private readonly int|null $nullablePropertyWithUnionTypeHintAndNullLast;
    /* testReadonlyPropertyWithArrayTypeHint */
    private readonly array $arrayProperty;
    /* testReadonlyPropertyWithSelfTypeHint */
    private readonly self $selfProperty;
    /* testReadonlyPropertyWithParentTypeHint */
    private readonly parent $parentProperty;
    /* testReadonlyPropertyWithFullyQualifiedTypeHint */
    private readonly \stdClass $propertyWithFullyQualifiedTypeHint;

    /* testReadonlyIsCaseInsensitive */
    public ReAdOnLy string $caseInsensitiveProperty;

    /* testReadonlyConstructorPropertyPromotion */
    public function __construct(private readonly bool $constructorPropertyPromotion)
    {
    }

    /* testReadonlyConstructorPropertyPromotionWithReference */
    public function __construct(private ReadOnly bool &$constructorPropertyPromotion) {}
}

$anonymousClass = new class () {
    /* testReadonlyPropertyInAnonymousClass */
    public readonly int $property;
};

class ClassName {
    /* testReadonlyUsedAsClassConstantName */
    const READONLY = 'readonly';

    /* testReadonlyUsedAsMethodName */
    public function readonly() {
        // Do something.

        /* testReadonlyUsedAsPropertyName */
        $this->readonly = 'foo';

        /* testReadonlyPropertyInTernaryOperator */
        $isReadonly = $this->readonly ? true : false;
    }
}

/* testReadonlyUsedAsFunctionName */
function readonly()
{
}

/* testReadonlyUsedAsNamespaceName */
namespace Readonly;
/* testReadonlyUsedAsPartOfNamespaceName */
namespace My\Readonly\Collection;
/* testReadonlyAsFunctionCall */
$var = readonly($a, $b);
/* testClassConstantFetchWithReadonlyAsConstantName */
echo ClassName::READONLY;

/* testReadonlyUsedAsFunctionCallWithSpaceBetweenKeywordAndParens */
$var = readonly /* comment */ ();

// These test cases are inspired by
// https://github.com/php/php-src/commit/08b75395838b4b42a41e3c70684fa6c6b113eee0
class Dnf
{
    /* testDNFPropertyReadonlyPublicABC */
    readonly public A|(B&C) $a;
    /* testDNFPropertyReadonlyPublicBCA */
    readonly public (B&C)|A $b;

    /* testDNFPropertyReadonlyProtectedABC */
    readonly protected A|(B&C) $c;
    /* testDNFPropertyReadonlyProtectedBCA */
    readonly protected (B&C)|A $d;

    /* testDNFPropertyReadonlyPrivateABC */
    readonly private A|(B&C) $e;
    /* testDNFPropertyReadonlyPrivateBCA */
    readonly private (B&C)|A $f;

    /* testDNFPropertyReadonlyABC */
    readonly A|(B&C) $g;
    /* testDNFPropertyReadonlyBCA */
    readonly (B&C)|A $h;

    /* testDNFPropertyPublicReadonlyABC */
    public readonly A|(B&C) $i;
    /* testDNFPropertyPublicReadonlyBCA */
    public readonly (B&C)|A $j;

    /* testDNFPropertyProtectedReadonlyABC */
    protected readonly A|(B&C) $k;
    /* testDNFPropertyProtectedReadonlyBCA */
    protected readonly (B&C)|A $l;

    /* testDNFPropertyPrivateReadonlyABC */
    private readonly A|(B&C) $m;
    /* testDNFPropertyPrivateReadonlyBCA */
    private readonly (B&C)|A $n;

    /* testDNFPropertyPrivateReadonlyB_space_CA */
    private readonly (B & C)|A $o;
    /* testDNFPropertyPrivateReadonlyBC_space_A */
    private readonly (B&C) | A $p;
    /* testDNFPropertyPrivateReadonlyB_space_C_space_A */
    private readonly (B & C) | A $q;

    /* testDNFMethodAB */
    public function readonly (A&B $param): void {}

    public function __construct(
        /* testDNFPropertyPromotionABC */
        private readonly A|(B&C) $a1,
        /* testDNFPropertyPromotionBCA */
        private readonly (B&C)|A $b1,
    ) {}
}

/* testParseErrorLiveCoding */
// This must be the last test in the file.
readonly
