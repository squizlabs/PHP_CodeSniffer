<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Common::isCamelCaps method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Baseline;

use PHP_CodeSniffer\Baseline\BaselineSetFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PHP_CodeSniffer\Baseline\BaselineSetFactory
 */
class BaselineSetFactoryTest extends TestCase
{
    /**
     * @covers ::fromFile
     */
    public function testFromFileShouldSucceed()
    {
        $filename = __DIR__ . '/TestFiles/baseline.xml';
        $set      = BaselineSetFactory::fromFile($filename);

        static::assertTrue($set->contains('Squiz.Functions.FunctionDeclarationArgumentSpacing.SpacingAfterOpen', '/test/src/foo/bar'));
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileShouldSucceedWithBackAndForwardSlashes()
    {
        $filename = __DIR__ . '/TestFiles/baseline.xml';
        $set      = BaselineSetFactory::fromFile($filename);

        static::assertTrue($set->contains('Squiz.Functions.FunctionDeclarationArgumentSpacing.SpacingAfterOpen', '/test\\src\\foo/bar'));
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileShouldReturnNullIfAbsent()
    {
        static::assertNull(BaselineSetFactory::fromFile('foobar.xml'));
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileShouldThrowExceptionForOnInvalidXML()
    {
        $this->expectException('PHP_CodeSniffer\Exceptions\RuntimeException');
        $this->expectExceptionMessage('Unable to read xml from');
        BaselineSetFactory::fromFile(__DIR__ .'/TestFiles/invalid-baseline.xml');
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileViolationMissingRuleShouldThrowException()
    {
        $this->expectException('PHP_CodeSniffer\Exceptions\RuntimeException');
        $this->expectExceptionMessage('Missing `sniff` attribute in `violation`');
        BaselineSetFactory::fromFile(__DIR__ .'/TestFiles/missing-sniff-baseline.xml');
    }

    /**
     * @covers ::fromFile
     */
    public function testFromFileViolationMissingFileShouldThrowException()
    {
        $this->expectException('PHP_CodeSniffer\Exceptions\RuntimeException');
        $this->expectExceptionMessage('Missing `file` attribute in `violation` in');
        BaselineSetFactory::fromFile(__DIR__ .'/TestFiles/missing-file-baseline.xml');
    }
}
