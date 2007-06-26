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
            Check that this is the only class or interface in the file.
        */

        $nextClass = $phpcsFile->findNext(array(T_CLASS, T_INTERFACE), ($stackPtr + 1));

        if ($nextClass !== false) {
            // We have another, so an error is thrown.
            $error = 'Only one interface or class is allowed in a file';
            $phpcsFile->addError($error, $nextClass);
        }

        /*
            Check that the opening brace is straight after the declaration.
        */

        $curlyBrace = $tokens[$stackPtr]['scope_opener'];
        $classLine  = $tokens[$stackPtr]['line'];
        $braceLine  = $tokens[$curlyBrace]['line'];
        if ($braceLine === $classLine) {
            $error  = 'Opening brace of a ';
            $error .= $tokens[$stackPtr]['content'];
            $error .= ' must be on the line after the definition';
            $phpcsFile->addError($error, $stackPtr);
        } else if ($braceLine > ($classLine + 1)) {
            $difference  = ($braceLine - $classLine - 1);
            $difference .= ($difference === 1) ? ' empty line' : ' empty lines';
            $error       = 'Opening brace of a ';
            $error      .= $tokens[$stackPtr]['content'];
            $error      .= ' must be on the line following the ';
            $error      .= $tokens[$stackPtr]['content'];
            $error      .= ' declaration; found '.$difference;
            $phpcsFile->addError($error, $curlyBrace);
        }

        if ($tokens[($curlyBrace + 1)]['content'] !== $phpcsFile->eolChar) {
            $type  = strtolower($tokens[$stackPtr]['content']);
            $error = "Opening $type brace must be on a line by itself";
            $phpcsFile->addError($error, $curlyBrace);
        }

        /*
            Check alignment of the keyword and braces.
        */

        if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($stackPtr - 1)]['content'];
            if ($prevContent !== $phpcsFile->eolChar) {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);

                if (in_array($tokens[($stackPtr - 2)]['code'], array(T_ABSTRACT, T_FINAL)) === false) {
                    if ($spaces !== 0) {
                        $type  = strtolower($tokens[$stackPtr]['content']);
                        $error = "Expected 0 spaces before $type keyword; $spaces found";
                        $phpcsFile->addError($error, $stackPtr);
                    }
                } else {
                    if ($spaces !== 1) {
                        $type        = strtolower($tokens[$stackPtr]['content']);
                        $prevContent = strtolower($tokens[($stackPtr - 2)]['content']);
                        $error       = "Expected 1 space between $prevContent and $type keywords; $spaces found";
                        $phpcsFile->addError($error, $stackPtr);
                    }
                }
            }
        }//end if

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

        $closeBrace = $tokens[$stackPtr]['scope_closer'];
        if ($tokens[($closeBrace - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($closeBrace - 1)]['content'];
            if ($prevContent !== $phpcsFile->eolChar) {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);
                if ($spaces !== 0) {
                    $error = "Expected 0 spaces before closing brace; $spaces found";
                    $phpcsFile->addError($error, $closeBrace);
                }
            }
        }

        // Check the closing brace is on it's own line, but allow
        // for comments like "//end class".
        $nextContent = $phpcsFile->findNext(T_COMMENT, ($closeBrace + 1), null, true);
        if ($tokens[$nextContent]['content'] !== $phpcsFile->eolChar && $tokens[$nextContent]['line'] === $tokens[$closeBrace]['line']) {
            $type  = strtolower($tokens[$stackPtr]['content']);
            $error = "Closing $type brace must be on a line by itself";
            $phpcsFile->addError($error, $closeBrace);
        }

        /*
            Check that each of the parent classes or interfaces specified
            are spaced correctly.
        */

        // We need to map out each of the possible tokens in the declaration.
        $keyword      = $stackPtr;
        $openingBrace = $tokens[$stackPtr]['scope_opener'];
        $className    = $phpcsFile->findNext(T_STRING, $stackPtr);

        /*
            Now check the spacing of each token.
        */

        $name = strtolower($tokens[$keyword]['content']);

        // Spacing of the keyword.
        $gap = $tokens[($stackPtr + 1)]['content'];
        if (strlen($gap) !== 1) {
            $found = strlen($gap);
            $error = "Expected 1 space between $name keyword and $name name; $found found";
            $phpcsFile->addError($error, $stackPtr);
        }

        // Check after the name.
        $gap = $tokens[($className + 1)]['content'];
        if (strlen($gap) !== 1) {
            $found = strlen($gap);
            $error = "Expected 1 space after $name name; $found found";
            $phpcsFile->addError($error, $stackPtr);
        }

        // Now check each of the parents.
        $parents    = array();
        $nextParent = ($className + 1);
        while (($nextParent = $phpcsFile->findNext(array(T_STRING, T_IMPLEMENTS), ($nextParent + 1), ($openingBrace - 1))) !== false) {
            $parents[] = $nextParent;
        }

        $parentCount = count($parents);

        for ($i = 0; $i < $parentCount; $i++) {
            if ($tokens[$parents[$i]]['code'] === T_IMPLEMENTS) {
                continue;
            }

            if ($tokens[($parents[$i] - 1)]['code'] !== T_WHITESPACE) {
                $name  = $tokens[$parents[$i]]['content'];
                $error = "Expected 1 space before \"$name\"; 0 found";
                $phpcsFile->addError($error, ($nextComma + 1));
            } else {
                $spaceBefore = strlen($tokens[($parents[$i] - 1)]['content']);
                if ($spaceBefore !== 1) {
                    $name  = $tokens[$parents[$i]]['content'];
                    $error = "Expected 1 space before \"$name\"; $spaceBefore found";
                    $phpcsFile->addError($error, $stackPtr);
                }
            }

            if ($tokens[($parents[$i] + 1)]['code'] !== T_COMMA) {
                if ($i !== ($parentCount - 1)) {
                    // This is not the last parent, and the comma
                    // is not where we expect it to be.
                    if ($tokens[($parents[$i] + 2)]['code'] !== T_IMPLEMENTS) {
                        $found = strlen($tokens[($parents[$i] + 1)]['content']);
                        $name  = $tokens[$parents[$i]]['content'];
                        $error = "Expected 0 spaces between \"$name\" and comma; $found found";
                        $phpcsFile->addError($error, $stackPtr);
                    }
                }

                $nextComma = $phpcsFile->findNext(T_COMMA, $parents[$i]);
            } else {
                $nextComma = ($parents[$i] + 1);
            }
        }//end for

    }//end process()


}//end class

?>
