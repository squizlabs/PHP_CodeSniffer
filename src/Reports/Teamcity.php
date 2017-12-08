<?php
/**
 * TeamCity report for PHP_CodeSniffer.
 *
 * @author    Stanislav Korchagin <korchasa@gmail.com>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;

class Teamcity implements Report
{


    /**
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array                       $report      Prepared report data.
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being reported on.
     * @param bool                        $showSources Show sources?
     * @param int                         $width       Maximum allowed line width.
     *
     * @return bool
     */
    public function generateFileReport($report, File $phpcsFile, $showSources=false, $width=80)
    {
        $out = $this->formatLine('testStarted', ['name' => $report['filename']]);

        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $message = sprintf('Line %d, column %d: %s', $line, $column, $error['message']);
                    if ($error['type'] === 'ERROR') {
                        $out .= $this->formatLine(
                            'testFailed',
                            [
                             'name'    => $report['filename'],
                             'message' => $message,
                            ]
                        );
                    } else {
                        $out .= $this->formatLine(
                            'message',
                            [
                             'text'   => $report['filename'].' '.$message,
                             'status' => 'WARNING',
                            ]
                        );
                    }
                }
            }//end foreach
        }//end foreach

        $out .= $this->formatLine('testFinished', ['name' => $report['filename']]);

        echo $out;
        return true;

    }//end generateFileReport()


     /**
      * Generates a JSON report.
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
        echo $cachedData;

    }//end generate()


    /**
     * Format one line
     *
     * @param string $type         Record type
     * @param array  $placeholders Hashmap with live parts
     *
     * @return string
     */
    public function formatLine($type, $placeholders)
    {
        $internalParts = [];
        foreach ($placeholders as $name => $value) {
            $internalParts[] = ' '.$name.'=\''.$this->escape($value).'\'';
        }

        $internal = implode(' ', array_values($internalParts));
        return "##teamcity[$type $internal]\n";

    }//end formatLine()


    /**
     * Escape special teamcity characters
     * https://confluence.jetbrains.com/display/TCD7/Build+Script+Interaction+with+TeamCity
     *
     * @param string $string String to escape
     *
     * @return string
     */
    public function escape($string)
    {
        $escapeCharacterMap = [
                               '\'' => '|\'',
                               "\n" => '|n',
                               "\r" => '|r',
                               '|'  => '||',
                               '['  => '|[',
                               ']'  => '|]',
                              ];
        return preg_replace_callback(
            '/([\'\n\r|[\]])|\\\\u(\d{4})/',
            function ($matches) use ($escapeCharacterMap) {
                if (isset($matches[1]) === true) {
                    return $escapeCharacterMap[$matches[1]];
                }

                if (isset($matches[2]) === true) {
                    return '|0x'.$matches[2];
                }

                throw new \LogicException('Unexpected match combination.');
            },
            $string
        );

    }//end escape()


}//end class
