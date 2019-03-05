<?php

/* testNamespaceOperator */
$a = namespace\fn();

/* testGlobalNamespaceSemiColon */
namespace;

/* testGlobalNamespaceCurlies */
namespace {}

/* testGlobalNamespaceCloseTag */
namespace ?>
<?php

/* testNamespaceSemiColon */
namespace Vendor;

/* testNamespaceCurlies */
namespace Vendor\Package\Sublevel\Deeperlevel\End {}

/* testNamespaceCurliesNoSpaceAtEnd */
namespace Vendor\Package\Sublevel\Deeperlevel\End{}

/* testNamespaceCloseTag */
namespace PHP_CodeSniffer\Exceptions\RuntimeException ?>
<?php

/* testNamespaceCloseTagNoSpaceAtEnd */
namespace PHP_CodeSniffer\Exceptions\RuntimeException?>
<?php

/* testNamespaceLotsOfWhitespace */
namespace          Vendor \
    Package\
        Sublevel	\
            Deeperlevel\
                End             ;

/* testNamespaceWithCommentsWhitespaceAndAnnotations */
namespace /*comment*/ Vendor\/*comment*/
    Package\Sublevel  \ //phpcs:ignore Standard.Category.Sniff -- for reasons.
            Deeperlevel\ // Another comment
                End             ;

// Intentional parse error. Reserved word used in namespace, but not our concern.
/* testNamespaceParseError */
namespace Vendor\while\Package\protected\name\try\this ;

// Intentional parse error. This has to be the last test in the file.
/* testLiveCoding */
namespace Vendor\
