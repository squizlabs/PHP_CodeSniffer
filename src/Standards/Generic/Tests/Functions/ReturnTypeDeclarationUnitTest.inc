<?php
// Shameless copy from:
// https://gist.githubusercontent.com/michaelcullum/c025f3870c9ea1dd2668/raw/0b6bec832b1bb30689e2271d4e931ddfba378718/returnTypesSpacing.php

// Chosen approach is to consider the closing parameter bracket and
// colon look visually as one character so no space between parenthesis and colon
// and have a preceding space before the type declaration. Demonstrated in method
// named `this()`

class ReturnTypeVariations
{
    public function this(): string
    {
        return;
    }

    public function func() :string
    {
        return;
    }

    public function func() : string
    {
        return;
    }

    public function func():string
    {
        return;
    }

    public function func()
    :string{
        return;
    }

    public function func()
    :string {
        return;
    }

    public function func()
    : string {
        return;
    }

    public function func()
    :string
    {
        return;
    }

    public function func()
    : string
    {
        return;
    }

    public function func():
    string
    {
        return;
    }

    public function func()
    {
        return;
    }
}

public function this(): string
{
    return;
}

public function func() :string
{
    return;
}

public function func() : string
{
    return;
}

public function func():string
{
    return;
}

public function func()
:string{
    return;
}

public function func()
:string {
    return;
}

public function func()
: string {
    return;
}

public function func()
:string
{
    return;
}

public function func()
: string
{
    return;
}

public function func():
string
{
    return;
}

public function func()
{
    return;
}

interface ReturnTypeVariations
{
    public function this(): string;

    public function func() :string;

    public function func() : string;

    public function func():string;

    public function func()
    :string;

    public function func()
    :string ;

    public function func()
    : string ;

    public function func()
    :string
    ;

    public function func()
    : string
    ;

    public function func():
    string
    ;

    public function func();
}

namespace ReturnTypeVariations
{
    $func = function() use($a): string
    {
        return;
    };

    $func = function() use($a) :string
    {
        return;
    };

    $func = function() use($a) : string
    {
        return;
    };

    $func = function() use($a):string
    {
        return;
    };

    $func = function() use($a)
    :string{
        return;
    };

    $func = function() use($a)
    :string {
        return;
    };

    $func = function() use($a)
    : string {
        return;
    };

    $func = function() use($a)
    :string
    {
        return;
    };

    $func = function() use($a)
    : string
    {
        return;
    };

    $func = function() use($a):
    string
    {
        return;
    };

    $func = function() use($a)
    {
        return;
    };

    $func = function() use($a):
    string // phpcs:ignore Standard.Category.Sniff -- for reasons.
    {
        return;
    };

    $func = function() use($a): // phpcs:ignore Standard.Category.Sniff -- for reasons.
    string
    {
        return;
    };

    $func = function() use($a):string // phpcs:ignore Standard.Category.Sniff -- for reasons.
    {
        return;
    };

    $func = function() use($a) :string // phpcs:ignore Standard.Category.Sniff -- for reasons.
    {
        return;
    };

    $func = function() use($a) : string // phpcs:ignore Standard.Category.Sniff -- for reasons.
    {
        return;
    };
}
