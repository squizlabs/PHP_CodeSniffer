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
 * @package  PHP_CodeSniffer
 * @category PEAR_Coding_Standards
 * @author   Squiz Pty Ltd
 */

require_once 'PHP/CodeSniffer/Sniff.php';


/**
 * PEAR_Sniffs_Methods_MethodCallArgumentSpacingSniff.
 *
 * Checks that calls to methods and functions are spaced correctly.
 *
 * @package  PHP_CodeSniffer
 * @category PEAR_Coding_Standards
 * @author   Squiz Pty Ltd
 */
class PEAR_Sniffs_Methods_MethodCallArgumentSpacingSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);

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

        // Skip tokens that are the names of functions or classes
        // within their definitions. For example:
        // function myFunction...
        // "myFunction" is T_STRING but we should skip because it is not a
        // function or method *call*.
        $functionName    = $stackPtr;
        $functionKeyword = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if ($tokens[$functionKeyword]['code'] === T_FUNCTION || $tokens[$functionKeyword]['code'] === T_CLASS) {
            return;
        }

        // If the next non-whitespace token after the function or method call
        // is not an opening parenthesis then it cant really be a *call*.
        $openBracket = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($functionName + 1), null, true);
        if ($tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }

        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];

        $nextSeperator = $openBracket;
        while (($nextSeperator = $phpcsFile->findNext(array(T_COMMA, T_VARIABLE), ($nextSeperator + 1), $closeBracket)) !== false) {
            if ($tokens[$nextSeperator]['code'] === T_COMMA) {
                if ($tokens[$nextSeperator - 1]['code'] === T_WHITESPACE) {
                    $error = 'Space found before comma in function call.';
                    $phpcsFile->addError($error, $stackPtr);
                }
                if ($tokens[$nextSeperator + 1]['code'] !== T_WHITESPACE) {
                    $error = 'No space found after comma in function call.';
                    $phpcsFile->addError($error, $stackPtr);
                } else {
                    // If there is a newline in the space, then the must be formatting
                    // each argument on a newline, which is valid, so ignore it.
                    if (strpos($tokens[$nextSeperator + 1]['content'], "\n") === false) {
                        $space = strlen($tokens[$nextSeperator + 1]['content']);
                        if ($space > 1) {
                            $error  = 'Expected 1 space after comma in function call, ';
                            $error .= $space.' found.';
                            $phpcsFile->addError($error, $stackPtr);
                        }
                    }
                }
            } else {
                // Token is a variable.
                $nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $nextSeperator + 1, $closeBracket, true);
                if ($nextToken !== false) {
                    if ($tokens[$nextToken]['code'] === T_EQUAL) {
                        if (($tokens[$nextToken - 1]['code']) !== T_WHITESPACE) {
                            $error = 'Expected one space before = sign of default value.';
                            $phpcsFile->addError($error, $stackPtr);
                        }
                        if ($tokens[$nextToken + 1]['code'] !== T_WHITESPACE) {
                            $error = 'Expected one space after = sign of default value.';
                            $phpcsFile->addError($error, $stackPtr);
                        }
                    }
                }
            }
        }//end while


    }//end process()


}//end class

?>
