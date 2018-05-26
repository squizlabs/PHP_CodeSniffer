<?php
namespace MyProject;

use Single\ClassA as A;
use Grouped\Classes\{ClassB, ClassC as C};
use const Grouped\Constants\{D, E};
use Single\ClassF;
use Grouped\Mixed\ {
    ClassG,
    const ConstH,
    function func_i,
    ClassJ as J
};
use function Grouped\Functions\ { func_k };

// Some examples from the original RFC.
use function foo\math\{ sin, cos, cosh };
use const foo\math\{ PI, E, GAMMA, GOLDEN_RATIO };
use foo\math\{ Math, const PI, function sin, function cos, function cosh };

use Grouped\TrailingComma\ {
    ClassL,
    const ConstM,
    function func_n,
    ClassO as O,
};

use function foo\math\trailingcomma\no\whitespace\{sin,cos,cosh,};

use function foo\math\multipleonnewline\{
	sin, cos, /* comment */ cosh
};

// phpcs:disable Standard.Cat.Sniff -- for reasons.
use Grouped\TrailingCommaWithCommentsAndAnnotations\ {
    // A comment.
    ClassP,
    /* Another comment. */
    const ConstQ, // A trailing comment.

    function func_r, // phpcs:ignore Standard.Cat.Sniff -- for reasons.
    // phpcs:ignore Standard.Cat.Sniff -- for reasons.
    ClassS as S,
    // Some other comment.
};

// phpcs:enable
