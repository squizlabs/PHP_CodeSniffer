<?php

// Test source: https://gist.github.com/iluuu1994/72e2154fc4150f2258316b0255b698f2#file-test-php

/* testSimple1 */
"$foo";
/* testSimple2 */
"{$foo}";
/* testSimple3 */
"${foo}";

/* testDIM1 */
"$foo[bar]";
/* testDIM2 */
"{$foo['bar']}";
/* testDIM3 */
"${foo['bar']}";

/* testProperty1 */
"$foo->bar";
/* testProperty2 */
"{$foo->bar}";

/* testMethod1 */
"{$foo->bar()}";

/* testClosure1 */
"{$foo()}";

/* testChain1 */
"{$foo['bar']->baz()()}";

/* testVariableVar1 */
"${$bar}";
/* testVariableVar2 */
"${(foo)}";
/* testVariableVar3 */
"${foo->bar}";

/* testNested1 */
"${foo["${bar}"]}";
/* testNested2 */
"${foo["${bar['baz']}"]}";
/* testNested3 */
"${foo->{$baz}}";
/* testNested4 */
"${foo->{${'a'}}}";
/* testNested5 */
"${foo->{"${'a'}"}}";

/* testParseError */
"${foo["${bar
