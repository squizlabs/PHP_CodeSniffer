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

class PHP7 {

}
