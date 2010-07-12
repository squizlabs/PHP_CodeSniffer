<?php
/**
 * Svnblame report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: IsCamelCapsTest.php 240585 2007-08-02 00:05:40Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Svnblame report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Gabriele Santini <gsantini@sqli.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Reports_Svnblame implements PHP_CodeSniffer_Report
{


    /**
     * Prints the author of all errors and warnings, as given by "svn blame".
     *
     * Requires you to have the svn command in your path.
     *
     * @param array   $report       Prepared report.
     * @param boolean $showSources  Show sources?
     * @param int     $width        Maximum allowed lne width.
     * 
     * @return string
     */
    public function generate(
        $report,
        $showSources=false,
        $width=80
    ) {
        $authors = array();
        $praise  = array();
        $sources = array();
        $width   = max($width, 70);

        $errorsShown = 0;

        foreach ($report['files'] as $filename => $file) {
            $blames = $this->getSvnblameContent($filename);

            foreach ($file['messages'] as $line => $lineErrors) {
                $author = $this->getSvnAuthor($blames[($line - 1)]);
                if ($author === false) {
                    continue;
                }

                if (isset($authors[$author]) === false) {
                    $authors[$author] = 0;
                    $praise[$author]  = array(
                                         'good' => 0,
                                         'bad'  => 0,
                                        );
                }

                $praise[$author]['bad']++;

                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $errorsShown++;
                        $authors[$author]++;

                        if ($showSources === true) {
                            $source = $error['source'];
                            if (isset($sources[$author][$source]) === false) {
                                $sources[$author][$source] = 1;
                            } else {
                                $sources[$author][$source]++;
                            }
                        }
                    }
                }

                unset($blames[$line]);
            }//end foreach

            // No go through and give the authors some credit for
            // all the lines that do not have errors.
            foreach ($blames as $line) {
                $author = $this->getSvnAuthor($line);
                if (false === $author) {
                    continue;
                }

                if (isset($authors[$author]) === false) {
                    // This author doesn't have any errors.
                    if (PHP_CODESNIFFER_VERBOSITY === 0) {
                        continue;
                    }

                    $authors[$author] = 0;
                    $praise[$author]  = array(
                                         'good' => 0,
                                         'bad'  => 0,
                                        );
                }

                $praise[$author]['good']++;
            }//end foreach
        }//end foreach

        if ($errorsShown === 0) {
            // Nothing to show.
            return 0;
        }

        arsort($authors);

        echo PHP_EOL.'PHP CODE SNIFFER SVN BLAME SUMMARY'.PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL;
        if ($showSources === true) {
            echo 'AUTHOR   SOURCE'.str_repeat(' ', ($width - 27)).'COUNT (%)'.PHP_EOL;
            echo str_repeat('-', $width).PHP_EOL;
        } else {
            echo 'AUTHOR'.str_repeat(' ', ($width - 18)).'COUNT (%)'.PHP_EOL;
            echo str_repeat('-', $width).PHP_EOL;
        }

        foreach ($authors as $author => $count) {
            if ($praise[$author]['good'] === 0) {
                $percent = 0;
            } else {
                $percent = round(($praise[$author]['bad'] / $praise[$author]['good'] * 100), 2);
            }

            echo $author.str_repeat(' ', ($width - 12 - strlen($author))).$count.' ('.$percent.')'.PHP_EOL;

            if ($showSources === true && isset($sources[$author]) === true) {
                $errors = $sources[$author];
                asort($errors);
                $errors = array_reverse($errors);

                foreach ($errors as $source => $count) {
                    if ($source === 'count') {
                        continue;
                    }

                    echo '         '.$source.str_repeat(' ', ($width - 21 - strlen($source))).$count.PHP_EOL;
                }
            }
        }//end foreach

        echo str_repeat('-', $width).PHP_EOL;
        echo 'A TOTAL OF '.$errorsShown.' SNIFF VIOLATION(S) ';
        echo 'WERE COMMITTED BY '.count($authors).' AUTHOR(S)'.PHP_EOL;
        echo str_repeat('-', $width).PHP_EOL.PHP_EOL;

        if (class_exists('PHP_Timer', false) === true) {
            echo PHP_Timer::resourceUsage().PHP_EOL.PHP_EOL;
        }

        return $errorsShown;

    }//end generate()


    /**
     * Extract the author from an svn blame line.
     * 
     * @param string $line Line to parse.
     * 
     * @return mixed string or false if impossible to recover.
     */
    protected static function getSvnAuthor($line)
    {
        $blameParts = array();
        preg_match('|\s*([^\s]+)\s+([^\s]+)|', $line, $blameParts);

        if (isset($blameParts[2]) === false) {
            return false;
        }

        return $blameParts[2];

    }//end getSvnAuthor()


    /**
     * Gets the svn output.
     * 
     * @param string $filename File to blame.
     * 
     * @return array
     */
    protected function getSvnblameContent($filename)
    {
        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            echo 'Getting SVN blame info for '.basename($filename).'... ';
        }

        $command = 'svn blame '.$filename;
        $handle  = popen($command, 'r');
        if ($handle === false) {
            echo 'ERROR: Could not execute "'.$command.'"'.PHP_EOL.PHP_EOL;
            exit(2);
        }

        $rawContent = stream_get_contents($handle);
        fclose($handle);

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            echo 'DONE'.PHP_EOL;
        }

        $blames = explode("\n", $rawContent);

        return $blames;

    }//end getSvnblameContent()


}//end class

?>
