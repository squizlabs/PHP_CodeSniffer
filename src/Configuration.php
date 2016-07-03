<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;

final class Configuration
{
    /**
     * @var array
     */
    private $standards = [];

    /**
     * @var int
     */
    private $reportWidth;

    /**
     * @var string
     */
    private $reportClass;

    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * @var string[]
     */
    private $sniffs = [];

    /**
     * @var array
     */
    private $source = [];

    public function __construct(ConfigurationResolver $configurationResolver)
    {
        $this->configurationResolver = $configurationResolver;
    }

    public function resolveFromArray(array $options)
    {
        $options = $this->configurationResolver->resolve($options);

        $this->standards = $options['standards'];
        $this->sniffs = $options['sniffs'];
        $this->reportWidth = $options['reportWidth'];
        $this->reportClass = $options['reportClass'];
        $this->source = $options['source'];
    }

    public function getStandards() : array
    {
        return $this->standards;
    }

    public function getSniff() : array
    {
        return $this->sniffs;
    }

    public function getReportWidth() : int
    {
        return $this->reportWidth;
    }

    public function getReportClass() : string
    {
        return $this->reportClass;
    }

    public function getSource() : array
    {
        return $this->source;
    }
}
