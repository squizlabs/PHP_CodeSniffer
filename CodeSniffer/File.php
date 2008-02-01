<?php
/**
 * A PHP_CodeSniffer_File object represents a PHP source file and the tokens
 * associated with it.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

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
 * 'nested_parenthesis' which is an array of the
 * left parenthesis => right parenthesis token positions.
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
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_File
{

    /**
     * The absolute path to the file associated with this object.
     *
     * @var string
     */
    private $_file = '';

    /**
     * The EOL character this file uses.
     *
     * @var string
     */
    public $eolChar = '';

    /**
     * The number of tokens in this file.
     *
     * Stored here to save calling count() everywhere.
     *
     * @var int
     */
    public $numTokens = 0;

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
     * An array of sniffs listening to this file's processing.
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
     * opener uses to open and close the scope, if the token strictly requires
     * an opener, if the token can share a scope closer, and who it can be shared
     * with. An example of a token that shares a scope closer is a CASE scope.
     *
     * @var array
     */
    private static $_scopeOpeners = array(
                                     T_IF            => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => false,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_TRY           => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => true,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_CATCH         => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => true,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_ELSE          => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => false,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_ELSEIF        => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => false,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_FOR           => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => false,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_FOREACH       => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => false,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_INTERFACE     => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => true,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_FUNCTION      => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => false,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_CLASS         => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => true,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_WHILE         => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => false,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_DO            => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => true,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_SWITCH        => array(
                                                         'start'  => T_OPEN_CURLY_BRACKET,
                                                         'end'    => T_CLOSE_CURLY_BRACKET,
                                                         'strict' => true,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                     T_CASE          => array(
                                                         'start'  => T_COLON,
                                                         'end'    => T_BREAK,
                                                         'strict' => true,
                                                         'shared' => true,
                                                         'with'   => array(
                                                                      T_DEFAULT,
                                                                      T_CASE,
                                                                     ),
                                                        ),
                                     T_DEFAULT       => array(
                                                         'start'  => T_COLON,
                                                         'end'    => T_BREAK,
                                                         'strict' => true,
                                                         'shared' => true,
                                                         'with'   => array(T_CASE),
                                                        ),
                                     T_START_HEREDOC => array(
                                                         'start'  => T_START_HEREDOC,
                                                         'end'    => T_END_HEREDOC,
                                                         'strict' => true,
                                                         'shared' => false,
                                                         'with'   => array(),
                                                        ),
                                    );

    /**
     * A list of tokens that end the scope.
     *
     * This array is just a unique collection of the end tokens
     * from the _scopeOpeners array. The data is duplicated here to
     * save time during parsing of the file.
     *
     * @var array
     */
    private static $_endScopeTokens = array(
                                       T_CLOSE_CURLY_BRACKET,
                                       T_BREAK,
                                       T_END_HEREDOC,
                                      );


    /**
     * Constructs a PHP_CodeSniffer_File.
     *
     * @param string        $file      The absolute path to the file
     *                                 to process.
     * @param array(string) $listeners The initial listeners listening
     *                                 to processing of this file.
     *
     * @throws PHP_CodeSniffer_Exception If the register() method does
     *                                   not return an array.
     */
    public function __construct($file, array $listeners)
    {
        foreach ($listeners as $listenerClass) {
            $listener = new $listenerClass();
            $tokens   = $listener->register();

            if (is_array($tokens) === false) {
                $msg = "Sniff $listenerClass register() method must return an array";
                throw new PHP_CodeSniffer_Exception($msg);
            }

            $this->addTokenListener($listener, $tokens);
        }

        $this->_file = $file;
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

            if (in_array($listener, $this->_listeners[$token], true) === false) {
                $this->_listeners[$token][] = $listener;
            }
        }

    }//end addTokenListener()


    /**
     * Removes a listener from listening from the specified tokens.
     *
     * @param PHP_CodeSniffer_Sniff $listener The listener to remove from the
     *                                        listener stack.
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
     * Starts the stack traversal and tells listeners when tokens are found.
     *
     * @return void
     */
    public function start()
    {
        if (PHP_CODESNIFFER_VERBOSITY > 2) {
            echo "\t*** START TOKEN PROCESSING ***".PHP_EOL;
        }

        // Foreach of the listeners that have registed to listen for this
        // token, get them to process it.
        foreach ($this->_tokens as $stackPtr => $token) {
            if (PHP_CODESNIFFER_VERBOSITY > 2) {
                $type    = $token['type'];
                $content = str_replace($this->eolChar, '\n', $token['content']);
                echo "\t\tProcess token $stackPtr: $type => $content".PHP_EOL;
            }

            $tokenType = $token['code'];
            if (isset($this->_listeners[$tokenType]) === true) {
                foreach ($this->_listeners[$tokenType] as $listener) {
                    if (PHP_CODESNIFFER_VERBOSITY > 2) {
                        $startTime = microtime(true);
                        echo "\t\t\tProcessing ".get_class($listener).'... ';
                    }

                    $listener->process($this, $stackPtr);

                    if (PHP_CODESNIFFER_VERBOSITY > 2) {
                        $timeTaken = round((microtime(true) - $startTime), 4);
                        echo "DONE in $timeTaken seconds".PHP_EOL;
                    }
                }
            }
        }//end foreach

        if (PHP_CODESNIFFER_VERBOSITY > 2) {
            echo "\t*** END TOKEN PROCESSING ***".PHP_EOL;
        }

        // We don't need the tokens any more, so get rid of them
        // to save some memory.
        $this->_tokens    = null;
        $this->_listeners = null;

    }//end start()


    /**
     * Processes the file and runs the PHP_CodeSniffer sniffs to verify that it
     * conforms with the tests.
     *
     * @return void
     */
    private function _parse()
    {
        $this->eolChar = self::detectLineEndings($this->_file);

        $contents        = file_get_contents($this->_file);
        $this->_tokens   = self::tokenizeString($contents, $this->eolChar);
        $this->numTokens = count($this->_tokens);

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            if ($this->numTokens === 0) {
                $numLines = 0;
            } else {
                $numLines = $this->_tokens[($this->numTokens - 1)]['line'];
            }

            echo "[$this->numTokens tokens in $numLines lines]... ";
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo PHP_EOL;
            }
        }

    }//end _parse()


    /**
     * Opens a file and detects the EOL character being used.
     *
     * @param string $file The full path to the file.
     *
     * @return string
     * @throws PHP_CodeSniffer_Exception If $file could not be opened.
     */
    public static function detectLineEndings($file)
    {
        // Determine the newline character being used in this file.
        // Will be either \r, \r\n or \n.
        $handle = fopen($file, 'r');
        if ($handle === false) {
            $error = 'File could not be opened; could not auto-detect line endings';
            throw new PHP_CodeSniffer_Exception($error);
        }

        $firstLine = fgets($handle);
        fclose($handle);

        $eolChar = substr($firstLine, -1);
        if ($eolChar === "\n") {
            $secondLastChar = substr($firstLine, -2, 1);
            if ($secondLastChar === "\r") {
                $eolChar = "\r\n";
            }
        }

        return $eolChar;

    }//end detectLineEndings()


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
        $column  = $this->_tokens[$stackPtr]['column'];

        if (isset($this->_errors[$lineNum]) === false) {
            $this->errors[$lineNum] = array();
        }

        if (isset($this->_errors[$lineNum][$column]) === false) {
            $this->errors[$lineNum][$column] = array();
        }

        $this->_errors[$lineNum][$column][] = $error;
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
        $column  = $this->_tokens[$stackPtr]['column'];

        if (isset($this->_warnings[$lineNum]) === false) {
            $this->_warnings[$lineNum] = array();
        }

        if (isset($this->_warnings[$lineNum][$column]) === false) {
            $this->_warnings[$lineNum][$column] = array();
        }

        $this->_warnings[$lineNum][$column][] = $warning;
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
     * Creates an array of tokens when given some PHP code.
     *
     * Starts by using token_get_all() but does a lot of extra processing
     * to insert information about the context of the token.
     *
     * @param string $string  The string to tokenize.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return array
     */
    public static function tokenizeString($string, $eolChar='\n')
    {
        $tokens      = @token_get_all($string);
        $finalTokens = array();

        $newStackPtr = 0;
        $numTokens   = count($tokens);
        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token        = $tokens[$stackPtr];
            $tokenIsArray = is_array($token);

            /*
                If we are using \r\n newline characters, the \r and \n are sometimes
                split over two tokens. This normally occurs after comments. We need
                to merge these two characters together so that our line endings are
                consistent for all lines.
            */

            if ($tokenIsArray === true && substr($token[1], -1) === "\r") {
                if (isset($tokens[($stackPtr + 1)]) === true && is_array($tokens[($stackPtr + 1)]) === true && $tokens[($stackPtr + 1)][1][0] === "\n") {
                    $token[1] .= "\n";

                    if ($tokens[($stackPtr + 1)][1] === "\n") {
                        // The next token's content has been merged into this token,
                        // so we can skip it.
                        $stackPtr++;
                    } else {
                        $tokens[($stackPtr + 1)][1] = substr($tokens[($stackPtr + 1)][1], 1);
                    }
                }
            }//end if

            /*
                If this is a double quoted string, PHP will tokenise the whole
                thing which causes problems with the scope map when braces are
                within the string. So we need to merge the tokens together to
                provide a single string.
            */

            if ($tokenIsArray === false && $token === '"') {

                $tokenContent = '"';
                for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                    $subTokenIsArray = is_array($tokens[$i]);

                    if ($subTokenIsArray === true) {
                        $tokenContent .= $tokens[$i][1];
                    } else {
                        $tokenContent .= $tokens[$i];
                    }

                    if ($subTokenIsArray === false && $tokens[$i] === '"') {
                        // We found the other end of the double quoted string.
                        break;
                    }
                }

                $stackPtr = $i;

                // Convert each line within the double quoted string to a
                // new token, so it conforms with other multiple line tokens.
                $tokenLines = explode($eolChar, $tokenContent);
                $numLines   = count($tokenLines);
                $newToken   = array();

                for ($j = 0; $j < $numLines; $j++) {
                    $newToken['content'] = $tokenLines[$j];
                    if ($j === ($numLines - 1)) {
                        if ($tokenLines[$j] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $eolChar;
                    }

                    $newToken['code']          = T_DOUBLE_QUOTED_STRING;
                    $newToken['type']          = 'T_DOUBLE_QUOTED_STRING';
                    $finalTokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                }

                // Continue, as we're done with this token.
                continue;
            }//end if

            /*
                If this is a heredoc, PHP will tokenise the whole
                thing which causes problems when heredocs don't
                contain real PHP code, which is almost never.
                We want to leave the start and end heredoc tokens
                alone though.
            */

            if ($tokenIsArray === true && $token[0] === T_START_HEREDOC) {

                // Add the start heredoc token to the final array.
                $finalTokens[$newStackPtr] = PHP_CodeSniffer::standardiseToken($token);
                $newStackPtr++;

                $tokenContent = '';
                for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                    $subTokenIsArray = is_array($tokens[$i]);
                    if ($subTokenIsArray === true && $tokens[$i][0] === T_END_HEREDOC) {
                        // We found the other end of the heredoc.
                        break;
                    }

                    if ($subTokenIsArray === true) {
                        $tokenContent .= $tokens[$i][1];
                    } else {
                        $tokenContent .= $tokens[$i];
                    }
                }

                $stackPtr = $i;

                // Convert each line within the heredoc to a
                // new token, so it conforms with other multiple line tokens.
                $tokenLines = explode($eolChar, $tokenContent);
                $numLines   = count($tokenLines);
                $newToken   = array();

                for ($j = 0; $j < $numLines; $j++) {
                    $newToken['content'] = $tokenLines[$j];
                    if ($j === ($numLines - 1)) {
                        if ($tokenLines[$j] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $eolChar;
                    }

                    $newToken['code']          = T_HEREDOC;
                    $newToken['type']          = 'T_HEREDOC';
                    $finalTokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                }

                // Add the end heredoc token to the final array.
                $finalTokens[$newStackPtr] = PHP_CodeSniffer::standardiseToken($tokens[$stackPtr]);
                $newStackPtr++;

                // Continue, as we're done with this token.
                continue;
            }//end if

            /*
                If this token has newlines in its content, split each line up
                and create a new token for each line. We do this so it's easier
                to asertain where errors occur on a line.
                Note that $token[1] is the token's content.
            */

            if ($tokenIsArray === true && strpos($token[1], $eolChar) !== false) {
                $tokenLines = explode($eolChar, $token[1]);
                $numLines   = count($tokenLines);
                $tokenName  = token_name($token[0]);

                for ($i = 0; $i < $numLines; $i++) {
                    $newToken['content'] = $tokenLines[$i];
                    if ($i === ($numLines - 1)) {
                        if ($tokenLines[$i] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $eolChar;
                    }

                    $newToken['type']          = $tokenName;
                    $newToken['code']          = $token[0];
                    $finalTokens[$newStackPtr] = $newToken;
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

                $finalTokens[$newStackPtr] = $newToken;
                $newStackPtr++;
            }//end if
        }//end for

        self::_createLineMap($finalTokens, $eolChar);
        self::_createBracketMap($finalTokens, $eolChar);
        self::_createParenthesisMap($finalTokens, $eolChar);
        self::_createParenthesisNestingMap($finalTokens, $eolChar);
        self::_createScopeMap($finalTokens, $eolChar);

        // If we know the width of each tab, convert tabs
        // into spaces so sniffs can use one method of checking.
        if (PHP_CODESNIFFER_TAB_WIDTH > 0) {
            self::_convertTabs($finalTokens, $eolChar);
        }

        // Column map requires the line map to be complete.
        self::_createColumnMap($finalTokens, $eolChar);
        self::_createLevelMap($finalTokens, $eolChar);

        return $finalTokens;

    }//end tokenizeString()


    /**
     * Creates a map of tokens => line numbers for each token.
     *
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createLineMap(&$tokens, $eolChar)
    {
        $lineNumber = 1;
        $count      = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $tokens[$i]['line'] = $lineNumber;
            $lineNumber        += substr_count($tokens[$i]['content'], $eolChar);
        }

    }//end _createLineMap()


    /**
     * Converts tabs into spaces.
     *
     * Each tab can represent between 1 and $width spaces, so
     * this cannot be a straight string replace.
     *
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _convertTabs(&$tokens, $eolChar)
    {
        $currColumn = 1;
        $count      = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $tokenContent = $tokens[$i]['content'];

            if (strpos($tokenContent, "\t") === false) {
                // There are no tabs in this content.
                $currColumn += (strlen($tokenContent) - 1);
            } else {
                // We need to determine the length of each tab.
                $tabs         = preg_split("|(\t)|", $tokenContent, -1, PREG_SPLIT_DELIM_CAPTURE);
                $tabNum       = 0;
                $adjustedTab  = false;
                $tabsToSpaces = array();
                $newContent   = '';

                foreach ($tabs as $content) {
                    if ($content === '') {
                        continue;
                    }

                    if (strpos($content, "\t") === false) {
                        // This piece of content is not a tab.
                        $currColumn += strlen($content);
                        $newContent .= $content;
                    } else {
                        $lastCurrColumn = $currColumn;
                        $tabNum++;

                        // Move the pointer to the next tab stop.
                        if (($currColumn % PHP_CODESNIFFER_TAB_WIDTH) === 0) {
                            // This is the first tab, and we are already at a
                            // tab stop, so this tab counts as a single space.
                            $currColumn++;
                            $adjustedTab = true;
                        } else {
                            $currColumn++;
                            while (($currColumn % PHP_CODESNIFFER_TAB_WIDTH) != 0) {
                                $currColumn++;
                            }

                            $currColumn++;
                        }

                        $newContent .= str_repeat(' ', ($currColumn - $lastCurrColumn));
                    }//end if
                }//end foreach

                if ($tabNum === 1 && $adjustedTab === true) {
                    $currColumn--;
                }

                $tokens[$i]['content'] = $newContent;
            }//end if

            if (isset($tokens[($i + 1)]['line']) === true && $tokens[($i + 1)]['line'] !== $tokens[$i]['line']) {
                $currColumn = 1;
            } else {
                $currColumn++;
            }
        }//end for

    }//end _convertTabs()


    /**
     * Creates a column map.
     *
     * The column map indicates where the token started on the line where it
     * exists.
     *
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createColumnMap(&$tokens, $eolChar)
    {
        $currColumn = 1;
        $count      = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $tokens[$i]['column'] = $currColumn;
            if (isset($tokens[($i + 1)]['line']) === true && $tokens[($i + 1)]['line'] !== $tokens[$i]['line']) {
                $currColumn = 1;
            } else {
                $currColumn += strlen($tokens[$i]['content']);
            }
        }

    }//end _createColumnMap()


    /**
     * Creates a map for opening and closing of square brackets.
     *
     * Each bracket token (T_OPEN_SQUARE_BRACKET and T_CLOSE_SQUARE_BRACKET)
     * has a reference to their opening and closing bracket
     * (bracket_opener and bracket_closer).
     *
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createBracketMap(&$tokens, $eolChar)
    {
        $openers   = array();
        $numTokens = count($tokens);
        $owners    = array();

        for ($i = 0; $i < $numTokens; $i++) {
            if ($tokens[$i]['code'] === T_OPEN_SQUARE_BRACKET) {
                $openers[] = $i;
            } else if ($tokens[$i]['code'] === T_CLOSE_SQUARE_BRACKET) {
                if (empty($openers) === false) {
                    $opener                            = array_pop($openers);
                    $tokens[$i]['bracket_opener']      = $opener;
                    $tokens[$i]['bracket_closer']      = $i;
                    $tokens[$opener]['bracket_opener'] = $opener;
                    $tokens[$opener]['bracket_closer'] = $i;
                }
            }
        }

    }//end _createBracketMap()


    /**
     * Creates a map for opening and closing of parenthesis.
     *
     * Each parenthesis token (T_OPEN_PARENTHESIS and T_CLOSE_PARENTHESIS) has a
     * reference to their opening and closing parenthesis (parenthesis_opener
     * and parenthesis_closer).
     *
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createParenthesisMap(&$tokens, $eolChar)
    {
        $openers   = array();
        $numTokens = count($tokens);
        $openOwner = null;

        for ($i = 0; $i < $numTokens; $i++) {
            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$parenthesisOpeners) === true) {
                $tokens[$i]['parenthesis_opener'] = null;
                $tokens[$i]['parenthesis_closer'] = null;
                $tokens[$i]['parenthesis_owner']  = $i;
                $openOwner                        = $i;
            } else if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $openers[]                        = $i;
                $tokens[$i]['parenthesis_opener'] = $i;
                if ($openOwner !== null) {
                    $tokens[$openOwner]['parenthesis_opener'] = $i;
                    $tokens[$i]['parenthesis_owner']          = $openOwner;
                    $openOwner                                = null;
                }
            } else if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                // Did we set an owner for this set of parenthesis?
                $numOpeners = count($openers);
                if ($numOpeners !== 0) {
                    $opener = array_pop($openers);
                    if (isset($tokens[$opener]['parenthesis_owner']) === true) {
                        $owner = $tokens[$opener]['parenthesis_owner'];

                        $tokens[$owner]['parenthesis_closer'] = $i;
                        $tokens[$i]['parenthesis_owner']      = $owner;
                    }

                    $tokens[$i]['parenthesis_opener']      = $opener;
                    $tokens[$i]['parenthesis_closer']      = $i;
                    $tokens[$opener]['parenthesis_closer'] = $i;
                }
            }//end if
        }//end for

    }//end _createParenthesisMap()


    /**
     * Creates a map for the parenthesis tokens that surround other tokens.
     *
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createParenthesisNestingMap(&$tokens, $eolChar)
    {
        $numTokens = count($tokens);
        $map       = array();
        for ($i = 0; $i < $numTokens; $i++) {
            if (isset($tokens[$i]['parenthesis_opener']) === true && $i === $tokens[$i]['parenthesis_opener']) {
                if (empty($map) === false) {
                    $tokens[$i]['nested_parenthesis'] = $map;
                }

                $map[$tokens[$i]['parenthesis_opener']] = $tokens[$i]['parenthesis_closer'];
            } else if (isset($tokens[$i]['parenthesis_closer']) === true && $i === $tokens[$i]['parenthesis_closer']) {
                array_pop($map);
                if (empty($map) === false) {
                    $tokens[$i]['nested_parenthesis'] = $map;
                }
            } else {
                if (empty($map) === false) {
                    $tokens[$i]['nested_parenthesis'] = $map;
                }
            }
        }

    }//end _createParenthesisNestingMap()


    /**
     * Creates a scope map of tokens that open scopes.
     *
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     * @see _recurseScopeMap()
     */
    private static function _createScopeMap(&$tokens, $eolChar)
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START SCOPE MAP ***".PHP_EOL;
        }

        $numTokens = count($tokens);
        for ($i = 0; $i < $numTokens; $i++) {
            // Check to see if the current token starts a new scope.
            if (isset(self::$_scopeOpeners[$tokens[$i]['code']]) === true) {
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $type    = $tokens[$i]['type'];
                    $content = str_replace($eolChar, '\n', $tokens[$i]['content']);
                    echo "\tStart scope map at $i: $type => $content".PHP_EOL;
                }

                $i = self::_recurseScopeMap($tokens, $numTokens, $eolChar, $i);
            }
        }

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END SCOPE MAP ***".PHP_EOL;
        }

    }//end _createScopeMap()


    /**
     * Recurses though the scope openers to build a scope map.
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param int    $numTokens The size of the tokens array.
     * @param string $eolChar   The EOL character to use for splitting strings.
     * @param int    $stackPtr  The position in the stack of the token that
     *                          opened the scope (eg. an IF token or FOR token).
     * @param int    $depth     How many scope levels down we are.
     *
     * @return int The position in the stack that closed the scope.
     */
    private static function _recurseScopeMap(&$tokens, $numTokens, $eolChar, $stackPtr, $depth=1)
    {
        $opener    = null;
        $currType  = $tokens[$stackPtr]['code'];
        $startLine = $tokens[$stackPtr]['line'];
        $ignore    = false;

        // If the start token for this scope opener is the same as
        // the scope token, we have already found our opener.
        if ($currType === self::$_scopeOpeners[$currType]['start']) {
            $opener = $stackPtr;
        }

        for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
            $tokenType = $tokens[$i]['code'];

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type    = $tokens[$i]['type'];
                $content = str_replace($eolChar, '\n', $tokens[$i]['content']);
                echo str_repeat("\t", $depth);
                echo "Process token $i [";
                if ($opener !== null) {
                    echo "opener:$opener;";
                }

                if ($ignore === true) {
                    echo 'ignore;';
                }

                echo "]: $type => $content".PHP_EOL;
            }

            // Is this an opening condition ?
            if (isset(self::$_scopeOpeners[$tokenType]) === true) {
                if ($opener === null) {
                    // Found another opening condition but still haven't
                    // found our opener, so we are never going to find one.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Couldn't find scope opener for $stackPtr ($type), bailing".PHP_EOL;
                    }

                    return $stackPtr;
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo '* token is an opening condition *'.PHP_EOL;
                }

                $isShared = (self::$_scopeOpeners[$tokenType]['shared'] === true);

                if (isset($tokens[$i]['scope_condition']) === true) {
                    // We've been here before.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* already processed, skipping *'.PHP_EOL;
                    }

                    if ($isShared === false && isset($tokens[$i]['scope_closer']) === true) {
                        $i = $tokens[$i]['scope_closer'];
                    }

                    continue;
                } else if ($currType === $tokenType && $isShared === false && $opener === null) {
                    // We haven't yet found our opener, but we have found another
                    // scope opener which is the same type as us, and we don't
                    // share openers, so we will never find one.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* it was another token\'s opener, bailing *'.PHP_EOL;
                    }

                    return $stackPtr;
                } else {
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* searching for opener *'.PHP_EOL;
                    }

                    $i = self::_recurseScopeMap($tokens, $numTokens, $eolChar, $i, ($depth + 1));
                }//end if
            }//end if start scope

            if ($tokenType === self::$_scopeOpeners[$currType]['start'] && $opener === null) {
                if ($tokenType === T_OPEN_CURLY_BRACKET) {
                    // Make sure this is actually an opener and not a
                    // string offset (e.g., $var{0}).
                    for ($x = ($i - 1); $x > 0; $x--) {
                        if (in_array($tokens[$x]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                            continue;
                        } else {
                            // If the first non-whitespace/comment token is a
                            // variable then this is an opener for a string offset
                            // and not a scope.
                            if ($tokens[$x]['code'] === T_VARIABLE) {
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    echo str_repeat("\t", $depth);
                                    echo '* ignoring curly brace *'.PHP_EOL;
                                }

                                $ignore = true;
                            }//end if

                            break;
                        }//end if
                    }//end for
                }//end if

                if ($ignore === false) {
                    // We found the opening scope token for $currType.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Found scope opener for $stackPtr ($type)".PHP_EOL;
                    }

                    $opener = $i;
                }
            } else if ($tokenType === self::$_scopeOpeners[$currType]['end'] && $opener !== null) {
                if ($ignore === true) {
                    // The last opening bracket must have been for a string
                    // offset or alike, so let's ignore it.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* finished ignoring curly brace *'.PHP_EOL;
                    }

                    $ignore = false;
                } else {
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Found scope closer for $stackPtr ($type)".PHP_EOL;
                    }

                    foreach (array($stackPtr, $opener, $i) as $token) {
                        $tokens[$token]['scope_condition'] = $stackPtr;
                        $tokens[$token]['scope_opener']    = $opener;
                        $tokens[$token]['scope_closer']    = $i;
                    }

                    if (self::$_scopeOpeners[$tokens[$stackPtr]['code']]['shared'] === true) {
                        return $opener;
                    } else {
                        return $i;
                    }
                }//end if
            } else if ($tokenType === T_OPEN_PARENTHESIS) {
                if (isset($tokens[$i]['parenthesis_owner']) === true) {

                    $owner = $tokens[$i]['parenthesis_owner'];
                    if (in_array($tokens[$owner]['code'], PHP_CodeSniffer_Tokens::$scopeOpeners) === true) {
                        // If we get into here, then we opened a parenthesis for
                        // a scope (eg. an if or else if). We can just skip to
                        // the closing parenthesis.
                        $i = $tokens[$i]['parenthesis_closer'];

                        // Update the start of the line so that when we check to see
                        // if the closing parenthesis is more than 3 lines away from
                        // the statement, we check from the closing parenthesis.
                        $startLine = $tokens[$tokens[$i]['parenthesis_closer']]['line'];

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo '* skipping parenthesis *'.PHP_EOL;
                        }
                    }
                }
            } else if ($tokenType === T_OPEN_CURLY_BRACKET && $opener !== null) {
                // We opened something that we don't have a scope opener for.
                // Examples of this are curly brackets for string offsets etc.
                // We want to ignore this so that we don't have an invalid scope
                // map.
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo '* ignoring curly brace *'.PHP_EOL;
                }

                $ignore = true;
            } else if ($opener === null && isset(self::$_scopeOpeners[$currType]) === true) {
                // If we still haven't found the opener after 3 lines,
                // we're not going to find it, unless we know it requires
                // an opener, in which case we better keep looking.
                if ($tokens[$i]['line'] >= ($startLine + 3)) {
                    if (self::$_scopeOpeners[$currType]['strict'] === true) {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type  = $tokens[$stackPtr]['type'];
                            $lines = ($tokens[$i]['line'] - $startLine);
                            echo str_repeat("\t", $depth);
                            echo "=> Still looking for $stackPtr ($type) scope opener after $lines lines".PHP_EOL;
                        }
                    } else {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = $tokens[$stackPtr]['type'];
                            echo str_repeat("\t", $depth);
                            echo "=> Couldn't find scope opener for $stackPtr ($type), bailing".PHP_EOL;
                        }

                        return $stackPtr;
                    }
                }
            } else if ($opener !== null && $tokenType !== T_BREAK && in_array($tokenType, self::$_endScopeTokens) === true) {
                if (isset($tokens[$i]['scope_condition']) === false) {
                    if ($ignore === true) {
                        // We found the end token for the opener we were ignoring.
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo '* finished ignoring curly brace *'.PHP_EOL;
                        }

                        $ignore = false;
                    } else {
                        // We found a token that closes the scope but it doesn't
                        // have a condition, so it belongs to another token and
                        // our token doesn't have a closer, so pretend this is
                        // the closer.
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = $tokens[$stackPtr]['type'];
                            echo str_repeat("\t", $depth);
                            echo "=> Found (unexpected) scope closer for $stackPtr ($type)".PHP_EOL;
                        }

                        foreach (array($stackPtr, $opener) as $token) {
                            $tokens[$token]['scope_condition'] = $stackPtr;
                            $tokens[$token]['scope_opener']    = $opener;
                            $tokens[$token]['scope_closer']    = $i;
                        }

                        return ($i - 1);
                    }//end if
                }//end if
            }//end if
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
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     */
    private static function _createLevelMap(&$tokens, $eolChar)
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START LEVEL MAP ***".PHP_EOL;
        }

        $numTokens  = count($tokens);
        $level      = 0;
        $conditions = array();
        $lastOpener = null;
        $openers    = array();

        for ($i = 0; $i < $numTokens; $i++) {

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type    = $tokens[$i]['type'];
                $line    = $tokens[$i]['line'];
                $content = str_replace($eolChar, '\n', $tokens[$i]['content']);
                echo str_repeat("\t", ($level + 1));
                echo "Process token $i on line $line [lvl:$level;";
                if (empty($conditions) !== true) {
                    $condString = 'conds;';
                    foreach ($conditions as $condition) {
                        $condString .= token_name($condition).',';
                    }

                    echo rtrim($condString, ',').';';
                }

                echo "]: $type => $content".PHP_EOL;
            }

            $tokens[$i]['level']      = $level;
            $tokens[$i]['conditions'] = $conditions;

            if (isset($tokens[$i]['scope_condition']) === true) {

                // Check to see if this token opened the scope.
                if ($tokens[$i]['scope_opener'] === $i) {
                    $stackPtr = $tokens[$i]['scope_condition'];
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", ($level + 1));
                        echo "=> Found scope opener for $stackPtr ($type)".PHP_EOL;
                    }

                    $stackPtr = $tokens[$i]['scope_condition'];

                    // If we find a scope opener that has a shared closer,
                    // then we need to go back over the condition map that we
                    // just created and fix ourselves as we just added some
                    // conditions where there was none. This happens for T_CASE
                    // statements that are using the same break statement.
                    if ($lastOpener !== null && $tokens[$lastOpener]['scope_closer'] === $tokens[$i]['scope_closer']) {
                        // This opener shares its closer with the previous opener,
                        // but we still need to check if the two openers share their
                        // closer with each other directly (like CASE and DEFAULT)
                        // or if they are just sharing because one doesn't have a
                        // closer (like CASE with no BREAK using a SWITCHes closer).
                        $thisType = $tokens[$tokens[$i]['scope_condition']]['code'];
                        $opener   = $tokens[$lastOpener]['scope_condition'];
                        if (in_array($tokens[$opener]['code'], self::$_scopeOpeners[$thisType]['with']) === true) {
                            $badToken = $tokens[$lastOpener]['scope_condition'];
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $type = $tokens[$badToken]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "* shared closer, cleaning up $badToken ($type) *".PHP_EOL;
                            }

                            for ($x = $tokens[$i]['scope_condition']; $x <= $i; $x++) {
                                $oldConditions = $tokens[$x]['conditions'];
                                $oldLevel      = $tokens[$x]['level'];
                                $tokens[$x]['level']--;
                                unset($tokens[$x]['conditions'][$badToken]);
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    $type     = $tokens[$x]['type'];
                                    $oldConds = '';
                                    foreach ($oldConditions as $condition) {
                                        $oldConds .= token_name($condition).',';
                                    }

                                    $oldConds = rtrim($oldConds, ',');

                                    $newConds = '';
                                    foreach ($tokens[$x]['conditions'] as $condition) {
                                        $newConds .= token_name($condition).',';
                                    }

                                    $newConds = rtrim($newConds, ',');

                                    $newLevel = $tokens[$x]['level'];
                                    echo str_repeat("\t", ($level + 1));
                                    echo "* cleaned $x ($type) *".PHP_EOL;
                                    echo str_repeat("\t", ($level + 2));
                                    echo "=> level changed from $oldLevel to $newLevel".PHP_EOL;
                                    echo str_repeat("\t", ($level + 2));
                                    echo "=> conditions changed from $oldConds to $newConds".PHP_EOL;
                                }//end if
                            }//end for

                            unset($conditions[$badToken]);
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $type = $tokens[$badToken]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "* token $badToken ($type) removed from conditions array *".PHP_EOL;
                            }

                            unset ($openers[$lastOpener]);

                            $level--;
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                echo str_repeat("\t", ($level + 2));
                                echo '* level decreased *'.PHP_EOL;
                            }
                        }//end if
                    }//end if

                    $level++;
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", ($level + 1));
                        echo '* level increased *'.PHP_EOL;
                    }

                    $conditions[$stackPtr] = $tokens[$stackPtr]['code'];
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", ($level + 1));
                        echo "* token $stackPtr ($type) added to conditions array *".PHP_EOL;
                    }

                    $lastOpener = $tokens[$i]['scope_opener'];
                    if ($lastOpener !== null) {
                        $openers[$lastOpener] = $lastOpener;
                    }

                } else if ($tokens[$i]['scope_closer'] === $i) {
                    $removedCondition = false;
                    foreach (array_reverse($openers) as $opener) {
                        if ($tokens[$opener]['scope_closer'] === $i) {
                            $oldOpener = array_pop($openers);
                            if (empty($openers) === false) {
                                $lastOpener           = array_pop($openers);
                                $openers[$lastOpener] = $lastOpener;
                            } else {
                                $lastOpener = null;
                            }

                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $type = $tokens[$oldOpener]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "=> Found scope closer for $oldOpener ($type)".PHP_EOL;
                            }

                            if ($removedCondition === false) {
                                $oldCondition = array_pop($conditions);
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    echo str_repeat("\t", ($level + 1));
                                    echo '* token '.token_name($oldCondition).' removed from conditions array *'.PHP_EOL;
                                }

                                $level--;
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    echo str_repeat("\t", ($level + 2));
                                    echo '* level decreased *'.PHP_EOL;
                                }
                            }

                            $tokens[$i]['level']      = $level;
                            $tokens[$i]['conditions'] = $conditions;
                        }//end if
                    }//end foreach
                }//end if
            }//end if
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END LEVEL MAP ***".PHP_EOL;
        }

    }//end _createLevelMap()


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
     *         'pass_by_reference' => false,   // Passed by reference.
     *         'type_hint'         => string,  // Type hint for array or custom type
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
        $passByReference = false;
        $typeHint        = '';

        for ($i = ($opener + 1); $i <= $closer; $i++) {

            // Check to see if this token has a parenthesis opener. If it does
            // its likely to be an array, which might have arguments in it, which
            // we cause problems in our parsing below, so lets just skip to the
            // end of it.
            if (isset($this->_tokens[$i]['parenthesis_opener']) === true) {
                // Don't do this if its the close parenthesis for the method.
                if ($i !== $this->_tokens[$i]['parenthesis_closer']) {
                    $i = ($this->_tokens[$i]['parenthesis_closer'] + 1);
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
                $typeHint = $this->_tokens[$i]['content'];
                break;
            case T_STRING:
                // This is a string, so it may be a type hint, but it could
                // also be a constant used as a default value.
                $prevComma = $this->findPrevious(T_COMMA, $i, $opener);
                if ($prevComma !== false) {
                    $nextEquals = $this->findNext(T_EQUAL, $prevComma, $i);
                    if ($nextEquals !== false) {
                        break;
                    }
                }

                $typeHint = $this->_tokens[$i]['content'];
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

                $vars[$paramCount]['pass_by_reference'] = $passByReference;
                $vars[$paramCount]['type_hint']         = $typeHint;

                // Reset the vars, as we are about to process the next parameter.
                $defaultStart    = null;
                $passByReference = false;
                $typeHint        = '';

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
     * Returns the visibility and implementation properies of a method.
     *
     * The format of the array is:
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
                $scope          = 'public';
                $scopeSpecified = true;
                break;
            case T_PRIVATE:
                $scope          = 'private';
                $scopeSpecified = true;
                break;
            case T_PROTECTED:
                $scope          = 'protected';
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
            }//end switch
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
     * @throws PHP_CodeSniffer_Exception If the specified position is not a
     *                                   T_VARIABLE token, or if the position is not
     *                                   a class member variable.
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

        $scope          = 'public';
        $scopeSpecified = false;
        $isStatic       = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {

            if (in_array($this->_tokens[$i]['code'], $valid) === false) {
                break;
            }

            switch ($this->_tokens[$i]['code']) {
            case T_PUBLIC:
                $scope          = 'public';
                $scopeSpecified = true;
                break;
            case T_PRIVATE:
                $scope          = 'private';
                $scopeSpecified = true;
                break;
            case T_PROTECTED:
                $scope          = 'protected';
                $scopeSpecified = true;
                break;
            case T_STATIC:
                $isStatic = true;
                break;
            }
        }//end for

        return array(
                'scope'           => $scope,
                'scope_specified' => $scopeSpecified,
                'is_static'       => $isStatic,
               );

    }//end getMemberProperties()


    /**
     * Determine if the passed token is a reference operator.
     *
     * Returns true if the specified token position represents a reference.
     * Returns false if the token represents a bitwise operator.
     *
     * @param int $stackPtr The position of the T_BITWISE_AND token.
     *
     * @return boolean
     */
    public function isReference($stackPtr)
    {
        if ($this->_tokens[$stackPtr]['code'] !== T_BITWISE_AND) {
            return false;
        }

        $tokenBefore = $this->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        if ($this->_tokens[$tokenBefore]['code'] === T_FUNCTION) {
            // Function returns a reference.
            return true;
        }

        if ($this->_tokens[$tokenBefore]['code'] === T_DOUBLE_ARROW) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if ($this->_tokens[$tokenBefore]['code'] === T_AS) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if (in_array($this->_tokens[$tokenBefore]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === true) {
            // This is directly after an assignment. It's a reference. Even if
            // it is part of an operation, the other tests will handle it.
            return true;
        }

        if (isset($this->_tokens[$stackPtr]['nested_parenthesis']) === true) {
            $brackets    = $this->_tokens[$stackPtr]['nested_parenthesis'];
            $lastBracket = array_pop($brackets);
            if (isset($this->_tokens[$lastBracket]['parenthesis_owner']) === true) {
                $owner = $this->_tokens[$this->_tokens[$lastBracket]['parenthesis_owner']];
                if ($owner['code'] === T_FUNCTION) {
                    // Inside a function declaration, this is a reference.
                    return true;
                }
            }
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
     * @param bool      $exclude If true, find the next token that are NOT of
     *                           the types specified in $types.
     * @param string    $value   The value that the token(s) must be equal to.
     *                           If value is ommited, tokens with any value will
     *                           be returned.
     * @param bool      $local   If true, tokens outside the current statement
     *                           will not be cheked. IE. checking will stop
     *                           at the next semi-colon found.
     *
     * @return int | bool
     * @see findNext()
     */
    public function findPrevious($types, $start, $end=null, $exclude=false, $value=null, $local=false)
    {
        $types = (array) $types;

        if ($end === null) {
            $end = 0;
        }

        for ($i = $start; $i >= $end; $i--) {
            $found = (bool) $exclude;
            foreach ($types as $type) {
                if ($this->_tokens[$i]['code'] === $type) {
                    $found = !$exclude;
                    break;
                }
            }

            if ($found === true) {
                if ($value === null) {
                    return $i;
                } else if ($this->_tokens[$i]['content'] === $value) {
                    return $i;
                }
            }

            if ($local === true && $this->_tokens[$i]['code'] === T_SEMICOLON) {
                break;
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
     * @param bool      $exclude If true, find the next token that is NOT of
     *                           a type specified in $types.
     * @param string    $value   The value that the token(s) must be equal to.
     *                           If value is ommited, tokens with any value will
     *                           be returned.
     * @param bool      $local   If true, tokens outside the current statement
     *                           will not be cheked. IE. checking will stop
     *                           at the next semi-colon found.
     *
     * @return int | bool
     * @see findPrevious()
     */
    public function findNext($types, $start, $end=null, $exclude=false, $value=null, $local=false)
    {
        $types = (array) $types;

        if ($end === null || $end > $this->numTokens) {
            $end = $this->numTokens;
        }

        for ($i = $start; $i < $end; $i++) {
            $found = (bool) $exclude;
            foreach ($types as $type) {
                if ($this->_tokens[$i]['code'] === $type) {
                    $found = !$exclude;
                    break;
                }
            }

            if ($found === true) {
                if ($value === null) {
                    return $i;
                } else if ($this->_tokens[$i]['content'] === $value) {
                    return $i;
                }
            }

            if ($local === true && $this->_tokens[$i]['code'] === T_SEMICOLON) {
                break;
            }
        }//end for

        return false;

    }//end findNext()


    /**
     * Returns the position of the first token on a line, matching given type.
     *
     * Returns false if no token can be found.
     *
     * @param int|array $types   The type(s) of tokens to search for.
     * @param int       $start   The position to start searching from in the
     *                           token stack. The first token matching on
     *                           this line before this token will be returned.
     * @param bool      $exclude If true, find the token that is NOT of
     *                           the types specified in $types.
     * @param string    $value   The value that the token must be equal to.
     *                           If value is ommited, tokens with any value will
     *                           be returned.
     *
     * @return int | bool
     */
    public function findFirstOnLine($types, $start, $exclude=false, $value=null)
    {
        if (is_array($types) === false) {
            $types = array($types);
        }

        $foundToken = false;

        for ($i = $start; $i >= 0; $i--) {
            if ($this->_tokens[$i]['line'] < $this->_tokens[$start]['line']) {
                break;
            }

            $found = $exclude;
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
                    $foundToken = $i;
                } else if ($this->_tokens[$i]['content'] === $value) {
                    $foundToken = $i;
                }
            }

        }//end for

        return $foundToken;

    }//end findFirstOnLine()


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

        $types      = (array) $types;
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
