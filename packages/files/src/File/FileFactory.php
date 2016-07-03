<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Files\File;

use Symplify\PHP7_CodeSniffer\Files\File;
use Symplify\PHP7_CodeSniffer\Fixer;
use Symplify\PHP7_CodeSniffer\Ruleset;

final class FileFactory
{
    /**
     * @var Ruleset
     */
    private $ruleset;

    /**
     * @var Fixer
     */
    private $fixer;

    public function __construct(Ruleset $ruleset, Fixer $fixer)
    {
        $this->ruleset = $ruleset;
        $this->fixer = $fixer;
    }

    public function create($path) : File
    {
        return new File($path, $this->ruleset, $this->fixer);
    }
}
