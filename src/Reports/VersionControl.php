<?php
/**
 * Version control report base class for PHP_CodeSniffer.
 *
 * @author    Ben Selby <benmatselby@gmail.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Timing;

abstract class VersionControl implements Report
{

    /**
     * The name of the report we want in the output.
     *
     * @var string
     */
    protected $reportName = 'VERSION CONTROL';


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
        $blames = $this->getBlameContent($report['filename']);

        $authorCache = [];
        $praiseCache = [];
        $sourceCache = [];

        foreach ($report['messages'] as $line => $lineErrors) {
            $author = 'Unknown';
            if (isset($blames[($line - 1)]) === true) {
                $blameAuthor = $this->getAuthor($blames[($line - 1)]);
                if ($blameAuthor !== false) {
                    $author = $blameAuthor;
                }
            }

            if (isset($authorCache[$author]) === false) {
                $authorCache[$author] = 0;
                $praiseCache[$author] = [
                    'good' => 0,
                    'bad'  => 0,
                ];
            }

            $praiseCache[$author]['bad']++;

            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $authorCache[$author]++;

                    if ($showSources === true) {
                        $source = $error['source'];
                        if (isset($sourceCache[$author][$source]) === false) {
                            $sourceCache[$author][$source] = [
                                'count'   => 1,
                                'fixable' => $error['fixable'],
                            ];
                        } else {
                            $sourceCache[$author][$source]['count']++;
                        }
                    }
                }
            }

            unset($blames[($line - 1)]);
        }//end foreach

        // Now go through and give the authors some credit for
        // all the lines that do not have errors.
        foreach ($blames as $line) {
            $author = $this->getAuthor($line);
            if ($author === false) {
                $author = 'Unknown';
            }

            if (isset($authorCache[$author]) === false) {
                // This author doesn't have any errors.
                if (PHP_CODESNIFFER_VERBOSITY === 0) {
                    continue;
                }

                $authorCache[$author] = 0;
                $praiseCache[$author] = [
                    'good' => 0,
                    'bad'  => 0,
                ];
            }

            $praiseCache[$author]['good']++;
        }//end foreach

        foreach ($authorCache as $author => $errors) {
            echo "AUTHOR>>$author>>$errors".PHP_EOL;
        }

        foreach ($praiseCache as $author => $praise) {
            echo "PRAISE>>$author>>".$praise['good'].'>>'.$praise['bad'].PHP_EOL;
        }

        foreach ($sourceCache as $author => $sources) {
            foreach ($sources as $source => $sourceData) {
                $count   = $sourceData['count'];
                $fixable = (int) $sourceData['fixable'];
                echo "SOURCE>>$author>>$source>>$count>>$fixable".PHP_EOL;
            }
        }

        return true;

    }//end generateFileReport()


    /**
     * Prints the author of all errors and warnings, as given by "version control blame".
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
        $errorsShown = ($totalErrors + $totalWarnings);
        if ($errorsShown === 0) {
            // Nothing to show.
            return;
        }

        $lines = explode(PHP_EOL, $cachedData);
        array_pop($lines);

        if (empty($lines) === true) {
            return;
        }

        $authorCache = [];
        $praiseCache = [];
        $sourceCache = [];

        foreach ($lines as $line) {
            $parts = explode('>>', $line);
            switch ($parts[0]) {
            case 'AUTHOR':
                if (isset($authorCache[$parts[1]]) === false) {
                    $authorCache[$parts[1]] = $parts[2];
                } else {
                    $authorCache[$parts[1]] += $parts[2];
                }
                break;
            case 'PRAISE':
                if (isset($praiseCache[$parts[1]]) === false) {
                    $praiseCache[$parts[1]] = [
                        'good' => $parts[2],
                        'bad'  => $parts[3],
                    ];
                } else {
                    $praiseCache[$parts[1]]['good'] += $parts[2];
                    $praiseCache[$parts[1]]['bad']  += $parts[3];
                }
                break;
            case 'SOURCE':
                if (isset($praiseCache[$parts[1]]) === false) {
                    $praiseCache[$parts[1]] = [];
                }

                if (isset($sourceCache[$parts[1]][$parts[2]]) === false) {
                    $sourceCache[$parts[1]][$parts[2]] = [
                        'count'   => $parts[3],
                        'fixable' => (bool) $parts[4],
                    ];
                } else {
                    $sourceCache[$parts[1]][$parts[2]]['count'] += $parts[3];
                }
                break;
            default:
                break;
            }//end switch
        }//end foreach

        // Make sure the report width isn't too big.
        $maxLength = 0;
        foreach ($authorCache as $author => $count) {
            $maxLength = max($maxLength, strlen($author));
            if ($showSources === true && isset($sourceCache[$author]) === true) {
                foreach ($sourceCache[$author] as $source => $sourceData) {
                    if ($source === 'count') {
                        continue;
                    }

                    $maxLength = max($maxLength, (strlen($source) + 9));
                }
            }
        }

        $width = min($width, ($maxLength + 30));
        $width = max($width, 70);
        arsort($authorCache);

        echo PHP_EOL."\033[1m".'PHP CODE SNIFFER '.$this->reportName.' BLAME SUMMARY'."\033[0m".PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL."\033[1m";
        if ($showSources === true) {
            echo 'AUTHOR   SOURCE'.str_repeat(' ', ($width - 43)).'(Author %) (Overall %) COUNT'.PHP_EOL;
            echo str_repeat('-', $width).PHP_EOL;
        } else {
            echo 'AUTHOR'.str_repeat(' ', ($width - 34)).'(Author %) (Overall %) COUNT'.PHP_EOL;
            echo str_repeat('-', $width).PHP_EOL;
        }

        echo "\033[0m";

        if ($showSources === true) {
            $maxSniffWidth = ($width - 15);

            if ($totalFixable > 0) {
                $maxSniffWidth -= 4;
            }
        }

        $fixableSources = 0;

        foreach ($authorCache as $author => $count) {
            if ($praiseCache[$author]['good'] === 0) {
                $percent = 0;
            } else {
                $total   = ($praiseCache[$author]['bad'] + $praiseCache[$author]['good']);
                $percent = round(($praiseCache[$author]['bad'] / $total * 100), 2);
            }

            $overallPercent = '('.round((($count / $errorsShown) * 100), 2).')';
            $authorPercent  = '('.$percent.')';
            $line           = str_repeat(' ', (6 - strlen($count))).$count;
            $line           = str_repeat(' ', (12 - strlen($overallPercent))).$overallPercent.$line;
            $line           = str_repeat(' ', (11 - strlen($authorPercent))).$authorPercent.$line;
            $line           = $author.str_repeat(' ', ($width - strlen($author) - strlen($line))).$line;

            if ($showSources === true) {
                $line = "\033[1m$line\033[0m";
            }

            echo $line.PHP_EOL;

            if ($showSources === true && isset($sourceCache[$author]) === true) {
                $errors = $sourceCache[$author];
                asort($errors);
                $errors = array_reverse($errors);

                foreach ($errors as $source => $sourceData) {
                    if ($source === 'count') {
                        continue;
                    }

                    $count = $sourceData['count'];

                    $srcLength = strlen($source);
                    if ($srcLength > $maxSniffWidth) {
                        $source = substr($source, 0, $maxSniffWidth);
                    }

                    $line = str_repeat(' ', (5 - strlen($count))).$count;

                    echo '         ';
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

                    echo $source;
                    if ($totalFixable > 0) {
                        echo str_repeat(' ', ($width - 18 - strlen($source)));
                    } else {
                        echo str_repeat(' ', ($width - 14 - strlen($source)));
                    }

                    echo $line.PHP_EOL;
                }//end foreach
            }//end if
        }//end foreach

        echo str_repeat('-', $width).PHP_EOL;
        echo "\033[1m".'A TOTAL OF '.$errorsShown.' SNIFF VIOLATION';
        if ($errorsShown !== 1) {
            echo 'S';
        }

        echo ' WERE COMMITTED BY '.count($authorCache).' AUTHOR';
        if (count($authorCache) !== 1) {
            echo 'S';
        }

        echo "\033[0m";

        if ($totalFixable > 0) {
            if ($showSources === true) {
                echo PHP_EOL.str_repeat('-', $width).PHP_EOL;
                echo "\033[1mPHPCBF CAN FIX THE $fixableSources MARKED SOURCES AUTOMATICALLY ($totalFixable VIOLATIONS IN TOTAL)\033[0m";
            } else {
                echo PHP_EOL.str_repeat('-', $width).PHP_EOL;
                echo "\033[1mPHPCBF CAN FIX $totalFixable OF THESE SNIFF VIOLATIONS AUTOMATICALLY\033[0m";
            }
        }

        echo PHP_EOL.str_repeat('-', $width).PHP_EOL.PHP_EOL;

        if ($toScreen === true && $interactive === false) {
            Timing::printRunTime();
        }

    }//end generate()


    /**
     * Extract the author from a blame line.
     *
     * @param string $line Line to parse.
     *
     * @return mixed string or false if impossible to recover.
     */
    abstract protected function getAuthor($line);


    /**
     * Gets the blame output.
     *
     * @param string $filename File to blame.
     *
     * @return array
     */
    abstract protected function getBlameContent($filename);


}//end class
