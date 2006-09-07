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
 * PEAR_Sniffs_Files_IncludingFileSniff.
 *
 * Checks that the include_once is used in conditional situations, and
 * require_once is used elsewhere. Also checks that brackets do not surround
 * the file being included.
 *
 * @package  PHP_CodeSniffer
 * @category PEAR_Coding_Standards
 * @author   Squiz Pty Ltd
 */
class PEAR_Sniffs_Files_IncludingFileSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Conditions that should use include_once
     *
     * @var array(int)
     */
    private static $_conditions = array(
                                   T_IF,
                                   T_ELSE,
                                   T_ELSEIF,
                                   T_SWITCH,
                                  );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_INCLUDE_ONCE,
                T_REQUIRE_ONCE,
                T_REQUIRE,
                T_INCLUDE,
               );

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

        $nextToken = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr + 1, null, true);
        if ($tokens[$nextToken]['code'] === T_OPEN_PARENTHESIS) {
            $error  = '"'.$tokens[$stackPtr]['content'].'"';
            $error .= ' is a statement, not a function. ';
            $error .= 'No parentheses are required.';
            $phpcsFile->addError($error, $stackPtr);
        }

        $inCondition = (count($tokens[$stackPtr]['conditions']) !== 0) ? true : false;

        // Check to see if this including statement is within the parenthesis of a condition.
        // If that's the case then we need to process it as being within a condition, as they
        // are checking the return value.
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            foreach ($tokens[$stackPtr]['nested_parenthesis'] as $left => $right) {
                if (isset($tokens[$left]['parenthesis_owner']) === true) {
                    $inCondition = true;
                }
            }
        }

        // Check to see if they are assigning the return value of this including call.
        // If they are then they are probably checking it, so its conditional.
        $previous = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr - 1, null, true);
        if (in_array($tokens[$previous]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === true) {
            // The have assigned the return value to it, so its conditional.
            $inCondition = true;
        }

        $tokenCode = $tokens[$stackPtr]['code'];
        if ($inCondition === true) {
            // We are inside a conditional statement. We need an include_once.
            if ($tokenCode === T_REQUIRE_ONCE) {
                $error  = 'File is being conditionally included. ';
                $error .= 'Use "include_once" instead.';
                $phpcsFile->addError($error, $stackPtr);
            } else if ($tokenCode === T_REQUIRE) {
                $error  = 'File is being conditionally included. ';
                $error .= 'Use "include" instead.';
                $phpcsFile->addError($error, $stackPtr);
            }
        } else {
            // We are unconditionally including, we need a require_once.
            if ($tokenCode === T_INCLUDE_ONCE) {
                $error  = 'File is being unconditionally included. ';
                $error .= 'Use "require_once" instead.';
                $phpcsFile->addError($error, $stackPtr);
            } else if ($tokenCode === T_INCLUDE) {
                $error  = 'File is being unconditionally included. ';
                $error .= 'Use "require" instead.';
                $phpcsFile->addError($error, $stackPtr);
            }
        }

    }//end process()


}//end class

?>
