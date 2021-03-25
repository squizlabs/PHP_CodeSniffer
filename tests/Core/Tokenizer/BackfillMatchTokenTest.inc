<?php

function simpleMatch($x) {
    /* testMatchSimple */
    return match ($x) {
        0 => 'Zero',
        1 => 'One',
        2 => 'Two',
    };
}

function matchNoTrailingComma($bool) {
    /* testMatchNoTrailingComma */
    echo match ($bool) {
        true => "true\n",
        false => "false\n"
    };
}

function matchWithDefault($i) {
    /* testMatchWithDefault */
    return match ($i) {
        1 => 1,
        2 => 2,
        default => 'default',
    };
}

function matchExpressionInCondition($i) {
    /* testMatchExpressionInCondition */
    return match (true) {
        $i >= 50 => '50+',
        $i >= 40 => '40-50',
        $i >= 30 => '30-40',
        $i >= 20 => '20-30',
        $i >= 10 => '10-20',
        default => '0-10',
    };
}

function matchMultiCase($day) {
    /* testMatchMultiCase */
    return match ($day) {
        1, 7 => false,
        2, 3, 4, 5, 6 => true,
    };
}

function matchMultiCaseTrailingCommaInCase($bool) {
    /* testMatchMultiCaseTrailingCommaInCase */
    echo match ($bool) {
        false,
        0,
            => "false\n",
        true,
        1,
            => "true\n",
        default,
            => "not bool\n",
    };
}

assert((function () {
    /* testMatchInClosureNotLowercase */
    Match ('foo') {
        'foo', 'bar' => false,
        'baz' => 'a',
        default => 'b',
    };
})());

function matchInArrowFunction($x) {
    /* testMatchInArrowFunction */
    $fn = fn($x) => match(true) {
        1, 2, 3, 4, 5 => 'foo',
        default => 'bar',
    };
}

function arrowFunctionInMatchNoTrailingComma($x) {
    /* testArrowFunctionInMatchNoTrailingComma */
    return match ($x) {
        1 => fn($y) => callMe($y),
        default => fn($y) => callThem($y)
    };
}

/* testMatchInFunctionCallParamNotLowercase */
var_dump(MATCH ( 'foo' ) {
    'foo' => dump_and_return('foo'),
    'bar' => dump_and_return('bar'),
});

/* testMatchInMethodCallParam */
Test::usesValue(match(true) { true => $i });

/* testMatchDiscardResult */
match (1) {
    1 => print "Executed\n",
};

/* testMatchWithDuplicateConditionsWithComments */
echo match /*comment*/ ( $value /*comment*/ ) {
    // Comment.
    2, 1 => '2, 1',
    1 => 1,
    3 => 3,
    4 => 4,
    5 => 5,
};

/* testNestedMatchOuter */
$x = match ($y) {
    /* testNestedMatchInner */
    default => match ($z) { 1 => 1 },
};

/* testMatchInTernaryCondition */
$x = match ($test) { 1 => 'a', 2 => 'b' } ?
    /* testMatchInTernaryThen */ match ($test) { 1 => 'a', 2 => 'b' } :
    /* testMatchInTernaryElse */ match ($notTest) { 3 => 'a', 4 => 'b' };

/* testMatchInArrayValue */
$array = array(
    match ($test) { 1 => 'a', 2 => 'b' },
);

/* testMatchInArrayKey */
$array = [
    match ($test) { 1 => 'a', 2 => 'b' } => 'dynamic keys, woho!',
];

/* testMatchreturningArray */
$matcher = match ($x) {
    0 => array( 0 => 1, 'a' => 2, 'b' => 3 ),
    1 => [1, 2, 3],
    2 => array( 1, [1, 2, 3], 2, 3),
    3 => [ 0 => 1, 'a' => array(1, 2, 3), 'b' => 2, 3],
};

/* testSwitchContainingMatch */
switch ($something) {
    /* testMatchWithDefaultNestedInSwitchCase1 */
    case 'foo':
        $var = [1, 2, 3];
        $var = match ($i) {
            1 => 1,
            default => 'default',
        };
        continue 2;

    /* testMatchWithDefaultNestedInSwitchCase2 */
    case 'bar' ;
        $i = callMe($a, $b);
        return match ($i) {
            1 => 1,
            default => 'default',
        };

    /* testMatchWithDefaultNestedInSwitchDefault */
    default:
        echo 'something', match ($i) {
            1 => 1,
            default => 'default',
        };
        break;
}

