<?php

namespace Symplify\PHP7_CodeSniffer\Ruleset\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\Ruleset\RulesetBuilder;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffFinder;
use Symplify\PHP7_CodeSniffer\SniffFinder\StandardFinder;

final class RulesetBuilderTest extends TestCase
{
    /**
     * @var RulesetBuilder
     */
    private $rulesetBuilder;

    protected function setUp()
    {
        $this->rulesetBuilder = new RulesetBuilder(
            new SniffFinder(),
            new ReferenceNormalizer(new SniffFinder(), new StandardFinder())
        );
    }

    public function testBuildFromRulesetXml()
    {
        $ruleset = $this->rulesetBuilder->buildFromRulesetXml(__DIR__.'/RulesetBuilderSource/ruleset.xml');
        $this->assertInternalType('array', $ruleset);
    }
}
