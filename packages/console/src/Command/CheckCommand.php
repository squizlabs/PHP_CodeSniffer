<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PHP7_CodeSniffer\Configuration;
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

    public function __construct(Runner $runner, Configuration $configuration)
    {
        $this->runner = $runner;
        $this->configuration = $configuration;

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

        $source = $input->getArgument('source');

        $this->runner->runPHPCS();
        
//        dump($source);
        die;
    }
}
