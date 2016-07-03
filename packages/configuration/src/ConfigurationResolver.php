<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symplify\PHP7_CodeSniffer\Reports\Cbf;
use Symplify\PHP7_CodeSniffer\Reports\Full;
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
            'reportClass' => Full::class,
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

            foreach ($standards as $standardName) {
                if (!array_key_exists($standardName, $availableStandards)) {
                    throw new \Exception(sprintf(
                        'Standard "%s" is not supported. Pick one of: %s', $standardName,
                        implode(array_keys($availableStandards), ', ')
                    ));
                }
            }

            return true;
        });

        $this->optionsResolver->setAllowedValues('sniffs', function (array $sniffs) {
//            $sniffs = explode(',', substr($arg, 7));
//            foreach ($sniffs as $sniff) {
//                if (substr_count($sniff, '.') !== 2) {
//                    // throw exception!
//                    // echo 'ERROR: The specified sniff code "'.$sniff.'" is invalid'.PHP_EOL.PHP_EOL;
//                }
//            }

            return true;
        });

        $this->optionsResolver->setAllowedValues('source', function ($source) {
//        $file = Util\Common::realpath($arg);
//        if (file_exists($file) === false) {
//            echo 'ERROR: The file "'.$arg.'" does not exist.'.PHP_EOL.PHP_EOL;
//            $this->printUsage();
//            exit(2);
//        } else {
//            $this->files[] = $file;
//        }

            return true;
        });
    }

    private function setNormalizers()
    {
        $this->optionsResolver->setNormalizer('standards', function (OptionsResolver $optionsResolver, array $standardNames) {
            return $this->standardFinder->getRulesetPathsForStandardNames($standardNames);
        });

        $this->optionsResolver->setNormalizer('reportClass', function (OptionsResolver $optionsResolver, $reportClass) {
            if ($optionsResolver['isFixer']) {
               return Cbf::class;
            }
            return $reportClass;
        });
    }
}
