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
require_once 'PHP/CodeSniffer/Tokens.php';


/**
 * PHP_CodeSniffer_Sniffs_PEAR_Statements_MultipleStatementSniff.
 *
 * Checks alignment of assignments. If there are multiple adjacent assignments,
 * it will check that the equals signs of each assignment are aligned. It will
 * display a warning to advise that the signs should be aligned.
 *
 * @package  PHP_CodeSniffer
 * @category PEAR_Coding_Standards
 * @author   Squiz Pty Ltd
 */
class PEAR_Sniffs_Statements_MultipleStatementSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$assignmentTokens;

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

        $assignedVariable = $phpcsFile->findPrevious(array(T_VARIABLE), ($stackPtr - 1), null, false);
        if (($assignedVariable === false) || (!$this->_isAssignment($phpcsFile, $assignedVariable))) {
            return;
        }

        /*
            By this stage, it is known that there is an assignment on this line.
            We only want to process the block once we reach the last assignment,
            so we need to determine if there are more to follow.
        */

        if (($nextAssign = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$assignmentTokens, $stackPtr + 1, null, false)) !== false) {
            if ($tokens[$nextAssign]['line'] === ($tokens[$stackPtr]['line'] + 1)) {
                return;
            }
        }

        // OK, getting here means that this is the last in a block of statements.
        $assignments         = array();
        $assignments[]       = $stackPtr;
        $prevAssignment      = $stackPtr;
        $lastLine            = $tokens[$stackPtr]['line'];
        $maxVariableLength   = 0;
        $maxAssignmentLength = 0;

        while (($prevAssignment = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$assignmentTokens, $prevAssignment - 1, null, false)) !== false) {
            if ($tokens[$prevAssignment]['line'] !== $lastLine - 1) {
                break;
            }
            $checkingVariable = $phpcsFile->findPrevious(array(T_VARIABLE), $prevAssignment - 1, null, false);
            if ($checkingVariable === false) {
                break;
            }

            if ($tokens[$checkingVariable]['line'] !== $tokens[$prevAssignment]['line']) {
                break;
            }

            if (!$this->_isAssignment($phpcsFile, $checkingVariable)) {
                break;
            }

            $assignments[] = $prevAssignment;
            $lastLine--;
        }

        foreach ($assignments as $assignment) {
            $variable = $phpcsFile->findPrevious(array(T_VARIABLE), $assignment, null, false);
            if ($this->_isAssignment($phpcsFile, $variable) === false) {
                break;
            }

            $variableContent = $phpcsFile->getTokensAsString($variable, $assignment - $variable);
            $variableContent = rtrim($variableContent);
            $contentLength   = strlen($variableContent);

            if ($maxVariableLength < ($contentLength + $tokens[$variable]['column'])) {
                $maxVariableLength = ($contentLength + $tokens[$variable]['column']);
            }
            if ($maxAssignmentLength < strlen($tokens[$assignment]['content'])) {
                $maxAssignmentLength = strlen($tokens[$assignment]['content']);
            }
        }

        // Determine the actual position that each equals sign should be in.
        $column = ($maxVariableLength + 1);
        foreach ($assignments as $assignment) {
            // Actual column takes into account the length of the assignment operator.
            $actualColumn = ($column + $maxAssignmentLength - strlen($tokens[$assignment]['content']));
            if ($tokens[$assignment]['column'] !== $actualColumn) {
                $variable        = $phpcsFile->findPrevious(array(T_VARIABLE), $assignment, null, false);
                $variableContent = $phpcsFile->getTokensAsString($variable, $assignment - $variable);
                $variableContent = rtrim($variableContent);
                $contentLength   = strlen($variableContent);

                $leadingSpace  = $tokens[$variable]['column'];
                $expected      = ($actualColumn - ($contentLength + $leadingSpace));
                $expected     .= ($expected == 1) ? ' space' : ' spaces';
                $found         = ($tokens[$assignment]['column'] - ($contentLength + $leadingSpace));
                $found        .= ($found == 1) ? ' space' : ' spaces';

                if (count($assignments) === 1) {
                    $error = 'Equals sign not aligned correctly. Expected '.$expected.', but found '.$found.'.';
                } else {
                    $error = 'Equals sign not aligned with surrounding assignments. Expected '.$expected.', but found '.$found.'.';
                }

                $phpcsFile->addWarning($error, $assignment);
            }
        }

    }//end process()


    /**
     * Determins if the stackPtr is an assignment or not.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where to token exists.
     * @param int                  $stackPtr  The position in the stack to check.
     *
     * @return boolean
     */
    private function _isAssignment(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if ($stackPtr === false) {
            return false;
        }
        $prevContent = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr - 1, null, true);
        // If there is something other than whitespace on this line before this variable, its not what we want.
        if ($tokens[$prevContent]['line'] === $tokens[$stackPtr]['line']) {
            return false;
        }

        $nextContent = $phpcsFile->findNext(array(T_VARIABLE, T_OPEN_SQUARE_BRACKET, T_CLOSE_SQUARE_BRACKET, T_CONSTANT_ENCAPSED_STRING, T_LNUMBER), $stackPtr + 1, null, true);
        $nextContent++;
        // If this isn't an assignment, then its not what we want.
        if (in_array($tokens[$nextContent]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === false) {
            return false;
        }
        return true;

    }//end _isAssignment()


}//end class

?>
