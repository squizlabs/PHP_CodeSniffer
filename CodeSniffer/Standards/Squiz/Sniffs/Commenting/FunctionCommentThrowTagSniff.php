<?php
/**
 * Verifies that a @throws tag exists for a function that throws exceptions.
 * Verifies the number of @throws tags and the number of throw tokens matches.
 * Verifies the exception type.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractScopeSniff', true) === false) {
    $error = 'Class PHP_CodeSniffer_Standards_AbstractScopeSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Verifies that a @throws tag exists for a function that throws exceptions.
 * Verifies the number of @throws tags and the number of throw tokens matches.
 * Verifies the exception type.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Commenting_FunctionCommentThrowTagSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
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
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     * @param int                  $currScope The current scope opener token.
     *
     * @return void
     */
    protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        // Is this the first throw token within the current function scope?
        // If so, we have to validate other throw tokens within the same scope.
        $previousThrow = $phpcsFile->findPrevious(T_THROW, ($stackPtr - 1), $currScope);
        if ($previousThrow !== false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $find   = PHP_CodeSniffer_Tokens::$methodPrefixes;
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
        $throwTokens = array();
        $currPos     = $stackPtr;
        $foundThrows = false;
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
                            $throwTokens[] = $tokens[$currException]['content'];
                        } else {
                            $throwTokens[] = $phpcsFile->getTokensAsString($currException, ($endException - $currException));
                        }
                    }//end if
                } else if ($tokens[$nextToken]['code'] === T_VARIABLE) {
                    // Find where the nearest 'catch' block in this scope.
                    $catch = $phpcsFile->findPrevious(
                        T_CATCH,
                        $currPos,
                        $tokens[$currScope]['scope_opener'],
                        false,
                        null,
                        false
                    );

                    if ($catch !== false) {
                        // Get the start of the 'catch' exception.
                        $currException = $phpcsFile->findNext(
                            array(
                             T_NS_SEPARATOR,
                             T_STRING,
                            ),
                            $tokens[$catch]['parenthesis_opener'],
                            $tokens[$catch]['parenthesis_closer'],
                            false,
                            null,
                            true
                        );

                        if ($currException !== false) {
                            // Find the next whitespace (which should be the end of the exception).
                            $endException = $phpcsFile->findNext(
                                T_WHITESPACE,
                                ($currException + 1),
                                $tokens[$catch]['parenthesis_closer'],
                                false,
                                null,
                                true
                            );

                            if ($endException !== false) {
                                // Find the variable that we're catching into.
                                $thrownVar = $phpcsFile->findNext(
                                    T_VARIABLE,
                                    ($endException + 1),
                                    $tokens[$catch]["parenthesis_closer"],
                                    false,
                                    null,
                                    true
                                );

                                // Sanity check that the variable that the exception is caught into is the one that's thrown.
                                if ($tokens[$thrownVar]['content'] === $tokens[$nextToken]['content']) {
                                    $throwTokens[] = $phpcsFile->getTokensAsString($currException, ($endException - $currException));
                                }//end if
                            }//end if
                        }//end if
                    }//end if
                }//end if
            }//end if

            $currPos = $phpcsFile->findNext(T_THROW, ($currPos + 1), $currScopeEnd);
        }//end while

        if ($foundThrows === false) {
            return;
        }

        // Only need one @throws tag for each type of exception thrown.
        $throwTokens = array_unique($throwTokens);

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
        } else if (empty($throwTokens) === true) {
            // If token count is zero, it means that only variables are being
            // thrown, so we need at least one @throws tag (checked above).
            // Nothing more to do.
            return;
        }

        // Make sure @throws tag count matches throw token count.
        $tokenCount = count($throwTokens);
        $tagCount   = count($throwTags);
        if ($tokenCount !== $tagCount) {
            $error = 'Expected %s @throws tag(s) in function comment; %s found';
            $data  = array(
                      $tokenCount,
                      $tagCount,
                     );
            $phpcsFile->addError($error, $commentEnd, 'WrongNumber', $data);
            return;
        }

        foreach ($throwTokens as $throw) {
            if (isset($throwTags[$throw]) === false) {
                $error = 'Missing @throws tag for "%s" exception';
                $data  = array($throw);
                $phpcsFile->addError($error, $commentEnd, 'Missing', $data);
            }
        }

    }//end processTokenWithinScope()


}//end class
