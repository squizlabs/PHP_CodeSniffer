<?php

public function functionName(int $arg1, $arg2): string
{
    return 'foo';
}

public function anotherFunction(
    int $baz
): string {
    return 'foo';
}

$longArgs_noVars = function (
    $longArgument,
): string {
   // body
};

$shortArgs_longVars = function ($arg) use (
    $longVar1,
): string {
   // body
};

public function functionName(int $arg1, $arg2)
:
string
{
    return 'foo';
}

public function anotherFunction(
    int $baz
)  :  string {
    return 'foo';
}

$longArgs_noVars = function (
    $longArgument,
) :string {
   // body
};

$shortArgs_longVars = function ($arg) use (
    $longVar1,
)
:string {
   // body
};

public function functionName(int $arg1, $arg2) /* can't fix */ : string {}

public function functionName(int $arg1, $arg2) /* can't fix */
: // phpcs:ignore Standard.Category.Sniff
string {}

function functionName(?string $arg1, ?int &$arg2): ?string {}
function functionName(?string $arg1, ?int &$arg2):?string {}
function functionName(?string $arg1, ?int &$arg2):  ?string {}
function functionName(?string $arg1, ?int &$arg2):
?string {}

fn (?\DateTime $arg) : ?\DateTime => $arg;

return (!$a ? [ new class { public function b(): c {} } ] : []);
