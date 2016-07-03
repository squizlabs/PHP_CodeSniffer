<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Symplify\PHP7_CodeSniffer\Files\File;
use Symplify\PHP7_CodeSniffer\Reporter\Report\ReportFactory;

final class Reporter
{
    /**
     * @var array
     */
    private $reports = [];

    /**
     * @var int
     */
    private $totalErrors = 0;

    /**
     * @var int
     */
    private $totalWarnings = 0;

    /**
     * @var int
     */
    private $totalFiles = 0;

    /**
     * @var int
     */
    private $totalFixable = 0;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    public function getTotalErrors() : int
    {
        return $this->totalErrors;
    }

    public function getTotalWarnings() : int
    {
        return $this->totalWarnings;
    }

    public function cacheFileReport(File $file)
    {
        $this->reports[] = $reportData = $this->prepareFileReport($file);

        if ($reportData['errors'] || $reportData['warnings']) {
            $this->totalFiles++;
        }
        $this->totalErrors += $reportData['errors'];
        $this->totalWarnings += $reportData['warnings'];
        $this->totalFixable  += $reportData['fixable'];
    }

    /**
     * @return array
     */
    private function prepareFileReport(File $file) : array
    {
        $report = [
            'filename' => $file->getFilename(),
            'errors'  => $file->getErrorCount(),
            'warnings' => $file->getWarningCount(),
            'fixable' => $file->getFixableCount(),
            'messages' => [],
        ];

        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            return $report;
        }

        $errors = [];

        // Merge errors and warnings.
        foreach ($file->getErrors() as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                $newErrors = [];
                foreach ($colErrors as $data) {
                    $newErrors[] = array(
                                    'message'  => $data['message'],
                                    'source'   => $data['source'],
                                    'fixable'  => $data['fixable'],
                                    'type'     => 'ERROR',
                                   );
                }

                $errors[$line][$column] = $newErrors;
            }

            ksort($errors[$line]);
        }//end foreach

        foreach ($file->getWarnings() as $line => $lineWarnings) {
            foreach ($lineWarnings as $column => $colWarnings) {
                $newWarnings = array();
                foreach ($colWarnings as $data) {
                    $newWarnings[] = array(
                                      'message'  => $data['message'],
                                      'source'   => $data['source'],
                                      'fixable'  => $data['fixable'],
                                      'type'     => 'WARNING',
                                     );
                }

                if (isset($errors[$line]) === false) {
                    $errors[$line] = array();
                }

                if (isset($errors[$line][$column]) === true) {
                    $errors[$line][$column] = array_merge(
                        $newWarnings,
                        $errors[$line][$column]
                    );
                } else {
                    $errors[$line][$column] = $newWarnings;
                }
            }//end foreach

            ksort($errors[$line]);
        }//end foreach

        ksort($errors);
        $report['messages'] = $errors;
        return $report;
    }

    public function getReports() : array
    {
        return $this->reports;
    }
}
