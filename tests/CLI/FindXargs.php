<?php
/**
 * Tests for the PHP_CodeSniffer CLI.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\CLI;

use PHP_CodeSniffer\Tests\CLI\CLI_TestCase;

class FindXargs extends CLI_TestCase
{

    /**
     * An array of external commands needed by this test suite.
     *
     * @var string
     */
    protected $command = [
                          'find',
                          'xargs',
                         ];


    /**
     * Test if we can find and xargs files with no failures in.
     *
     * @return void
     */
    public function testFindXargsDetectSuccess()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['find']} src/Standards/PEAR/Tests/WhiteSpace/ -type f -print0 | {$this->command['xargs']} -0 {$this->phpcs}");
        $this->assertEquals(0, $exitCode);

    }//end testFindXargsDetectSuccess()


    /**
     * Test if we can find and xargs files with failures in.
     *
     * @return void
     */
    public function testFindXargsDetectErrors()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['find']} src/Standards/PSR2/Tests/ -type f -print0 | {$this->command['xargs']} -0 {$this->phpcs} --standard=PSR2");
        $this->assertNotEquals(0, $exitCode);

    }//end testFindXargsDetectErrors()


}//end class
