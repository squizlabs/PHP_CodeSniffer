<?php
namespace Coding_Standards_Analysis\Reports;

use PHP_CodeSniffer\Reports\Report;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Timing;

/**
 * Summary report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Summary report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHPCSSummaryReport implements Report
{

    /**
     * TRUE if this report needs error messages instead of just totals.
     *
     * @var boolean
     */
    public $recordErrors = false;


    /**
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array                $report      Prepared report data.
     * @param PHP_CodeSniffer_File $phpcsFile   The file being reported on.
     * @param boolean              $showSources Show sources?
     * @param int                  $width       Maximum allowed line width.
     *
     * @return boolean
     */
    public function generateFileReport($report, File $phpcsFile, $showSources=false, $width=80)
    {
        $tokens = $phpcsFile->getTokens();
        if ($phpcsFile->numTokens === 0) {
            $lines = 0;
        } else if (isset($tokens[($phpcsFile->numTokens - 1)]) === true) {
            $lines = $tokens[($phpcsFile->numTokens - 1)]['line'];
        } else {
            $lines = 0;
        }

        echo $lines.'|'.$phpcsFile->numTokens.'|'.(int) $phpcsFile->fromCache.PHP_EOL;
        return true;

    }//end generateFileReport()


    /**
     * Prints the source of all errors and warnings.
     *
     * @param string  $cachedData    Any partial report data that was returned from
     *                               generateFileReport during the run.
     * @param int     $totalFiles    Total number of files processed during the run.
     * @param int     $totalErrors   Total number of errors found during the run.
     * @param int     $totalWarnings Total number of warnings found during the run.
     * @param int     $totalFixable  Total number of problems that can be fixed.
     * @param boolean $showSources   Show sources?
     * @param int     $width         Maximum allowed line width.
     * @param boolean $toScreen      Is the report being printed to screen?
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
        $numTokens   = 0;
        $numLines    = 0;
        $totalFiles  = 0;
        $cachedFiles = 0;

        $lines = explode(PHP_EOL, trim($cachedData));
        foreach ($lines as $line) {
            $parts = explode('|', $line);

            $totalFiles++;
            $numTokens += $parts[1];
            $numLines  += $parts[0];

            if ($parts[2] === '1') {
                $cachedFiles++;
            }
        }

        echo "Processed $totalFiles files ($cachedFiles cached) containing $numTokens tokens across $numLines uncached lines".PHP_EOL;
        Timing::printRunTime();

    }//end generate()


}//end class
