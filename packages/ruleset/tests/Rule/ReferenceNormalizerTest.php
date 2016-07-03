<?php

namespace Symplify\PHP7_CodeSniffer\Ruleset\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffFinder;
use Symplify\PHP7_CodeSniffer\SniffFinder\StandardFinder;

final class ReferenceNormalizerTest extends TestCase
{
    /**
     * @var ReferenceNormalizer
     */
    private $referenceNormalizer;

    protected function setUp()
    {
        $this->referenceNormalizer = new ReferenceNormalizer(
            new SniffFinder(),
            new StandardFinder()
        );
    }

    public function testNormalizeStandard()
    {
        $standard = $this->referenceNormalizer->normalize('PSR1');
        $this->assertSame([
            0 => ''
        ], $standard);
    }
}