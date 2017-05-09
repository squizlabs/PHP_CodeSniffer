<?php


// Valid class name (matching filename).
class ClassFileNameUnitTest {}


// Invalid filename matching class name (case sensitive).
class classFileNameUnitTest {}
class classfilenameunittest {}
class CLASSFILENAMEUNITTEST {}


// Invalid non-filename matching class names.
class CompletelyWrongClassName {}
class ClassFileNameUnitTestExtra {}
class ClassFileNameUnitTestInc {}
class ExtraClassFileNameUnitTest {}


?>