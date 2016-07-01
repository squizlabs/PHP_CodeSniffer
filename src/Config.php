<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

final class Config
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

    public function __construct(array $options)
    {
        $this->standards = $options['standards'];
        $this->reportWidth = $options['reportWidth'];
        $this->reportClass = $options['reportClass'];
    }

    public function getStandards() : array
    {
        return $this->standards;
    }

    public function getReportWidth() : int
    {
        return $this->reportWidth;
    }

    public function getReportClass() : string
    {
        return $this->reportClass;
    }
}
