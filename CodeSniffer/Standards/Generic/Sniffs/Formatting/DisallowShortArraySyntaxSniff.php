<?php

/**
 * Generic_Sniffs_Formatting_DisallowShortArraySyntaxSniff
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Xaver Loppenstedt <xaver@loppenstedt.de>
 * @copyright 2015 Xaver Loppenstedt
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Formatting_DisallowShortArraySyntaxSniff.
 *
 * Disallow short array syntax.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Xaver Loppenstedt <xaver@loppenstedt.de>
 * @copyright 2015 Xaver Loppenstedt, All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Formatting_DisallowShortArraySyntaxSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * How many spaces should precede the opening parenthesis.
     *
     * @var int
     */
    public $requiredSpacesBeforeOpen = 0;


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array(int)
     */
    public function register()
    {
        return array(T_OPEN_SHORT_ARRAY);

    }//end register()


    /**
     * Called when one of the token types that this sniff is listening for
     * is found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where the
     *                                        token was found.
     * @param int                  $stackPtr  The position in the PHP_CodeSniffer
     *                                        file's token stack where the token
     *                                        was found.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $fix = $phpcsFile->addFixableError(
            'Long array syntax must be used',
            $stackPtr
        );

        if ($fix === true) {
            $tokens = $phpcsFile->getTokens();
            $token  = $tokens[$stackPtr];

            $arrayOpen = 'array'.str_repeat(' ', $this->requiredSpacesBeforeOpen).'(';

            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($token['bracket_opener'], $arrayOpen);
            $phpcsFile->fixer->replaceToken($token['bracket_closer'], ')');
            $phpcsFile->fixer->endChangeset();
        }

    }//end process()


}//end class
