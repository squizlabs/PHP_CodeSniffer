<?php

declare(strict_types=1);

namespace Test;

/**
 * Sniff should recognize the PHP 8.2 `readonly` keyword as an OO prefix. 
 */
readonly class Example
{
    public function test() : void
    {
        echo '123';
    }
}
