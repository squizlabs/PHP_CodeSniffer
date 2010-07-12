<?php
/**
 * Gitblame report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Ben Selby <benmatselby@gmail.com>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id: IsCamelCapsTest.php 240585 2007-08-02 00:05:40Z squiz $
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Gitblame report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Ben Selby <benmatselby@gmail.com>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.2
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Reports_Gitblame implements PHP_CodeSniffer_Report
{


    /**
     * Prints the author of all errors and warnings, as given by "git blame".
     *
     * Requires you to have the git command in your path.
     *
     * @param array   $report       Prepared report.
     * @param boolean $showWarnings Show warnings?
     * @param boolean $showSources  Show sources?
     * @param int     $width        Maximum allowed lne width.
     *
     * @return string
     */
    public function generate(
        $report,
        $showWarnings=true,
        $showSources=false,
        $width=80
    ) {
        $authors = array();
        $praise  = array();
        $sources = array();
        $width   = max($width, 70);

        $errorsShown = 0;

        foreach ($report['files'] as $filename => $file) {
            $blames = $this->getGitblameContent($filename);

            foreach ($file['messages'] as $line => $lineErrors) {
                $author = $this->getGitAuthor($blames[($line - 1)]);
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

                unset($blames[$line - 1]);
            }//end foreach

            // No go through and give the authors some credit for
            // all the lines that do not have errors.
            foreach ($blames as $line) {
                $author = $this->getGitAuthor($line);
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

        echo PHP_EOL.'PHP CODE SNIFFER GIT BLAME SUMMARY'.PHP_EOL;
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
     * Extract the author from a git blame line.
     *
     * @param string $line Line to parse.
     *
     * @return mixed string or false if impossible to recover.
     */
    protected static function getGitAuthor($line)
    {
        $blameParts = array();
        $line = preg_replace('|\s+|', ' ', $line);
        preg_match('|\(.+[0-9]{4}-[0-9]{2}-[0-9]{2}\s+[0-9]+\)|', $line, $blameParts);

        if (!isset($blameParts[0])) {
            return false;
        }

        $parts = explode(' ', $blameParts[0]);

        if (count($parts) < 2) {
            return false;
        }

        $parts = array_slice($parts, 0, count($parts) -2);

        return preg_replace('|\(|', '', implode($parts, ' '));

    }//end getGitAuthor()


    /**
     * Gets the git output.
     *
     * @param string $filename File to blame.
     *
     * @return array
     */
    protected function getGitblameContent($filename)
    {
        $cwd = getcwd();

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            echo 'Getting GIT blame info for '.basename($filename).'... ';
        }

        $fileParts = explode('/', $filename);
        $found     = false;
        $location  = '';
        while (empty($fileParts) === false) {
            array_pop($fileParts);
            $location = implode($fileParts, '/');
            if (is_dir($location.'/.git')) {
                $found = true;
                break;
            }
        }

        if ($found === true) {
            chdir($location);
        } else {
            echo 'ERROR: Could not locate .git directory '.PHP_EOL.PHP_EOL;
            exit(2);
        }

        $command    = 'git blame --date=short '.$filename;
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
        chdir($cwd);

        return $blames;

    }//end getGitblameContent()


}//end class

?>
