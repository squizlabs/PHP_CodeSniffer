<?php
/**
 * Tests for the \PHP_CodeSniffer\Filters\Filter::accept method.
 *
 * @author    Willington Vega <wvega@wvega.com>
 * @copyright 2006-2018 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Filters\Filter;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Filters\Filter;
use PHP_CodeSniffer\Ruleset;
use PHPUnit\Framework\TestCase;

class AcceptTest extends TestCase
{


    /**
     * Test paths that include the name of a standard with associated
     * exclude-patterns are still accepted.
     *
     * @return void
     */
    public function testExcludePatternsForStandards()
    {
        $standard = __DIR__.'/'.basename(__FILE__, '.php').'.inc';
        $config   = new Config(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $paths = ['/path/to/generic-project/src/Main.php'];

        $fakeDI   = new \RecursiveArrayIterator($paths);
        $filter   = new Filter($fakeDI, '/path/to/generic-project/src', $config, $ruleset);
        $iterator = new \RecursiveIteratorIterator($filter);
        $files    = [];

        foreach ($iterator as $file) {
            $files[] = $file;
        }

        $this->assertEquals($paths, $files);

    }//end testExcludePatternsForStandards()


}//end class
