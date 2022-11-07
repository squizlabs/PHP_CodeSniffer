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


// Usage of numeric characters.
class ValidCamelCaseClassWith1Number extends MyClass {}
class ValidCamelCaseClassWith12345Numbers extends MyClass {}
class 5InvalidCamelCaseClassStartingWithNumber extends MyClass {}
class ValidCamelCaseClassEndingWithNumber5 extends MyClass {}
class 12345 extends MyClass {}

class Testing{}

class Base
{
    protected $anonymous;

    public function __construct()
    {
        $this->anonymous = new class extends ArrayObject
        {
            public function __construct()
            {
                parent::__construct(['a' => 1, 'b' => 2]);
            }
        };
    }
}

// Valid interface name.
interface ValidCamelCaseClass extends MyClass {}


// Incorrect usage of camel case.
interface invalidCamelCaseClass extends MyClass {}
interface Invalid_Camel_Case_Class_With_Underscores implements MyClass {}


// All lowercase.
interface invalidlowercaseclass extends MyClass {}
interface invalid_lowercase_class_with_underscores extends MyClass {}


// All uppercase.
interface VALIDUPPERCASECLASS extends MyClass {}
interface INVALID_UPPERCASE_CLASS_WITH_UNDERSCORES extends MyClass {}


// Mix camel case with uppercase.
interface ValidCamelCaseClassWithUPPERCASE extends MyClass {}


// Usage of numeric characters.
interface ValidCamelCaseClassWith1Number extends MyClass {}
interface ValidCamelCaseClassWith12345Numbers extends MyClass {}
interface 5InvalidCamelCaseClassStartingWithNumber extends MyClass {}
interface ValidCamelCaseClassEndingWithNumber5 extends MyClass {}
interface 12345 extends MyClass {}

interface Testing{}

interface Base
{
    protected $anonymous;

    public function __construct();
}


// Valid trait name.
trait ValidCamelCaseClass extends MyClass {}


// Incorrect usage of camel case.
trait invalidCamelCaseClass extends MyClass {}
trait Invalid_Camel_Case_Class_With_Underscores implements MyClass {}


// All lowercase.
trait invalidlowercaseclass extends MyClass {}
trait invalid_lowercase_class_with_underscores extends MyClass {}


// All uppercase.
trait VALIDUPPERCASECLASS extends MyClass {}
trait INVALID_UPPERCASE_CLASS_WITH_UNDERSCORES extends MyClass {}


// Mix camel case with uppercase.
trait ValidCamelCaseClassWithUPPERCASE extends MyClass {}


// Usage of numeric characters.
trait ValidCamelCaseClassWith1Number extends MyClass {}
trait ValidCamelCaseClassWith12345Numbers extends MyClass {}
trait 5InvalidCamelCaseClassStartingWithNumber extends MyClass {}
trait ValidCamelCaseClassEndingWithNumber5 extends MyClass {}
trait 12345 extends MyClass {}

trait Testing{}

trait Base
{
    protected $anonymous;

    public function __construct()
    {
        $this->anonymous = new class extends ArrayObject
        {
            public function __construct()
            {
                parent::__construct(['a' => 1, 'b' => 2]);
            }
        };
    }
}

// Valid enum name.
enum ValidCamelCaseClass: string {}


// Incorrect usage of camel case.
enum invalidCamelCaseClass {}
enum Invalid_Camel_Case_Class_With_Underscores {}


// All lowercase.
enum invalidlowercaseclass: INT {}
enum invalid_lowercase_class_with_underscores {}


// All uppercase.
enum VALIDUPPERCASECLASS: int {}
enum INVALID_UPPERCASE_CLASS_WITH_UNDERSCORES {}


// Mix camel case with uppercase.
enum ValidCamelCaseClassWithUPPERCASE : string {}


// Usage of numeric characters.
enum ValidCamelCaseClassWith1Number {}
enum ValidCamelCaseClassWith12345Numbers : string {}
enum ValidCamelCaseClassEndingWithNumber5 {}

enum Testing{}

enum Base
{
    public function __construct()
    {
        $this->anonymous = new class extends ArrayObject
        {
            public function __construct()
            {
                parent::__construct(['a' => 1, 'b' => 2]);
            }
        };
    }
}

if ( class_exists( Test :: class ) ) {}
if ( class_exists( Test2 ::class ) ) {}

$foo = new class(
    new class implements Countable {
    }
) extends DateTime {
};
