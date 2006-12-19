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

require_once 'PHP/CodeSniffer/Sniff.php';

/**
 * Class Declaration Test.
 *
 * Checks the declaration of the class and its' inheritance is correct.
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
class Squiz_Sniffs_Classes_ClassDeclarationSniff implements PHP_CodeSniffer_Sniff
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

        /*
            First, check that this is the only class or interface in the file.
        */

        $nextClass = $phpcsFile->findNext(array(T_CLASS, T_INTERFACE), $stackPtr + 1);

        if ($nextClass !== false) {
            // We have another, so an error is thrown.
            $error = 'Only one interface or class is allowed in a file.';
            $phpcsFile->addError($error, $nextClass);
        }

        /*
            Checks the Opening brace is straight after the declaration.
        */

        $curlyBrace = $phpcsFile->findNext(array(T_OPEN_CURLY_BRACKET), $stackPtr, null, false);

        $classLine = $tokens[$stackPtr]['line'];
        $braceLine = $tokens[$curlyBrace]['line'];
        if ($braceLine === $classLine) {
            $error  = 'Opening brace of a ';
            $error .= $tokens[$stackPtr]['content'];
            $error .= ' must be on the line after the definition.';
            $phpcsFile->addError($error, $stackPtr);
            return;
        } else if ($braceLine > ($classLine + 1)) {
            $difference  = ($braceLine - $classLine - 1);
            $difference .= ($difference === 1) ? ' empty line' : ' empty lines';
            $error       = 'Opening brace of a ';
            $error      .= $tokens[$stackPtr]['content'];
            $error      .= ' should be on the line following a ';
            $error      .= $tokens[$stackPtr]['content'];
            $error      .= ' declaration. Found '.$difference.'.';
            $phpcsFile->addError($error, $curlyBrace);
            return;
        }

        if ($tokens[$curlyBrace - 1]['content'] === "\n") {
            // Only a newline behind it.
            return;
        }

        $blankSpace  = substr($tokens[$curlyBrace - 1]['content'], strpos($tokens[$curlyBrace - 1]['content'], "\n"));
        $spaces      = strlen($blankSpace);
        $spaces     .= ($spaces === 1) ? ' space' : ' spaces';
        $error       = 'Found '.$spaces.' before opening brace. Expected 0.';
        $phpcsFile->addError($error, $curlyBrace);

        /*
            Check that each of the parent classes or interfaces specified
            are spaced correctly.
        */

        // We need to map out each of the possible tokens in the declaration.
        $keyword      = $stackPtr;
        $openingBrace = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $stackPtr);
        $className    = $phpcsFile->findNext(T_STRING, $stackPtr);
        $extends      = $phpcsFile->findNext(array(T_IMPLEMENTS, T_EXTENDS), $stackPtr, $openingBrace);
        $parents      = array();

        $nextParent   = ($className + 1);
        while (($nextParent = $phpcsFile->findNext(T_STRING, $nextParent + 1, $openingBrace)) !== false) {
            $parents[] = $nextParent;
        }

        /*
            Now check the spacing of each token.
        */

        $name = $tokens[$keyword]['content'];
        // Spacing of the keyword.
        $gap = $tokens[$stackPtr + 1]['content'];
        if (strlen($gap) !== 1) {
            $error  = strlen($gap).' spaces found between "'.$name;
            $error .= '" keyword, and '.strtolower($name).' name. Expected 1.';
            $phpcsFile->addError($error, $stackPtr);
        }

        // Check after the name.
        $gap = $tokens[$className + 1]['content'];
        if (strlen($gap) !== 1) {
            $error  = strlen($gap).' spaces found after '.$name;
            $error .= 'name. Expected 1.';
            $phpcsFile->addError($error, $stackPtr);
        }

        // Now check each of the parents.
        $parentCount = count($parents);
        for ($i = 0; $i < $parentCount; $i++) {
            if ($i === 0) {
                $spaceBefore = strlen($tokens[$parents[$i] - 1]['content']);
                if ($spaceBefore !== 1) {
                    $error  = $spaceBefore.' spaces found before ';
                    $error .= '"'.$tokens[$parents[$i]]['content'].'". ';
                    $error .= 'Expected 1.';
                    $phpcsFile->addError($error, $stackPtr);
                }
            }

            if ($tokens[$parents[$i] + 1]['code'] !== T_NONE) {
                if ($i !== ($parentCount - 1)) {
                    $error  = 'Space found between ';
                    $error .= $tokens[$parents[$i]]['content'].' and comma.';
                }
            } else {
                if ($tokens[$parents[$i] + 2]['code'] !== T_WHITESPACE) {
                    $content = $tokens[$parents[$i] + 2]['content'];
                    $error   = 'Space required before "'.$content.'".';
                    $phpcsFile->addError($error, $stackPtr);
                } else {
                    if ($i !== ($parentCount - 1)) {
                        $space = strlen($tokens[$parents[$i] + 2]['content']);
                        if ($space !== 1) {
                            $content  = $tokens[$parents[$i] + 3]['content'];
                            $error    = $space.' spaces found before ';
                            $error   .= '"'.$content.'"';
                            $error   .= '. Expected 1.';
                            $phpcsFile->addError($error, $stackPtr);
                        }
                    }
                }
            }//end if
        }//end for

    }//end process()


}//end class

?>
