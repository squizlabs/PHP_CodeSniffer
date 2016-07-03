<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Reporter\Report;

use Symplify\PHP7_CodeSniffer\Configuration;
use Symplify\PHP7_CodeSniffer\Reports\ReportInterface;
use Symplify\PHP7_CodeSniffer\SniffFinder\Composer\VendorDirProvider;

final class ReportFactory
{
    /**
     * @var Configuration
     */
    private $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @return ReportInterface
     */
    public function create() : ReportInterface
    {
        dump($this->config->getReportClass());
        die;
        
        return new $this->config->getReportClass();
    }
}