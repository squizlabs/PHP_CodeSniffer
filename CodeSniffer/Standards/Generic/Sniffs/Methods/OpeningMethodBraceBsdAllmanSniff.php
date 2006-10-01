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
 * @category Generic_Coding_Standards
 * @author   Squiz Pty Ltd
 */

require_once 'PHP/CodeSniffer/Sniff.php';


/**
 * Generic_Sniffs_Methods_OpeningMethodBraceBsdAllmanSniff.
 *
 * Checks that the opening brace of a function is on the line after the
 * function declaration.
 *
 * @package  PHP_CodeSniffer
 * @category Generic_Coding_Standards
 * @author   Squiz Pty Ltd
 */
class Generic_Sniffs_Methods_OpeningMethodBraceBsdAllmanSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return void
     */
    public function register()
    {
        return array(T_FUNCTION);

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

        if (!isset($tokens[$stackPtr]['scope_opener'])) {
            return;
        }

        $openingBrace = $tokens[$stackPtr]['scope_opener'];

        // The end of the function occurs at the end of the argument list. Its 
        // like this because some people like to break long function declarations
        // over multiple lines.
        $functionLine = $tokens[$tokens[$stackPtr]['parenthesis_closer']]['line'];
        $braceLine    = $tokens[$openingBrace]['line'];

        $lineDifference = $braceLine - $functionLine;

        if ($lineDifference === 0) {
            $error = 'Opening function brace should be on a new line.';
            $phpcsFile->addError($error, $openingBrace);
            return;
        }

        if ($lineDifference > 1) {
            $ender = (($lineDifference - 1) === 1) ? 'line' : 'lines';
            $error = 'Opening function brace should be on the line after the declaration. Found '.($lineDifference - 1).' blank '.$ender.'.';
            $phpcsFile->addError($error, $openingBrace);
            return;
        }

        // We need to actually find the first piece of content on this line,
        // as if this is a method with tokens before it (public, static etc)
        // or an if with an else before it, then we need to start the scope
        // checking from there, rather than the current token.
        $lineStart = $stackPtr;
        while (($lineStart = $phpcsFile->findPrevious(array(T_WHITESPACE), ($lineStart - 1), null, false)) !== false) {
            if (strpos($tokens[$lineStart]['content'], "\n") !== false) {
                break;
            }
        }

        // We found a new line, now go forward and find the first non-whitespace
        // token.
        $lineStart = $phpcsFile->findNext(array(T_WHITESPACE), $lineStart, null, true);

        // The opening brace is on the correct line, now it needs to be
        // checked to be correctly indented.
        $startColumn = $tokens[$lineStart]['column'];
        $braceIndent = $tokens[$openingBrace]['column'];

        if ($braceIndent !== $startColumn) {
            $error = 'Opening Brace Indented Incorrectly. Expected '.($startColumn - 1).' spaces, but found '.($braceIndent - 1).'.';
            $phpcsFile->addError($error, $openingBrace);
        }

    }//end process()


}//end class

?>