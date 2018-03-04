<?php
/**
 * The base class for all PHP_CodeSniffer documentation generators.
 *
 * Documentation generators are used to print documentation about code sniffs
 * in a standard.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Generators;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Autoload;
use PHP_CodeSniffer\Util\Common;

abstract class Generator
{

    /**
     * The ruleset used for the run.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    public $ruleset = null;

    /**
     * XML documentation files used to produce the final output.
     *
     * @var string[]
     */
    public $docFiles = [];


    /**
     * Constructs a doc generator.
     *
     * @param \PHP_CodeSniffer\Ruleset $ruleset The ruleset used for the run.
     *
     * @see generate()
     */
    public function __construct(Ruleset $ruleset)
    {
        $this->ruleset = $ruleset;

        foreach ($ruleset->sniffs as $className => $sniffClass) {
            $file    = Autoload::getLoadedFileName($className);
            $docFile = str_replace(
                DIRECTORY_SEPARATOR.'Sniffs'.DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR.'Docs'.DIRECTORY_SEPARATOR,
                $file
            );
            $docFile = str_replace('Sniff.php', 'Standard.xml', $docFile);

            if (is_file($docFile) === true) {
                $this->docFiles[] = $docFile;
            }
        }

    }//end __construct()


    /**
     * Retrieves the title of the sniff from the DOMNode supplied.
     *
     * @param \DOMNode $doc The DOMNode object for the sniff.
     *                      It represents the "documentation" tag in the XML
     *                      standard file.
     *
     * @return string
     */
    protected function getTitle(\DOMNode $doc)
    {
        return $doc->getAttribute('title');

    }//end getTitle()


    /**
     * Generates the documentation for a standard.
     *
     * It's probably wise for doc generators to override this method so they
     * have control over how the docs are produced. Otherwise, the processSniff
     * method should be overridden to output content for each sniff.
     *
     * @return void
     * @see    processSniff()
     */
    public function generate()
    {
        foreach ($this->docFiles as $file) {
            $doc = new \DOMDocument();
            $doc->load($file);
            $documentation = $doc->getElementsByTagName('documentation')->item(0);
            $this->processSniff($documentation);
        }

    }//end generate()


    /**
     * Process the documentation for a single sniff.
     *
     * Doc generators must implement this function to produce output.
     *
     * @param \DOMNode $doc The DOMNode object for the sniff.
     *                      It represents the "documentation" tag in the XML
     *                      standard file.
     *
     * @return void
     * @see    generate()
     */
    abstract protected function processSniff(\DOMNode $doc);


}//end class
