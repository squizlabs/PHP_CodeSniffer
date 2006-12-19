<?php


// Valid class name.
class ValidCamelCaseClass {}


// Incorrect usage of camel case.
class invalidCamelCaseClass {}
class Invalid_Camel_Case_Class_With_Underscores {}


// All lowercase.
class invalidlowercaseclass {}
class invalid_lowercase_class_with_underscores {}


// All uppercase.
class INVALIDUPPERCASECLASS {}
class INVALID_UPPERCASE_CLASS_WITH_UNDERSCORES {}


// Mix camel case with uppercase.
class InvalidCamelCaseClassWithUPPERCASE {}


// Invalid usage of numeric characters.
class InvalidCamelCaseClassWith1Number {}
class InvalidCamelCaseClassWith12345Numbers {}
class 5InvalidCamelCaseClassStartingWithNumber {}
class InvalidCamelCaseClassEndingWithNumber5 {}
class 12345 {}


?>