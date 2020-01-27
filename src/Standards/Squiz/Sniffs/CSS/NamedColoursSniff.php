<?php
/**
 * Ensure colour names are not used.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\CSS;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class NamedColoursSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['CSS'];

    /**
     * A list of named colours.
     *
     * This is the list of standard colours defined in the CSS specification.
     *
     * @var array
     */
    protected $colourNames = [
        'aqua'    => 'aqua',
        'black'   => 'black',
        'blue'    => 'blue',
        'fuchsia' => 'fuchsia',
        'gray'    => 'gray',
        'green'   => 'green',
        'lime'    => 'lime',
        'maroon'  => 'maroon',
        'navy'    => 'navy',
        'olive'   => 'olive',
        'orange'  => 'orange',
        'purple'  => 'purple',
        'red'     => 'red',
        'silver'  => 'silver',
        'teal'    => 'teal',
        'white'   => 'white',
        'yellow'  => 'yellow',
    ];


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_STRING];

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[($stackPtr - 1)]['code'] === T_HASH
            || $tokens[($stackPtr - 1)]['code'] === T_STRING_CONCAT
        ) {
            // Class name.
            return;
        }

        if (isset($this->colourNames[strtolower($tokens[$stackPtr]['content'])]) === true) {
            $error = 'Named colours are forbidden; use hex, rgb, or rgba values instead';
            $phpcsFile->addError($error, $stackPtr, 'Forbidden');
        }

    }//end process()


}//end class
