<?php
/**
 * An exception thrown by PHP_CodeSniffer when it wants to exit from somewhere not in the main runner.
 * Allows the runner to return an exit code instead of putting exit codes elsewhere
 * in the source code.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Baseline;

class BaselineSet
{

    /**
     * A collection of a baselined violations
     *
     * @var array<string, ViolationBaseline[]>
     */
    private $violations = [];


    /**
     * Add a single entry to the baseline set
     *
     * @param  ViolationBaseline $entry the entry to add to the collection
     * @return void
     */
    public function addEntry(ViolationBaseline $entry)
    {
        $this->violations[$entry->getSniffName()][] = $entry;

    }//end addEntry()


    /**
     * Test if the given sniff and filename is in the baseline collection
     *
     * @param string $sniffName the name of the sniff to search for
     * @param string $fileName  the full filename of the file to match
     *
     * @return bool
     */
    public function contains($sniffName, $fileName)
    {
        if (isset($this->violations[$sniffName]) === false) {
            return false;
        }

        // Normalize slashes in file name.
        $fileName = str_replace('\\', '/', $fileName);

        foreach ($this->violations[$sniffName] as $baseline) {
            if ($baseline->matches($fileName) === true) {
                return true;
            }
        }

        return false;

    }//end contains()


}//end class
