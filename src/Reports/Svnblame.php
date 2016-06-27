<?php
/**
 * SVN blame report for Symplify\PHP7_CodeSniffer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/Symplify\PHP7_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Symplify\PHP7_CodeSniffer\Reports;

class Svnblame extends VersionControl
{

    /**
     * The name of the report we want in the output
     *
     * @var string
     */
    protected $reportName = 'SVN';


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
        preg_match('|\s*([^\s]+)\s+([^\s]+)|', $line, $blameParts);

        if (isset($blameParts[2]) === false) {
            return false;
        }

        return $blameParts[2];

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
        if (PHP_CodeSniffer_VERBOSITY > 0) {
            echo 'Getting SVN blame info for '.basename($filename).'... ';
        }

        $command = 'svn blame "'.$filename.'" 2>&1';
        $handle  = popen($command, 'r');
        if ($handle === false) {
            echo 'ERROR: Could not execute "'.$command.'"'.PHP_EOL.PHP_EOL;
            exit(2);
        }

        $rawContent = stream_get_contents($handle);
        fclose($handle);

        if (PHP_CodeSniffer_VERBOSITY > 0) {
            echo 'DONE'.PHP_EOL;
        }

        $blames = explode("\n", $rawContent);

        return $blames;

    }//end getBlameContent()


}//end class
