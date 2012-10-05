<?php
class ClassName extends ParentClass implements \ArrayAccess,  \Countable
{
    // constants, properties, methods
}

class ClassName extends ParentClass,AnotherParentClass implements \ArrayAccess,\Countable {
    // constants, properties, methods
}

class ClassName
extends ParentClass
implements \ArrayAccess, \Countable
{
    // constants, properties, methods
    
}

class ClassName extends  ParentClass  implements
   \ArrayAccess,
        \Foo\Bar\Countable,
    \Serializable {
    // constants, properties, methods

}

class ClassName extends ParentClass  implements \ArrayAccess,
    \Countable,
    \Serializable
{
    // constants, properties, methods
}

class ClassName extends ParentClass  implements
    \ArrayAccess, \Countable, \Foo\Serializable
{
    // constants, properties, methods
}

// Different indent
if ($foo) {
    class ClassName extends ParentClass implements
        \ArrayAccess,
    \Countable,
            \Serializable
    {
        // constants, properties, methods
    }
}

class Foo extends \Foo\Bar\Object
{
}

class ClassName extends ParentClass implements
    \Foo\Bar\Countable,
    \Serializable
{
    // constants, properties, methods
}

class ClassName extends ParentClass implements
    \Foo\Bar\Countable ,
    \Serializable
{
    // constants, properties, methods
}

?>
