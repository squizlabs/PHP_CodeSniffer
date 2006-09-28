<?php
/**
 * +------------------------------------------------------------------------+
 * | BSD Licence                                                            |
 * +------------------------------------------------------------------------+
 * | This software is available to you under the BSD license,               |
 * | available in the LICENSE file accompanying this software.              |
 * | You may obtain a copy of the License at                                |
 * |                                                                        |
 * | http://matrix.squiz.net/developer/tools/php_cs/licence                 |
 * |                                                                        |
 * | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS    |
 * | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT      |
 * | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR  |
 * | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT   |
 * | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,  |
 * | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT       |
 * | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,  |
 * | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY  |
 * | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT    |
 * | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE  |
 * | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.   |
 * +------------------------------------------------------------------------+
 * | Copyright (c), 2006 Squiz Pty Ltd (ABN 77 084 670 600).                |
 * | All rights reserved.                                                   |
 * +------------------------------------------------------------------------+
 *
 * @package PHP_CodeSniffer
 * @author  Squiz Pty Ltd
 */

require_once 'PHP/CodeSniffer.php';
require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';
require_once 'PHPUnit2/Util/Filter.php';

// Require this here so that the unit tests don't have to try and find the
// abstract class once it is installed into the PEAR tests directory.
require_once dirname(__FILE__).'/AbstractSniffUnitTest.php';

/**
 * A test class for testing all sniffs for installed standards.
 *
 * Usage: phpunit AllSniffs.php
 *
 * This test class loads all unit tests for all installed standards into a
 * single test suite and runs them. Errors are reported on the command line.
 *
 * @package PHP_CodeSniffer
 * @author  Squiz Pty Ltd
 */
class AllSniffs
{


    /**
     * Prepare the test runner.
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());

    }//end main()


    /**
     * Add all sniff unit tests into a test suite.
     *
     * Sniff unit tests are found by recursing through the 'Tests' directory
     * of each installed coding standard.
     *
     * @return PHPUnit2_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('PHP_CodeSniffer Standards');

        $standards = PHP_CodeSniffer::getInstalledStandards(true);
        foreach ($standards as $standard) {
            $standardDir = dirname(__FILE__).'/'.$standard.'/Tests/';
            $di          = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($standardDir));

            foreach ($di as $file) {
                // Tests must have the extention 'php'.
                $parts = explode('.', $file);
                $ext   = array_pop($parts);
                if ($ext !== 'php') {
                    continue;
                }

                $filePath  = $file->getPathname();
                $className = str_replace(dirname(__FILE__).'/', '', $filePath);
                $className = substr($className, 0, -4);
                $className = str_replace('/', '_', $className);

                include_once $file->getPathname();
                $suite->addTestSuite($className);
            }
        }

        return $suite;

    }//end suite()


}//end class

?>
