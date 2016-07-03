<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Exception;
use Symplify\PHP7_CodeSniffer\Files\File;
use Symplify\PHP7_CodeSniffer\Files\File\SourceFilesProvider;

final class Runner
{
    /**
     * @var Ruleset
     */
    public $ruleset;

    /**
     * @var Reporter
     */
    public $reporter;

    /**
     * @var SourceFilesProvider
     */
    private $sourceFilesProvider;

    public function __construct(Ruleset $ruleset, Reporter $reporter, SourceFilesProvider $sourceFilesProvider)
    {
        $this->ruleset = $ruleset;
        $this->reporter = $reporter;
        $this->sourceFilesProvider = $sourceFilesProvider;

        $this->ensureLineEndingsAreDetected();
    }

    public function runPHPCS()
    {
        $filesToBeChecked = $this->sourceFilesProvider->getFiles();
        foreach ($filesToBeChecked as $file) {
            $this->processFile($file);
        }
    }

    /**
     * Processes a single file, including checking and fixing.
     */
    private function processFile(File $file)
    {
        try {
            $file->process();
        } catch (Exception $e) {
            $error = 'An error occurred during processing; checking has been aborted. The error message was: '.$e->getMessage();
            $file->addErrorOnLine($error, 1, 'Internal.Exception');
        }

        $this->reporter->cacheFileReport($file);

        // Clean up the file to save (a lot of) memory.
        $file->cleanUp();
    }

    /**
     * Ensure this option is enabled or else line endings will not always
     * be detected properly for files created on a Mac with the /r line ending.
     */
    private function ensureLineEndingsAreDetected()
    {
        ini_set('auto_detect_line_endings', true);
    }
}
