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

// Parse error in PHP 8.0+, but will be detected correctly cross-version anyway.
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
