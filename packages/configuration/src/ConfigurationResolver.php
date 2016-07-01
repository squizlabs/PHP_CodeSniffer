<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffFinder;
use Symplify\PHP7_CodeSniffer\SniffFinder\StandardFinder;

final class ConfigurationResolver
{
    /**
     * @var StandardFinder
     */
    private $standardFinder;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    public function __construct(StandardFinder $standardFinder)
    {
        $this->standardFinder = $standardFinder;
        $this->createAndSetupOptionsResolver();
    }

    public function resolve(array $options) : array
    {
        return $this->optionsResolver->resolve($options);
    }

    private function createAndSetupOptionsResolver()
    {
        $this->optionsResolver = new OptionsResolver();
        $this->setDefaults();
        $this->setRequired();
        $this->setAllowedValues();
        $this->setNormalizers();
    }

    private function setDefaults()
    {
        $this->optionsResolver->setDefaults([
            'isFixer' => false,
            'source' => null,
            'standards' => ['PSR2'],
            'sniffs' => [],
            'reports' => ['full' => null],
            'reportWidth' => $this->getDefaultReportWidth()
        ]);
    }

    private function getDefaultReportWidth() : int
    {
        if (preg_match('|\d+ (\d+)|', shell_exec('stty size 2>&1'), $matches) === 1) {
            return (int) $matches[1];
        }
        return 80;
    }

    private function setRequired()
    {
        $this->optionsResolver->setRequired(['source', 'standards']);
    }

    private function setAllowedValues()
    {
        $this->optionsResolver->setAllowedValues('standards', function (array $standards) {
            $availableStandards = $this->standardFinder->getStandards();
            foreach ($standards as $standard) {
                if (!array_key_exists($standard, $availableStandards)) {
                    throw new \Exception(sprintf(
                        'Standard "%s" is not supported. Pick one of: %s', $standard,
                        implode(array_keys($availableStandards), ', ')
                    ));
                }
            }

            return true;
        });
    }

    private function setNormalizers()
    {
        $this->optionsResolver->setNormalizer('standards', function (OptionsResolver $optionsResolver, array $standardNames) {
            return $this->standardFinder->getRulesetPathsForStandardNames($standardNames);
        });

        $this->optionsResolver->setNormalizer('reports', function (OptionsResolver $optionsResolver, array $reports) {
            if ($optionsResolver['isFixer']) {
               return ['cbf' => null];
            }
            return $reports;
        });
    }
}
