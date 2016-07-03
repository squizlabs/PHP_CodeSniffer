<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PHP7_CodeSniffer\Configuration;
use Symplify\PHP7_CodeSniffer\Reporter;
use Symplify\PHP7_CodeSniffer\Runner;

final class CheckCommand extends AbstractCommand
{
    /**
     * @var Runner
     */
    private $runner;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Reporter
     */
    private $reporter;

    public function __construct(Runner $runner, Configuration $configuration, Reporter $reporter)
    {
        $this->runner = $runner;
        $this->configuration = $configuration;
        $this->reporter = $reporter;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('check');
        $this->setDescription('Checks code against coding standard.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $arguments = $input->getArguments();
        unset($arguments['command']);
        $this->configuration->resolveFromArray($arguments);

        $this->runner->runPHPCS();

        if ($this->reporter->getTotalErrors() + $this->reporter->getTotalWarnings()) {
            // print errors
            // add custom style with report writing out
            $reports = $this->reporter->getReports();
            $output->write('EEE');

            $output->write('Some errors were found');
            return 1;
        }
        
        return 0;
    }
}
