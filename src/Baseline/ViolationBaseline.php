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

class ViolationBaseline
{
    /** @var string */
    private $sniffName;

    /** @var string */
    private $fileName;

    /** @var int */
    private $fileNameLength;

    /**
     * @param string $ruleName
     * @param string $fileName
     */
    public function __construct($ruleName, $fileName)
    {
        $this->sniffName      = $ruleName;
        $this->fileName       = $fileName;
        $this->fileNameLength = strlen($fileName);
    }

    /**
     * @return string
     */
    public function getSniffName()
    {
        return $this->sniffName;
    }

    /**
     * Test if the given filepath matches the relative filename in the baseline
     *
     * @param string $filepath
     *
     * @return bool
     */
    public function matches($filepath)
    {
        return substr($filepath, -$this->fileNameLength) === $this->fileName;
    }
}
