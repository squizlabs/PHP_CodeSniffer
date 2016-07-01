<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Reporter\Report;

use Symplify\PHP7_CodeSniffer\Config;
use Symplify\PHP7_CodeSniffer\Reports\ReportInterface;
use Symplify\PHP7_CodeSniffer\SniffFinder\Composer\VendorDirProvider;

final class ReportFactory
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function create() : ReportInterface
    {
        return new $this->config->getReportClass();
    }
}