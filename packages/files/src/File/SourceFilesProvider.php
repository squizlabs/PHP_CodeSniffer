<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Files\File;

use Symplify\PHP7_CodeSniffer\Configuration;
use Symplify\PHP7_CodeSniffer\Files\Finder\SourceFinder;
use Symplify\PHP7_CodeSniffer\Files\LocalFile;
use Symplify\PHP7_CodeSniffer\Ruleset;

final class SourceFilesProvider
{
    /**
     * @var LocalFile[]
     */
    private $files;

    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    /**
     * @var FileFactory
     */
    private $fileFactory;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(SourceFinder $sourceFinder, FileFactory $fileFactory, Configuration $configuration)
    {
        $this->sourceFinder = $sourceFinder;
        $this->fileFactory = $fileFactory;
        $this->configuration = $configuration;
    }

    /**
     * @return LocalFile[]
     */
    public function getFiles() : array
    {
        if ($this->files) {
            return $this->files;
        }

        foreach ($this->sourceFinder->find($this->configuration->getSource()) as $name => $fileInfo) {
            $this->files[] = $this->fileFactory->create($name);
        }

        return $this->files;
    }

}
