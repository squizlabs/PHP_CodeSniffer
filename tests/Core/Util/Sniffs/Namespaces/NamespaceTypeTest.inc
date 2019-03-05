<?php

/* testNamespaceDeclaration */
namespace MyNamespace;

/* testNamespaceDeclarationWithComment */
namespace /*comment*/ MyNamespace;

/* testNamespaceDeclarationScoped */
namespace {
}

/* testNamespaceOperator */
$a = namespace\functionCall();

/* testNamespaceOperatorWithAnnotation */
$a = namespace
     // phpcs:ignore Standard.Category.SniffName -- for reasons.
     \functionCall();

/* testParseError */
$a = namespace::Something();

// Intentional parse error. This has to be the last test in the file.
/* testLiveCoding */
namespace
