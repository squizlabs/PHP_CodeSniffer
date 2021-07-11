<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Common::isCamelCaps method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Baseline;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Reports\Baseline;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Testcases for the baseline report output file
 *
 * @coversDefaultClass \PHP_CodeSniffer\Reports\Baseline
 */
class BaselineTest extends TestCase
{

    /**
     * The mock file object
     *
     * @var File|MockObject
     */
    private $file;


    protected function setup()
    {
        $this->file = $this->createMock('PHP_CodeSniffer\Files\File');

    }//end setup()


    /**
     * Test that generation is skipped when there are no errors
     *
     * @covers ::generateFileReport
     * @return void
     */
    public function testGenerateFileReportEmptyShouldReturnFalse()
    {
        $report = new Baseline();
        static::assertFalse($report->generateFileReport(['errors' => 0, 'warnings' => 0], $this->file));

    }//end testGenerateFileReportEmptyShouldReturnFalse()


    /**
     * Test the generation of a single error message
     *
     * @covers ::generateFileReport
     * @return void
     */
    public function testGenerateFileReportShouldPrintReport()
    {
        $reportData = [
            'filename' => '/test/foobar.txt',
            'errors'   => 1,
            'warnings' => 0,
            'messages' => [[[['sniff' => 'MySniff']]]],
        ];

        $report = new Baseline();
        ob_start();
        static::assertTrue($report->generateFileReport($reportData, $this->file));
        $result = ob_get_clean();
        static::assertSame('<violation file="/test/foobar.txt" sniff="MySniff"/>'."\n", $result);

    }//end testGenerateFileReportShouldPrintReport()


    /**
     * Test the generate of the complete file
     *
     * @covers ::generate
     * @return void
     */
    public function testGenerate()
    {
        $expected  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".PHP_EOL;
        $expected .= "<phpcs-baseline version=\"3.6.1\">".PHP_EOL;
        $expected .= "<violation file=\"/test/foobar.txt\" sniff=\"MySniff\"/></phpcs-baseline>".PHP_EOL;

        $report = new Baseline();
        ob_start();
        $report->generate('<violation file="/test/foobar.txt" sniff="MySniff"/>', 1, 1, 0, 1);
        $result = ob_get_clean();
        static::assertSame($expected, $result);

    }//end testGenerate()


}//end class
