<?php
/* @codingStandardsIgnoreFile */

namespace PHP_CodeSniffer\Tests\Core\File;

\PHP_CodeSniffer\Tests\Core\File\testFECNClass
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
