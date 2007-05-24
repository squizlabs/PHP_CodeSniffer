<?php


// Valid class name.
class ValidCamelCaseClass extends MyClass {}


// Incorrect usage of camel case.
class invalidCamelCaseClass extends MyClass {}
class Invalid_Camel_Case_Class_With_Underscores implements MyClass {}


// All lowercase.
class invalidlowercaseclass extends MyClass {}
class invalid_lowercase_class_with_underscores extends MyClass {}


// All uppercase.
class VALIDUPPERCASECLASS extends MyClass {}
class INVALID_UPPERCASE_CLASS_WITH_UNDERSCORES extends MyClass {}


// Mix camel case with uppercase.
class ValidCamelCaseClassWithUPPERCASE extends MyClass {}


// Invalid usage of numeric characters.
class InvalidCamelCaseClassWith1Number extends MyClass {}
class InvalidCamelCaseClassWith12345Numbers extends MyClass {}
class 5InvalidCamelCaseClassStartingWithNumber extends MyClass {}
class InvalidCamelCaseClassEndingWithNumber5 extends MyClass {}
class 12345 extends MyClass {}


?>