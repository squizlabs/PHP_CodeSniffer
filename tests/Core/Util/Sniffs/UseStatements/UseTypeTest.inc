<?php

/* testUseImport1 */
use Imported\ClassName;

/* testUseImport2 */
use function Imported\functionName;

/* testUseImport3 */
use const Imported\CONSTANT_NAME;

namespace Some\NS {
    /* testUseImport4 */
    use Imported\AnotherClass;
}

/* testClosureUse */
$closure = function($param) use ($var) {};

class ClassUsingTrait {
    /* testUseTrait */
    use SomeTrait;

    public function functionName() {
        /* testClosureUseNestedInClass */
        $closure = function($param) use ($var) {};

        $anon_class = new class($param) {
            /* testUseTraitInNestedAnonClass */
            use SomeOtherTrait;
        };
    }
}

trait TraitUsingTrait {
    /* testUseTraitInTrait */
    use SomeTrait;

    public function functionName() {
        /* testClosureUseNestedInTrait */
        $closure = function($param) use ($var) {};
    }
}

// Intentional parse error. Interfaces can not use traits.
interface InterfaceUsingTrait {
    /* testUseTraitInInterface */
    use SomeTrait;
}

// Intentional parse error. Live coding. This has to be the last test in the file.
/* testLiveCoding */
use