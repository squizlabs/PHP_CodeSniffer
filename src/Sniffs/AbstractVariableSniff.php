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

abstract class AbstractVariableSniff extends AbstractScopeSniff
{

    /**
     * The end token of the current function that we are in.
     *
     * @var integer
     */
    private $endFunction = -1;

    /**
     * TRUE if a function is currently open.
     *
     * @var boolean
     */
    private $functionOpen = false;

    /**
     * The current PHP_CodeSniffer file that we are processing.
     *
     * @var \PHP_CodeSniffer\Files\File
     */
    protected $currentFile = null;


    /**
     * Constructs an AbstractVariableTest.
     */
    public function __construct()
    {
        $scopes = array(
                   T_CLASS,
                   T_ANON_CLASS,
                   T_TRAIT,
                   T_INTERFACE,
                  );

        $listen = array(
                   T_FUNCTION,
                   T_VARIABLE,
                   T_DOUBLE_QUOTED_STRING,
                   T_HEREDOC,
                  );

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
     * @return void
     */
    final protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        if ($this->currentFile !== $phpcsFile) {
            $this->currentFile  = $phpcsFile;
            $this->functionOpen = false;
            $this->endFunction  = -1;
        }

        $tokens = $phpcsFile->getTokens();

        if ($stackPtr > $this->endFunction) {
            $this->functionOpen = false;
        }

        if ($tokens[$stackPtr]['code'] === T_FUNCTION
            && $this->functionOpen === false
        ) {
            $this->functionOpen = true;

            $methodProps = $phpcsFile->getMethodProperties($stackPtr);

            // If the function is abstract, or is in an interface,
            // then set the end of the function to it's closing semicolon.
            if ($methodProps['is_abstract'] === true
                || $tokens[$currScope]['code'] === T_INTERFACE
            ) {
                $this->endFunction
                    = $phpcsFile->findNext(array(T_SEMICOLON), $stackPtr);
            } else {
                if (isset($tokens[$stackPtr]['scope_closer']) === false) {
                    $error = 'Possible parse error: non-abstract method defined as abstract';
                    $phpcsFile->addWarning($error, $stackPtr, 'Internal.ParseError.NonAbstractDefinedAbstract');
                    return;
                }

                $this->endFunction = $tokens[$stackPtr]['scope_closer'];
            }
        }//end if

        if ($tokens[$stackPtr]['code'] === T_DOUBLE_QUOTED_STRING
            || $tokens[$stackPtr]['code'] === T_HEREDOC
        ) {
            // Check to see if this string has a variable in it.
            $pattern = '|(?<!\\\\)(?:\\\\{2})*\${?[a-zA-Z0-9_]+}?|';
            if (preg_match($pattern, $tokens[$stackPtr]['content']) !== 0) {
                $this->processVariableInString($phpcsFile, $stackPtr);
            }

            return;
        }

        if ($this->functionOpen === true) {
            if ($tokens[$stackPtr]['code'] === T_VARIABLE) {
                $this->processVariable($phpcsFile, $stackPtr);
            }
        } else {
            // What if we assign a member variable to another?
            // ie. private $_count = $this->_otherCount + 1;.
            $this->processMemberVar($phpcsFile, $stackPtr);
        }

    }//end processTokenWithinScope()


    /**
     * Processes the token outside the scope in the file.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void
     */
    final protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        // These variables are not member vars.
        if ($tokens[$stackPtr]['code'] === T_VARIABLE) {
            $this->processVariable($phpcsFile, $stackPtr);
        } else if ($tokens[$stackPtr]['code'] === T_DOUBLE_QUOTED_STRING
            || $tokens[$stackPtr]['code'] === T_HEREDOC
        ) {
            // Check to see if this string has a variable in it.
            $pattern = '|(?<!\\\\)(?:\\\\{2})*\${?[a-zA-Z0-9_]+}?|';
            if (preg_match($pattern, $tokens[$stackPtr]['content']) !== 0) {
                $this->processVariableInString($phpcsFile, $stackPtr);
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
     * @return void
     */
    abstract protected function processMemberVar(File $phpcsFile, $stackPtr);


    /**
     * Called to process normal member vars.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where this
     *                                               token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void
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
     * @return void
     */
    abstract protected function processVariableInString(File $phpcsFile, $stackPtr);


}//end class
