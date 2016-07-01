<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Console;

use Symfony\Component\Console\Application;
use Symplify\PHP7_CodeSniffer\Php7CodeSniffer;

final class CodeSnifferApplication extends Application
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('PHP 7 Code Sniffer', Php7CodeSniffer::VERSION);
    }
}
