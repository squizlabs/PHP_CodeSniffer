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

require_once 'PHP/CodeSniffer/Sniff.php';
require_once 'PHP/CodeSniffer/SniffException.php';
require_once 'PHP/CodeSniffer/File.php';

/**
 * An <i>AbstractScopeTest</i> allows for tests that extend from this class to
 * listen for tokens within a particluar scope.
 *
 * Below is a test that listens to methods that exist only within classes:
 * <code>
 *    class ClassScopeTest extends PHP_CodeSniffer_Standards_AbstractScopeSniff
 *    {
 *        public function __construct()
 *        {
 *            parent::__construct(array(T_CLASS), array(T_FUNCTION));
 *        }
 *
 *        protected function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $)
 *        {
 *            $className = $phpcsFile->getDeclarationName($currScope);
 *            echo 'encountered a method within class '.$className;
 *        }
 *    }
 * </code>
 *
 * @package PHP_CodeSniffer
 * @author  Squiz Pty Ltd
 */
abstract class PHP_CodeSniffer_Standards_AbstractScopeSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The token types that this test wishes to listen to within the scope.
     *
     * @var array()
     */
    private $_tokens = array();

    /**
     * The type of scope opener tokens that this test wishes to listen to.
     *
     * @var string
     */
    private $_scopeTokens = array();

    /**
     * The position in the tokens array that opened the current scope.
     *
     * @var array()
     */
    protected $currScope = null;

    /**
     * true if this test should alert the extending class of tokens outside of
     * the scope.
     *
     * @var boolean
     */
    private $_listenOutside = false;


    /**
     * Constructs a new AbstractScopeTest.
     *
     * @param array   $scopeTokens   The type of scope the test wishes to listen to.
     * @param array   $tokens        The tokens that the test wishes to listen to
     *                               within the scope.
     * @param boolean $listenOutside If true this test will also alert the
     *                               extending class when a token is found outside
     *                               the scope, by calling the processTokenOutideScope.
     *
     * @see PHP_CodeSniffer.getValidScopeTokeners()
     * @throws PHP_CodeSniffer_Test_Exception If the specified tokens array is empty.
     */
    public function __construct(array $scopeTokens, array $tokens, $listenOutside=false)
    {
        if (empty($scopeTokens) === true) {
            throw new PHP_CodeSniffer_Test_Exception('The scope tokens list cannot be empty');
        }

        if (empty($tokens) === true) {
            throw new PHP_CodeSniffer_Test_Exception('The tokens list cannot be empty');
        }

        $invalidScopeTokens = array_diff($scopeTokens, PHP_CodeSniffer_File::getValidScopeOpeners());
        if (empty($invalidScopeTokens) === false) {
            $invalid = implode(', ', $invalidScopeTokens);
            throw new PHP_CodeSniffer_Test_Exception("Supplied scope tokens [$invalid] are not valid scope opener");
        }

        $invalidScopeTokens = array_intersect($scopeTokens, $tokens);
        if (empty($invalidScopeTokens) === false) {
            $invalid = implode(', ', $invalidScopeTokens);
            throw new PHP_CodeSniffer_Test_Exception("Supplied scope tokens [$invalid] cannot be in the tokens array");
        }

        $this->_listenOutside = $listenOutside;
        $this->_scopeTokens   = $scopeTokens;
        $this->_tokens        = $tokens;

    }//end __construct()


    /**
     * The method that is called to register the tokens this test wishes to
     * listen to.
     *
     * DO NOT OVERRIDE THIS METHOD. Use the constructor of this class to register
     * for the desired tokens and scope.
     *
     * @return array(int)
     * @see __constructor()
     */
    public final function register()
    {
        return $this->_scopeTokens;

    }//end register()


    /**
     * Processes the tokens that this test is listening for.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position in the stack where this token
     *                                        was found.
     *
     * @return void
     * @see processTokenWithinScope()
     */
    public final function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (in_array($tokens[$stackPtr]['code'], $this->_scopeTokens) === true) {
            $this->currScope = $stackPtr;
            $phpcsFile->addTokenListener($this, $this->_tokens);
        } else if ($this->currScope !== null && isset($tokens[$this->currScope]['scope_closer']) && $stackPtr > $tokens[$this->currScope]['scope_closer']) {
            $this->currScope = null;
            if ($this->_listenOutside === true) {
                // This is a token outside the current scope, so notify the
                // exender as the wish to know about this.
                $this->processTokenOutsideScope($phpcsFile, $stackPtr);
            } else {
                // Don't remove the listener if the extender wants to know about
                // tokens that live outside the current scope.
                $phpcsFile->removeTokenListener($this, $this->_tokens);
            }
        } else if ($this->currScope !== null) {
            $this->processTokenWithinScope($phpcsFile, $stackPtr, $this->currScope);
        } else {
            $this->processTokenOutsideScope($phpcsFile, $stackPtr);
        }

    }//end process()


    /**
     * Processes a token that is found within the scope that this test is
     * listening to.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position in the stack where this token
     *                                        was found.
     * @param int                  $currScope The position in the tokens array that
     *                                        opened the scope that this test is
     *                                        listening for.
     *
     * @return void
     */
    protected abstract function processTokenWithinScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope);


    /**
     * Processes a token that is found within the scope that this test is
     * listening to.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position in the stack where this token
     *                                        was found.
     *
     * @return void
     */
    protected function processTokenOutsideScope(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        return;

    }//end processTokenOutsideScope()


}//end class

?>
