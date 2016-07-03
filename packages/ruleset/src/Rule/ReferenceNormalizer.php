<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset\Rule;

use Nette\Utils\Strings;
use Symplify\PHP7_CodeSniffer\Ruleset\Contract\Rule\ReferenceNormalizerInterface;
use Symplify\PHP7_CodeSniffer\SniffFinder\Contract\SniffFinderInterface;
use Symplify\PHP7_CodeSniffer\SniffFinder\StandardFinder;
use Symplify\PHP7_CodeSniffer\Util\Common;
use Symplify\PHP7_CodeSniffer\Util\Standards;

final class ReferenceNormalizer implements ReferenceNormalizerInterface
{
    /**
     * @var SniffFinderInterface
     */
    private $sniffFinder;

    /**
     * @var StandardFinder
     */
    private $standardFinder;

    public function __construct(SniffFinderInterface $sniffFinder, StandardFinder $standardFinder)
    {
        $this->sniffFinder = $sniffFinder;
        $this->standardFinder = $standardFinder;
    }

    public function normalize(string $reference) : array
    {
        if ($this->isSniffFileReference($reference)) {
            return [$reference];
        }

        return [$this->normalizeSniffNameToFile($reference)];
    }


    public function isRulesetReference(string $reference) : bool
    {
        if (Strings::endsWith($reference, 'ruleset.xml')) {
            return true;
        }

        return false;
    }

    public function isStandardReference(string $reference) : bool
    {
        $standards = $this->standardFinder->getStandards();
        if (isset($standards[$reference])) {
            return true;
        }

        return false;
    }

    private function isSniffFileReference(string $reference) : bool
    {
        if (Strings::endsWith($reference, 'Sniff.php')) {
            return true;
        }

        return false;
    }

    private function normalizeSniffNameToFile(string $name) : string
    {
        $sepPos = strpos($name, DIRECTORY_SEPARATOR);

        if ($sepPos !== false) {
            $stdName = substr($name, 0, $sepPos);
            $path    = substr($name, $sepPos);
        } else {
            $parts   = explode('.', $name);
            $stdName = $parts[0];
            if (count($parts) === 1) {
                // A whole standard?
                $path = '';
            } else {
                // A single sniff?
                $path = DIRECTORY_SEPARATOR.'Sniffs'.DIRECTORY_SEPARATOR.$parts[1].DIRECTORY_SEPARATOR.$parts[2].'Sniff.php';
            }
        }

        $stdPath = Standards::getInstalledStandardPath($stdName);
        if ($stdPath !== null && $path !== '') {
            return Common::realpath(dirname($stdPath).$path);
        }

        return '';
    }
}