/* testMatchContainingSwitch */
$x = match ($y) {
    5, 8 => function($z) {
        /* testSwitchNestedInMatch1 */
        switch($z) {
            case 'a':
                $var = [1, 2, 3];
                return 'a';
            /* testSwitchDefaultNestedInMatchCase */
            default:
                $i = callMe($a, $b);
                return 'default1';
        }
    },
    default => function($z) {
        /* testSwitchNestedInMatch2 */
        switch($z) {
            case 'a';
                $i = callMe($a, $b);
                return 'b';
            /* testSwitchDefaultNestedInMatchDefault */
            default;
                $var = [1, 2, 3];
                return 'default2';
        }
    }
};

/* testMatchNoCases */
// Intentional fatal error.
$x = match (true) {};

/* testMatchMultiDefault */
// Intentional fatal error.
echo match (1) {
    default => 'foo',
    1 => 'bar',
    2 => 'baz',
    default => 'qux',
};

/* testNoMatchStaticMethodCall */
$a = Foo::match($param);

/* testNoMatchClassConstantAccess */
$a = MyClass::MATCH;

/* testNoMatchClassConstantArrayAccessMixedCase */
$a = MyClass::Match[$a];

/* testNoMatchMethodCall */
$a = $obj->match($param);

/* testNoMatchMethodCallUpper */
$a = $obj??->MATCH()->chain($param);

/* testNoMatchPropertyAccess */
$a = $obj->match;

/* testNoMatchNamespacedFunctionCall */
// Intentional fatal error.
$a = MyNS\Sub\match($param);

/* testNoMatchNamespaceOperatorFunctionCall */
// Intentional fatal error.
$a = namespace\match($param);

interface MatchInterface {
    /* testNoMatchInterfaceMethodDeclaration */
    public static function match($param);
}

class MatchClass {
    /* testNoMatchClassConstantDeclarationLower */
    const match = 'a';

    /* testNoMatchClassMethodDeclaration */
    public static function match($param) {
        /* testNoMatchPropertyAssignment */
        $this->match = 'a';
    }
}

/* testNoMatchClassInstantiation */
$obj = new Match();

$anon = new class() {
    /* testNoMatchAnonClassMethodDeclaration */
    protected function maTCH($param) {
    }
};

/* testNoMatchClassDeclaration */
// Intentional fatal error. Match is now a reserved keyword.
class Match {}

/* testNoMatchInterfaceDeclaration */
// Intentional fatal error. Match is now a reserved keyword.
interface Match {}

/* testNoMatchTraitDeclaration */
// Intentional fatal error. Match is now a reserved keyword.
trait Match {}

/* testNoMatchConstantDeclaration */
// Intentional fatal error. Match is now a reserved keyword.
const MATCH = '1';

/* testNoMatchFunctionDeclaration */
// Intentional fatal error. Match is now a reserved keyword.
function match() {}

/* testNoMatchNamespaceDeclaration */
// Intentional fatal error. Match is now a reserved keyword.
namespace Match {}

/* testNoMatchExtendedClassDeclaration */
// Intentional fatal error. Match is now a reserved keyword.
class Foo extends Match {}

/* testNoMatchImplementedClassDeclaration */
// Intentional fatal error. Match is now a reserved keyword.
class Bar implements Match {}

/* testNoMatchInUseStatement */
// Intentional fatal error in PHP < 8. Match is now a reserved keyword.
use Match\me;

function brokenMatchNoCurlies($x) {
    /* testNoMatchMissingCurlies */
    // Intentional fatal error. New control structure is not supported without curly braces.
    return match ($x)
        0 => 'Zero',
        1 => 'One',
        2 => 'Two',
    ;
}

function brokenMatchAlternativeSyntax($x) {
    /* testNoMatchAlternativeSyntax */
    // Intentional fatal error. Alternative syntax is not supported.
    return match ($x) :
        0 => 'Zero',
        1 => 'One',
        2 => 'Two',
    endmatch;
}

/* testLiveCoding */
// Intentional parse error. This has to be the last test in the file.
echo match
