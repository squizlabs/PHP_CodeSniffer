<?php
/**
 * Git blame report for PHP_CodeSniffer.
 *
 * @author    Ben Selby <benmatselby@gmail.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

class Gitblame extends VersionControl
{

    /**
     * The name of the report we want in the output
     *
     * @var string
     */
    protected $reportName = 'GIT';


    /**
     * Extract the author from a blame line.
     *
     * @param string $line Line to parse.
     *
     * @return mixed string or false if impossible to recover.
     */
    protected function getAuthor($line)
    {
        $blameParts = array();
        $line       = preg_replace('|\s+|', ' ', $line);
        preg_match(
            '|\(.+[0-9]{4}-[0-9]{2}-[0-9]{2}\s+[0-9]+\)|',
            $line,
            $blameParts
        );

        if (isset($blameParts[0]) === false) {
            return false;
        }

        $parts = explode(' ', $blameParts[0]);

        if (count($parts) < 2) {
            return false;
        }

        $parts  = array_slice($parts, 0, (count($parts) - 2));
        $author = preg_replace('|\(|', '', implode($parts, ' '));
        return $author;

    }//end getAuthor()


    /**
     * Gets the blame output.
     *
     * @param string $filename File to blame.
     *
     * @return array
     */
    protected function getBlameContent($filename)
    {
        $cwd = getcwd();

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            echo 'Getting GIT blame info for '.basename($filename).'... ';
        }

        $fileParts = explode(DIRECTORY_SEPARATOR, $filename);
        $found     = false;
        $location  = '';
        while (empty($fileParts) === false) {
            array_pop($fileParts);
            $location = implode($fileParts, DIRECTORY_SEPARATOR);
            if (is_dir($location.DIRECTORY_SEPARATOR.'.git') === true) {
                $found = true;
                break;
            }
        }

        if ($found === true) {
            chdir($location);
        } else {
            echo 'ERROR: Could not locate .git directory '.PHP_EOL.PHP_EOL;
            exit(3);
        }

        $command = 'git blame --date=short "'.$filename.'" 2>&1';
        $handle  = popen($command, 'r');
        if ($handle === false) {
            echo 'ERROR: Could not execute "'.$command.'"'.PHP_EOL.PHP_EOL;
            exit(3);
        }

        $rawContent = stream_get_contents($handle);
        fclose($handle);

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            echo 'DONE'.PHP_EOL;
        }

        $blames = explode("\n", $rawContent);
        chdir($cwd);

        return $blames;

    }//end getBlameContent()


}//end class
