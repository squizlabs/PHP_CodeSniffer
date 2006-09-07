<?php
/**
 * +------------------------------------------------------------------------+
 * | BSD Licence                                                            |
 * +------------------------------------------------------------------------+
 * | This software is available to you under the BSD license,               |
 * | available in the LICENSE file accompanying this software.              |
 * | You may obtain a copy of the License at                                |
 * |                                                                        |
 * | http://matrix.squiz.net/developer/tools/php_cs/licence                 |
 * |                                                                        |
 * | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS    |
 * | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT      |
 * | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR  |
 * | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT   |
 * | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,  |
 * | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT       |
 * | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,  |
 * | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY  |
 * | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT    |
 * | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE  |
 * | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.   |
 * +------------------------------------------------------------------------+
 * | Copyright (c), 2006 Squiz Pty Ltd (ABN 77 084 670 600).                |
 * | All rights reserved.                                                   |
 * +------------------------------------------------------------------------+
 *
 * @package PHP_CodeSniffer
 * @author  Squiz Pty Ltd
 */

require_once 'PHP/CodeSniffer/Standards/AbstractScopeSniff.php';

/**
 * A class to find T_VARIABLE tokens.
 *
 * This class can distingush between normal T_VARIABLE tokens, and those tokens
 * that represent class members. If a class member is encountered, then then
 * processMemberVar method is called so the extending class can process it. If
 * the token is found to be a normal T_VARIABLE token, then processVariable is
 * called.
 *
 * @package PHP_CodeSniffer
 * @author  Squiz Pty Ltd
 */
abstract class PHP_CodeSniffer_Standards_AbstractVariableSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{

    /**
     * The end token of the current function that we are in.
     *
     * @var int
     */
    private $_endFunction = -1;

    /**
     * true if a function is currently open.
     *
     * @var boolean
     */
    private $_functionOpen = false;

    /**
     * The current PHP_CodeSniffer file that we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    private $_phpcsFile = null;


    /**
     * Constructs an AbstractVariableTest.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS), array(T_FUNCTION, T_VARIABLE, T_DOUBLE_QUOTED_STRING), true);

    }//end __construct()


    /**
     * Processes the token in the specified PHP_CodeSniffer_File.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     * @param array                $currScope The current scope opener token.
     *
     * @return void
     */
    protected final function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope)
    {
        if ($this->_phpcsFile !== $phpcsFile) {
            $this->_phpcsFile   = $phpcsFile;
            $this->_functionOpen = false;
            $this->_endFunction  = -1;
        }

        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_FUNCTION) {

            $this->_functionOpen = true;

            $methodProps = $phpcsFile->getMethodProperties($stackPtr);

            // If the function is abstract, then set the end of the function
            // to it's closing semicolon.
            if ($methodProps['is_abstract'] === true) {
                $this->_endFunction = $phpcsFile->findNext(array(T_SEMICOLON), $stackPtr);
            } else {
                $this->_endFunction = $tokens[$stackPtr]['scope_closer'];
            }


        } else if ($stackPtr > $this->_endFunction) {
            $this->_functionOpen = false;
        }

        if ($this->_functionOpen === true) {
            if ($tokens[$stackPtr]['code'] === T_VARIABLE) {
                $this->processVariable($phpcsFile, $stackPtr);
            } else if ($tokens[$stackPtr]['code'] === T_DOUBLE_QUOTED_STRING) {
                // Check to see if this string has a variable in it.
                if (preg_match('|\$[a-zA-Z0-9_]+|', $tokens[$stackPtr]['content']) !== 0) {
                    $this->processVariableInString($phpcsFile, $stackPtr);
                }
            }

            return;
        } else {
            // What if we assign a memeber variable to another?
            // ie. private $_count = $this->_otherCount + 1;.
            $this->processMemberVar($phpcsFile, $stackPtr);
        }

    }//end processTokenWithinScope()


    /**
     * Processes the token outside the scope in the file.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected final function processTokenOutsideScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        // These variables are not member vars.
        if ($tokens[$stackPtr]['code'] === T_VARIABLE) {
            $this->processVariable($phpcsFile, $stackPtr);
        } else {
            $this->processVariableInString($phpcsFile, $stackPtr);
        }

    }//end processTokenOutsideScope()


    /**
     * Called to process class member vars.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     *
     * @return void
     */
    abstract protected function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr);


    /**
     * Called to process normal member vars.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                  $stackPtr  The position where the token was found.
     *
     * @return void
     */
    abstract protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr);


    /**
     * Called to process variables found in duoble quoted strings.
     *
     * Note that there may be more than one variable in the string, which will
     * result only in one call for the string.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where this token was found.
     * @param int                  $stackPtr  The position where the double quoted
     *                                        string was found.
     *
     * @return void
     */
    abstract protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr);


}//end class

?>
