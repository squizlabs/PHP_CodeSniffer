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

require_once 'PHP/CodeSniffer.php';
require_once 'PHP/CodeSniffer/Tokens.php';
require_once 'PHP/CodeSniffer/Exception.php';

/**
 * A PHP_CodeSniffer_File object represents a PHP source file and the tokens
 * associated with it.
 *
 * It provides a means for traversing the token stack, along with
 * other token related operations. If a PHP_CodeSniffer_Sniff finds and error or
 *  warning within a PHP_CodeSniffer_File, you can raise an error using the
 *  addError() or addWarning() methods.
 *
 * <b>Token Information</b>
 *
 * Each token within the stack contains information about itself:
 *
 * <code>
 *   array(
 *    'code'       => 301,       // the token type code (see token_get_all())
 *    'content'    => 'if',      // the token content
 *    'type'       => 'T_IF',    // the token name
 *    'line'       => 56,        // the line number when the token is located
 *    'column'     => 12,        // the column in the line where this token
 *                               // starts (starts from 1)
 *    'level'      => 2          // the depth a token is within the scopes open
 *    'conditions' => array(     // a list of scope condition token
 *                               // positions => codes that
 *                     2 => 50,  // openened the scopes that this token exists
 *                     9 => 353, // in (see conditional tokens section below)
 *                    ),
 *   );
 * </code>
 *
 * <b>Conditional Tokens</b>
 *
 * In addition to the standard token fields, conditions contain information to
 * determine where their scope begins and ends:
 *
 * <code>
 *   array(
 *    'scope_condition' => 38, // the token position of the condition
 *    'scope_opener'    => 41, // the token position that started the scope
 *    'scope_closer'    => 70, // the token position that ended the scope
 *   );
 * </code>
 *
 * The condition, the scope opener and the scope closer each contain this
 * information.
 *
 * <b>Parenthesis Tokens</b>
 *
 * Each parenthesis token (T_OPEN_PARENTHESIS and T_CLOSE_PARENTHESIS) has a
 * reference to their opening and closing parenthesis, one being itself, the
 * other being its oposite.
 *
 * <code>
 *   array(
 *    'parenthesis_opener' => 34,
 *    'parenthesis_closer' => 40,
 *   );
 * </code>
 *
 * Some tokens can "own" a set of parethesis. For example a T_FUNCTION token
 * has parenthesis around its argument list. These tokens also have the
 * parenthesis_opener and and parenthesis_closer indicies. Not all parethesis
 * have owners, for example parenthesis used for arithmetic operations and
 * function calls. The parenthesis tokens that have an owner have the following
 * auxilery array indicies.
 *
 * <code>
 *   array(
 *    'parenthesis_opener' => 34,
 *    'parenthesis_closer' => 40,
 *    'parenthesis_owner'  => 33,
 *   );
 * </code>
 *
 * Each token within a set of parenthesis also has an array indicy 
 * 'nested_parenthesis' which is an array of the left parenthesis => right parenthesis 
 * token positions.
 *
 * <code>
 *   'nested_parentheisis' => array(
 *                             12 => 15
 *                             11 => 14
 *                            );
 * </code>
 *
 * <b>Extended Tokens</b>
 *
 * PHP_CodeSniffer extends and augments some of the tokens created by
 * <i>token_get_all()</i>. A full list of these tokens can be seen in the
 * <i>Tokens.php</i> file.
 *
 * @package PHP_CodeSniffer
 * @author  Squiz Pty Ltd
 */
class PHP_CodeSniffer_File
{

    /**
     * The absolute path to the file associated with this object.
     *
     * @var string
     */
    private $_file = array();

    /**
     * The tokens stack map.
     *
     * Note that the tokens in this array differ in format to the tokens
     * produced by token_get_all(). Tokens are initially produced with
     * token_get_all(), then augmented so that it's easier to process them.
     *
     * @var array()
     * @see Tokens.php
     */
    private $_tokens = array();

    /**
     * The errors raised from PHP_CodeSniffer_Sniffs.
     *
     * @var array()
     * @see getErrors()
     */
    private $_errors = array();

    /**
     * The warnings raised form PHP_CodeSniffer_Sniffs.
     *
     * @var array()
     * @see getWarnings()
     */
    private $_warnings = array();

    /**
     * The total number of errors raised.
     *
     * @var int
     */
    private $_errorCount = 0;

    /**
     * The total number of warnings raised.
     *
     * @var int
     */
    private $_warningCount = 0;

    /**
     * An array of PHP_CodeSniffer_Sniff listeners listening to this file's processing.
     *
     * @var array(PHP_CodeSniffer_Sniff)
     */
    private $_listeners = array();

    /**
     * A constant to represent an error in PHP_CodeSniffer.
     *
     * @var int
     */
    const ERROR = 0;

    /**
     * A constant to represent a warning in PHP_CodeSniffer.
     *
     * @var int
     */
    const WARNING = 1;

