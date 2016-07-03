<?php

namespace Symplify\PHP7_CodeSniffer\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Configuration;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Ruleset;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffFinder;
use Symplify\PHP7_CodeSniffer\SniffFinder\StandardFinder;

final class RulesetTest extends TestCase
{
    /**
     * @var Ruleset
     */
    private $ruleset;

    protected function setUp()
    {
        $configuration = new Configuration(new ConfigurationResolver(new StandardFinder()));
        $this->ruleset = new Ruleset($configuration, new SniffFinder());
    }

    public function testGetSniffs()
    {
        $this->assertSame([], $this->ruleset->getSniffs());
    }
}
