<?php

namespace PHP_CodeSniffer\Tests\Core\Util\Sniffs\ObjectDeclarations;

class testFECNClass {}

/* testExtendedClass */
class testFECNExtendedClass extends testFECNClass {}

/* testNamespacedClass */
class testFECNNamespacedClass extends \PHP_CodeSniffer\Tests\Core\File\testFECNClass {}

/* testNonExtendedClass */
class testFECNNonExtendedClass {}

/* testInterface */
interface testFECNInterface {}

/* testInterfaceThatExtendsInterface */
interface testInterfaceThatExtendsInterface extends testFECNInterface{}

/* testInterfaceThatExtendsFQCNInterface */
interface testInterfaceThatExtendsFQCNInterface extends \PHP_CodeSniffer\Tests\Core\File\testFECNInterface{}

/* testNestedExtendedClass */
class testFECNNestedExtendedClass {
	public function someMethod() {
		/* testNestedExtendedAnonClass */
		$anon = new class extends testFECNAnonClass {};
	}
}

/* testClassThatExtendsAndImplements */
class testFECNClassThatExtendsAndImplements extends testFECNClass implements InterfaceA, InterfaceB {}

/* testClassThatImplementsAndExtends */
class testFECNClassThatImplementsAndExtends implements InterfaceA, InterfaceB extends testFECNClass {}

/* testExtendedClassWithComments */
class testFECNClassWithComments
	extends 
	/* a comment */
	\PHP_CodeSniffer\Tests /* comment */ \ /* comment */ testFECNClass /* comment */ {}

// Intentional parse error. Has to be the last test in the file.
/* testParseError */
class testFECNExtendedClass extends testFECNClass