    /**
     * A list of tokens that are allowed to open a scope.
     *
     * This array also contains information about what kind of token the scope
     * opener uses to oken a scope, and if the token can share a scope closer.
     * An example of a token that shares a scope closer is a CASE scope.
     *
     * @var array
     */
    private static $_scopeOpeners = array(
                                     T_IF            => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_TRY           => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_CATCH         => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_ELSE          => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_ELSEIF        => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_FOR           => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_FOREACH       => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_INTERFACE     => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_FUNCTION      => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_CLASS         => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_WHILE         => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_DO            => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_SWITCH        => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'shared' => false,
                                                    ),
                                     T_CASE          => array(
                                                         'start'  => T_COLON,
                                                         'end'    => T_BREAK,
                                                         'shared' => true,
                                                    ),
                                     T_DEFAULT       => array(
                                                         'start'  => T_COLON,
                                                         'end'    => T_BREAK,
                                                         'shared' => false,
                                                    ),
                                     T_START_HEREDOC => array(
                                                         'start'  => T_START_HEREDOC,
                                                         'end'    => T_END_HEREDOC,
                                                         'shared' => false,
                                                    ),
                                    );


    /**
     * Constructs a PHP_CodeSniffer_File.
     *
     * @param string                       $file      The absolute path to the file
     *                                                to process.
     * @param array(PHP_CodeSniffer_Sniff) $listeners The initial listeners listening
     *                                                to processing of this file.
     */
    public function __construct($file, array $listeners)
    {
        $this->_file = $file;
        $this->_listeners = $listeners;
        $this->_parse();

    }//end __construct()


    /**
     * Adds a listener to the token stack that listens to the specific tokens.
     *
     * When PHP_CodeSniffer encounters on the the tokens specified in $tokens, it
     *  invokes the process method of the sniff.
     *
     * @param PHP_CodeSniffer_Sniff $listener The listener to add to the
     *                                        listener stack.
     * @param array(int)            $tokens   The token types the listener wishes to
     *                                        listen to.
     *
     * @return void
     */
    public function addTokenListener(PHP_CodeSniffer_Sniff $listener, array $tokens)
    {
        foreach ($tokens as $token) {
            if (isset($this->_listeners[$token]) === false) {
                $this->_listeners[$token] = array();
            }

            if (in_array($listener, $this->_listeners[$token]) === false) {
                $this->_listeners[$token][] = $listener;
            }
        }

    }//end addTokenListener()


    /**
     * Removes a listener from listening from the specified toekns.
     *
     * @param PHP_CodeSniffer_Sniff $listener The listener to remove from the listener
     *                                        stack.
     * @param array(int)            $tokens   The token types the listener wishes to
     *                                        stop listen to.
     *
     * @return void
     */
    public function removeTokenListener(PHP_CodeSniffer_Sniff $listener, array $tokens)
    {
        foreach ($tokens as $token) {
            if (isset($this->_listeners[$token]) === false) {
                continue;
            }

            if (in_array($listener, $this->_listeners[$token]) === true) {
                foreach ($this->_listeners[$token] as $pos => $value) {
                    if ($value === $listener) {
                        unset($this->_listeners[$token][$pos]);
                    }
                }
            }
        }

    }//end removeTokenListener()


    /**
     * Returns the token stack for this file.
     *
     * @return array()
     */
    public function getTokens()
    {
        return $this->_tokens;

    }//end getTokens()


    /**
     * Starts the stack traversal, alerting PHP_CodeSniffer_Sniff listeners when their
     * listening tokens are encountered.
     *
     * @return void
     */
    public function start()
    {
        // Foreach of the listeners that have registed to listen for this
        // token, get them to process it.
        foreach ($this->_tokens as $stackPtr => $token) {
            $tokenType = $token['code'];
            if (isset($this->_listeners[$tokenType]) === true) {
                foreach ($this->_listeners[$tokenType] as $listener) {
                    $listener->process($this, $stackPtr);
                }
            }
        }

        // We don't need the tokens any more, so get rid of them
        // to save some memory.
        $this->_tokens = null;

    }//end start()


    /**
     * Processes the file and runs the PHP_CodeSniffer sniffs to verify that it
     * conforms with the tests.
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If the file could not be processed.
     */
    private function _parse()
    {
        $contents = file_get_contents($this->_file);
        $tokens   = token_get_all($contents);

        $newStackPtr = 0;
        $numTokens   = count($tokens);
        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token = $tokens[$stackPtr];

            // If this is a double quoted string, PHP will tokenise the whole
            // thing which causes problems with the scope map when braces are
            // within the string. So we need to merge the tokens together to
            // provide a single string.
            if (is_array($token) === false && $token === '"') {

                $tokenContent = '"';
                for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                    if (is_array($tokens[$i]) === true) {
                        $tokenContent .= $tokens[$i][1];
                    } else {
                        $tokenContent .= $tokens[$i];
                    }

                    if (is_array($tokens[$i]) === false && $tokens[$i] === '"') {
                        // We found the other end of the double quoted string.
                        break;
                    }
                }

                $stackPtr = $i;

                // Convert each line within the double quoted string to a 
                // new token, so it conforms with other multiple line tokens.
                $tokenLines = explode("\n", $tokenContent);
                $numLines   = count($tokenLines);
                $newToken   = array();

                for ($j = 0; $j < $numLines; $j++) {
                    $newToken['content'] = $tokenLines[$j];
                    if ($j === ($numLines - 1)) {
                        if ($tokenLines[$j] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= "\n";
                    }

                    $newToken['code']            = T_DOUBLE_QUOTED_STRING;
                    $newToken['type']            = 'T_DOUBLE_QUOTED_STRING';
                    $this->_tokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                }

                // Continue, as we're done with this token.
                continue;
            }//end if

            // If this token has newlines in its content, split each line up
            // and create a new token for each line. We do this so it's easier
            // to asertain where errors occur on a line.
            if (is_array($token) === true && strpos($token[1], "\n") !== false) {
                $tokenLines = explode("\n", $token[1]);
                $numLines   = count($tokenLines);

                for ($i = 0; $i < $numLines; $i++) {
                    $newToken['content'] = $tokenLines[$i];
                    if ($i === ($numLines - 1)) {
                        if ($tokenLines[$i] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= "\n";
                    }

                    $newToken['type'] = token_name($token[0]);
                    $newToken['code'] = $token[0];
                    $this->_tokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                }
            } else {
                $newToken = PHP_CodeSniffer::standardiseToken($token);

                // This is a special condition for T_ARRAY tokens use to
                // type hint function arguments as being arrays. We want to keep
                // the parenthsis map clean, so let's tag these tokens as
                // T_ARRAY_HINT.
                if ($newToken['code'] === T_ARRAY) {
                    // Recalculate number of tokens.
                    $numTokens = count($tokens);
                    for ($i = $stackPtr; $i < $numTokens; $i++) {
                        if (is_array($tokens[$i]) === false) {
                            if ($tokens[$i] === '(') {
                                break;
                            }
                        } else if ($tokens[$i][0] === T_VARIABLE) {
                            $newToken['code'] = T_ARRAY_HINT;
                            $newToken['type'] = 'T_ARRAY_HINT';
                            break;
                        }
                    }
                }

                $this->_tokens[$newStackPtr] = $newToken;
                $newStackPtr++;
            }//end else
        }//end foreach

        $this->_createLineMap();
        $this->_createParenthesisMap();
        $this->_createParenthesisNestingMap();
        $this->_createScopeMap();
        // Column map requires the line map to be complete.
        $this->_createColumnMap();
        $this->_createLevelMap();

        if (VERBOSE === true) {
            $numTokens = count($this->_tokens);
            $numLines  = $this->_tokens[($numTokens -1)]['line'];
            echo "[$numTokens tokens in $numLines lines]... ";
        }

    }//end _parse()


    /**
     * Adds an error to the error stack.
     *
     * @param string $error    The error message.
     * @param int    $stackPtr The stack position where the error occured.
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If $stackPtr is null.
     */
    public function addError($error, $stackPtr)
    {
        if ($stackPtr === null) {
            throw new PHP_CodeSniffer_Exception('$stackPtr cannot be null');
        }

        $lineNum = $this->_tokens[$stackPtr]['line'];

        if (isset($this->_errors[$lineNum]) === false) {
            $this->errors[$lineNum] = array();
        }

        $this->_errors[$lineNum][] = $error;
        $this->_errorCount++;

    }//end addError()


    /**
     * Adds an warning to the warning stack.
     *
     * @param string $warning  The error message.
     * @param int    $stackPtr The stack position where the error occured.
     *
     * @return void
     * @throws PHP_CodeSniffer_Exception If $stackPtr is null.
     */
    public function addWarning($warning, $stackPtr)
    {
        if ($stackPtr === null) {
            throw new PHP_CodeSniffer_Exception('$stackPtr cannot be null');
        }

        $lineNum = $this->_tokens[$stackPtr]['line'];
        if (isset($this->_warnings[$lineNum]['line']) === false) {
            $this->_warnings[$lineNum] = array();
        }

        $this->_warnings[$lineNum][] = $warning;
        $this->_warningCount++;

    }//end addWarning()


    /**
     * Returns the number of errors raised.
     *
     * @return int
     */
    public function getErrorCount()
    {
        return $this->_errorCount;

    }//end getErrorCount()


    /**
     * Returns the number of warnings raised.
     *
     * @return int
     */
    public function getWarningCount()
    {
        return $this->_warningCount;

    }//end getWarningCount()


    /**
     * Returns the errors raised from processing this file.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;

    }//end getErrors()


    /**
     * Returns the warnings raised from processing this file.
     *
     * @return array
     */
    public function getWarnings()
    {
        return $this->_warnings;

    }//end getWarnings()


    /**
     * Returns the absolute filename of this file.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_file;

    }//end getFilename()


    /**
     * Creates a map of tokens => line numbers for each token.
     *
     * @return void
     */
    private function _createLineMap()
    {
        $lineNumber = 1;
        $count      = count($this->_tokens);

        for ($i = 0; $i < $count; $i++) {
            $this->_tokens[$i]['line'] = $lineNumber;
            $lineNumber += substr_count($this->_tokens[$i]['content'], "\n");
        }

    }//end _createLineMap()


    /**
     * Creates a column map.
     *
     * The column map indicates where the token started on the line where it
     * exists.
     *
     * @return void
     */
    private function _createColumnMap()
    {
        $currColumn = 1;
        $count      = count($this->_tokens);

        for ($i = 0; $i < $count; $i++) {
            $this->_tokens[$i]['column'] = $currColumn;
            if (isset($this->_tokens[$i + 1]['line']) === true && $this->_tokens[$i + 1]['line'] !== $this->_tokens[$i]['line']) {
                $currColumn = 1;
            } else {
                $currColumn += strlen($this->_tokens[$i]['content']);
            }
        }

    }//end _createColumnMap()


    /**
     * Creates a map for opening and closing of parenthesis.
     *
     * Each parenthesis token (T_OPEN_PARENTHESIS and T_CLOSE_PARENTHESIS) has a
     * reference to their opening and closing parenthesis (parenthesis_opener
     * and parenthesis_closer).
     *
     * @return void
     */
    private function _createParenthesisMap()
    {
        $openers   = array();
        $numTokens = count($this->_tokens);
        $owners    = array();

        for ($i = 0; $i < $numTokens; $i++) {
            if (in_array($this->_tokens[$i]['code'], PHP_CodeSniffer_Tokens::$parenthesisOpeners) === true) {
                $owners[] = $i;
            } else if ($this->_tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $openers[] = $i;
            } else if ($this->_tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                // Did we set an owner for this set of parenthesis?
                $hasOwner = (count($openers) === count($owners));

                $opener = array_pop($openers);
                $this->_tokens[$i]['parenthesis_opener']      = $opener;
                $this->_tokens[$i]['parenthesis_closer']      = $i;
                $this->_tokens[$opener]['parenthesis_opener'] = $opener;
                $this->_tokens[$opener]['parenthesis_closer'] = $i;

                // Check to see if this parethesis has an owner. Some
                // parenthesis do not have owners, for example arithmetic
                // operations.
                if ($hasOwner === true) {
                    $owner = array_pop($owners);
                    $this->_tokens[$owner]['parenthesis_owner']  = $owner;
                    $this->_tokens[$owner]['parenthesis_opener'] = $opener;
                    $this->_tokens[$owner]['parenthesis_closer'] = $i;
                    $this->_tokens[$i]['parenthesis_owner']      = $owner;
                    $this->_tokens[$opener]['parenthesis_owner'] = $owner;
                }
            }//end if
        }//end for

    }//end _createParenthesisMap()


    /**
     * Creates a map for the parenthesis tokens that surround other tokens.
     *
     * @return void
     */
    private function _createParenthesisNestingMap()
    {
        $numTokens = count($this->_tokens);
        $map = array();
        for ($i = 0; $i < $numTokens; $i++) {
            if (isset($this->_tokens[$i]['parenthesis_opener']) === true && $i === $this->_tokens[$i]['parenthesis_opener']) {
                $map[$this->_tokens[$i]['parenthesis_opener']] = $this->_tokens[$i]['parenthesis_closer'];
            } else if (isset($this->_tokens[$i]['parenthesis_closer']) === true && $i === $this->_tokens[$i]['parenthesis_closer']) {
                array_pop($map);
            } else {
                if (empty($map) === false) {
                    $this->_tokens[$i]['nested_parenthesis'] = $map;
                }
            }
        }
    }//end _createParenthesisNestingMap()


    /**
     * Creates a scope map of tokens that open scopes.
     *
     * @return void
     * @see _recurseScopeMap()
     */
    private function _createScopeMap()
    {
        $numTokens = count($this->_tokens);
        for ($i = 0; $i < $numTokens; $i++) {
            // Check to see if the current token starts a new scope.
            if (isset(self::$_scopeOpeners[$this->_tokens[$i]['code']]) === true) {
                $i = $this->_recurseScopeMap($i);
            }
        }

    }//end _createScopeMap()


    /**
     * Recurses though the scope openers to build a scope map.
     *
     * @param int $stackPtr The position in the stack of the token that opened
     *                      the scope (eg. and if token or for token)
     *
     * @return int The position in the stack that closed the scope.
     */
    private function _recurseScopeMap($stackPtr)
    {
        $opener         = null;
        $closer         = null;
        $currType       = $this->_tokens[$stackPtr]['code'];
        $endScopeTokens = $this->_getEndScopeTokens();
        $startLine      = $this->_tokens[$stackPtr]['line'];
        $ignore         = false;

        // If the start token for this scope opener is the same as
        // the scope token, we have already found our opener.
        if ($currType === self::$_scopeOpeners[$currType]['start']) {
            $opener = $stackPtr;
        }

        $numTokens = count($this->_tokens);
        for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
            $tokenType = $this->_tokens[$i]['code'];

            // Is this an opening condition ?
            if (isset(self::$_scopeOpeners[$tokenType]) === true) {

                $isShared = self::$_scopeOpeners[$tokenType]['shared'];
                if ($currType === $tokenType && $isShared === true) {

                    $closer = $this->_recurseScopeMap($i);

                    foreach (array($stackPtr, $opener, $closer) as $token) {
                        $this->_tokens[$token]['scope_condition'] = $stackPtr;
                        $this->_tokens[$token]['scope_opener']    = $opener;
                        $this->_tokens[$token]['scope_closer']    = $closer;
                    }

                    return $stackPtr;

                } else if ($currType == $tokenType && $isShared === false && $opener === null) {
                    // We haven't yet found our opener, but we have found another scope opener
                    // which is the same type as us, and we don't share openers, so
                    // we will never find one.
                    return $stackPtr;
                } else if (isset($this->_tokens[$i]['scope_condition'])) {
                    // We've been here before.
                    $i = $this->_tokens[$i]['scope_closer'];
                } else {
                    $i = $this->_recurseScopeMap($i);
                }
            }//end if start scope

            if ($tokenType === self::$_scopeOpeners[$currType]['start'] && $opener === null) {
                // We found the opening scope token for $currType.
                $opener = $i;
            } else if ($tokenType === self::$_scopeOpeners[$currType]['end'] && $opener !== null) {
                if ($ignore === true) {
                    // The last opening bracket must have been for a string
                    // offset or alike, so let's ignore it.
                    $ignore = false;
                } else {
                    foreach (array($stackPtr, $opener, $i) as $token) {
                        $this->_tokens[$token]['scope_condition'] = $stackPtr;
                        $this->_tokens[$token]['scope_opener']    = $opener;
                        $this->_tokens[$token]['scope_closer']    = $i;
                    }

                    return $i;
                }
            } else if ($tokenType === T_OPEN_PARENTHESIS) {
                if (isset($this->_tokens[$i]['parenthesis_owner']) === true) {

                    $owner = $this->_tokens[$i]['parenthesis_owner'];
                    if (in_array($this->_tokens[$owner]['code'], PHP_CodeSniffer_Tokens::$scopeOpeners) === true) {
                        // If we get into here, then we opened a parenthesis for a scope
                        // for example an if or else if. We can just skip to the closing
                        // parenthesis.
                        $i = $this->_tokens[$i]['parenthesis_closer'];
                        // Update the start of the line so that when we check to see
                        // if the closing parenthesis is more than 3 lines away from the
                        // statement, we check from the closing parenthesis.
                        $startLine = $this->_tokens[$i]['parenthesis_closer'];
                    }
                }
            } else if ($tokenType === T_OPEN_CURLY_BRACKET && $opener !== null) {
                // We opened something that we don't have a scope opener for.
                // Examples of this are curly brackets for string offsets etc.
                // We want to ignore this so that we don't have an invalid scope
                // map.
                $ignore = true;
            } else if ($opener === null && isset(self::$_scopeOpeners[$currType]) === true) {
                // If we still haven't found the opener after 3 lines,
                // we're not going to find it.
                if ($this->_tokens[$i]['line'] >= ($startLine + 3)) {
                    return $stackPtr;
                }
            }
        }//end for

        return $stackPtr;

    }//end _recurseScopeMap()


    /**
     * Constructs the level map.
     *
     * The level map adds a 'level' indice to each token which indicates the
     * depth that a token within a set of scope blocks. It also adds a
     * 'condition' indice which is an array of the scope conditions that opened
     * each of the scopes - position 0 being the first scope opener.
     *
     * @return void
     */
    private function _createLevelMap()
    {
        $numTokens  = count($this->_tokens);
        $level      = 0;
        $conditions = array();
        $lastOpener = null;

        for ($i = 0; $i < $numTokens; $i++) {

            $this->_tokens[$i]['level']      = $level;
            $this->_tokens[$i]['conditions'] = $conditions;

            if (isset($this->_tokens[$i]['scope_condition']) === true) {

                // Check to see if this token opened the scope.
                if ($this->_tokens[$i]['scope_opener'] === $i) {
                    $stackPtr = $this->_tokens[$i]['scope_condition'];

                    // If we find a scope opener that has a shared closer,
                    // then we need to go back over the condition map that we
                    // just created and fix ourselves as we just added some
                    // conditions where there was none. This happens for T_CASE
                    // statements that are using the same break statement.
                    if ($lastOpener !== null && $this->_tokens[$lastOpener]['scope_closer'] === $this->_tokens[$i]['scope_closer']) {

                        for (($x = $this->_tokens[$lastOpener]['scope_opener'] + 1); $x <= $this->_tokens[$i]['scope_opener']; $x++) {
                            $this->_tokens[$x]['level']--;
                            array_pop($this->_tokens[$x]['conditions']);
                        }

                        $lastOpener = $this->_tokens[$i]['scope_opener'];
                        continue;
                    }
                    $conditions[$stackPtr] = $this->_tokens[$stackPtr]['code'];
                    $level++;
                    $lastOpener = $this->_tokens[$i]['scope_opener'];
                } else if ($this->_tokens[$i]['scope_closer'] === $i) {
                    array_pop($conditions);
                    $level--;
                    $this->_tokens[$i]['level']      = $level;
                    $this->_tokens[$i]['conditions'] = $conditions;
                    $lastOpener = $this->_tokens[$i]['scope_opener'];
                }
            }//end if
        }//end for

    }//end _createLevelMap()


    /**
     * Returns the tokens that can end scopes.
     *
     * @return array(int)
     * @see _createScopeMap
     */
    private function _getEndScopeTokens()
    {
        $endScopeTokens = array();
        foreach (self::$_scopeOpeners as $type => $scopeInfo) {
            $endScopeTokens[] = $scopeInfo['end'];
        }

        return array_unique($endScopeTokens);

    }//end _getEndScopeTokens()


    /**
     * Returns the token types that are allowed to open scopes.
     *
     * @return array(int)
     */
    public static function getValidScopeOpeners()
    {
        return array_keys(self::$_scopeOpeners);

    }//end getValidScopeOpeners()


    /**
     * Returns the declaration names for T_CLASS, T_INTERFACE and T_FUNCTION tokens.
     *
     * @param int $stackPtr The position of the declaration token which
     *                      declared the class, interface or function.
     *
     * @return string The name of the class, interface or function.
     * @throws PHP_CodeSniffer_Exception If the specified token is not of type
     *                                   T_FUNCTION, T_CLASS or T_INTERFACE.
     */
    public function getDeclarationName($stackPtr)
    {
        $tokenCode = $this->_tokens[$stackPtr]['code'];
        if ($tokenCode !== T_FUNCTION && $tokenCode !== T_CLASS && $tokenCode !== T_INTERFACE) {
            throw new PHP_CodeSniffer_Exception('Token type is not T_FUNCTION, T_CLASS OR T_INTERFACE');
        }

        $token = $this->findNext(T_STRING, $stackPtr);
        return $this->_tokens[$token]['content'];

    }//end getDeclarationName()


    /**
     * Returns the method parameters for the specified T_FUNCTION token.
     *
     * Each parameter is in the following format:
     *
     * <code>
     *   0 => array(
     *         'name'              => '$var',  // The variable name.
     *         'is_array'          => false,   // An array hint was used.
     *         'pass_by_reference' => false,   // Passed by reference.
     *        )
     * </code>
     *
     * Parameters with default values have and additional array indice of
     * 'default' with the value of the default as a string.
     *
     * @param int $stackPtr The position in the stack of the T_FUNCTION token
     *                      to acquire the parameters for.
     *
     * @return array()
     * @throws PHP_CodeSniffer_Exception If the specified $stackPtr is not of
     *                                   type T_FUNCTION.
     */
    public function getMethodParameters($stackPtr)
    {
        if ($this->_tokens[$stackPtr]['code'] !== T_FUNCTION) {
            throw new PHP_CodeSniffer_Exception('$stackPtr must be of type T_FUNCTION');
        }

        $opener = $this->_tokens[$stackPtr]['parenthesis_opener'];
        $closer = $this->_tokens[$stackPtr]['parenthesis_closer'];

        $vars            = array();
        $currVar         = null;
        $defaultStart    = null;
        $paramCount      = 0;
        $isArray         = false;
        $passByReference = false;

        for ($i = ($opener + 1); $i <= $closer; $i++) {

            // Check to see if this token has a parenthesis opener. If it does
            // its likely to be an array, which might have arguments in it, which
            // we cause problems in our parsing below, so lets just skip to the
            // end of it.
            if (isset($this->_tokens[$i]['parenthesis_opener']) === true) {
                // Don't do this if its the close parenthesis for the method.
                if ($i !== $this->_tokens[$i]['parenthesis_closer']) {
                    $i = $this->_tokens[$i]['parenthesis_closer'] + 1;
                }
            }

            switch ($this->_tokens[$i]['code']) {
            case T_BITWISE_AND:
                $passByReference = true;
            break;
            case T_VARIABLE:
                $currVar = $i;
            break;
            case T_ARRAY_HINT:
                $isArray = true;
            break;
            case T_CLOSE_PARENTHESIS:
            case T_COMMA:
                // If it's null, then there must be no parameters for this
                // method.
                if ($currVar === null) {
                    continue;
                }

                $vars[$paramCount]         = array();
                $vars[$paramCount]['name'] = $this->_tokens[$currVar]['content'];

                if ($defaultStart !== null) {
                    $vars[$paramCount]['default'] = $this->getTokensAsString($defaultStart, ($i - $defaultStart));
                }

                $vars[$paramCount]['is_array']          = $isArray;
                $vars[$paramCount]['pass_by_reference'] = $passByReference;

                // Reset the vars, as we are about to process the next parameter.
                $defaultStart    = null;
                $isArray         = false;
                $passByReference = false;

                $paramCount++;
            break;
            case T_EQUAL:
                $defaultStart = ($i + 1);
            break;
            }//end switch
        }//end for

        return $vars;

    }//end getMethodParameters()


    /**
     * Returns the visibility and implementation properies of the method found
     * at the specified position in the stack.
     *
     * The format of the array is:
     *
     * <code>
     *   array(
     *    'scope'           => 'public', // public private or protected
     *    'scope_specified' => true,     // true is scope keyword was found.
     *    'is_abstract'     => false,    // true if the abstract keyword was found.
     *    'is_final'        => false,    // true if the final keyword was found.
     *    'is_static'       => false,    // true if the static keyword was found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_FUNCTION token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If the specified position is not a
     *                                   T_FUNCTION token.
     */
    public function getMethodProperties($stackPtr)
    {
        if ($this->_tokens[$stackPtr]['code'] !== T_FUNCTION) {
            throw new PHP_CodeSniffer_Exception('$stackPtr must be of type T_FUNCTION');
        }

        $valid = array(
                  T_PUBLIC,
                  T_PRIVATE,
                  T_PROTECTED,
                  T_STATIC,
                  T_FINAL,
                  T_ABSTRACT,
                  T_WHITESPACE,
                  T_COMMENT,
                  T_DOC_COMMENT,
                 );

        $scope          = 'public';
        $scopeSpecified = false;
        $isAbstract     = false;
        $isFinal        = false;
        $isStatic       = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {

            if (in_array($this->_tokens[$i]['code'], $valid) === false) {
                break;
            }

            switch ($this->_tokens[$i]['code']) {
            case T_PUBLIC:
                $scope = 'public';
                $scopeSpecified = true;
            break;
            case T_PRIVATE:
                $scope = 'private';
                $scopeSpecified = true;
            break;
            case T_PROTECTED:
                $scope = 'protected';
                $scopeSpecified = true;
            break;
            case T_ABSTRACT:
                $isAbstract = true;
            break;
            case T_FINAL:
                $isFinal = true;
            break;
            case T_STATIC:
                $isStatic = true;
            break;
            }
        }//end for

        return array(
                'scope'           => $scope,
                'scope_specified' => $scopeSpecified,
                'is_abstract'     => $isAbstract,
                'is_final'        => $isFinal,
                'is_static'       => $isStatic,
               );

    }//end getMethodProperties()


    /**
     * Returns the visibility and implementation properies of the class member
     * variable found  at the specified position in the stack.
     *
     * The format of the array is:
     *
     * <code>
     *   array(
     *    'scope'       => 'public', // public private or protected
     *    'is_static'   => false,    // true if the static keyword was found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_VARIABLE token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If the specified position is not a T_VARIABLE
     *                                   token, or if the position is not a class
     *                                   member variable.
     */
    public function getMemberProperties($stackPtr)
    {
        if ($this->_tokens[$stackPtr]['code'] !== T_VARIABLE) {
            throw new PHP_CodeSniffer_Exception('$stackPtr must be of type T_VARIABLE');
        }

        if (count($this->_tokens[$stackPtr]['conditions']) > 1) {
            throw new PHP_CodeSniffer_Exception('$stackPtr is not a class member var');
        }

        $valid = array(
                  T_PUBLIC,
                  T_PRIVATE,
                  T_PROTECTED,
                  T_STATIC,
                  T_WHITESPACE,
                  T_COMMENT,
                  T_DOC_COMMENT,
                 );

        $scope    = 'public';
        $isStatic = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {

            if (in_array($this->_tokens[$i]['code'], $valid) === false) {
                break;
            }

            switch ($this->_tokens[$i]['code']) {
            case T_PUBLIC:
                $scope = 'public';
            break;
            case T_PRIVATE:
                $scope = 'private';
            break;
            case T_PROTECTED:
                $scope = 'protected';
            break;
            case T_STATIC:
                $isStatic = true;
            break;
            }
        }//end for

        return array(
                'scope'     => $scope,
                'is_static' => $isStatic,
               );

    }//end getMemberProperties()


    //-- STACK SEARCHING --//


    /**
     * Determine if the passed token is a reference operator.
     *
     * Returns true if the specified token position represents a reference.
     * Returns false if the token represents a bitwise operator.
     *
     * @param int $stackPtr The position of the T_BITWISE_AND token.
     *
     * @return boolean
     * @throws PHP_CodeSniffer_Exception If the token is not T_BITWISE_AND.
     */
    public function isReference($stackPtr)
    {
        if ($this->_tokens[$stackPtr]['code'] !== T_BITWISE_AND) {
            throw new PHP_CodeSniffer_Exception('$stackPtr must represent a T_BITWISE_AND token');
        }

        $tokenBefore = $this->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr - 1, null, true);

        if ($this->_tokens[$tokenBefore]['code'] === T_AS) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if (in_array($this->_tokens[$tokenBefore]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === true) {
            // This is directly after an assignment. It's a reference. Even if
            // it is part of an operation, the other tests will handle it.
            return true;
        }

        return false;

    }//end isReference()


    /**
     * Returns the content of the tokens from the specified start position in
     * the token stack for the specified legnth.
     *
     * @param int $start  The position to start from in the token stack.
     * @param int $length The length of tokens to traverse from the start pos.
     *
     * @return string The token contents.
     */
    public function getTokensAsString($start, $length)
    {
        $str = '';
        $end = ($start + $length);
        for ($i = $start; $i < $end; $i++) {
            $str .= $this->_tokens[$i]['content'];
        }

        return $str;

    }//end getTokensAsString()


    /**
     * Returns the position of the next specified token(s).
     *
     * If a value is specified, the next token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types   The type(s) of tokens to search for.
     * @param int       $start   The position to start searching from in the
     *                           token stack.
     * @param int       $end     The end position to fail if no token is found.
     *                           if not specified or null, end will default to
     *                           the start of the token stack.
     * @param boolean   $exclude If true, find the next token that are NOT of
     *                           the types specified in $types.
     * @param string    $value   The value that the token(s) must be equal to.
     *                           If value is ommited, tokens with any value will
     *                           be returned.
     *
     * @return int
     * @see findNext()
     */
    public function findPrevious($types, $start, $end=null, $exclude=false, $value=null)
    {
        if (is_array($types) === false) {
            $types = array($types);
        }

        $count = 0;
        if ($end === null || $end > $count) {
            $end = $count;
        }

        for ($i = $start; $i >= $end; $i--) {
            $found = ($exclude === true) ? true : false;
            foreach ($types as $type) {
                if ($exclude === false) {
                    if ($this->_tokens[$i]['code'] === $type) {
                        $found = true;
                        break;
                    }
                } else {
                    if ($this->_tokens[$i]['code'] === $type) {
                        $found = false;
                        break;
                    }
                }
            }

            if ($found === true) {
                if ($value === null) {
                    return $i;
                } else if ($this->_tokens[$i]['content'] === $value) {
                    return $i;
                }
            }
        }//end for

        return false;

    }//end findPrevious()


    /**
     * Returns the position of the next specified token(s).
     *
     * If a value is specified, the next token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types   The type(s) of tokens to search for.
     * @param int       $start   The position to start searching from in the
     *                           token stack.
     * @param int       $end     The end position to fail if no token is found.
     *                           if not specified or null, end will default to
     *                           the end of the token stack.
     * @param boolean   $exclude If true, find the next token that are NOT of
     *                           the types specified in $types.
     * @param string    $value   The value that the token(s) must be equal to.
     *                           If value is ommited, tokens with any value will
     *                           be returned.
     *
     * @return int
     * @see findPrevious()
     */
    public function findNext($types, $start, $end=null, $exclude=false, $value=null)
    {
        if (is_array($types) === false) {
            $types = array($types);
        }

        $count = count($this->_tokens);
        if ($end === null || $end > $count) {
            $end = $count;
        }

        for ($i = $start; $i < $end; $i++) {
            $found = ($exclude === true) ? true : false;
            foreach ($types as $type) {
                if ($exclude === false) {
                    if ($this->_tokens[$i]['code'] === $type) {
                        $found = true;
                        break;
                    }
                } else {
                    if ($this->_tokens[$i]['code'] === $type) {
                        $found = false;
                        break;
                    }
                }
            }

            if ($found === true) {
                if ($value === null) {
                    return $i;
                } else if ($this->_tokens[$i]['content'] === $value) {
                    return $i;
                }
            }
        }//end for

        return false;

    }//end findNext()


    /**
     * Determine if the passed token has a condition of one of the passed types.
     *
     * @param int       $stackPtr The position of the token we are checking.
     * @param int|array $types    The type(s) of tokens to search for.
     *
     * @return boolean
     */
    public function hasCondition($stackPtr, $types)
    {
        // Check for the existence of the token.
        if (isset($this->_tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (isset($this->_tokens[$stackPtr]['conditions']) === false) {
            return false;
        }

        if (is_array($types) === false) {
            $types = array($types);
        }

        $conditions = $this->_tokens[$stackPtr]['conditions'];
        foreach ($types as $type) {
            if (in_array($type, $conditions) === true) {
                // We found a token with the required type.
                return true;
            }
        }

        return false;

    }//end hasCondition()


}//end class

?>
