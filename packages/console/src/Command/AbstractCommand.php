<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractCommand extends Command
{
    protected function addCommonArgumentsAndOptions()
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'One or more files or directories to process');
        $this->addOption('standard', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The name(s) of the coding standard to use', ['psr2']);
        $this->addOption('sniffs', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'List of sniff codes to use.');
    }
}
