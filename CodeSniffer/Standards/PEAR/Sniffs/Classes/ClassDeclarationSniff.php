<?php
/**
 * Class Declaration Test.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Class Declaration Test.
 *
 * Checks the declaration of the class is correct.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PEAR_Sniffs_Classes_ClassDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            $error  = 'Possible parse error: ';
            $error .= $tokens[$stackPtr]['content'];
            $error .= ' missing opening or closing brace';
            $phpcsFile->addWarning($error, $stackPtr);
            return;
        }

        $curlyBrace  = $tokens[$stackPtr]['scope_opener'];
        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($curlyBrace - 1), $stackPtr, true);
        $classLine   = $tokens[$lastContent]['line'];
        $braceLine   = $tokens[$curlyBrace]['line'];
        if ($braceLine === $classLine) {
            $error  = 'Opening brace of a ';
            $error .= $tokens[$stackPtr]['content'];
            $error .= ' must be on the line after the definition';
            $phpcsFile->addError($error, $curlyBrace);
            return;
        } else if ($braceLine > ($classLine + 1)) {
            $difference  = ($braceLine - $classLine - 1);
            $difference .= ($difference === 1) ? ' line' : ' lines';
            $error       = 'Opening brace of a ';
            $error      .= $tokens[$stackPtr]['content'];
            $error      .= ' must be on the line following the ';
            $error      .= $tokens[$stackPtr]['content'];
            $error      .= ' declaration; found '.$difference;
            $phpcsFile->addError($error, $curlyBrace);
            return;
        }

        if ($tokens[($curlyBrace + 1)]['content'] !== $phpcsFile->eolChar) {
            $type  = strtolower($tokens[$stackPtr]['content']);
            $error = "Opening $type brace must be on a line by itself";
            $phpcsFile->addError($error, $curlyBrace);
        }

        if ($tokens[($curlyBrace - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($curlyBrace - 1)]['content'];
            if ($prevContent !== $phpcsFile->eolChar) {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);
                if ($spaces !== 0) {
                    $error = "Expected 0 spaces before opening brace; $spaces found";
                    $phpcsFile->addError($error, $curlyBrace);
                }
            }
        }

    }//end process()


}//end class

?>
