<?php
/**
 * Checks that traits are suffixed by Trait.
 *
 * @author  Anna Borzenko <annnechko@gmail.com>
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class TraitSuffixRequiredForTraitSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_TRAIT];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $traitName = $phpcsFile->getDeclarationName($stackPtr);
        if ($traitName === null) {
            return;
        }

        $traitNameLength = strlen($traitName);
        $suffix          = substr($traitName, ($traitNameLength - 5), $traitNameLength);
        if ($suffix !== 'Trait') {
            $phpcsFile->addError('Traits MUST be suffixed by Trait: e.g. Psr\Foo\BarTrait.', $stackPtr, 'RequiredTraitSuffix');
        }

    }//end process()


}//end class
