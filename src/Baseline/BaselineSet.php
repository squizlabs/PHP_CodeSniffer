<?php
/**
 * Baseline collection class to store and query baselined violations
 *
 * @author    Frank Dekker <fdekker@123inkt.nl>
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
     * @param ViolationBaseline $entry the entry to add to the collection
     *
     * @return void
     */
    public function addEntry(ViolationBaseline $entry)
    {
        $this->violations[$entry->getSniffName()][$entry->getSignature()][] = $entry;

    }//end addEntry()


    /**
     * Test if the given sniff and filename is in the baseline collection
     *
     * @param string $sniffName the name of the sniff to search for
     * @param string $fileName  the full filename of the file to match
     * @param string $signature the code signature of the violation
     *
     * @return bool
     */
    public function contains($sniffName, $fileName, $signature)
    {
        if (isset($this->violations[$sniffName][$signature]) === false) {
            return false;
        }

        // Normalize slashes in file name.
        $fileName = str_replace('\\', '/', $fileName);

        foreach ($this->violations[$sniffName][$signature] as $baseline) {
            if ($baseline->matches($fileName) === true) {
                return true;
            }
        }

        return false;

    }//end contains()


}//end class
