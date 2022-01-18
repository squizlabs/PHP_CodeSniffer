<?php


// Valid class name (matching filename).
class ClassFileNameUnitTest {}
interface ClassFileNameUnitTest {}
trait ClassFileNameUnitTest {}
enum ClassFileNameUnitTest {}
enum ClassFileNameUnitTest: int {}

// Invalid filename matching class name (case sensitive).
class classFileNameUnitTest {}
class classfilenameunittest {}
class CLASSFILENAMEUNITTEST {}
interface classFileNameUnitTest {}
interface classfilenameunittest {}
interface CLASSFILENAMEUNITTEST {}
trait classFileNameUnitTest {}
trait classfilenameunittest {}
trait CLASSFILENAMEUNITTEST {}
enum classFileNameUnitTest {}
enum classfilenameunittest {}
enum CLASSFILENAMEUNITTEST {}


// Invalid non-filename matching class names.
class CompletelyWrongClassName {}
class ClassFileNameUnitTestExtra {}
class ClassFileNameUnitTestInc {}
class ExtraClassFileNameUnitTest {}
interface CompletelyWrongClassName {}
interface ClassFileNameUnitTestExtra {}
interface ClassFileNameUnitTestInc {}
interface ExtraClassFileNameUnitTest {}
trait CompletelyWrongClassName {}
trait ClassFileNameUnitTestExtra {}
trait ClassFileNameUnitTestInc {}
trait ExtraClassFileNameUnitTest {}
enum CompletelyWrongClassName {}
enum ClassFileNameUnitTestExtra {}
enum ClassFileNameUnitTestInc {}
enum ExtraClassFileNameUnitTest {}


?>
