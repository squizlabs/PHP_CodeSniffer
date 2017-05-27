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

class Cat extends CLI_TestCase
{

    /**
     * An array of external commands needed by this test suite.
     *
     * @var string
     */
    protected $command = ['cat'];


    /**
     * Cat on stdin detecting success.
     *
     * @return void
     */
    public function testCatStdinDetectSuccess()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['cat']} tests/CLI/File/LargeFile.php | {$this->phpcs}");
        $this->assertEquals(0, $exitCode);

    }//end testCatStdinDetectSuccess()


    /**
     * Cat on stdin detecting failure.
     *
     * @return void
     */
    public function testCatStdinDetectErrors()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['cat']} tests/CLI/File/LargeFile.inc | {$this->phpcs}");
        $this->assertNotEquals(0, $exitCode);

    }//end testCatStdinDetectErrors()


    /**
     * Cat on stdin (`-`) detecting success.
     *
     * @return void
     */
    public function testCatExplicitStdinDetectSuccess()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['cat']} tests/CLI/File/LargeFile.php | {$this->phpcs} -");
        $this->assertEquals(0, $exitCode);

    }//end testCatExplicitStdinDetectSuccess()


    /**
     * Cat on stdin (`-`) detecting failure.
     *
     * @return void
     */
    public function testCatExplicitStdinDetectErrors()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['cat']} tests/CLI/File/LargeFile.inc | {$this->phpcs} -");
        $this->assertNotEquals(0, $exitCode);

    }//end testCatExplicitStdinDetectErrors()


}//end class
