<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\DependencyInjection\DI;

use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

/**
 * @method ContainerBuilder getContainerBuilder()
 */
trait ExtensionHelperTrait
{
    private function addServicesToCollector(string $collectorClass, string $collectedClass, string $adderMethodName)
    {
        $collectorDefinition = $this->getDefinitionByType($collectorClass);
        $containerBuilder = $this->getContainerBuilder();

        foreach ($containerBuilder->findByType($collectedClass) as $definition) {
            $collectorDefinition->addSetup(
                $adderMethodName,
                ['@'.$definition->getClass()]
            );
        }
    }

    private function getDefinitionByType(string $type) : ServiceDefinition
    {
        $containerBuilder = $this->getContainerBuilder();
        $definitionName = $containerBuilder->getByType($type);

        return $containerBuilder->getDefinition($definitionName);
    }
}
