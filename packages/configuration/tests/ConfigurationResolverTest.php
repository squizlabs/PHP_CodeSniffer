<?php

namespace Symplify\PHP7_CodeSniffer\Configuration\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\SniffFinder\StandardFinder;

final class ConfigurationResolverTest extends TestCase
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    protected function setUp()
    {
        $this->configurationResolver = new ConfigurationResolver(new StandardFinder());
    }

    public function testDefaults()
    {
        $resolved = $this->configurationResolver->resolve([]);

        $this->assertSame(null, $resolved['source']);
        $this->assertSame([], $resolved['sniffs']);
        $this->assertSame([
            'full' => null
        ], $resolved['reports']);
        $this->assertInternalType('int', $resolved['reportWidth']);

        $this->assertArrayHasKey('PSR2', $resolved['standards']);
        $this->assertStringMatchesFormat(
            '%s/vendor/squizlabs/php_codesniffer/src/Standards/PSR2/ruleset.xml',
            $resolved['standards']['PSR2']
        );
    }

    /**
     * @expectedException Exception
     */
    public function testNonExistingStandard()
    {
        $this->configurationResolver->resolve([
            'standards' => ['fake']
        ]);
    }

    public function testDifferentReportForFixer()
    {
        $resolved = $this->configurationResolver->resolve([
           'isFixer' => true
        ]);

        $this->assertSame([
            'cbf' => null
        ], $resolved['reports']);
    }
}
