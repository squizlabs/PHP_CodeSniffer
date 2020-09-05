<?php

/* testUseImportSimple */
use Imported\ClassName;

$a = (1 + 2);

/* testUseImportGroup */
use Imported\Package\{
    Classname,
};

$a = (1 + 2);

$closure = function($param) /* testClosureUse */ use ($var) {};

class ClassUsingTrait {
    /* testUseTrait */
    use MyTopLevelTrait { oldfunction as Foo; }

    public function functionName() {
        $closure = function($param) /* testClosureUseNestedInClass */ use ($var) {} ?>
        <?php

        $anon_class = new class($param) {
            /* testUseTraitInNestedAnonClass */
            use MyNestedTrait { oldfunction as Foo; }
        };

        return $anon_class('foo');
    }
}

// Intentional parse error. Live coding. This has to be the last test in the file.
/* testLiveCoding */
use