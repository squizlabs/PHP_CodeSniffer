<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Reports;

use Symplify\PHP7_CodeSniffer\Files\File;
use Symplify\PHP7_CodeSniffer\Util;

final class Full implements ReportInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateFileReport(array $report, File $file) : bool
    {
        $width = 80;
        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            // Nothing to print.
            return false;
        }

        // The length of the word ERROR or WARNING; used for padding.
        if ($report['warnings'] > 0) {
            $typeLength = 7;
        } else {
            $typeLength = 5;
        }

        // Work out the max line number length for formatting.
        $maxLineNumLength = max(array_map('strlen', array_keys($report['messages'])));

        // The padding that all lines will require that are
        // printing an error message overflow.
        $paddingLine2  = str_repeat(' ', ($maxLineNumLength + 1));
        $paddingLine2 .= ' | ';
        $paddingLine2 .= str_repeat(' ', $typeLength);
        $paddingLine2 .= ' | ';
        if ($report['fixable'] > 0) {
            $paddingLine2 .= '    ';
        }

        $paddingLength = strlen($paddingLine2);

        // Make sure the report width isn't too big.
        $maxErrorLength = 0;
        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $length = strlen($error['message']);
                    $length += (strlen($error['source']) + 3);

                    $maxErrorLength = max($maxErrorLength, ($length + 1));
                }
            }
        }

        $file       = $report['filename'];
        $fileLength = strlen($file);
        $maxWidth   = max(($fileLength + 6), ($maxErrorLength + $paddingLength));
        $width      = min($width, $maxWidth);
        if ($width < 70) {
            $width = 70;
        }

        echo PHP_EOL."\033[1mFILE: ";
        if ($fileLength <= ($width - 6)) {
            echo $file;
        } else {
            echo '...'.substr($file, ($fileLength - ($width - 6)));
        }

        echo "\033[0m".PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;

        echo "\033[1m".'FOUND '.$report['errors'].' ERROR';
        if ($report['errors'] !== 1) {
            echo 'S';
        }

        if ($report['warnings'] > 0) {
            echo ' AND '.$report['warnings'].' WARNING';
            if ($report['warnings'] !== 1) {
                echo 'S';
            }
        }

        echo ' AFFECTING '.count($report['messages']).' LINE';
        if (count($report['messages']) !== 1) {
            echo 'S';
        }

        echo "\033[0m".PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;

        // The maximum amount of space an error message can use.
        $maxErrorSpace = ($width - $paddingLength - 1);
        // Account for the chars used to print colors.
        $maxErrorSpace += 8;

        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $message = $error['message'];
                    $message = str_replace("\n", "\n".$paddingLine2, $message);
                    $message = "\033[1m".$message."\033[0m".' ('.$error['source'].')';

                    // The padding that goes on the front of the line.
                    $padding  = ($maxLineNumLength - strlen($line));
                    $errorMsg = wordwrap(
                        $message,
                        $maxErrorSpace,
                        PHP_EOL.$paddingLine2
                    );

                    echo ' '.str_repeat(' ', $padding).$line.' | ';
                    if ($error['type'] === 'ERROR') {
                        echo "\033[31mERROR\033[0m";
                        if ($report['warnings'] > 0) {
                            echo '  ';
                        }
                    } else {
                        echo "\033[33mWARNING\033[0m";
                    }

                    echo ' | ';
                    if ($report['fixable'] > 0) {
                        echo '[';
                        if ($error['fixable'] === true) {
                            echo 'x';
                        } else {
                            echo ' ';
                        }

                        echo '] ';
                    }

                    echo $errorMsg.PHP_EOL;
                }//end foreach
            }//end foreach
        }//end foreach

        echo str_repeat('-', $width).PHP_EOL;
        if ($report['fixable'] > 0) {
            echo "\033[1m".'PHPCBF CAN FIX THE '.$report['fixable'].' MARKED SNIFF VIOLATIONS AUTOMATICALLY'."\033[0m".PHP_EOL;
            echo str_repeat('-', $width).PHP_EOL;
        }

        echo PHP_EOL;
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $cachedData, int $totalFiles, int $totalErrors, int $totalWarnings, int $totalFixable)
    {
        if ($cachedData === '') {
            return;
        }

        echo $cachedData;
    }
}
