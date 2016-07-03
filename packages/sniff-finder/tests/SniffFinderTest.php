<?php

namespace Symplify\PHP7_CodeSniffer\SniffFinder\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\SniffFinder\Composer\VendorDirProvider;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffFinder;
use Symplify\PHP7_CodeSniffer\SniffFinder\StandardFinder;

final class SniffFinderTest extends TestCase
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var StandardFinder
     */
    private $standardFinder;

    protected function setUp()
    {
        $this->sniffFinder = new SniffFinder();
        $this->standardFinder = new StandardFinder();
    }

    public function testFindSniffsInRuleset()
    {
        $psr2RulesetPath = $this->standardFinder->getRulesetPathForStandardName('PSR2');

        $sniffs = $this->sniffFinder->findSniffsInRuleset($psr2RulesetPath);
        $this->assertCount(42, $sniffs);
    }

    public function testFindAllSniffs()
    {
        $allSniffs = $this->sniffFinder->findAllSniffs();
        $this->assertGreaterThan(250, $allSniffs);
    }

    public function testFindSniffsInDirectory()
    {
        $psr2RulesetPath = $this->standardFinder->getRulesetPathForStandardName('PSR2');

        $sniffs = $this->sniffFinder->findSniffsInDirectory(dirname($psr2RulesetPath));
        $this->assertCount(12, $sniffs);
    }
}
