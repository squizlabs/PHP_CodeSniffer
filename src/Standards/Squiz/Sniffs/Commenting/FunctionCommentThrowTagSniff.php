<?php
/**
 * Verifies that a @throws tag exists for each exception type a function throws.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class FunctionCommentThrowTagSniff extends AbstractScopeSniff
{


    /**
     * Constructs a Squiz_Sniffs_Commenting_FunctionCommentThrowTagSniff.
     */
    public function __construct()
    {
        parent::__construct(array(T_FUNCTION), array(T_THROW));

    }//end __construct()


    /**
     * Processes the function tokens within the class.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position where the token was found.
     * @param int                         $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        // Is this the first throw token within the current function scope?
        // If so, we have to validate other throw tokens within the same scope.
        $previousThrow = $phpcsFile->findPrevious(T_THROW, ($stackPtr - 1), $currScope);
        if ($previousThrow !== false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $find   = Tokens::$methodPrefixes;
        $find[] = T_WHITESPACE;

        $commentEnd = $phpcsFile->findPrevious($find, ($currScope - 1), null, true);
        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            // Function is using the wrong type of comment.
            return;
        }

        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT
        ) {
            // Function doesn't have a doc comment.
            return;
        }

        $currScopeEnd = $tokens[$currScope]['scope_closer'];

        // Find all the exception type token within the current scope.
        $thrownExceptions = array();
        $currPos          = $stackPtr;
        $foundThrows      = false;
        while ($currPos < $currScopeEnd && $currPos !== false) {
            if ($phpcsFile->hasCondition($currPos, T_CLOSURE) === false) {
                $foundThrows = true;

                /*
                    If we can't find a NEW, we are probably throwing
                    a variable.

                    If we're throwing the same variable as the exception container
                    from the nearest 'catch' block, we take that exception, as it is
                    likely to be a re-throw.

                    If we can't find a matching catch block, or the variable name
                    is different, it's probably a different variable, so we ignore it,
                    but they still need to provide at least one @throws tag, even through we
                    don't know the exception class.
                */

                $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($currPos + 1), null, true);
                if ($tokens[$nextToken]['code'] === T_NEW) {
                    $currException = $phpcsFile->findNext(
                        array(
                         T_NS_SEPARATOR,
                         T_STRING,
                        ),
                        $currPos,
                        $currScopeEnd,
                        false,
                        null,
                        true
                    );

                    if ($currException !== false) {
                        $endException = $phpcsFile->findNext(
                            array(
                             T_NS_SEPARATOR,
                             T_STRING,
                            ),
                            ($currException + 1),
                            $currScopeEnd,
                            true,
                            null,
                            true
                        );

                        if ($endException === false) {
                            $thrownExceptions[] = $tokens[$currException]['content'];
                        } else {
                            $thrownExceptions[] = $phpcsFile->getTokensAsString($currException, ($endException - $currException));
                        }
                    }//end if
                } else if ($tokens[$nextToken]['code'] === T_VARIABLE) {
                    // Find the nearest catch block in this scope and, if the caught var
                    // matches our rethrown var, use the exception types being caught as
                    // exception types that are being thrown as well.
                    $catch = $phpcsFile->findPrevious(
                        T_CATCH,
                        $currPos,
                        $tokens[$currScope]['scope_opener'],
                        false,
                        null,
                        false
                    );

                    if ($catch !== false) {
                        $thrownVar = $phpcsFile->findPrevious(
                            T_VARIABLE,
                            ($tokens[$catch]['parenthesis_closer'] - 1),
                            $tokens[$catch]['parenthesis_opener']
                        );

                        if ($tokens[$thrownVar]['content'] === $tokens[$nextToken]['content']) {
                            $exceptions = explode('|', $phpcsFile->getTokensAsString(($tokens[$catch]['parenthesis_opener'] + 1), ($thrownVar - $tokens[$catch]['parenthesis_opener'] - 1)));
                            foreach ($exceptions as $exception) {
                                $thrownExceptions[] = trim($exception);
                            }
                        }
                    }
                }//end if
            }//end if

            $currPos = $phpcsFile->findNext(T_THROW, ($currPos + 1), $currScopeEnd);
        }//end while

        if ($foundThrows === false) {
            return;
        }

        // Only need one @throws tag for each type of exception thrown.
        $thrownExceptions = array_unique($thrownExceptions);

        $throwTags    = array();
        $commentStart = $tokens[$commentEnd]['comment_opener'];
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] !== '@throws') {
                continue;
            }

            if ($tokens[($tag + 2)]['code'] === T_DOC_COMMENT_STRING) {
                $exception = $tokens[($tag + 2)]['content'];
                $space     = strpos($exception, ' ');
                if ($space !== false) {
                    $exception = substr($exception, 0, $space);
                }

                $throwTags[$exception] = true;
            }
        }

        if (empty($throwTags) === true) {
            $error = 'Missing @throws tag in function comment';
            $phpcsFile->addError($error, $commentEnd, 'Missing');
            return;
        } else if (empty($thrownExceptions) === true) {
            // If token count is zero, it means that only variables are being
            // thrown, so we need at least one @throws tag (checked above).
            // Nothing more to do.
            return;
        }

        // Make sure @throws tag count matches thrown count.
        $thrownCount = count($thrownExceptions);
        $tagCount    = count($throwTags);
        if ($thrownCount !== $tagCount) {
            $error = 'Expected %s @throws tag(s) in function comment; %s found';
            $data  = array(
                      $thrownCount,
                      $tagCount,
                     );
            $phpcsFile->addError($error, $commentEnd, 'WrongNumber', $data);
            return;
        }

        foreach ($thrownExceptions as $throw) {
            if (isset($throwTags[$throw]) === false) {
                $error = 'Missing @throws tag for "%s" exception';
                $data  = array($throw);
                $phpcsFile->addError($error, $commentEnd, 'Missing', $data);
            }
        }

    }//end processTokenWithinScope()


    /**
     * Processes a token that is found within the scope that this test is
     * listening to.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack where this
     *                                               token was found.
     *
     * @return void
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {

    }//end processTokenOutsideScope()


}//end class
