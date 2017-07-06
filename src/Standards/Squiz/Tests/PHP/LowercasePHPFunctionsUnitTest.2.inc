<?php
namespace A {
    use function \Max as Min;
    use function Min as Is_Int;

    function Count($arr) {
        return \Count($arr);
    }

    function Max($a, $b) {
        return Min($a, $b);
    }

    $arr = \Range(1, 10);
    $global = \Count($arr);
    $local = Count($arr);
    $min = Is_Int($global, $local);
    $isInt = \is_INT($global);
}

namespace B {
    use function A\Count as CounT;
    use function A\Max;

    $arr = Range(1, 20);
    $a = CounT($arr);
    $global = \Count($arr);
    $max = Max($a, 2 * $a);
}
