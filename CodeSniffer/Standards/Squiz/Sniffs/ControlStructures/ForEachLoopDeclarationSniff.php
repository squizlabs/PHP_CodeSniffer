<?php
/**
 * Squiz_Sniffs_ControlStructures_ForEachLoopDeclarationSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_ControlStructures_ForEachLoopDeclarationSniff.
 *
 * Verifies that there is a space between each condition of foreach loops.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_ControlStructures_ForEachLoopDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FOREACH);

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

        $openingBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        $closingBracket = $tokens[$openingBracket]['parenthesis_closer'];

        if ($tokens[($openingBracket + 1)]['code'] === T_WHITESPACE) {
            $error = 'Space found after opening bracket of FOREACH loop';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterOpen');
            if ($fix === true && $phpcsFile->fixer->enabled === true) {
                $phpcsFile->fixer->replaceToken(($openingBracket + 1), '');
            }
        }

        if ($tokens[($closingBracket - 1)]['code'] === T_WHITESPACE) {
            $error = 'Space found before closing bracket of FOREACH loop';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeClose');
            if ($fix === true && $phpcsFile->fixer->enabled === true) {
                $phpcsFile->fixer->replaceToken(($closingBracket - 1), '');
            }
        }

        $asToken = $phpcsFile->findNext(T_AS, $openingBracket);
        $content = $tokens[$asToken]['content'];
        if ($content !== strtolower($content)) {
            $expected = strtolower($content);
            $error    = 'AS keyword must be lowercase; expected "%s" but found "%s"';
            $data     = array(
                         $expected,
                         $content,
                        );

            $fix = $phpcsFile->addFixableError($error, $asToken, 'AsNotLower', $data);
            if ($fix === true && $phpcsFile->fixer->enabled === true) {
                $phpcsFile->fixer->replaceToken($asToken, $expected);
            }
        }

        $doubleArrow = $phpcsFile->findNext(T_DOUBLE_ARROW, $openingBracket, $closingBracket);

        if ($doubleArrow !== false) {
            if ($tokens[($doubleArrow - 1)]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space before "=>"; 0 found';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBeforeArrow');
                if ($fix === true && $phpcsFile->fixer->enabled === true) {
                    $phpcsFile->fixer->addContentBefore($doubleArrow, ' ');
                }
            } else {
                if (strlen($tokens[($doubleArrow - 1)]['content']) !== 1) {
                    $spaces = strlen($tokens[($doubleArrow - 1)]['content']);
                    $error  = 'Expected 1 space before "=>"; %s found';
                    $data   = array($spaces);
                    $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBeforeArrow', $data);
                    if ($fix === true && $phpcsFile->fixer->enabled === true) {
                        $phpcsFile->fixer->replaceToken(($doubleArrow - 1), ' ');
                    }
                }

            }

            if ($tokens[($doubleArrow + 1)]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space after "=>"; 0 found';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfterArrow');
                if ($fix === true && $phpcsFile->fixer->enabled === true) {
                    $phpcsFile->fixer->addContent($doubleArrow, ' ');
                }
            } else {
                if (strlen($tokens[($doubleArrow + 1)]['content']) !== 1) {
                    $spaces = strlen($tokens[($doubleArrow + 1)]['content']);
                    $error  = 'Expected 1 space after "=>"; %s found';
                    $data   = array($spaces);
                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterArrow', $data);
                    if ($fix === true && $phpcsFile->fixer->enabled === true) {
                        $phpcsFile->fixer->replaceToken(($doubleArrow + 1), ' ');
                    }
                }

            }

        }//end if

        if ($tokens[($asToken - 1)]['code'] !== T_WHITESPACE) {
            $error = 'Expected 1 space before "as"; 0 found';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBeforeAs');
            if ($fix === true && $phpcsFile->fixer->enabled === true) {
                $phpcsFile->fixer->addContentBefore($asToken, ' ');
            }
        } else {
            if (strlen($tokens[($asToken - 1)]['content']) !== 1) {
                $spaces = strlen($tokens[($asToken - 1)]['content']);
                $error  = 'Expected 1 space before "as"; %s found';
                $data   = array($spaces);
                $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBeforeAs', $data);
                if ($fix === true && $phpcsFile->fixer->enabled === true) {
                    $phpcsFile->fixer->replaceToken(($asToken - 1), ' ');
                }
            }
        }

        if ($tokens[($asToken + 1)]['code'] !== T_WHITESPACE) {
            $error = 'Expected 1 space after "as"; 0 found';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfterAs');
            if ($fix === true && $phpcsFile->fixer->enabled === true) {
                $phpcsFile->fixer->addContent($asToken, ' ');
            }
        } else {
            if (strlen($tokens[($asToken + 1)]['content']) !== 1) {
                $spaces = strlen($tokens[($asToken + 1)]['content']);
                $error  = 'Expected 1 space after "as"; %s found';
                $data   = array($spaces);
                $fix    = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterAs', $data);
                if ($fix === true && $phpcsFile->fixer->enabled === true) {
                    $phpcsFile->fixer->replaceToken(($asToken + 1), ' ');
                }
            }
        }

    }//end process()


}//end class
