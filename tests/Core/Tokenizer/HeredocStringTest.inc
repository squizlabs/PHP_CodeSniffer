<?php

// Test source: https://gist.github.com/iluuu1994/72e2154fc4150f2258316b0255b698f2#file-test-php

/* testSimple1 */
$heredoc = <<<EOD
$foo
EOD;

/* testSimple2 */
$heredoc = <<<"EOD"
{$foo}
EOD;

/* testSimple3 */
$heredoc = <<<EOD
${foo}
EOD;

/* testDIM1 */
$heredoc = <<<"EOD"
$foo[bar]
EOD;

/* testDIM2 */
$heredoc = <<<EOD
{$foo['bar']}
EOD;

/* testDIM3 */
$heredoc = <<<"EOD"
${foo['bar']}
EOD;

/* testProperty1 */
$heredoc = <<<EOD
$foo->bar
EOD;

/* testProperty2 */
$heredoc = <<<"EOD"
{$foo->bar}
EOD;

/* testMethod1 */
$heredoc = <<<EOD
{$foo->bar()}
EOD;

/* testClosure1 */
$heredoc = <<<"EOD"
{$foo()}
EOD;

/* testChain1 */
$heredoc = <<<EOD
{$foo['bar']->baz()()}
EOD;

/* testVariableVar1 */
$heredoc = <<<"EOD"
${$bar}
EOD;

/* testVariableVar2 */
$heredoc = <<<EOD
${(foo)}
EOD;

/* testVariableVar3 */
$heredoc = <<<"EOD"
${foo->bar}
EOD;

/* testNested1 */
$heredoc = <<<EOD
${foo["${bar}"]}
EOD;

/* testNested2 */
$heredoc = <<<"EOD"
${foo["${bar['baz']}"]}
EOD;

/* testNested3 */
$heredoc = <<<EOD
${foo->{$baz}}
EOD;

/* testNested4 */
$heredoc = <<<"EOD"
${foo->{${'a'}}}
EOD;

/* testNested5 */
$heredoc = <<<EOD
${foo->{"${'a'}"}}
EOD;

/* testSimple1Wrapped */
$heredoc = <<<EOD
Do $foo Something
EOD;

/* testSimple2Wrapped */
$heredoc = <<<"EOD"
Do {$foo} Something
EOD;

/* testSimple3Wrapped */
$heredoc = <<<EOD
Do ${foo} Something
EOD;

/* testDIM1Wrapped */
$heredoc = <<<"EOD"
Do $foo[bar] Something
EOD;

/* testDIM2Wrapped */
$heredoc = <<<EOD
Do {$foo['bar']} Something
EOD;

/* testDIM3Wrapped */
$heredoc = <<<"EOD"
Do ${foo['bar']} Something
EOD;

/* testProperty1Wrapped */
$heredoc = <<<EOD
Do $foo->bar Something
EOD;

/* testProperty2Wrapped */
$heredoc = <<<"EOD"
Do {$foo->bar} Something
EOD;

/* testMethod1Wrapped */
$heredoc = <<<EOD
Do {$foo->bar()} Something
EOD;

/* testClosure1Wrapped */
$heredoc = <<<"EOD"
Do {$foo()} Something
EOD;

/* testChain1Wrapped */
$heredoc = <<<EOD
Do {$foo['bar']->baz()()} Something
EOD;

/* testVariableVar1Wrapped */
$heredoc = <<<"EOD"
Do ${$bar} Something
EOD;

/* testVariableVar2Wrapped */
$heredoc = <<<EOD
Do ${(foo)} Something
EOD;

/* testVariableVar3Wrapped */
$heredoc = <<<"EOD"
Do ${foo->bar} Something
EOD;

/* testNested1Wrapped */
$heredoc = <<<EOD
Do ${foo["${bar}"]} Something
EOD;

/* testNested2Wrapped */
$heredoc = <<<"EOD"
Do ${foo["${bar['baz']}"]} Something
EOD;

/* testNested3Wrapped */
$heredoc = <<<EOD
Do ${foo->{$baz}} Something
EOD;

/* testNested4Wrapped */
$heredoc = <<<"EOD"
Do ${foo->{${'a'}}} Something
EOD;

/* testNested5Wrapped */
$heredoc = <<<EOD
Do ${foo->{"${'a'}"}} Something
EOD;
