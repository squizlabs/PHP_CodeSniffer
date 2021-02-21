<?php
/**
 * Checks that the declaration of an anon class is correct.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\FunctionCallArgumentSpacingSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\MultiLineFunctionDeclarationSniff;
use PHP_CodeSniffer\Util\Tokens;

class AnonClassDeclarationSniff extends ClassDeclarationSniff
{

    /**
     * The PSR2 MultiLineFunctionDeclarations sniff.
     *
     * @var \PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\MultiLineFunctionDeclarationSniff
     */
    private $multiLineSniff = null;

    /**
     * The Generic FunctionCallArgumentSpacing sniff.
     *
     * @var \PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\FunctionCallArgumentSpacingSniff
     */
    private $functionCallSniff = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_ANON_CLASS];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        $this->multiLineSniff    = new MultiLineFunctionDeclarationSniff();
        $this->functionCallSniff = new FunctionCallArgumentSpacingSniff();

        $this->processOpen($phpcsFile, $stackPtr);
        $this->processClose($phpcsFile, $stackPtr);

        if (isset($tokens[$stackPtr]['parenthesis_opener']) === true) {
            $openBracket = $tokens[$stackPtr]['parenthesis_opener'];
            if ($this->multiLineSniff->isMultiLineDeclaration($phpcsFile, $stackPtr, $openBracket, $tokens) === true) {
                $this->processMultiLineArgumentList($phpcsFile, $stackPtr);
            } else {
                $this->processSingleLineArgumentList($phpcsFile, $stackPtr);
            }

            $this->functionCallSniff->checkSpacing($phpcsFile, $stackPtr, $openBracket);
        }

        $opener = $tokens[$stackPtr]['scope_opener'];
        if ($tokens[$opener]['line'] === $tokens[$stackPtr]['line']) {
            return;
        }

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($opener - 1), $stackPtr, true);

        $implements = $phpcsFile->findPrevious(T_IMPLEMENTS, ($opener - 1), $stackPtr);
        if ($implements !== false
            && $tokens[$opener]['line'] !== $tokens[$implements]['line']
            && $tokens[$opener]['line'] === $tokens[$prev]['line']
        ) {
            // Opening brace must be on a new line as implements list wraps.
            $error = 'Opening brace must be on the line after the last implemented interface';
            $fix   = $phpcsFile->addFixableError($error, $opener, 'OpenBraceSameLine');
            if ($fix === true) {
                $first  = $phpcsFile->findFirstOnLine(T_WHITESPACE, $stackPtr, true);
                $indent = str_repeat(' ', ($tokens[$first]['column'] - 1));
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken(($prev + 1), '');
                $phpcsFile->fixer->addNewline($prev);
                $phpcsFile->fixer->addContentBefore($opener, $indent);
                $phpcsFile->fixer->endChangeset();
            }
        }

        if ($tokens[$opener]['line'] > ($tokens[$prev]['line'] + 1)) {
            // Opening brace is on a new line, so there must be no blank line before it.
            $error = 'Opening brace must not be preceded by a blank line';
            $fix   = $phpcsFile->addFixableError($error, $opener, 'OpenBraceLine');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($x = ($prev + 1); $x < $opener; $x++) {
                    if ($tokens[$x]['line'] === $tokens[$prev]['line']) {
                        // Maintain existing newline.
                        continue;
                    }

                    if ($tokens[$x]['line'] === $tokens[$opener]['line']) {
                        // Maintain existing indent.
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($x, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }//end if

    }//end process()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function processSingleLineArgumentList(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $openBracket  = $tokens[$stackPtr]['parenthesis_opener'];
        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        if ($openBracket === ($closeBracket - 1)) {
            return;
        }

        if ($tokens[($openBracket + 1)]['code'] === T_WHITESPACE) {
            $error = 'Space after opening parenthesis of single-line argument list prohibited';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterOpenBracket');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($openBracket + 1), '');
            }
        }

        $spaceBeforeClose = 0;
        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBracket - 1), $openBracket, true);
        if ($tokens[$prev]['code'] === T_END_HEREDOC || $tokens[$prev]['code'] === T_END_NOWDOC) {
            // Need a newline after these tokens, so ignore this rule.
            return;
        }

        if ($tokens[$prev]['line'] !== $tokens[$closeBracket]['line']) {
            $spaceBeforeClose = 'newline';
        } else if ($tokens[($closeBracket - 1)]['code'] === T_WHITESPACE) {
            $spaceBeforeClose = $tokens[($closeBracket - 1)]['length'];
        }

        if ($spaceBeforeClose !== 0) {
            $error = 'Space before closing parenthesis of single-line argument list prohibited';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeCloseBracket');
            if ($fix === true) {
                if ($spaceBeforeClose === 'newline') {
                    $phpcsFile->fixer->beginChangeset();

                    $closingContent = ')';

                    $next = $phpcsFile->findNext(T_WHITESPACE, ($closeBracket + 1), null, true);
                    if ($tokens[$next]['code'] === T_SEMICOLON) {
                        $closingContent .= ';';
                        for ($i = ($closeBracket + 1); $i <= $next; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }
                    }

                    // We want to jump over any whitespace or inline comment and
                    // move the closing parenthesis after any other token.
                    $prev = ($closeBracket - 1);
                    while (isset(Tokens::$emptyTokens[$tokens[$prev]['code']]) === true) {
                        if (($tokens[$prev]['code'] === T_COMMENT)
                            && (strpos($tokens[$prev]['content'], '*/') !== false)
                        ) {
                            break;
                        }

                        $prev--;
                    }

                    $phpcsFile->fixer->addContent($prev, $closingContent);

                    $prevNonWhitespace = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBracket - 1), null, true);
                    for ($i = ($prevNonWhitespace + 1); $i <= $closeBracket; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                } else {
                    $phpcsFile->fixer->replaceToken(($closeBracket - 1), '');
                }//end if
            }//end if
        }//end if

    }//end processSingleLineArgumentList()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function processMultiLineArgumentList(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $openBracket = $tokens[$stackPtr]['parenthesis_opener'];

        $this->multiLineSniff->processBracket($phpcsFile, $openBracket, $tokens, 'argument');
        $this->multiLineSniff->processArgumentList($phpcsFile, $stackPtr, $this->indent, 'argument');

    }//end processMultiLineArgumentList()


}//end class
