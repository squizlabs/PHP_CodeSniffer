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
    /** @var array<string, ViolationBaseline[]> */
    private $violations = [];

    public function addEntry(ViolationBaseline $entry)
    {
        $this->violations[$entry->getSniffName()][] = $entry;
    }

    /**
     * @param string      $sniffName
     * @param string      $fileName
     *
     * @return bool
     */
    public function contains($sniffName, $fileName)
    {
        if (isset($this->violations[$sniffName]) === false) {
            return false;
        }

        // normalize slashes in file name
        $fileName = str_replace('\\', '/', $fileName);

        foreach ($this->violations[$sniffName] as $baseline) {
            if ($baseline->matches($fileName)) {
                return true;
            }
        }

        return false;
    }
}
