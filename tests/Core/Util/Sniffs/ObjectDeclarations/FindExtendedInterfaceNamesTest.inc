<?php

/* testInterface */
interface testFEINInterface {}

/* testExtendedInterface */
interface testFEINExtendedInterface extends testFEINInterface {}

/* testMultiExtendedInterface */
interface testFEINMultiExtendedInterface extends testFEINInterface, testFEINInterface2 {}

/* testNamespacedInterface */
interface testFEINNamespacedInterface extends \PHP_CodeSniffer\Tests\Core\File\testFEINInterface {}

/* testMultiNamespacedInterface */
interface testFEINMultiNamespacedInterface extends \PHP_CodeSniffer\Tests\Core\File\testFEINInterface, \PHP_CodeSniffer\Tests\Core\File\testFEINInterface2 {}

/* testMultiExtendedInterfaceWithComment */
interface testFEINMultiExtendedInterface
	extends
		/* a comment */
		testFEINInterface,
		\PHP_CodeSniffer\Tests /* comment */ \Core \ File \testFEINInterface2,
		\testFEINInterface3 /* comment */
{
}
