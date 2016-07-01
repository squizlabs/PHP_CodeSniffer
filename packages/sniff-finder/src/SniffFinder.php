<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\SniffFinder;

use Symfony\Component\Finder\Finder;
use Symplify\PHP7_CodeSniffer\SniffFinder\Composer\VendorDirProvider;

final class SniffFinder
{
    public function findSniffsInRuleset(string $rulesetXml) : array
    {

    }
    
    /**
     * @return string[]
     */
    public function findAllSniffs() : array
    {
        $sniffFilesInfo = (new Finder())->files()
            ->in(VendorDirProvider::provide())
            ->name('*Sniff.php')
            ->sortByName();

        return array_keys(iterator_to_array($sniffFilesInfo));
    }
}
