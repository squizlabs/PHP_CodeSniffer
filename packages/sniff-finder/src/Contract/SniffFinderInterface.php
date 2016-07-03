<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\SniffFinder\Contract;

interface SniffFinderInterface
{
    /**
     * @return string[]
     */
    public function findAllSniffs() : array;

    /**
     * @return string[]
     */
    public function findSniffsInDirectory(string $directory) : array;
}