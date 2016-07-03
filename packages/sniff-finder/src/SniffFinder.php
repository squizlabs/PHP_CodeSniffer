<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\SniffFinder;

use Symfony\Component\Finder\Finder;
use Symplify\PHP7_CodeSniffer\SniffFinder\Composer\VendorDirProvider;
use Symplify\PHP7_CodeSniffer\SniffFinder\Contract\SniffFinderInterface;

final class SniffFinder implements SniffFinderInterface
{
    public function findAllSniffs() : array
    {
        return $this->findSniffsInDirectory(VendorDirProvider::provide());
    }

    public function findSniffsInDirectory(string $directory) : array
    {
        $filesInfo = (new Finder())->files()
            ->in($directory)
            ->name('*Sniff.php');

        return array_keys(iterator_to_array($filesInfo));
    }
}
