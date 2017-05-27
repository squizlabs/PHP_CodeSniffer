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

class EchoCmd extends CLI_TestCase
{

    /**
     * An array of external commands needed by this test suite.
     *
     * @var string
     */
    protected $command = ['echo'];


    /**
     * Echo on stdin detecting success.
     *
     * @return void
     */
    public function testEchoStdinDetectSuccess()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['echo']} '<?php return true;' | {$this->phpcs} --standard=PSR2");
        $this->assertEquals(0, $exitCode);

    }//end testEchoStdinDetectSuccess()


    /**
     * Echo on stdin detecting errors.
     *
     * @return void
     */
    public function testEchoStdinDetectErrors()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['echo']} '<?php RETURN true;' | {$this->phpcs} --standard=PSR2");
        $this->assertNotEquals(0, $exitCode);

    }//end testEchoStdinDetectErrors()


    /**
     * Echo on stdin (`-`) detecting success.
     *
     * @return void
     */
    public function testEchoExplicitStdinDetectSuccess()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['echo']} '<?php return true;' | {$this->phpcs} --standard=PSR2 -");
        $this->assertEquals(0, $exitCode);

    }//end testEchoExplicitStdinDetectSuccess()


    /**
     * Echo on stdin (`-`) detecting errors.
     *
     * @return void
     */
    public function testEchoExplicitStdinDetectErrors()
    {
        $exitCode = $this->shellExecExitCode("{$this->command['echo']} '<?php RETURN true;' | {$this->phpcs} --standard=PSR2 -");
        $this->assertNotEquals(0, $exitCode);

    }//end testEchoExplicitStdinDetectErrors()


}//end class
