<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\DependencyInjection\DI;

use Nette\DI\CompilerExtension;

final class Php7CodeSnifferExtension extends CompilerExtension
{
    /**
     * {@inheritdoc}
     */
    public function loadConfiguration()
    {
        $this->loadServicesFromConfig();
    }

    private function loadServicesFromConfig()
    {
        $containerBuilder = $this->getContainerBuilder();
        $config = $this->loadFromFile(__DIR__ . '/../config/services.neon');
        $this->compiler->parseServices($containerBuilder, $config);
    }
}
