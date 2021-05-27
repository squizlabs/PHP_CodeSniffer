<?php

try {
    // Do something.
} catch(Exception $e) {
    // Do something.
}
/* testTryCatchFinally */
finally {
    // Do something.
}

/* testTryFinallyCatch */
try {
    // Do something.
} finally {
    // Do something.
} catch(Exception $e) {
    // Do something.
}

/* testTryFinally */
try {
    // Do something.
} FINALLY {
    // Do something.
}

class FinallyAsMethod {
    /* testFinallyUsedAsClassConstantName */
    const FINALLY = 'foo';

    /* testFinallyUsedAsMethodName */
    public function finally() {
        // Do something.

        /* testFinallyUsedAsPropertyName */
        $this->finally = 'foo';
    }
}
