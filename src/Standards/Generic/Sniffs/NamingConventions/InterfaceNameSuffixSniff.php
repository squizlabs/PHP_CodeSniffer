<?php
/**
 * Checks that interfaces are suffixed by Interface.
 *
 * @author  Anna Borzenko <annnechko@gmail.com>
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class InterfaceNameSuffixSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_INTERFACE];

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
        $interfaceName = $phpcsFile->getDeclarationName($stackPtr);
        if ($interfaceName === null) {
            return;
        }

        $suffix = substr($interfaceName, -9);
        if (strtolower($suffix) !== 'interface') {
            $phpcsFile->addError('Interface names must be suffixed with "Interface"; found "%s"', $stackPtr, 'Missing', [$interfaceName]);
        }

    }//end process()


}//end class
