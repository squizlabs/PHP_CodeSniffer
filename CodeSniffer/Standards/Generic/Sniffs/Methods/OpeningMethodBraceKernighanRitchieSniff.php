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
 * Generic_Sniffs_Methods_OpeningMethodBraceKernighanRitchieSniff.
 *
 * Checks that the opening brace of a function is on the same line
 * as the function declaration.
 *
 * @package  PHP_CodeSniffer
 * @category Generic_Coding_Standards
 * @author   Squiz Pty Ltd
 */
class Generic_Sniffs_Methods_OpeningMethodBraceKernighanRitchieSniff implements PHP_CodeSniffer_Sniff
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

        if ($lineDifference > 0) {
            $error = 'Opening function brace should be on the same line as the declaration.';
            $phpcsFile->addError($error, $openingBrace);
            return;
        }

        // Checks that the closing parenthesis and the opening brace are
        // separated by a whitespace character.
        $closerColumn = $tokens[$tokens[$stackPtr]['parenthesis_closer']]['column'];
        $braceColumn = $tokens[$openingBrace]['column'];

        $columnDifference = $braceColumn - $closerColumn;

        if ($columnDifference != 2) {
            $error = 'Expected 1 space between the closing parenthesis and the opening brace, but found '.($columnDifference - 1).'.';
            $phpcsFile->addError($error, $openingBrace);
            return;
        }

        // Check that a tab was not used instead of a space.
        $spaceTokenPtr = ($tokens[$stackPtr]['parenthesis_closer'] + 1);
        $spaceContent = $tokens[$spaceTokenPtr]['content'];
        if ($spaceContent !== ' ') {
            $error = 'Expected a single space character between the closing parenthesis and the opening brace, but found "'.$spaceContent.'".';
            $phpcsFile->addError($error, $openingBrace);
            return;
        }

    }//end process()


}//end class

?>