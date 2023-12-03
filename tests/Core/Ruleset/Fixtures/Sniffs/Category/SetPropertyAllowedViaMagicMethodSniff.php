<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\SetSniffPropertyTest
 */

namespace Fixtures\Sniffs\Category;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class SetPropertyAllowedViaMagicMethodSniff implements Sniff
{
    private $magic = [];

    public function __set($name, $value)
    {
        $this->magic[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->magic[$name])) {
            return $this->magic[$name];
        }

        return null;
    }

    public function register()
    {
        return [T_WHITESPACE];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
