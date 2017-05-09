<?php
/* @codingStandardsIgnoreFile */

namespace PHP_CodeSniffer\Tests\Core\File;

class testFECNClass {}

/* testExtendedClass */
class testFECNExtendedClass extends testFECNClass {}

/* testNamespacedClass */
class testFECNNamespacedClass extends \PHP_CodeSniffer\Tests\Core\File\testFECNClass {}

/* testNonExtendedClass */
class testFECNNonExtendedClass {}

/* testInterface */
interface testFECNInterface {}
