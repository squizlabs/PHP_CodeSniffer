<?php


// Valid class name (matching filename).
class ClassFileNameUnitTest {}
interface ClassFileNameUnitTest {}
trait ClassFileNameUnitTest {}


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


?>