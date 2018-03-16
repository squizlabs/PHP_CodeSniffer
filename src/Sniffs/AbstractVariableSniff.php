<?php
/**
 * A class to find T_VARIABLE tokens.
 *
 * This class can distinguish between normal T_VARIABLE tokens, and those tokens
 * that represent class members. If a class member is encountered, then the
 * processMemberVar method is called so the extending class can process it. If
 * the token is found to be a normal T_VARIABLE token, then processVariable is
 * called.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

abstract class AbstractVariableSniff extends AbstractScopeSniff
{


    /**
     * List of PHP Reserved variables.
     *
     * Used by various naming convention sniffs.
     *
     * @var array
     */
    protected $phpReservedVars = [
        '_SERVER'              => true,
        '_GET'                 => true,
        '_POST'                => true,
        '_REQUEST'             => true,
        '_SESSION'             => true,
        '_ENV'                 => true,
        '_COOKIE'              => true,
        '_FILES'               => true,
        'GLOBALS'              => true,
        'http_response_header' => true,
        'HTTP_RAW_POST_DATA'   => true,
        'php_errormsg'         => true,
    ];


    /**
     * Constructs an AbstractVariableTest.
     */
    public function __construct()
    {
        $scopes = Tokens::$ooScopeTokens;

        $listen = [
            T_VARIABLE,
            T_DOUBLE_QUOTED_STRING,
            T_HEREDOC,
        ];

        parent::__construct($scopes, $listen, true);

    }//end __construct()


    /**
     * Processes the token in the specified PHP_CodeSniffer_File.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the token was found.
     * @param int                         $currScope The current scope opener token.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     */
    final protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_DOUBLE_QUOTED_STRING
            || $tokens[$stackPtr]['code'] === T_HEREDOC
        ) {
            // Check to see if this string has a variable in it.
            $pattern = '|(?<!\\\\)(?:\\\\{2})*\${?[a-zA-Z0-9_]+}?|';
            if (preg_match($pattern, $tokens[$stackPtr]['content']) !== 0) {
                return $this->processVariableInString($phpcsFile, $stackPtr);
            }

            return;
        }

        // If this token is inside nested inside a function at a deeper
        // level than the current OO scope that was found, it's a normal
        // variable and not a member var.
        $conditions = array_reverse($tokens[$stackPtr]['conditions'], true);
        $inFunction = false;
        foreach ($conditions as $scope => $code) {
            if (isset(Tokens::$ooScopeTokens[$code]) === true) {
                break;
            }

            if ($code === T_FUNCTION || $code === T_CLOSURE) {
                $inFunction = true;
            }
        }

        if ($scope !== $currScope) {
            // We found a closer scope to this token, so ignore
            // this particular time through the sniff. We will process
            // this token when this closer scope is found to avoid
            // duplicate checks.
            return;
        }

        // Just make sure this isn't a variable in a function declaration.
        if ($inFunction === false && isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            foreach ($tokens[$stackPtr]['nested_parenthesis'] as $opener => $closer) {
                if (isset($tokens[$opener]['parenthesis_owner']) === false) {
                    // Check if this is a USE statement in a closure.
                    $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($opener - 1), null, true);
                    if ($tokens[$prev]['code'] === T_USE) {
                        $inFunction = true;
                        break;
                    }

                    continue;
                }

                $owner = $tokens[$opener]['parenthesis_owner'];
                if ($tokens[$owner]['code'] === T_FUNCTION
                    || $tokens[$owner]['code'] === T_CLOSURE
                ) {
                    $inFunction = true;
                    break;
                }
            }
        }//end if

        if ($inFunction === true) {
            return $this->processVariable($phpcsFile, $stackPtr);
        } else {
            return $this->processMemberVar($phpcsFile, $stackPtr);
        }

    }//end processTokenWithinScope()


    /**
     * Processes the token outside the scope in the file.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     */
    final protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        // These variables are not member vars.
        if ($tokens[$stackPtr]['code'] === T_VARIABLE) {
            return $this->processVariable($phpcsFile, $stackPtr);
        } else if ($tokens[$stackPtr]['code'] === T_DOUBLE_QUOTED_STRING
            || $tokens[$stackPtr]['code'] === T_HEREDOC
        ) {
            // Check to see if this string has a variable in it.
            $pattern = '|(?<!\\\\)(?:\\\\{2})*\${?[a-zA-Z0-9_]+}?|';
            if (preg_match($pattern, $tokens[$stackPtr]['content']) !== 0) {
                return $this->processVariableInString($phpcsFile, $stackPtr);
            }
        }

    }//end processTokenOutsideScope()


    /**
     * Called to process class member vars.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     */
    abstract protected function processMemberVar(File $phpcsFile, $stackPtr);


    /**
     * Called to process normal member vars.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     */
    abstract protected function processVariable(File $phpcsFile, $stackPtr);


    /**
     * Called to process variables found in double quoted strings or heredocs.
     *
     * Note that there may be more than one variable in the string, which will
     * result only in one call for the string or one call per line for heredocs.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the double quoted
     *                                               string was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     */
    abstract protected function processVariableInString(File $phpcsFile, $stackPtr);


}//end class
