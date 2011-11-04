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
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PEAR_Sniffs_Classes_ClassDeclarationSniff', true) === false) {
    $error = 'Class PEAR_Sniffs_Classes_ClassDeclarationSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Class Declaration Test.
 *
 * Checks the declaration of the class and its inheritance is correct.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Classes_ClassDeclarationSniff extends PEAR_Sniffs_Classes_ClassDeclarationSniff
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
     * @param int                  $stackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // We want all the errors from the PEAR standard, plus some of our own.
        parent::process($phpcsFile, $stackPtr);

        $tokens = $phpcsFile->getTokens();

        /*
            Check that this is the only class or interface in the file.
        */

        $nextClass = $phpcsFile->findNext(array(T_CLASS, T_INTERFACE), ($stackPtr + 1));

        if ($nextClass !== false) {
            // We have another, so an error is thrown.
            $error = 'Only one interface or class is allowed in a file';
            $phpcsFile->addError($error, $nextClass, 'MultipleClasses');
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
                        $error = 'Expected 0 spaces before %s keyword; %s found';
                        $data  = array(
                                  $type,
                                  $spaces,
                                 );
                        $phpcsFile->addError($error, $stackPtr, 'SpaceBeforeKeyword', $data);
                    }
                } else {
                    if ($spaces !== 1) {
                        $type        = strtolower($tokens[$stackPtr]['content']);
                        $prevContent = strtolower($tokens[($stackPtr - 2)]['content']);
                        $error       = 'Expected 1 space between %s and %s keywords; %s found';
                        $data        = array(
                                        $prevContent,
                                        $type,
                                        $spaces,
                                       );
                        $phpcsFile->addError($error, $stackPtr, 'SpacesBeforeKeyword', $data);
                    }
                }
            }
        }//end if

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            $error = 'Possible parse error: %s missing opening or closing brace';
            $data  = array($tokens[$stackPtr]['content']);
            $phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $data);
            return;
        }

        $closeBrace = $tokens[$stackPtr]['scope_closer'];
        if ($tokens[($closeBrace - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($closeBrace - 1)]['content'];
            if ($prevContent !== $phpcsFile->eolChar) {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);
                if ($spaces !== 0) {
                    if ($tokens[($closeBrace - 1)]['line'] !== $tokens[$closeBrace]['line']) {
                        $error = 'Expected 0 spaces before closing brace; newline found';
                        $phpcsFile->addError($error, $closeBrace, 'NewLineBeforeCloseBrace');
                    } else {
                        $error = 'Expected 0 spaces before closing brace; %s found';
                        $data  = array($spaces);
                        $phpcsFile->addError($error, $closeBrace, 'SpaceBeforeCloseBrace', $data);
                    }
                }
            }
        }

        // Check that the closing brace has one blank line after it.
        $nextContent = $phpcsFile->findNext(array(T_WHITESPACE, T_COMMENT), ($closeBrace + 1), null, true);
        if ($nextContent === false) {
            // No content found, so we reached the end of the file.
            // That means there was no closing tag either.
            $error = 'Closing brace of a %s must be followed by a blank line and then a closing PHP tag';
            $data  = array($tokens[$stackPtr]['content']);
            $phpcsFile->addError($error, $closeBrace, 'EndFileAfterCloseBrace', $data);
        } else {
            $nextLine  = $tokens[$nextContent]['line'];
            $braceLine = $tokens[$closeBrace]['line'];
            if ($braceLine === $nextLine) {
                $error = 'Closing brace of a %s must be followed by a single blank line';
                $data  = array($tokens[$stackPtr]['content']);
                $phpcsFile->addError($error, $closeBrace, 'NoNewlineAfterCloseBrace', $data);
            } else if ($nextLine !== ($braceLine + 2)) {
                $difference = ($nextLine - $braceLine - 1);
                $error      = 'Closing brace of a %s must be followed by a single blank line; found %s';
                $data       = array(
                               $tokens[$stackPtr]['content'],
                               $difference,
                              );
                $phpcsFile->addError($error, $closeBrace, 'NewlinesAfterCloseBrace', $data);
            }
        }//end if

        // Check the closing brace is on it's own line, but allow
        // for comments like "//end class".
        $nextContent = $phpcsFile->findNext(T_COMMENT, ($closeBrace + 1), null, true);
        if ($tokens[$nextContent]['content'] !== $phpcsFile->eolChar && $tokens[$nextContent]['line'] === $tokens[$closeBrace]['line']) {
            $type  = strtolower($tokens[$stackPtr]['content']);
            $error = 'Closing %s brace must be on a line by itself';
            $data  = array($tokens[$stackPtr]['content']);
            $phpcsFile->addError($error, $closeBrace, 'CloseBraceSameLine', $data);
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
            $error = 'Expected 1 space between %s keyword and %s name; %s found';
            $data  = array(
                      $name,
                      $name,
                      $found,
                     );
            $phpcsFile->addError($error, $stackPtr, 'SpaceAfterKeyword', $data);
        }

        // Check after the name.
        $gap = $tokens[($className + 1)]['content'];
        if (strlen($gap) !== 1) {
            $found = strlen($gap);
            $error = 'Expected 1 space after %s name; %s found';
            $data  = array(
                      $name,
                      $found,
                     );
            $phpcsFile->addError($error, $stackPtr, 'SpaceAfterName', $data);
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

            if ($tokens[($parents[$i] - 1)]['code'] === T_COMMA
                || ($tokens[($parents[$i] - 1)]['code'] === T_NS_SEPARATOR
                && $tokens[($parents[$i] - 2)]['code'] === T_COMMA)
            ) {
                $name  = $tokens[$parents[$i]]['content'];
                $error = 'Expected 1 space before "%s"; 0 found';
                $data  = array($name);
                $phpcsFile->addError($error, ($nextComma + 1), 'NoSpaceBeforeName', $data);
            } else {
                $spaceBefore = strlen($tokens[($parents[$i] - 1)]['content']);
                if ($spaceBefore !== 1) {
                    $name  = $tokens[$parents[$i]]['content'];
                    $error = 'Expected 1 space before "%s"; %s found';
                    $data  = array(
                              $name,
                              $spaceBefore,
                             );
                    $phpcsFile->addError($error, $stackPtr, 'SpaceBeforeName', $data);
                }
            }

            if ($tokens[($parents[$i] + 1)]['code'] !== T_COMMA) {
                if ($i !== ($parentCount - 1)) {
                    // This is not the last parent, and the comma
                    // is not where we expect it to be.
                    if ($tokens[($parents[$i] + 2)]['code'] !== T_IMPLEMENTS) {
                        $found = strlen($tokens[($parents[$i] + 1)]['content']);
                        $name  = $tokens[$parents[$i]]['content'];
                        $error = 'Expected 0 spaces between "%s" and comma; $%s found';
                        $data  = array(
                                  $name,
                                  $found,
                                 );
                        $phpcsFile->addError($error, $stackPtr, 'SpaceBeforeComma', $data);
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
