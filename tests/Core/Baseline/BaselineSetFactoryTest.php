<?php
/**
 * Tests for the BaselineSetFactory
 *
 * @author    Frank Dekker <fdekker@123inkt.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Baseline;

use PHP_CodeSniffer\Baseline\BaselineSetFactory;
use PHPUnit\Framework\TestCase;

/**
 * Testcases for the reading the baseline set from file
 *
 * @coversDefaultClass \PHP_CodeSniffer\Baseline\BaselineSetFactory
 */
class BaselineSetFactoryTest extends TestCase
{


    /**
     * Read the baseline from a file
     *
     * @covers ::fromFile
     * @return void
     */
    public function testFromFileShouldSucceed()
    {
        $filename = __DIR__.'/TestFiles/baseline.xml';
        $set      = BaselineSetFactory::fromFile($filename);

        static::assertTrue($set->contains('Squiz.Functions.FunctionDeclarationArgumentSpacing.SpacingAfterOpen', '/test/src/foo/bar', 'foobar'));

    }//end testFromFileShouldSucceed()


    /**
     * Read the baseline from a file with different slashes
     *
     * @covers ::fromFile
     * @return void
     */
    public function testFromFileShouldSucceedWithBackAndForwardSlashes()
    {
        $filename = __DIR__.'/TestFiles/baseline.xml';
        $set      = BaselineSetFactory::fromFile($filename);

        static::assertTrue($set->contains('Squiz.Functions.FunctionDeclarationArgumentSpacing.SpacingAfterOpen', '/test\\src\\foo/bar', 'foobar'));

    }//end testFromFileShouldSucceedWithBackAndForwardSlashes()


    /**
     * Test that reading absent file returns null
     *
     * @covers ::fromFile
     * @return void
     */
    public function testFromFileShouldReturnNullIfAbsent()
    {
        static::assertNull(BaselineSetFactory::fromFile('foobar.xml'));

    }//end testFromFileShouldReturnNullIfAbsent()


    /**
     * Test that reading invalid xml throws exception
     *
     * @covers ::fromFile
     * @return void
     */
    public function testFromFileShouldThrowExceptionForOnInvalidXML()
    {
        $this->expectException('PHP_CodeSniffer\Exceptions\RuntimeException');
        $this->expectExceptionMessage('Unable to read xml from');
        BaselineSetFactory::fromFile(__DIR__.'/TestFiles/invalid-baseline.xml');

    }//end testFromFileShouldThrowExceptionForOnInvalidXML()


    /**
     * Test that missing attributes throws exception
     *
     * @covers ::fromFile
     * @return void
     */
    public function testFromFileViolationMissingSniffShouldThrowException()
    {
        $this->expectException('PHP_CodeSniffer\Exceptions\RuntimeException');
        $this->expectExceptionMessage('Missing `sniff` attribute in `violation`');
        BaselineSetFactory::fromFile(__DIR__.'/TestFiles/missing-sniff-baseline.xml');

    }//end testFromFileViolationMissingSniffShouldThrowException()


    /**
     * Test that missing signature attribute throws exception
     *
     * @covers ::fromFile
     * @return void
     */
    public function testFromFileViolationMissingSignatureShouldThrowException()
    {
        $this->expectException('PHP_CodeSniffer\Exceptions\RuntimeException');
        $this->expectExceptionMessage('Missing `signature` attribute in `violation` in');
        BaselineSetFactory::fromFile(__DIR__.'/TestFiles/missing-signature-baseline.xml');

    }//end testFromFileViolationMissingSignatureShouldThrowException()


    /**
     * Test that missing attributes throws exception
     *
     * @covers ::fromFile
     * @return void
     */
    public function testFromFileViolationMissingFileShouldThrowException()
    {
        $this->expectException('PHP_CodeSniffer\Exceptions\RuntimeException');
        $this->expectExceptionMessage('Missing `file` attribute in `violation` in');
        BaselineSetFactory::fromFile(__DIR__.'/TestFiles/missing-file-baseline.xml');

    }//end testFromFileViolationMissingFileShouldThrowException()


}//end class
