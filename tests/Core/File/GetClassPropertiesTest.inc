<?php

/* testNotAClass */
interface NotAClass {}

/* testAnonClass */
$anon = new class() {};

/* testEnum */
enum NotAClassEither {}

/* testClassWithoutProperties */
class ClassWithoutProperties {}

/* testAbstractClass */
abstract class AbstractClass {}

/* testFinalClass */
final class FinalClass {}

/* testReadonlyClass */
readonly class ReadOnlyClass {}

/* testFinalReadonlyClass */
final readonly class FinalReadOnlyClass extends Foo {}

/* testReadonlyFinalClass */
readonly /*comment*/ final class ReadOnlyFinalClass {}

/* testAbstractReadonlyClass */
abstract readonly class AbstractReadOnlyClass {}

/* testReadonlyAbstractClass */
readonly
abstract
class ReadOnlyAbstractClass {}

/* testWithCommentsAndNewLines */
abstract
    /* comment */
    class ClassWithCommentsAndNewLines {}

/* testWithDocblockWithoutProperties */
/**
 * Class docblock.
 *
 * @package SomePackage
 *
 * @phpcs:disable Standard.Cat.SniffName -- Just because.
 */
class ClassWithDocblock {}

/* testParseErrorAbstractFinal */
final /* comment */

    abstract // Intentional parse error, class cannot both be final and abstract.

        class AbstractFinal {}
