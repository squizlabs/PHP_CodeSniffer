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
 * PHP_CodeSniffer_Sniffs_PEAR_Files_LineLengthSniff.
 *
 * Checks all lines in the file, and throws warnings if they are over 85
 * characters in length.
 *
 * @package  PHP_CodeSniffer
 * @category PEAR_Coding_Standards
 * @author   Squiz Pty Ltd
 */
class PEAR_Sniffs_Files_LineLengthSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The limit that the length of a line should not exceed.
     *
     * @var int
     */
    const LINE_LIMIT = 85;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

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

        // Make sure this is the first open tag.
        $previousOpenTag = $phpcsFile->findPrevious(array(T_OPEN_TAG), $stackPtr - 1);
        if ($previousOpenTag !== false) {
            return;
        }

        $tokenLimit         = count($tokens);
        $tokenCount         = 0;
        $currentLineContent = '';
        $currentLine        = 1;
        $longLines          = array();

        for (; $tokenCount < $tokenLimit; $tokenCount++) {
            if ($tokens[$tokenCount]['line'] === $currentLine) {
                $currentLineContent .= $tokens[$tokenCount]['content'];
            } else {
                $currentLineContent = trim($currentLineContent, "\n");
                $lineLength         = strlen($currentLineContent);

                if ($lineLength > self::LINE_LIMIT) {
                    $longLines[] = ($tokenCount - 1);
                }

                $currentLineContent = $tokens[$tokenCount]['content'];
                $currentLine++;
            }
        }

        foreach ($longLines as $lineToken) {
            $warning = 'Line exceeds '.self::LINE_LIMIT.' characters. It is recommended that this be shortened.';
            $phpcsFile->addWarning($warning, $lineToken);
        }

    }//end process()


}//end class

?>
