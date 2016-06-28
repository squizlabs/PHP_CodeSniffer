<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\SniffFinder;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Finder\Finder;
use Symplify\PHP7_CodeSniffer\SniffFinder\Composer\VendorDirProvider;

final class StandardFinder
{
    /**
     * @var string[]
     */
    private $rulesets = [];

    /**
     * @param string $standardName
     * @return string
     */
    public function getRulesetPathForStandardName($standardName)
    {
        if (isset($this->getStandards()[$standardName])) {
            return $this->getStandards()[$standardName];
        }

        throw new \Exception(
            sprintf(
                'Ruleset for standard "%s" was not found. Found standards are: %s.',
                $standardName,
                implode($this->getRulesetNames(), ', ')
            )
        );
    }

    /**
     * @return string[]
     */
    public function getStandards() : array
    {
        if ($this->rulesets) {
            return $this->rulesets;
        }

        foreach ($this->findRulesetFiles() as $rulesetFile) {
            $rulesetXml = simplexml_load_file($rulesetFile);

            $rulesetName = (string) $rulesetXml['name'];
            $this->rulesets[$rulesetName] = $rulesetFile;
        }

        return $this->rulesets;
    }

    /**
     * @return string[]
     */
    private function findRulesetFiles()
    {
        $installedStandards = (new Finder())->files()
            ->in(VendorDirProvider::provide())
            ->name('ruleset.xml');

        return array_keys(iterator_to_array($installedStandards));
    }

    private function getRulesetNames() : array
    {
        return array_keys($this->getStandards());
    }
}
