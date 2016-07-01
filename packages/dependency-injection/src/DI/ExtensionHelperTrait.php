<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\DependencyInjection\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class Php7CodeSnifferExtension extends CompilerExtension
{
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
        $containerBuilder = $this->getContainerBuilder();
        $config = $this->loadFromFile(__DIR__ . '/../config/services.neon');
        $this->compiler->parseServices($containerBuilder, $config);
    }

    private function loadConsoleCommandsToConsoleApplication()
    {
        $consoleApplication = $this->getDefinitionByType(Application::class);
        $containerBuilder = $this->getContainerBuilder();
        foreach ($containerBuilder->findByType(Command::class) as $definition) {
            $consoleApplication->addSetup('add', ['@'.$definition->getClass()]);
        }
    }

    private function loadEventSubscribersToEventDispatcher()
    {
        $eventDispatcher = $this->getDefinitionByType(EventDispatcherInterface::class);
        $containerBuilder = $this->getContainerBuilder();
        foreach ($containerBuilder->findByType(EventSubscriberInterface::class) as $definition) {
            $eventDispatcher->addSetup('addSubscriber', ['@'.$definition->getClass()]);
        }
    }

    private function getDefinitionByType(string $type) : ServiceDefinition
    {
        $containerBuilder = $this->getContainerBuilder();
        $definitionName = $containerBuilder->getByType($type);
        return $containerBuilder->getDefinition($definitionName);
    }
}
