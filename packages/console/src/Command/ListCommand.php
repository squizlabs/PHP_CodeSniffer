<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PHP7_CodeSniffer\SniffFinder\StandardFinder;

final class ListCommand extends Command
{
    /**
     * @var StandardFinder
     */
    private $standardFinder;

    public function __construct(StandardFinder $standardFinder)
    {
        $this->standardFinder = $standardFinder;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('list-standards');
        $this->setDescription('Shows a list of installed coding standards.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $standards = $this->standardFinder->getStandards();
        foreach ($standards as $name => $ruleset) {
            $output->writeln($name);
        }
    }
}
