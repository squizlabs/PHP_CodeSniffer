<?php
/**
 * Tests for the PHP_CodeSniffer CLI.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2017 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\CLI;

use PHPUnit\Framework\TestCase;

abstract class CLI_TestCase extends TestCase
{

    /**
     * Location of the phpcs cli command.
     *
     * @var string
     */
    protected $phpcs = 'bin/phpcs';


    /**
     * Are external commands available?
     *
     * @return void
     */
    public function setup()
    {
        $commands = array();

        if (is_executable($this->phpcs) === false) {
            $this->markTestSkipped("No PHPCS command `bin/phpcs` available.");
        }

        foreach ($this->command as $command) {
            $commands["$command"] = trim(shell_exec("which $command 2> /dev/null"));
            if (empty($commands["$command"]) === true) {
                $this->markTestSkipped("No command `$command` available.");
            }
        }

        $this->command = $commands;

    }//end setup()


    /**
     * Helper function to shell_exec.
     *
     * @param string $command The command line to execute in shell.
     *
     * @return int The exit code.
     */
    protected function shellExecEXitCode($command)
    {
        return (int) trim(shell_exec($command.' > /dev/null 2>&1 ; echo $?'));

    }//end shellExecEXitCode()


}//end class
