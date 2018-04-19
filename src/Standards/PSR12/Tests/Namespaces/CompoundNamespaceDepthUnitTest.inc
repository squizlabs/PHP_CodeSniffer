<?php
use Vendor\Package\SomeNamespace\{
    SubnamespaceOne\ClassA,
    SubnamespaceOne\ClassB,
    SubnamespaceTwo\ClassY,
    ClassZ,
};

use Vendor\Package\SomeNamespace\{
    SubnamespaceOne\AnotherNamespace\ClassA,
    SubnamespaceOne\ClassB,
    ClassZ,
};

use Vendor\Package\SomeNamespace\{
    SubnamespaceOne /* comment */
    \AnotherNamespace // comment
    \ClassA,
    SubnamespaceOne
    \AnotherNamespace
    \ClassB,
    ClassZ,
};

// phpcs:set PSR12.Namespaces.CompoundNamespaceDepth maxDepth 3

use Vendor\Package\SomeNamespace\{
    SubnamespaceOne\AnotherNamespace\ClassA,
    SubnamespaceOne\ClassB,
    ClassZ,
};
