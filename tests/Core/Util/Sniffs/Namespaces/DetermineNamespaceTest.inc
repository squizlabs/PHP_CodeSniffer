<?php

/* Global namespace */
echo namespace\fn();

/* testNoNamespace */
echo 0;

class Foo {
    function test() {
        /* testNoNamespaceNested */
        echo 0;
    }
}

/* Non-scoped named namespace 1 */
namespace Vendor\Package\Baz;

echo namespace\fn();

/* testNonScopedNamedNamespace1 */
echo 0;

$array = [ /*long array */ ];

class Foo {
    function test() {
        /* testNonScopedNamedNamespace1Nested */
        echo 0;
    }
}


/* Scoped global namespace */
namespace {
    echo namespace\fn();

    /* testGlobalNamespaceScoped */
    echo 0;

    class Bar {
        function test() {
            /* testGlobalNamespaceScopedNested */
            echo 0;
        }
    }
}

echo namespace\fn();

/* testNoNamespaceAfterScoped */
echo 0;

class Baz {
    function test() {
        /* testNoNamespaceNestedAfterScoped */
        echo 0;
    }
}


/* Scoped named namespace */
namespace Vendor\Package\Foo {
    echo namespace\fn();

    /* testNamedNamespaceScoped */
    echo 0;

    $array = array( /*long array */ );

    class ABC {
        function test() {
            /* testNamedNamespaceScopedNested */
            echo 0;
        }
    }
}


/* Non-scoped global namespace */
namespace;

if ($a) {
    // Just testing that the skipping works.
}

echo namespace\fn();

/* testNonScopedGlobalNamespace */
echo 0;

class Foo {
    function test() {
        /* testNonScopedGlobalNamespaceNested */
        echo 0;
    }
}


/* Non-scoped named namespace 2 */
namespace Vendor\Package\Foz;

echo namespace\fn();

echo ${$string};

/* testNonScopedNamedNamespace2 */
echo 0;

class Foo {
    function test() {
        /* testNonScopedNamedNamespace2Nested */
        echo 0;
    }
}

// Intentional parse error. This has to be the last test in the file.
/* testParseError */
namespace MyNS\ /* comment */
