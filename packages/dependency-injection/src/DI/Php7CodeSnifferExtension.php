<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\DependencyInjection\DI;

use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class Php7CodeSnifferExtension extends CompilerExtension
{
    use ExtensionHelperTrait;

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration()
    {
        $this->loadServicesFromConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeCompile()
    {
        $this->loadConsoleCommandsToConsoleApplication();
        $this->loadEventSubscribersToEventDispatcher();
    }

    private function loadServicesFromConfig()
    {
        $config = $this->loadFromFile(__DIR__ . '/../config/services.neon');
        $this->compiler->parseServices($this->getContainerBuilder(), $config);
    }

    private function loadConsoleCommandsToConsoleApplication()
    {
        $this->addServicesToCollector(Application::class, Command::class, 'add');
    }

    private function loadEventSubscribersToEventDispatcher()
    {
        $this->addServicesToCollector(EventDispatcherInterface::class, EventSubscriberInterface::class, 'addSubscriber');
    }
}
