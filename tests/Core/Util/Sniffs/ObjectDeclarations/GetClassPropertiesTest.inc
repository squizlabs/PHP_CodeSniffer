<?php

/* testNotAClass */
interface NotAClass {}

/* testClassWithoutProperties */
class ClassWithoutProperties {}

/* testAbstractClass */
abstract class AbstractClass {}

/* testFinalClass */
final class AbstractClass {}

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

/* testWithPHPCSAnnotation */
final
	// phpcs:ignore Standard.Cat.SniffName -- Just because.
	class ClassWithCommentsAndNewLines {}

/* testWithDocblockWithWeirdlyPlacedProperty */
final

/**
 * Class docblock.
 *
 * @package SomePackage
 *
 * @phpcs:disable Standard.Cat.SniffName -- Just because.
 */
class ClassWithDocblockAndProperty {}
