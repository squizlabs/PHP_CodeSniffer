<?php
/**
 * Discourages the use of not strict operators.
 *
 * @author    Vincent Langlet <vincentlanglet@exemple.com>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class StrictComparisonSniff implements Sniff
{

    /**
     * Types to replace: key is operator to replace, value is operator to replace with.
     *
     * @var array
     */
    public $operators = [
        T_IS_EQUAL     => '===',
        T_IS_NOT_EQUAL => '!==',
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array_keys($this->operators);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $phpcsFile->addError(
            'Using the %s operator is forbidden, use %s instead',
            $stackPtr,
            'NotStrict',
            [
                $tokens[$stackPtr]['content'],
                $this->operators[$tokens[$stackPtr]['code']],
            ]
        );

    }//end process()


}//end class
