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

class SlowCat extends CLI_TestCase
{

    /**
     * An array of external commands needed by this test suite.
     *
     * @var string
     */
    protected $command = ['pv'];


    /**
     * Slow cat on stdin detecting success.
     *
     * @return void
     */
    public function testSlowCatStdinDetectSuccess()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['pv']} -qL 64k tests/CLI/File/LargeFile.php | {$this->phpcs}");
        $this->assertEquals(0, $exitCode);

    }//end testSlowCatStdinDetectSuccess()


    /**
     * Slow cat on stdin detecting errors.
     *
     * @return void
     */
    public function testSlowCatStdinDetectErrors()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['pv']} -qL 64k tests/CLI/File/LargeFile.inc | {$this->phpcs}");
        $this->assertNotEquals(0, $exitCode);

    }//end testSlowCatStdinDetectErrors()


    /**
     * Slow cat on stdin (`-`) detecting success.
     *
     * @return void
     */
    public function testSlowCatExplicitStdinDetectSuccess()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['pv']} -qL 64k tests/CLI/File/LargeFile.php | {$this->phpcs} -");
        $this->assertEquals(0, $exitCode);

    }//end testSlowCatExplicitStdinDetectSuccess()


    /**
     * Slow cat on stdin (`-`) detecting errors.
     *
     * @return void
     */
    public function testSlowCatExplicitStdinDetectErrors()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['pv']} -qL 64k tests/CLI/File/LargeFile.inc | {$this->phpcs} -");
        $this->assertNotEquals(0, $exitCode);

    }//end testSlowCatExplicitStdinDetectErrors()


}//end class
