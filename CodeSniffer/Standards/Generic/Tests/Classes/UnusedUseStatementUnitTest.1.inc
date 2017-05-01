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
use FooNamespace\OnlyUsedInParamTag;
use FooNamespace\OnlyUsedInReturnTag;
use FooNamespace\ImportedAnnotationThatIsUsed;
use FooNamespace\AnotherUsedAnnotation;
use FooNamespace\MentionedInDocstringButShouldNotBeUsed;
use Qux\UsedInBaz;
use MyNamespace\Depth\AlsoUsedInBaz;
use Foo\Used; use Foo\UnusedOnSameLine;
use Bar\FirstUnused; use Bar\SecondUnusedOnSameLine;
use somenamespace\orother\{ClassA as A, ClassB, ClassC as C, ClassD};
use somenamespace\orother\{This, Line, Is as Completely, Unused};
use function somenamespace\orother\{fn_a, fn_b, fn_c};
use function somenamespace\orother\{fn_d, fn_e, fn_f};
use const somenamespace\orother\{ConstA, ConstB, ConstC};

/**
 * Bla.
 *
 * @AnotherUsedAnnotation()
 */
class Pum {
  use TraitTest;
  use Test\AnotherTrait;

  /**
   * Description.
   *
   * This is a mention of MentionedInDocstringButShouldNotBeUsed but it isn't as
   * part of any sort of syntactically meaningful PHPDoc construct so the Sniff
   * should throw an error for MentionedInDocstringButShouldNotBeUsed, not just a
   * warning.
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

  /**
   * @param OnlyUsedInParamTag $foo
   * @return OnlyUsedInReturnTag
   */
  protected function test6($foo) {

  }

  /**
   * @ImportedAnnotationThatIsUsed(qux='baz')
   */
  protected function test7() {

  }

}

new Used;
new C;
new ClassD;
fn_b(fn_d(fn_e(ConstB)));

namespace Baz;

// These references don't work because `use`s are scoped to the namespace block in
// which they appear; the `use`s from the top of the file aren't in effect here.
$x = new UsedInBaz;
$y = new AlsoUsedInBaz;
