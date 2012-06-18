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
        \Countable,
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
    \ArrayAccess, \Countable, \Serializable
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

?>