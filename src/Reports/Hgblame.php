<?php
/**
 * Mercurial blame report for PHP_CodeSniffer.
 *
 * @author    Ben Selby <benmatselby@gmail.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Exceptions\DeepExitException;

class Hgblame extends VersionControl
{

    /**
     * The name of the report we want in the output
     *
     * @var string
     */
    protected $reportName = 'MERCURIAL';


    /**
     * Extract the author from a blame line.
     *
     * @param string $line Line to parse.
     *
     * @return mixed string or false if impossible to recover.
     */
    protected function getAuthor($line)
    {
        $blameParts = [];
        $line       = preg_replace('|\s+|', ' ', $line);

        preg_match(
            '|(.+[0-9]{2}:[0-9]{2}:[0-9]{2}\s[0-9]{4}\s.[0-9]{4}:)|',
            $line,
            $blameParts
        );

        if (isset($blameParts[0]) === false) {
            return false;
        }

        $parts = explode(' ', $blameParts[0]);

        if (count($parts) < 6) {
            return false;
        }

        $parts = array_slice($parts, 0, (count($parts) - 6));

        return trim(preg_replace('|<.+>|', '', implode($parts, ' ')));

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

        $fileParts = explode(DIRECTORY_SEPARATOR, $filename);
        $found     = false;
        $location  = '';
        while (empty($fileParts) === false) {
            array_pop($fileParts);
            $location = implode($fileParts, DIRECTORY_SEPARATOR);
            if (is_dir($location.DIRECTORY_SEPARATOR.'.hg') === true) {
                $found = true;
                break;
            }
        }

        if ($found === true) {
            chdir($location);
        } else {
            $error = 'ERROR: Could not locate .hg directory '.PHP_EOL.PHP_EOL;
            throw new DeepExitException($error, 3);
        }

        $command = 'hg blame -u -d -v "'.$filename.'" 2>&1';
        $handle  = popen($command, 'r');
        if ($handle === false) {
            $error = 'ERROR: Could not execute "'.$command.'"'.PHP_EOL.PHP_EOL;
            throw new DeepExitException($error, 3);
        }

        $rawContent = stream_get_contents($handle);
        fclose($handle);

        $blames = explode("\n", $rawContent);
        chdir($cwd);

        return $blames;

    }//end getBlameContent()


}//end class
