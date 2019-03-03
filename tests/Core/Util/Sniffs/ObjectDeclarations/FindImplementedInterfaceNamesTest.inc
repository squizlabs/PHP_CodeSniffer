<?php

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\ObjectDeclarations;

interface testFIINInterface2 {}

/* testInterface */
interface testFIINInterface {}

/* testImplementedClass */
class testFIINImplementedClass implements testFIINInterface {}

/* testMultiImplementedClass */
class testFIINMultiImplementedClass implements testFIINInterface, testFIINInterface2 {}

/* testNamespacedClass */
class testFIINNamespacedClass implements \PHP_CodeSniffer\Tests\Core\File\testFIINInterface {}

/* testNonImplementedClass */
class testFIINNonImplementedClass {}

/* testClassThatExtendsAndImplements */
class testFECNClassThatExtendsAndImplements extends testFECNClass implements InterfaceA, \NameSpaced\Cat\InterfaceB {}

/* testClassThatImplementsAndExtends */
class testFECNClassThatImplementsAndExtends implements \InterfaceA, InterfaceB extends testFECNClass {}

/* testImplementedClassWithComments */
class testFIINNamespacedClass
	implements
		//phpcs:ignore Standard.Cat.Sniff -- because
		\PHP_CodeSniffer
		/* comment */
		\Tests\Core
		//phpcs:disable Standard.Cat.Sniff -- because
		\File         \			testFIINInterface {}
