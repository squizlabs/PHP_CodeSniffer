<?php
/**
 * Checks that the opening brace of a function is on the line after the function declaration.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Functions;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class OpeningFunctionBraceBsdAllmanSniff implements Sniff
{

    /**
     * Should this sniff check function braces?
     *
     * @var boolean
     */
    public $checkFunctions = true;

    /**
     * Should this sniff check closure braces?
     *
     * @var boolean
     */
    public $checkClosures = false;


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return void
     */
    public function register()
    {
        return array(
                T_FUNCTION,
                T_CLOSURE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        if (($tokens[$stackPtr]['code'] === T_FUNCTION
            && (bool) $this->checkFunctions === false)
            || ($tokens[$stackPtr]['code'] === T_CLOSURE
            && (bool) $this->checkClosures === false)
        ) {
            return;
        }

        $openingBrace = $tokens[$stackPtr]['scope_opener'];
        $closeBracket = $tokens[$stackPtr]['parenthesis_closer'];
        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $use = $phpcsFile->findNext(T_USE, ($closeBracket + 1), $tokens[$stackPtr]['scope_opener']);
            if ($use !== false) {
                $openBracket  = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1));
                $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
            }
        }

        $functionLine = $tokens[$closeBracket]['line'];
        $braceLine    = $tokens[$openingBrace]['line'];

        $lineDifference = ($braceLine - $functionLine);

        if ($lineDifference === 0) {
            $error = 'Opening brace should be on a new line';
            $fix   = $phpcsFile->addFixableError($error, $openingBrace, 'BraceOnSameLine');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $indent = $phpcsFile->findFirstOnLine(array(), $openingBrace);
                if ($tokens[$indent]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->addContentBefore($openingBrace, $tokens[$indent]['content']);
                }

                $phpcsFile->fixer->addNewlineBefore($openingBrace);
                $phpcsFile->fixer->endChangeset();
            }

            $phpcsFile->recordMetric($stackPtr, 'Function opening brace placement', 'same line');
        } else if ($lineDifference > 1) {
            $error = 'Opening brace should be on the line after the declaration; found %s blank line(s)';
            $data  = array(($lineDifference - 1));
            $fix   = $phpcsFile->addFixableError($error, $openingBrace, 'BraceSpacing', $data);
            if ($fix === true) {
                for ($i = ($tokens[$stackPtr]['parenthesis_closer'] + 1); $i < $openingBrace; $i++) {
                    if ($tokens[$i]['line'] === $braceLine) {
                        $phpcsFile->fixer->addNewLineBefore($i);
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }
            }
        }//end if

        $next = $phpcsFile->findNext(T_WHITESPACE, ($openingBrace + 1), null, true);
        if ($tokens[$next]['line'] === $tokens[$openingBrace]['line']) {
            if ($next === $tokens[$stackPtr]['scope_closer']) {
                // Ignore empty functions.
                return;
            }

            $error = 'Opening brace must be the last content on the line';
            $fix   = $phpcsFile->addFixableError($error, $openingBrace, 'ContentAfterBrace');
            if ($fix === true) {
                $phpcsFile->fixer->addNewline($openingBrace);
            }
        }

        // Only continue checking if the opening brace looks good.
        if ($lineDifference !== 1) {
            return;
        }

        // We need to actually find the first piece of content on this line,
        // as if this is a method with tokens before it (public, static etc)
        // or an if with an else before it, then we need to start the scope
        // checking from there, rather than the current token.
        $lineStart = $phpcsFile->findFirstOnLine(T_WHITESPACE, $stackPtr, true);

        // The opening brace is on the correct line, now it needs to be
        // checked to be correctly indented.
        $startColumn = $tokens[$lineStart]['column'];
        $braceIndent = $tokens[$openingBrace]['column'];

        if ($braceIndent !== $startColumn) {
            $expected = ($startColumn - 1);
            $found    = ($braceIndent - 1);

            $error = 'Opening brace indented incorrectly; expected %s spaces, found %s';
            $data  = array(
                      $expected,
                      $found,
                     );

            $fix = $phpcsFile->addFixableError($error, $openingBrace, 'BraceIndent', $data);
            if ($fix === true) {
                $indent = str_repeat(' ', $expected);
                if ($found === 0) {
                    $phpcsFile->fixer->addContentBefore($openingBrace, $indent);
                } else {
                    $phpcsFile->fixer->replaceToken(($openingBrace - 1), $indent);
                }
            }
        }//end if

        $phpcsFile->recordMetric($stackPtr, 'Function opening brace placement', 'new line');

    }//end process()


}//end class
