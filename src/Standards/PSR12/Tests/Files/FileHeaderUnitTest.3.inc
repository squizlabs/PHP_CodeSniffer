<?php

/**
 * This file contains an example of coding styles.
 */

namespace Vendor\Package;

declare(strict_types=1);

use Vendor\Package\{ClassA as A, ClassB, ClassC as C};
use Vendor\Package\SomeNamespace\ClassD as D;
use Vendor\Package\AnotherNamespace\ClassE as E;

use const Vendor\Package\{CONSTANT_A, CONSTANT_B, CONSTANT_C};
use const Another\Vendor\CONSTANT_D;

use function Vendor\Package\{functionA, functionB, functionC};
use function Another\Vendor\functionD;

/**
 * FooBar is an example class.
 */
class FooBar
{
    // ... additional PHP code ...
}
