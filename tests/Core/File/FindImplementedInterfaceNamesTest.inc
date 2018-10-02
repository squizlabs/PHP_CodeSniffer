<?php
/* @codingStandardsIgnoreFile */

namespace PHP_CodeSniffer\Tests\Core\File;

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
