<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\SniffFinder;

use Symplify\PHP7_CodeSniffer\Configuration;
use Symplify\PHP7_CodeSniffer\Ruleset\RulesetBuilder;
use Symplify\PHP7_CodeSniffer\SniffFinder\Composer\VendorDirProvider;
use Symplify\PHP7_CodeSniffer\SniffFinder\Contract\SniffFinderInterface;

final class SniffProvider
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var RulesetBuilder
     */
    private $rulesetBuilder;

    public function __construct(Configuration $configuration, RulesetBuilder $rulesetBuilder)
    {
        $this->configuration = $configuration;
        $this->rulesetBuilder = $rulesetBuilder;
    }

    public function getActiveSniffs() : array
    {
        $sniffs = [];
        foreach ($this->configuration->getStandards() as $name => $rulesetXmlPath) {
            $newSniffs = $this->rulesetBuilder->buildFromRulesetXml($rulesetXmlPath);
            $sniffs = array_merge($sniffs, $newSniffs);
        }
        return $sniffs;
    }

    public function getSniffRegistrations() : array
    {
        $sniffRestrictions = [];
        foreach ($this->configuration->getSniff() as $sniffCode) {
            $parts = explode('.', strtolower($sniffCode));
            $sniffName = 'CodeSniffer\\Standards\\'.$parts[0].'\\Sniffs\\'.$parts[1].'\\'.$parts[2].'Sniff';
            $sniffRestrictions[$sniffName] = true;
        }

        return $sniffRestrictions;
    }
}
