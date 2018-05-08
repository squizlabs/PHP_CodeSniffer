<?php
/**
 * Unit test class for the ConstantDeclaration sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Tests\Classes;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class ConstantDeclarationUnitTest extends AbstractSniffUnitTest
{
    const FILENAME_PHP_VERSION_70000 = 'ConstantDeclarationUnitTest.1.inc';

    const FILENAME_PHP_VERSION_70100 = 'ConstantDeclarationUnitTest.2.inc';


    /**
     * Get a list of CLI values to set before the file is tested.
     *
     * @param string                  $filename The name of the file being tested.
     * @param \PHP_CodeSniffer\Config $config   The config data for the run.
     *
     * @return void
     */
    public function setCliValues($filename, $config)
    {
        if ($filename === self::FILENAME_PHP_VERSION_70000) {
            $config->setCommandLineValues(['--runtime-set', 'php_version', '70001']);
        }

        if ($filename === self::FILENAME_PHP_VERSION_70100) {
            $config->setCommandLineValues(['--runtime-set', 'php_version', '70101']);
        }

        parent::setCliValues($filename, $config);

    }//end setCliValues()


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @param string $filename The name of the file being tested.
     *
     * @return array<int, int>
     */
    protected function getErrorList($filename='')
    {
        if ($filename === self::FILENAME_PHP_VERSION_70100) {
            return [12 => 1];
        }

        return [];

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array<int, int>
     */
    protected function getWarningList()
    {
        return [];

    }//end getWarningList()


}//end class
