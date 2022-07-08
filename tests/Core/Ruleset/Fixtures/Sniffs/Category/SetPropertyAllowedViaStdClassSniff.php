<?php
/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\SetSniffPropertyTest
 */

namespace Fixtures\Sniffs\Category;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use stdClass;

class SetPropertyAllowedViaStdClassSniff extends stdClass implements Sniff
{

    public function register()
    {
        return [T_WHITESPACE];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
