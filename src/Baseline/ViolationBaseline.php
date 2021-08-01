<?php
/**
 * A class to manage a single baselined violation
 *
 * @author    Frank Dekker <fdekker@123inkt.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Baseline;

class ViolationBaseline
{

    /**
     * The name of the sniff
     *
     * @var string
     */
    private $sniffName;

    /**
     * The relative file path
     *
     * @var string
     */
    private $fileName;

    /**
     * The length of the filename to improve comparison performance
     *
     * @var integer
     */
    private $fileNameLength;

    /**
     * The code signature for the baseline
     *
     * @var string
     */
    private $signature;


    /**
     * Initialize the violation baseline
     *
     * @param string $sniffName The name of the sniff that's baselined.
     * @param string $fileName  The relative file path.
     * @param string $signature The code signature for the baseline.
     */
    public function __construct($sniffName, $fileName, $signature)
    {
        $this->sniffName      = $sniffName;
        $this->fileName       = $fileName;
        $this->fileNameLength = strlen($fileName);
        $this->signature      = $signature;

    }//end __construct()


    /**
     * Get the sniff name that was baselined
     *
     * @return string
     */
    public function getSniffName()
    {
        return $this->sniffName;

    }//end getSniffName()


    /**
     * Get the code signature for this baseline
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;

    }//end getSignature()


    /**
     * Test if the given filepath matches the relative filename in the baseline
     *
     * @param string $filepath the full filepath to match against
     *
     * @return bool
     */
    public function matches($filepath)
    {
        return substr($filepath, -$this->fileNameLength) === $this->fileName;

    }//end matches()


}//end class
