<?php
/**
 * A testable implementation of \PHP_CodeSniffer\Sniffs\AbstractArraySniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Sniffs;

use PHP_CodeSniffer\Sniffs\AbstractArraySniff;

class AbstractArraySniffTestable extends AbstractArraySniff
{

    /**
     * The array indicies that were found during processing.
     *
     * @var array
     */
    public $indicies = [];


    /**
     * Processes a single-line array definition.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile  The current file being checked.
     * @param int                         $stackPtr   The position of the current token
     *                                                in the stack passed in $tokens.
     * @param int                         $arrayStart The token that starts the array definition.
     * @param int                         $arrayEnd   The token that ends the array definition.
     * @param array                       $indices    An array of token positions for the array keys,
     *                                                double arrows, and values.
     *
     * @return void
     */
    public function processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices)
    {
        $this->indicies = $indices;

    }//end processSingleLineArray()


    /**
     * Processes a multi-line array definition.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile  The current file being checked.
     * @param int                         $stackPtr   The position of the current token
     *                                                in the stack passed in $tokens.
     * @param int                         $arrayStart The token that starts the array definition.
     * @param int                         $arrayEnd   The token that ends the array definition.
     * @param array                       $indices    An array of token positions for the array keys,
     *                                                double arrows, and values.
     *
     * @return void
     */
    public function processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd, $indices)
    {
        $this->indicies = $indices;

    }//end processMultiLineArray()


}//end class
