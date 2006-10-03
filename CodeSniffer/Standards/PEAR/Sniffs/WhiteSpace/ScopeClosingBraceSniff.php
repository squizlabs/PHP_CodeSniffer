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
 * PEAR_Sniffs_Whitespace_ScopeClosingBraceSniff.
 *
 * Checks that the closing braces of scopes are aligned correctly.
 *
 * @package  PHP_CodeSniffer
 * @category PEAR_Coding_Standards
 * @author   Squiz Pty Ltd
 */
class PEAR_Sniffs_Whitespace_ScopeClosingBraceSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$scopeOpeners;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // If this is an inline condition (ie. there is no scope opener), then
        // return, as this is not a new scope.
        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        // We need to actually find the first piece of content on this line,
        // as if this is a method with tokens before it (public, static etc)
        // or an if with an else before it, then we need to start the scope
        // checking from there, rather than the current token.
        $lineStart = ($stackPtr - 1);
        for ($lineStart; $lineStart > 0; $lineStart--) {
            if (strpos($tokens[$lineStart]['content'], "\n") !== false) {
                break;
            }
        }

        // We found a new line, now go forward and find the first non-whitespace
        // token.
        $lineStart = $phpcsFile->findNext(array(T_WHITESPACE), ($lineStart + 1), null, true);

        $startColumn = $tokens[$lineStart]['column'];
        $scopeStart  = $tokens[$stackPtr]['scope_opener'];
        $scopeEnd    = $tokens[$stackPtr]['scope_closer'];

        // Check that the closing brace is on it's own line.
        $lastContent = $phpcsFile->findPrevious(array(T_WHITESPACE), $scopeEnd - 1, $scopeStart, true);
        if ($tokens[$lastContent]['line'] === $tokens[$scopeEnd]['line']) {
            $error = 'Closing brace should be on a line by itself.';
            $phpcsFile->addError($error, $scopeEnd);
            return;
        }

        // Check now that the closing brace is lined up correctly.
        $braceIndent = $tokens[$scopeEnd]['column'];
        if ($tokens[$stackPtr]['code'] !== T_CASE) {
            if ($braceIndent !== $startColumn) {
                $error = 'Closing Brace Indented Incorrectly. Expected '.($startColumn - 1).' spaces, but found '.($braceIndent - 1).'.';
                $phpcsFile->addError($error, $scopeEnd);
            }
        }

    }//end process()


}//end class

?>
