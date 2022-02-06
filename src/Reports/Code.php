<?php
/**
 * Full report for PHP_CodeSniffer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util;

class Code implements Report
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

        // How many lines to show about and below the error line.
        $surroundingLines = 2;

        $file   = $report['filename'];
        $tokens = $phpcsFile->getTokens();
        if (empty($tokens) === true) {
            if (PHP_CODESNIFFER_VERBOSITY === 1) {
                $startTime = microtime(true);
                echo 'CODE report is parsing '.basename($file).' ';
            } else if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo "CODE report is forcing parse of $file".PHP_EOL;
            }

            try {
                $phpcsFile->parse();
            } catch (\Exception $e) {
                // This is a second parse, so ignore exceptions.
                // They would have been added to the file's error list already.
            }

            if (PHP_CODESNIFFER_VERBOSITY === 1) {
                $timeTaken = ((microtime(true) - $startTime) * 1000);
                if ($timeTaken < 1000) {
                    $timeTaken = round($timeTaken);
                    echo "DONE in {$timeTaken}ms";
                } else {
                    $timeTaken = round(($timeTaken / 1000), 2);
                    echo "DONE in $timeTaken secs";
                }

                echo PHP_EOL;
            }

            $tokens = $phpcsFile->getTokens();
        }//end if

        // Create an array that maps lines to the first token on the line.
        $lineTokens = [];
        $lastLine   = 0;
        $stackPtr   = 0;
        foreach ($tokens as $stackPtr => $token) {
            if ($token['line'] !== $lastLine) {
                if ($lastLine > 0) {
                    $lineTokens[$lastLine]['end'] = ($stackPtr - 1);
                }

                $lastLine++;
                $lineTokens[$lastLine] = [
                    'start' => $stackPtr,
                    'end'   => null,
                ];
            }
        }

        // Make sure the last token in the file sits on an imaginary
        // last line so it is easier to generate code snippets at the
        // end of the file.
        $lineTokens[$lastLine]['end'] = $stackPtr;

        // Determine the longest code line we will be showing.
        $maxSnippetLength = 0;
        $eolLen           = strlen($phpcsFile->eolChar);
        foreach ($report['messages'] as $line => $lineErrors) {
            $startLine = max(($line - $surroundingLines), 1);
            $endLine   = min(($line + $surroundingLines), $lastLine);

            $maxLineNumLength = strlen($endLine);

            for ($i = $startLine; $i <= $endLine; $i++) {
                if ($i === 1) {
                    continue;
                }

                $lineLength       = ($tokens[($lineTokens[$i]['start'] - 1)]['column'] + $tokens[($lineTokens[$i]['start'] - 1)]['length'] - $eolLen);
                $maxSnippetLength = max($lineLength, $maxSnippetLength);
            }
        }

        $maxSnippetLength += ($maxLineNumLength + 8);

        // Determine the longest error message we will be showing.
        $maxErrorLength = 0;
        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $length = strlen($error['message']);
                    if ($showSources === true) {
                        $length += (strlen($error['source']) + 3);
                    }

                    $maxErrorLength = max($maxErrorLength, ($length + 1));
                }
            }
        }

        // The padding that all lines will require that are printing an error message overflow.
        if ($report['warnings'] > 0) {
            $typeLength = 7;
        } else {
            $typeLength = 5;
        }

        $errorPadding  = str_repeat(' ', ($maxLineNumLength + 7));
        $errorPadding .= str_repeat(' ', $typeLength);
        $errorPadding .= ' ';
        if ($report['fixable'] > 0) {
            $errorPadding .= '    ';
        }

        $errorPaddingLength = strlen($errorPadding);

        // The maximum amount of space an error message can use.
        $maxErrorSpace = ($width - $errorPaddingLength);
        if ($showSources === true) {
            // Account for the chars used to print colors.
            $maxErrorSpace += 8;
        }

        // Figure out the max report width we need and can use.
        $fileLength = strlen($file);
        $maxWidth   = max(($fileLength + 6), ($maxErrorLength + $errorPaddingLength));
        $width      = max(min($width, $maxWidth), $maxSnippetLength);
        if ($width < 70) {
            $width = 70;
        }

        // Print the file header.
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

        foreach ($report['messages'] as $line => $lineErrors) {
            $startLine = max(($line - $surroundingLines), 1);
            $endLine   = min(($line + $surroundingLines), $lastLine);

            $snippet = '';
            if (isset($lineTokens[$startLine]) === true) {
                for ($i = $lineTokens[$startLine]['start']; $i <= $lineTokens[$endLine]['end']; $i++) {
                    $snippetLine = $tokens[$i]['line'];
                    if ($lineTokens[$snippetLine]['start'] === $i) {
                        // Starting a new line.
                        if ($snippetLine === $line) {
                            $snippet .= "\033[1m".'>> ';
                        } else {
                            $snippet .= '   ';
                        }

                        $snippet .= str_repeat(' ', ($maxLineNumLength - strlen($snippetLine)));
                        $snippet .= $snippetLine.':  ';
                        if ($snippetLine === $line) {
                            $snippet .= "\033[0m";
                        }
                    }

                    if (isset($tokens[$i]['orig_content']) === true) {
                        $tokenContent = $tokens[$i]['orig_content'];
                    } else {
                        $tokenContent = $tokens[$i]['content'];
                    }

                    if (strpos($tokenContent, "\t") !== false) {
                        $token            = $tokens[$i];
                        $token['content'] = $tokenContent;
                        if (stripos(PHP_OS, 'WIN') === 0) {
                            $tab = "\000";
                        } else {
                            $tab = "\033[30;1m»\033[0m";
                        }

                        $phpcsFile->tokenizer->replaceTabsInToken($token, $tab, "\000");
                        $tokenContent = $token['content'];
                    }

                    $tokenContent = Util\Common::prepareForOutput($tokenContent, ["\r", "\n", "\t"]);
                    $tokenContent = str_replace("\000", ' ', $tokenContent);

                    $underline = false;
                    if ($snippetLine === $line && isset($lineErrors[$tokens[$i]['column']]) === true) {
                        $underline = true;
                    }

                    // Underline invisible characters as well.
                    if ($underline === true && trim($tokenContent) === '') {
                        $snippet .= "\033[4m".' '."\033[0m".$tokenContent;
                    } else {
                        if ($underline === true) {
                            $snippet .= "\033[4m";
                        }

                        $snippet .= $tokenContent;

                        if ($underline === true) {
                            $snippet .= "\033[0m";
                        }
                    }
                }//end for
            }//end if

            echo str_repeat('-', $width).PHP_EOL;

            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $padding = ($maxLineNumLength - strlen($line));
                    echo 'LINE '.str_repeat(' ', $padding).$line.': ';

                    if ($error['type'] === 'ERROR') {
                        echo "\033[31mERROR\033[0m";
                        if ($report['warnings'] > 0) {
                            echo '  ';
                        }
                    } else {
                        echo "\033[33mWARNING\033[0m";
                    }

                    echo ' ';
                    if ($report['fixable'] > 0) {
                        echo '[';
                        if ($error['fixable'] === true) {
                            echo 'x';
                        } else {
                            echo ' ';
                        }

                        echo '] ';
                    }

                    $message = $error['message'];
                    $message = str_replace("\n", "\n".$errorPadding, $message);
                    if ($showSources === true) {
                        $message = "\033[1m".$message."\033[0m".' ('.$error['source'].')';
                    }

                    $errorMsg = wordwrap(
                        $message,
                        $maxErrorSpace,
                        PHP_EOL.$errorPadding
                    );

                    echo $errorMsg.PHP_EOL;
                }//end foreach
            }//end foreach

            echo str_repeat('-', $width).PHP_EOL;
            echo rtrim($snippet).PHP_EOL;
        }//end foreach

        echo str_repeat('-', $width).PHP_EOL;
        if ($report['fixable'] > 0) {
            echo "\033[1m".'PHPCBF CAN FIX THE '.$report['fixable'].' MARKED SNIFF VIOLATIONS AUTOMATICALLY'."\033[0m".PHP_EOL;
            echo str_repeat('-', $width).PHP_EOL;
        }

        return true;

    }//end generateFileReport()


    /**
     * Prints all errors and warnings for each file processed.
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
        if ($cachedData === '') {
            return;
        }

        echo $cachedData;

        if ($toScreen === true && $interactive === false) {
            Util\Timing::printRunTime();
        }

    }//end generate()


}//end class
