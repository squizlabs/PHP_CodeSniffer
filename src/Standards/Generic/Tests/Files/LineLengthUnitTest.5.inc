phpcs:set Generic.Files.LineLength ignoreUseStatementsLines true
phpcs:set Generic.Files.LineLength lineLimit 80
phpcs:set Generic.Files.LineLength absoluteLineLimit 120
<?php

use This\Line\Is\Tooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo\Long\But\Its\Ok\Because\Will\Be\Skipped;

class foo extends bar {
    use thisIsToooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooLongNameOfTraitAndThisLineMustRaiseError;

    $array->map(function (int $a) use ($thisIsToooooooooooooooooooooooooooLongVariableNameAndThisLIneMustRaiseError)) {
        return
    }
}
