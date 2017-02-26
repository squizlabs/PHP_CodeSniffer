<?php

namespace MyNamespace\Depth;

use Foo\Bar;
use Bar\Fail;
use Test\Bar\Thing;
use Thing\Fail\ActuallyUsed;
use Test\TraitTest;
use Thing\NotUsed;
use Test\AnotherTrait;
use Thing\SomeName as OtherName;
use Thing\DifferentName as UsedOtherName;
use Another\UnusedUse;
use Example\MyUrlHelper;
use MyNamespace\Depth\UnusedSameNamespace;
use /* I like weird comment placements */ MyNamespace\Depth\AnotherUnusedSameNamespace /* Oh yes I do */;
use MyNamespace\Depth\SomeClass as CoreSomeClass;
use SomeNamespace\someFunction;
use const BlaBlaNamespace\SOME_UNUSED_CONSTANT;
use const BlubBlubNamespace\CONSTANT_THAT_IS_USED;

/**
 * Bla.
 */
class Pum {
  use TraitTest;
  use Test\AnotherTrait;

  /**
   * Description.
   */
  protected function test(ActuallyUsed $x, UsedOtherName $y) {
    someFunction();
  }

  /**
   * Description.
   */
  protected function test2(\Thing\NotUsed $x) {
    echo CONSTANT_THAT_IS_USED;
  }

  /**
   * PHP is not case sensitive.
   */
  protected function test3(MyURLHelper $x) {

  }

  /**
   * Don't need to use classes in the same namespace.
   */
  protected function test4(UnusedSameNamespace $x, AnotherUnusedSameNamespace $y) {

  }

  /**
   * Renamed class from same namespace.
   */
  protected function test5(CoreSomeClass $x) {

  }

}
