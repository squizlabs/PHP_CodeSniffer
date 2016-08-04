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

class PHP7 {

}
