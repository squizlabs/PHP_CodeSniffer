<?php
/**
 * Source report for PHP_CodeSniffer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Timing;

class Source implements Report
{


    /**
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array                 $report      Prepared report data.
     * @param \PHP_CodeSniffer\File $phpcsFile   The file being reported on.
     * @param bool                  $showSources Show sources?
     * @param int                   $width       Maximum allowed line width.
     *
     * @return bool
     */
    public function generateFileReport($report, File $phpcsFile, $showSources=false, $width=80)
    {
        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            // Nothing to print.
            return false;
        }

        $sources = array();

        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $src = $error['source'];
                    if (isset($sources[$src]) === false) {
                        $sources[$src] = array(
                                          'fixable' => (int) $error['fixable'],
                                          'count'   => 1,
                                         );
                    } else {
                        $sources[$src]['count']++;
                    }
                }
            }
        }

        foreach ($sources as $source => $data) {
            echo $source.'>>'.$data['fixable'].'>>'.$data['count'].PHP_EOL;
        }

        return true;

    }//end generateFileReport()


    /**
     * Prints the source of all errors and warnings.
     *
     * @param string $cachedData    Any partial report data that was returned from
     *                              generateFileReport during the run.
     * @param int    $totalFiles    Total number of files processed during the run.
     * @param int    $totalErrors   Total number of errors found during the run.
     * @param int    $totalWarnings Total number of warnings found during the run.
     * @param int    $totalFixable  Total number of problems that can be fixed.
     * @param bool   $showSources   Show sources?
     * @param int    $width         Maximum allowed line width.
     * @param bool   $interactive   Are we running in interactive mode?
     * @param bool   $toScreen      Is the report being printed to screen?
     *
     * @return void
     */
    public function generate(
        $cachedData,
        $totalFiles,
        $totalErrors,
        $totalWarnings,
        $totalFixable,
        $showSources=false,
        $width=80,
        $interactive=false,
        $toScreen=true
    ) {
        $lines = explode(PHP_EOL, $cachedData);
        array_pop($lines);

        if (empty($lines) === true) {
            return;
        }

        $sources   = array();
        $maxLength = 0;

        foreach ($lines as $line) {
            $parts   = explode('>>', $line);
            $source  = $parts[0];
            $fixable = (bool) $parts[1];
            $count   = $parts[2];

            if (isset($sources[$source]) === false) {
                if ($showSources === true) {
                    $parts = null;
                    $sniff = $source;
                } else {
                    $parts = explode('.', $source);
                    if ($parts[0] === 'Internal') {
                        $parts[2] = $parts[1];
                        $parts[1] = '';
                    }

                    $parts[1] = $this->makeFriendlyName($parts[1]);

                    $sniff = $this->makeFriendlyName($parts[2]);
                    if (isset($parts[3]) === true) {
                        $name    = $this->makeFriendlyName($parts[3]);
                        $name[0] = strtolower($name[0]);
                        $sniff  .= ' '.$name;
                        unset($parts[3]);
                    }

                    $parts[2] = $sniff;
                }//end if

                $maxLength = max($maxLength, strlen($sniff));

                $sources[$source] = array(
                                     'count'   => $count,
                                     'fixable' => $fixable,
                                     'parts'   => $parts,
                                    );
            } else {
                $sources[$source]['count'] += $count;
            }//end if

            $fileLen = strlen($parts[0]);
            $reportFiles[$parts[0]] = array(
                                       'errors'   => $parts[1],
                                       'warnings' => $parts[2],
                                       'strlen'   => $fileLen,
                                      );
        }//end foreach

        if ($showSources === true) {
            $width = min($width, ($maxLength + 11));
        } else {
            $width = min($width, ($maxLength + 41));
        }

        $width = max($width, 70);

        asort($sources);
        $sources = array_reverse($sources);

        echo PHP_EOL."\033[1mPHP CODE SNIFFER VIOLATION SOURCE SUMMARY\033[0m".PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL."\033[1m";
        if ($showSources === true) {
            if ($totalFixable > 0) {
                echo '    SOURCE'.str_repeat(' ', ($width - 15)).'COUNT'.PHP_EOL;
            } else {
                echo 'SOURCE'.str_repeat(' ', ($width - 11)).'COUNT'.PHP_EOL;
            }
        } else {
            if ($totalFixable > 0) {
                echo '    STANDARD  CATEGORY            SNIFF'.str_repeat(' ', ($width - 44)).'COUNT'.PHP_EOL;
            } else {
                echo 'STANDARD  CATEGORY            SNIFF'.str_repeat(' ', ($width - 40)).'COUNT'.PHP_EOL;
            }
        }

        echo "\033[0m".str_repeat('-', $width).PHP_EOL;

        $fixableSources = 0;

        if ($showSources === true) {
            $maxSniffWidth = ($width - 7);
        } else {
            $maxSniffWidth = ($width - 37);
        }

        if ($totalFixable > 0) {
            $maxSniffWidth -= 4;
        }

        foreach ($sources as $source => $sourceData) {
            if ($totalFixable > 0) {
                echo '[';
                if ($sourceData['fixable'] === true) {
                    echo 'x';
                    $fixableSources++;
                } else {
                    echo ' ';
                }

                echo '] ';
            }

            if ($showSources === true) {
                if (strlen($source) > $maxSniffWidth) {
                    $source = substr($source, 0, $maxSniffWidth);
                }

                echo $source;
                if ($totalFixable > 0) {
                    echo str_repeat(' ', ($width - 9 - strlen($source)));
                } else {
                    echo str_repeat(' ', ($width - 5 - strlen($source)));
                }
            } else {
                $parts = $sourceData['parts'];

                if (strlen($parts[0]) > 8) {
                    $parts[0] = substr($parts[0], 0, ((strlen($parts[0]) - 8) * -1));
                }

                echo $parts[0].str_repeat(' ', (10 - strlen($parts[0])));

                $category = $parts[1];
                if (strlen($category) > 18) {
                    $category = substr($category, 0, ((strlen($category) - 18) * -1));
                }

                echo $category.str_repeat(' ', (20 - strlen($category)));

                $sniff = $parts[2];
                if (strlen($sniff) > $maxSniffWidth) {
                    $sniff = substr($sniff, 0, $maxSniffWidth);
                }

                if ($totalFixable > 0) {
                    echo $sniff.str_repeat(' ', ($width - 39 - strlen($sniff)));
                } else {
                    echo $sniff.str_repeat(' ', ($width - 35 - strlen($sniff)));
                }
            }//end if

            echo $sourceData['count'].PHP_EOL;
        }//end foreach

        echo str_repeat('-', $width).PHP_EOL;
        echo "\033[1m".'A TOTAL OF '.($totalErrors + $totalWarnings).' SNIFF VIOLATION';
        if (($totalErrors + $totalWarnings) > 1) {
            echo 'S';
        }

        echo ' WERE FOUND IN '.count($sources).' SOURCE';
        if (count($sources) !== 1) {
            echo 'S';
        }

        echo "\033[0m";

        if ($totalFixable > 0) {
            echo PHP_EOL.str_repeat('-', $width).PHP_EOL;
            echo "\033[1mPHPCBF CAN FIX THE $fixableSources MARKED SOURCES AUTOMATICALLY ($totalFixable VIOLATIONS IN TOTAL)\033[0m";
        }

        echo PHP_EOL.str_repeat('-', $width).PHP_EOL.PHP_EOL;

        if ($toScreen === true && $interactive === false) {
            Timing::printRunTime();
        }

    }//end generate()


    /**
     * Converts a camel caps name into a readable string.
     *
     * @param string $name The camel caps name to convert.
     *
     * @return string
     */
    public function makeFriendlyName($name)
    {
        if (trim($name) === '') {
            return '';
        }

        $friendlyName = '';
        $length       = strlen($name);

        $lastWasUpper   = false;
        $lastWasNumeric = false;
        for ($i = 0; $i < $length; $i++) {
            if (is_numeric($name[$i]) === true) {
                if ($lastWasNumeric === false) {
                    $friendlyName .= ' ';
                }

                $lastWasUpper   = false;
                $lastWasNumeric = true;
            } else {
                $lastWasNumeric = false;

                $char = strtolower($name[$i]);
                if ($char === $name[$i]) {
                    // Lowercase.
                    $lastWasUpper = false;
                } else {
                    // Uppercase.
                    if ($lastWasUpper === false) {
                        $friendlyName .= ' ';
                        if ($i < ($length - 1)) {
                            $next = $name[($i + 1)];
                            if (strtolower($next) === $next) {
                                // Next char is lowercase so it is a word boundary.
                                $name[$i] = strtolower($name[$i]);
                            }
                        }
                    }

                    $lastWasUpper = true;
                }
            }//end if

            $friendlyName .= $name[$i];
        }//end for

        $friendlyName    = trim($friendlyName);
        $friendlyName[0] = strtoupper($friendlyName[0]);

        return $friendlyName;

    }//end makeFriendlyName()


}//end class
