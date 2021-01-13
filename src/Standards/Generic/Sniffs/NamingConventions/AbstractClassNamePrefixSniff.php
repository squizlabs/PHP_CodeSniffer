<?php
/**
 * Checks that abstract classes are prefixed by Abstract.
 *
 * @author  Anna Borzenko <annnechko@gmail.com>
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class AbstractClassNamePrefixSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_CLASS];

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
        if ($phpcsFile->getClassProperties($stackPtr)['is_abstract'] === false) {
            // This class is not abstract so we don't need to check it.
            return;
        }

        $className = $phpcsFile->getDeclarationName($stackPtr);
        if ($className === null) {
            // We are not interested in anonymous classes.
            return;
        }

        $prefix = substr($className, 0, 8);
        if (strtolower($prefix) !== 'abstract') {
            $phpcsFile->addError('Abstract class names must be prefixed with "Abstract"; found "%s"', $stackPtr, 'Missing', [$className]);
        }

    }//end process()


}//end class
