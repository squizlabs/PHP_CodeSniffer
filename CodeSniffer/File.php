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
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
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
 * other being its opposite.
 *
 * <code>
 *   array(
 *    'parenthesis_opener' => 34,
 *    'parenthesis_closer' => 40,
 *   );
 * </code>
 *
 * Some tokens can "own" a set of parenthesis. For example a T_FUNCTION token
 * has parenthesis around its argument list. These tokens also have the
 * parenthesis_opener and and parenthesis_closer indices. Not all parenthesis
 * have owners, for example parenthesis used for arithmetic operations and
 * function calls. The parenthesis tokens that have an owner have the following
 * auxiliary array indices.
 *
 * <code>
 *   array(
 *    'parenthesis_opener' => 34,
 *    'parenthesis_closer' => 40,
 *    'parenthesis_owner'  => 33,
 *   );
 * </code>
 *
 * Each token within a set of parenthesis also has an array indice
 * 'nested_parenthesis' which is an array of the
 * left parenthesis => right parenthesis token positions.
 *
 * <code>
 *   'nested_parenthesis' => array(
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
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
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
     * The PHP_CodeSniffer object controlling this run.
     *
     * @var PHP_CodeSniffer
     */
    public $phpcs = null;

    /**
     * The Fixer object to control fixing errors.
     *
     * @var PHP_CodeSniffer_Fixer
     */
    public $fixer = null;

    /**
     * The tokenizer being used for this file.
     *
     * @var object
     */
    public $tokenizer = null;

    /**
     * The tokenizer being used for this file.
     *
     * @var string
     */
    public $tokenizerType = 'PHP';

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
     * The warnings raised from PHP_CodeSniffer_Sniffs.
     *
     * @var array()
     * @see getWarnings()
     */
    private $_warnings = array();

    /**
     * The metrics recorded from PHP_CodeSniffer_Sniffs.
     *
     * @var array()
     * @see getMetrics()
     */
    private $_metrics = array();

    /**
     * Record the errors and warnings raised.
     *
     * @var bool
     */
    private $_recordErrors = true;

    /**
     * And array of lines being ignored by PHP_CodeSniffer.
     *
     * @var array()
     */
    private $_ignoredLines = array();

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
     * The total number of errors/warnings that can be fixed.
     *
     * @var int
     */
    private $_fixableCount = 0;

    /**
     * An array of sniffs listening to this file's processing.
     *
     * @var array(PHP_CodeSniffer_Sniff)
     */
    private $_listeners = array();

    /**
     * The class name of the sniff currently processing the file.
     *
     * @var string
     */
    private $_activeListener = '';

    /**
     * An array of sniffs that are ignoring tokens for the current file.
     *
     * The sniff's process() method can return a token to ignore up to
     * so the sniff will not be run again until that token is reached.
     *
     * @var array()
     */
    private $_listenerIgnoreTo = array();

    /**
     * An array of sniffs being processed and how long they took.
     *
     * @var array()
     */
    private $_listenerTimes = array();

    /**
     * An array of extensions mapping to the tokenizer to use.
     *
     * This value gets set by PHP_CodeSniffer when the object is created.
     *
     * @var array
     */
    protected $tokenizers = array();

    /**
     * An array of rules from the ruleset.xml file.
     *
     * This value gets set by PHP_CodeSniffer when the object is created.
     * It may be empty, indicating that the ruleset does not override
     * any of the default sniff settings.
     *
     * @var array
     */
    protected $ruleset = array();


    /**
     * Constructs a PHP_CodeSniffer_File.
     *
     * @param string          $file       The absolute path to the file to process.
     * @param array(string)   $listeners  The initial listeners listening
     *                                    to processing of this file.
     * @param array           $tokenizers An array of extensions mapping
     *                                    to the tokenizer to use.
     * @param array           $ruleset    An array of rules from the
     *                                    ruleset.xml file.
     * @param PHP_CodeSniffer $phpcs      The PHP_CodeSniffer object controlling
     *                                    this run.
     *
     * @throws PHP_CodeSniffer_Exception If the register() method does
     *                                   not return an array.
     */
    public function __construct(
        $file,
        array $listeners,
        array $tokenizers,
        array $ruleset,
        PHP_CodeSniffer $phpcs
    ) {
        $this->_file      = trim($file);
        $this->_listeners = $listeners;
        $this->tokenizers = $tokenizers;
        $this->ruleset    = $ruleset;
        $this->phpcs      = $phpcs;
        $this->fixer      = new PHP_CodeSniffer_Fixer();

        $cliValues = $phpcs->cli->getCommandLineValues();
        if (isset($cliValues['showSources']) === true
            && $cliValues['showSources'] !== true
        ) {
            $recordErrors = false;
            foreach ($cliValues['reports'] as $report => $output) {
                $reportClass = $phpcs->reporting->factory($report);
                if (property_exists($reportClass, 'recordErrors') === false
                    || $reportClass->recordErrors === true
                ) {
                    $recordErrors = true;
                    break;
                }
            }

            $this->_recordErrors = $recordErrors;
        }

    }//end __construct()


    /**
     * Sets the name of the currently active sniff.
     *
     * @param string $activeListener The class name of the current sniff.
     *
     * @return void
     */
    public function setActiveListener($activeListener)
    {
        $this->_activeListener = $activeListener;

    }//end setActiveListener()


    /**
     * Adds a listener to the token stack that listens to the specific tokens.
     *
     * When PHP_CodeSniffer encounters on the the tokens specified in $tokens,
     * it invokes the process method of the sniff.
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
        $class = get_class($listener);
        foreach ($tokens as $token) {
            if (isset($this->_listeners[$token]) === false) {
                $this->_listeners[$token] = array();
            }

            if (isset($this->_listeners[$token][$class]) === false) {
                $this->_listeners[$token][$class] = $listener;
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
    public function removeTokenListener(
        PHP_CodeSniffer_Sniff $listener,
        array $tokens
    ) {
        $class = get_class($listener);
        foreach ($tokens as $token) {
            if (isset($this->_listeners[$token]) === false) {
                continue;
            }

            unset($this->_listeners[$token][$class]);
        }

    }//end removeTokenListener()


    /**
     * Rebuilds the list of listeners to ensure their state is cleared.
     *
     * @return void
     */
    public function refreshTokenListeners()
    {
        $this->phpcs->populateTokenListeners();
        $this->_listeners        = $this->phpcs->getTokenSniffs();
        $this->_listenerIgnoreTo = null;

    }//end refreshTokenListeners()


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
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return void
     */
    public function start($contents=null)
    {
        $this->_errors       = array();
        $this->_warnings     = array();
        $this->_errorCount   = 0;
        $this->_warningCount = 0;
        $this->_fixableCount = 0;

        try {
            $this->eolChar = self::detectLineEndings($this->_file, $contents);
        } catch (PHP_CodeSniffer_Exception $e) {
            $this->addWarning($e->getMessage(), null, 'Internal.DetectLineEndings');
            return;
        }

        // If this is standard input, see if a filename was passed in as well.
        // This is done by including: phpcs_input_file: [file path]
        // as the first line of content.
        if ($this->_file === 'STDIN' && $contents !== null) {
            if (substr($contents, 0, 17) === 'phpcs_input_file:') {
                $eolPos      = strpos($contents, $this->eolChar);
                $filename    = trim(substr($contents, 17, ($eolPos - 17)));
                $contents    = substr($contents, ($eolPos + strlen($this->eolChar)));
                $this->_file = $filename;
            }
        }

        $this->_parse($contents);
        $this->fixer->startFile($this);

        if (PHP_CODESNIFFER_VERBOSITY > 2) {
            echo "\t*** START TOKEN PROCESSING ***".PHP_EOL;
        }

        $foundCode = false;
        $ignoring  = false;
        $listeners = $this->phpcs->getSniffs();

        // Foreach of the listeners that have registered to listen for this
        // token, get them to process it.
        foreach ($this->_tokens as $stackPtr => $token) {
            // Check for ignored lines.
            if ($token['code'] === T_COMMENT || $token['code'] === T_DOC_COMMENT) {
                if (strpos($token['content'], '@codingStandardsIgnoreStart') !== false) {
                    $ignoring = true;
                } else if ($ignoring === true && strpos($token['content'], '@codingStandardsIgnoreEnd') !== false) {
                    $ignoring = false;
                    // Ignore this comment too.
                    $this->_ignoredLines[$token['line']] = true;
                } else if (strpos($token['content'], '@codingStandardsIgnoreFile') !== false) {
                    // Ignoring the whole file, just a little late.
                    $this->_errors       = array();
                    $this->_warnings     = array();
                    $this->_errorCount   = 0;
                    $this->_warningCount = 0;
                    $this->_fixableCount = 0;
                    return;
                } else if (strpos($token['content'], '@codingStandardsChangeSetting') !== false) {
                    $start         = strpos($token['content'], '@codingStandardsChangeSetting');
                    $comment       = substr($token['content'], $start + 30);
                    $parts         = explode(' ', $comment);
                    $sniffParts    = explode('.', $parts[0]);
                    $listenerClass = $sniffParts[0].'_Sniffs_'.$sniffParts[1].'_'.$sniffParts[2].'Sniff';
                    $this->phpcs->setSniffProperty($listenerClass, $parts[1], $parts[2]);
                }//end if
            }//end if

            if ($ignoring === true) {
                $this->_ignoredLines[$token['line']] = true;
                continue;
            }

            if (PHP_CODESNIFFER_VERBOSITY > 2) {
                $type    = $token['type'];
                $content = str_replace($this->eolChar, '\n', $token['content']);
                if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                    $content = str_replace(' ', "\033[30;1m·\033[0m", $content);
                }

                echo "\t\tProcess token $stackPtr: $type => $content".PHP_EOL;
            }

            if ($token['code'] !== T_INLINE_HTML) {
                $foundCode = true;
            }

            if (isset($this->_listeners[$token['code']]) === false) {
                continue;
            }

            foreach ($this->_listeners[$token['code']] as $listenerData) {
                if (isset($this->_listenerIgnoreTo[$listenerData['class']]) === true
                    && $this->_listenerIgnoreTo[$listenerData['class']] > $stackPtr
                ) {
                    // This sniff is ignoring past this token.
                    continue;
                }

                // Make sure this sniff supports the tokenizer
                // we are currently using.
                $class = $listenerData['class'];

                if (isset($listenerData['tokenizers'][$this->tokenizerType]) === false) {
                    continue;
                }

                // If the file path matches one of our ignore patterns, skip it.
                // While there is support for a type of each pattern
                // (absolute or relative) we don't actually support it here.
                foreach ($listenerData['ignore'] as $pattern) {
                    if (preg_match("|{$pattern}|i", $this->_file) === 1) {
                        continue(2);
                    }
                }

                $this->setActiveListener($class);

                if (PHP_CODESNIFFER_VERBOSITY > 2) {
                    $startTime = microtime(true);
                    echo "\t\t\tProcessing ".$this->_activeListener.'... ';
                }

                $ignoreTo = $listeners[$class]->process($this, $stackPtr);
                if ($ignoreTo !== null) {
                    $this->_listenerIgnoreTo[$this->_activeListener] = $ignoreTo;
                }

                if (PHP_CODESNIFFER_VERBOSITY > 2) {
                    $timeTaken = (microtime(true) - $startTime);
                    if (isset($this->_listenerTimes[$this->_activeListener]) === false) {
                        $this->_listenerTimes[$this->_activeListener] = 0;
                    }

                    $this->_listenerTimes[$this->_activeListener] += $timeTaken;

                    $timeTaken = round(($timeTaken), 4);
                    echo "DONE in $timeTaken seconds".PHP_EOL;
                }

                $this->_activeListener = '';
            }//end foreach
        }//end foreach

        // Remove errors and warnings for ignored lines.
        foreach ($this->_ignoredLines as $line => $ignore) {
            if (isset($this->_errors[$line]) === true) {
                if ($this->_recordErrors === false) {
                    $this->_errorCount -= $this->_errors[$line];
                } else {
                    foreach ($this->_errors[$line] as $col => $errors) {
                        $this->_errorCount -= count($errors);
                    }
                }

                unset($this->_errors[$line]);
            }

            if (isset($this->_warnings[$line]) === true) {
                if ($this->_recordErrors === false) {
                    $this->_errorCount -= $this->_warnings[$line];
                } else {
                    foreach ($this->_warnings[$line] as $col => $warnings) {
                        $this->_warningCount -= count($warnings);
                    }
                }

                unset($this->_warnings[$line]);
            }
        }//end foreach

        if ($this->_recordErrors === false) {
            $this->_errors   = array();
            $this->_warnings = array();
        }

        // If short open tags are off but the file being checked uses
        // short open tags, the whole content will be inline HTML
        // and nothing will be checked. So try and handle this case.
        if ($foundCode === false) {
            $shortTags = (bool) ini_get('short_open_tag');
            if ($shortTags === false) {
                $error = 'No PHP code was found in this file and short open tags are not allowed by this install of PHP. This file may be using short open tags but PHP does not allow them.';
                $this->addWarning($error, null, 'Internal.NoCodeFound');
            }
        }

        if (PHP_CODESNIFFER_VERBOSITY > 2) {
            echo "\t*** END TOKEN PROCESSING ***".PHP_EOL;
            echo "\t*** START SNIFF PROCESSING REPORT ***".PHP_EOL;

            asort($this->_listenerTimes, SORT_NUMERIC);
            $this->_listenerTimes = array_reverse($this->_listenerTimes, true);
            foreach ($this->_listenerTimes as $listener => $timeTaken) {
                echo "\t$listener: ".round(($timeTaken), 4).' secs'.PHP_EOL;
            }

            echo "\t*** END SNIFF PROCESSING REPORT ***".PHP_EOL;
        }

    }//end start()


    /**
     * Remove vars stored in this sniff that are no longer required.
     *
     * @return void
     */
    public function cleanUp()
    {
        $this->_tokens           = null;
        $this->_listeners        = null;
        $this->_listenerIgnoreTo = null;

    }//end cleanUp()


    /**
     * Tokenizes the file and prepares it for the test run.
     *
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return void
     */
    private function _parse($contents=null)
    {
        if ($contents === null && empty($this->_tokens) === false) {
            // File has already been parsed.
            return;
        }

        // Determine the tokenizer from the file extension.
        $fileParts = explode('.', $this->_file);
        $extension = array_pop($fileParts);
        if (isset($this->tokenizers[$extension]) === true) {
            $tokenizerClass      = 'PHP_CodeSniffer_Tokenizers_'.$this->tokenizers[$extension];
            $this->tokenizerType = $this->tokenizers[$extension];
        } else {
            // Revert to default.
            $tokenizerClass = 'PHP_CodeSniffer_Tokenizers_'.$this->tokenizerType;
        }

        $this->tokenizer = new $tokenizerClass();
        $this->tokenizer->setEncoding(PHP_CODESNIFFER_ENCODING);
        $this->tokenizer->setVerbose(PHP_CODESNIFFER_VERBOSITY);
        $this->tokenizer->setTabWidth(PHP_CODESNIFFER_TAB_WIDTH);

        if ($contents === null) {
            $contents = file_get_contents($this->_file);
        }

        $this->_tokens   = self::tokenizeString($contents, $this->tokenizer, $this->eolChar);
        $this->numTokens = count($this->_tokens);

        // Check for mixed line endings as these can cause tokenizer errors and we
        // should let the user know that the results they get may be incorrect.
        // This is done by removing all backslashes, removing the newline char we
        // detected, then converting newlines chars into text. If any backslashes
        // are left at the end, we have additional newline chars in use.
        $contents = str_replace('\\', '', $contents);
        $contents = str_replace($this->eolChar, '', $contents);
        $contents = str_replace("\n", '\n', $contents);
        $contents = str_replace("\r", '\r', $contents);
        if (strpos($contents, '\\') !== false) {
            $error = 'File has mixed line endings; this may cause incorrect results';
            $this->addWarning($error, 0, 'Internal.LineEndings.Mixed');
        }

        if (PHP_CODESNIFFER_VERBOSITY > 0) {
            if ($this->numTokens === 0) {
                $numLines = 0;
            } else {
                $numLines = $this->_tokens[($this->numTokens - 1)]['line'];
            }

            echo "[$this->tokenizerType => $this->numTokens tokens in $numLines lines]... ";
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo PHP_EOL;
            }
        }

    }//end _parse()


    /**
     * Opens a file and detects the EOL character being used.
     *
     * @param string $file     The full path to the file.
     * @param string $contents The contents to parse. If NULL, the content
     *                         is taken from the file system.
     *
     * @return string
     * @throws PHP_CodeSniffer_Exception If $file could not be opened.
     */
    public static function detectLineEndings($file, $contents=null)
    {
        if ($contents === null) {
            // Determine the newline character being used in this file.
            // Will be either \r, \r\n or \n.
            if (is_readable($file) === false) {
                $error = 'Error opening file; file no longer exists or you do not have access to read the file';
                throw new PHP_CodeSniffer_Exception($error);
            } else {
                $handle = fopen($file, 'r');
                if ($handle === false) {
                    $error = 'Error opening file; could not auto-detect line endings';
                    throw new PHP_CodeSniffer_Exception($error);
                }
            }

            $firstLine = fgets($handle);
            fclose($handle);

            $eolChar = substr($firstLine, -1);
            if ($eolChar === "\n") {
                $secondLastChar = substr($firstLine, -2, 1);
                if ($secondLastChar === "\r") {
                    $eolChar = "\r\n";
                }
            } else if ($eolChar !== "\r") {
                // Must not be an EOL char at the end of the line.
                // Probably a one-line file, so assume \n as it really
                // doesn't matter considering there are no newlines.
                $eolChar = "\n";
            }
        } else {
            if (preg_match("/\r\n?|\n/", $contents, $matches) !== 1) {
                // Assuming there are no newlines.
                $eolChar = "\n";
            } else {
                $eolChar = $matches[0];
            }
        }//end if

        return $eolChar;

    }//end detectLineEndings()


    /**
     * Adds an error to the error stack.
     *
     * @param string  $error    The error message.
     * @param int     $stackPtr The stack position where the error occurred.
     * @param string  $code     A violation code unique to the sniff message.
     * @param array   $data     Replacements for the error message.
     * @param int     $severity The severity level for this error. A value of 0
     *                          will be converted into the default severity level.
     * @param boolean $fixable  Can the error be fixed by the sniff?
     *
     * @return boolean
     */
    public function addError(
        $error,
        $stackPtr,
        $code='',
        $data=array(),
        $severity=0,
        $fixable=false
    ) {
        // Don't bother doing any processing if errors are just going to
        // be hidden in the reports anyway.
        if ($this->phpcs->cli->errorSeverity === 0) {
            return false;
        }

        // Work out which sniff generated the error.
        if (substr($code, 0, 9) === 'Internal.') {
            // Any internal message.
            $sniff     = $code;
            $sniffCode = $code;
        } else {
            $parts = explode('_', str_replace('\\', '_', $this->_activeListener));
            if (isset($parts[3]) === true) {
                $sniff = $parts[0].'.'.$parts[2].'.'.$parts[3];

                // Remove "Sniff" from the end.
                $sniff = substr($sniff, 0, -5);
            } else {
                $sniff = 'unknownSniff';
            }

            $sniffCode = $sniff;
            if ($code !== '') {
                $sniffCode .= '.'.$code;
            }
        }//end if

        // Make sure this message type has not been set to "warning".
        if (isset($this->ruleset[$sniffCode]['type']) === true
            && $this->ruleset[$sniffCode]['type'] === 'warning'
        ) {
            // Pass this off to the warning handler.
            return $this->addWarning($error, $stackPtr, $code, $data, $severity);
        }

        // Make sure we are interested in this severity level.
        if (isset($this->ruleset[$sniffCode]['severity']) === true) {
            $severity = $this->ruleset[$sniffCode]['severity'];
        } else if ($severity === 0) {
            $severity = PHPCS_DEFAULT_ERROR_SEV;
        }

        if ($this->phpcs->cli->errorSeverity > $severity) {
            return false;
        }

        // Make sure we are not ignoring this file.
        $patterns = $this->phpcs->getIgnorePatterns($sniffCode);
        foreach ($patterns as $pattern => $type) {
            // While there is support for a type of each pattern
            // (absolute or relative) we don't actually support it here.
            $replacements = array(
                             '\\,' => ',',
                             '*'   => '.*',
                            );

            $pattern = strtr($pattern, $replacements);
            if (preg_match("|{$pattern}|i", $this->_file) === 1) {
                return false;
            }
        }

        if ($stackPtr === null) {
            $lineNum = 1;
            $column  = 1;
        } else {
            $lineNum = $this->_tokens[$stackPtr]['line'];
            $column  = $this->_tokens[$stackPtr]['column'];
        }

        $this->_errorCount++;
        if ($fixable === true) {
            $this->_fixableCount++;
        }

        if ($this->_recordErrors === false) {
            if (isset($this->_errors[$lineNum]) === false) {
                $this->_errors[$lineNum] = 0;
            }

            $this->_errors[$lineNum]++;
            return true;
        }

        // Work out the warning message.
        if (isset($this->ruleset[$sniffCode]['message']) === true) {
            $error = $this->ruleset[$sniffCode]['message'];
        }

        if (empty($data) === true) {
            $message = $error;
        } else {
            $message = vsprintf($error, $data);
        }

        if (isset($this->_errors[$lineNum]) === false) {
            $this->_errors[$lineNum] = array();
        }

        if (isset($this->_errors[$lineNum][$column]) === false) {
            $this->_errors[$lineNum][$column] = array();
        }

        $this->_errors[$lineNum][$column][] = array(
                                               'message'  => $message,
                                               'source'   => $sniffCode,
                                               'severity' => $severity,
                                               'fixable'  => $fixable,
                                              );

        if (PHP_CODESNIFFER_VERBOSITY > 1
            && $this->fixer->enabled === true
            && $fixable === true
        ) {
            @ob_end_clean();
            echo "\tE: $message ($sniffCode)".PHP_EOL;
            ob_start();
        }

        return true;

    }//end addError()


    /**
     * Adds an warning to the warning stack.
     *
     * @param string  $warning  The error message.
     * @param int     $stackPtr The stack position where the error occurred.
     * @param string  $code     A violation code unique to the sniff message.
     * @param array   $data     Replacements for the warning message.
     * @param int     $severity The severity level for this warning. A value of 0
     *                          will be converted into the default severity level.
     * @param boolean $fixable  Can the warning be fixed by the sniff?
     *
     * @return boolean
     */
    public function addWarning(
        $warning,
        $stackPtr,
        $code='',
        $data=array(),
        $severity=0,
        $fixable=false
    ) {
        // Don't bother doing any processing if warnings are just going to
        // be hidden in the reports anyway.
        if ($this->phpcs->cli->warningSeverity === 0) {
            return false;
        }

        // Work out which sniff generated the warning.
        if (substr($code, 0, 9) === 'Internal.') {
            // Any internal message.
            $sniff     = $code;
            $sniffCode = $code;
        } else {
            $parts = explode('_', str_replace('\\', '_', $this->_activeListener));
            if (isset($parts[3]) === true) {
                $sniff = $parts[0].'.'.$parts[2].'.'.$parts[3];

                // Remove "Sniff" from the end.
                $sniff = substr($sniff, 0, -5);
            } else {
                $sniff = 'unknownSniff';
            }

            $sniffCode = $sniff;
            if ($code !== '') {
                $sniffCode .= '.'.$code;
            }
        }//end if

        // Make sure this message type has not been set to "error".
        if (isset($this->ruleset[$sniffCode]['type']) === true
            && $this->ruleset[$sniffCode]['type'] === 'error'
        ) {
            // Pass this off to the error handler.
            return $this->addError($warning, $stackPtr, $code, $data, $severity);
        }

        // Make sure we are interested in this severity level.
        if (isset($this->ruleset[$sniffCode]['severity']) === true) {
            $severity = $this->ruleset[$sniffCode]['severity'];
        } else if ($severity === 0) {
            $severity = PHPCS_DEFAULT_WARN_SEV;
        }

        if ($this->phpcs->cli->warningSeverity > $severity) {
            return false;
        }

        // Make sure we are not ignoring this file.
        $patterns = $this->phpcs->getIgnorePatterns($sniffCode);
        foreach ($patterns as $pattern => $type) {
            // While there is support for a type of each pattern
            // (absolute or relative) we don't actually support it here.
            $replacements = array(
                             '\\,' => ',',
                             '*'   => '.*',
                            );

            $pattern = strtr($pattern, $replacements);
            if (preg_match("|{$pattern}|i", $this->_file) === 1) {
                return false;
            }
        }

        if ($stackPtr === null) {
            $lineNum = 1;
            $column  = 1;
        } else {
            $lineNum = $this->_tokens[$stackPtr]['line'];
            $column  = $this->_tokens[$stackPtr]['column'];
        }

        $this->_warningCount++;
        if ($fixable === true) {
            $this->_fixableCount++;
        }

        if ($this->_recordErrors === false) {
            if (isset($this->_warnings[$lineNum]) === false) {
                $this->_warnings[$lineNum] = 0;
            }

            $this->_warnings[$lineNum]++;
            return true;
        }

        // Work out the warning message.
        if (isset($this->ruleset[$sniffCode]['message']) === true) {
            $warning = $this->ruleset[$sniffCode]['message'];
        }

        if (empty($data) === true) {
            $message = $warning;
        } else {
            $message = vsprintf($warning, $data);
        }

        if (isset($this->_warnings[$lineNum]) === false) {
            $this->_warnings[$lineNum] = array();
        }

        if (isset($this->_warnings[$lineNum][$column]) === false) {
            $this->_warnings[$lineNum][$column] = array();
        }

        $this->_warnings[$lineNum][$column][] = array(
                                                 'message'  => $message,
                                                 'source'   => $sniffCode,
                                                 'severity' => $severity,
                                                 'fixable'  => $fixable,
                                                );

        if (PHP_CODESNIFFER_VERBOSITY > 1
            && $this->fixer->enabled === true
            && $fixable === true
        ) {
            @ob_end_clean();
            echo "\tW: $message ($sniffCode)".PHP_EOL;
            ob_start();
        }

        return true;

    }//end addWarning()


    /**
     * Adds an warning to the warning stack.
     *
     * @param int    $stackPtr The stack position where the metric was recorded.
     * @param string $metric   The name of the metric being recorded.
     * @param string $value    The value of the metric being recorded.
     *
     * @return boolean
     */
    public function recordMetric($stackPtr, $metric, $value)
    {
        if (isset($this->_metrics[$metric]) === false) {
            $this->_metrics[$metric] = array(
                                        'values' => array(
                                                     $value => array($stackPtr),
                                                    ),
                                       );
        } else {
            if (isset($this->_metrics[$metric]['values'][$value]) === false) {
                $this->_metrics[$metric]['values'][$value] = array($stackPtr);
            } else {
                $this->_metrics[$metric]['values'][$value][] = $stackPtr;
            }
        }

        return true;

    }//end recordMetric()


    /**
     * Adds a fixable error to the error stack.
     *
     * @param string $error    The error message.
     * @param int    $stackPtr The stack position where the error occurred.
     * @param string $code     A violation code unique to the sniff message.
     * @param array  $data     Replacements for the error message.
     * @param int    $severity The severity level for this error. A value of 0
     *                         will be converted into the default severity level.
     *
     * @return boolean
     */
    public function addFixableError(
        $error,
        $stackPtr,
        $code='',
        $data=array(),
        $severity=0
    ) {
        return $this->addError($error, $stackPtr, $code, $data, $severity, true);

    }//end addFixableError()


    /**
     * Adds a fixable warning to the warning stack.
     *
     * @param string $warning  The error message.
     * @param int    $stackPtr The stack position where the error occurred.
     * @param string $code     A violation code unique to the sniff message.
     * @param array  $data     Replacements for the warning message.
     * @param int    $severity The severity level for this warning. A value of 0
     *                         will be converted into the default severity level.
     *
     * @return boolean
     */
    public function addFixableWarning(
        $warning,
        $stackPtr,
        $code='',
        $data=array(),
        $severity=0
    ) {
        return $this->addWarning($warning, $stackPtr, $code, $data, $severity, true);

    }//end addFixableWarning()


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
     * Returns the number of successes recorded.
     *
     * @return int
     */
    public function getSuccessCount()
    {
        return $this->_successCount;

    }//end getSuccessCount()


    /**
     * Returns the number of fixable errors/warnings raised.
     *
     * @return int
     */
    public function getFixableCount()
    {
        return $this->_fixableCount;

    }//end getFixableCount()


    /**
     * Returns the list of ignored lines.
     *
     * @return array
     */
    public function getIgnoredLines()
    {
        return $this->_ignoredLines;

    }//end getIgnoredLines()


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
     * Returns the metrics found while processing this file.
     *
     * @return array
     */
    public function getMetrics()
    {
        return $this->_metrics;

    }//end getMetrics()


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
     * @param string $string    The string to tokenize.
     * @param object $tokenizer A tokenizer class to use to tokenize the string.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return array
     */
    public static function tokenizeString($string, $tokenizer, $eolChar='\n')
    {
        $tokens = $tokenizer->tokenizeString($string, $eolChar);

        return $tokens;

    }//end tokenizeString()


    /**
     * Returns the declaration names for T_CLASS, T_INTERFACE and T_FUNCTION tokens.
     *
     * @param int $stackPtr The position of the declaration token which
     *                      declared the class, interface or function.
     *
     * @return string|null The name of the class, interface or function.
     *                     or NULL if the function is a closure.
     * @throws PHP_CodeSniffer_Exception If the specified token is not of type
     *                                   T_FUNCTION, T_CLASS or T_INTERFACE.
     */
    public function getDeclarationName($stackPtr)
    {
        $tokenCode = $this->_tokens[$stackPtr]['code'];
        if ($tokenCode !== T_FUNCTION
            && $tokenCode !== T_CLASS
            && $tokenCode !== T_INTERFACE
            && $tokenCode !== T_TRAIT
        ) {
            throw new PHP_CodeSniffer_Exception('Token type "'.$this->_tokens[$stackPtr]['type'].'" is not T_FUNCTION, T_CLASS, T_INTERFACE or T_TRAIT');
        }

        if ($tokenCode === T_FUNCTION
            && $this->isAnonymousFunction($stackPtr) === true
        ) {
            return null;
        }

        for ($i = $stackPtr; $i < $this->numTokens; $i++) {
            if ($this->_tokens[$i]['code'] === T_STRING) {
                break;
            }
        }

        return $this->_tokens[$i]['content'];

    }//end getDeclarationName()


    /**
     * Check if the token at the specified position is a anonymous function.
     *
     * @param int $stackPtr The position of the declaration token which
     *                      declared the class, interface or function.
     *
     * @return boolean
     * @throws PHP_CodeSniffer_Exception If the specified token is not of type
     *                                   T_FUNCTION
     */
    public function isAnonymousFunction($stackPtr)
    {
        $tokenCode = $this->_tokens[$stackPtr]['code'];
        if ($tokenCode !== T_FUNCTION) {
            throw new PHP_CodeSniffer_Exception('Token type is not T_FUNCTION');
        }

        if (isset($this->_tokens[$stackPtr]['parenthesis_opener']) === false) {
            // Something is not right with this function.
            return false;
        }

        $name = false;
        for ($i = ($stackPtr + 1); $i < $this->numTokens; $i++) {
            if ($this->_tokens[$i]['code'] === T_STRING) {
                $name = $i;
                break;
            }
        }

        if ($name === false) {
            // No name found.
            return true;
        }

        $open = $this->_tokens[$stackPtr]['parenthesis_opener'];
        if ($name > $open) {
            return true;
        }

        return false;

    }//end isAnonymousFunction()


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
                // Don't do this if it's the close parenthesis for the method.
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
            case T_CALLABLE:
                $typeHint = $this->_tokens[$i]['content'];
                break;
            case T_STRING:
                // This is a string, so it may be a type hint, but it could
                // also be a constant used as a default value.
                $prevComma = false;
                for ($t = $i; $t >= $opener; $t--) {
                    if ($this->_tokens[$t]['code'] === T_COMMA) {
                        $prevComma = $t;
                        break;
                    }
                }

                if ($prevComma !== false) {
                    $nextEquals = false;
                    for ($t = $prevComma; $t < $i; $t++) {
                        if ($this->_tokens[$t]['code'] === T_EQUAL) {
                            $nextEquals = $t;
                            break;
                        }
                    }

                    if ($nextEquals !== false) {
                        break;
                    }
                }

                if ($defaultStart === null) {
                    $typeHint .= $this->_tokens[$i]['content'];
                }

                break;
            case T_NS_SEPARATOR:
                // Part of a type hint or default value.
                if ($defaultStart === null) {
                    $typeHint .= $this->_tokens[$i]['content'];
                }

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
                    $vars[$paramCount]['default']
                        = $this->getTokensAsString(
                            $defaultStart,
                            ($i - $defaultStart)
                        );
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
     * Returns the visibility and implementation properties of a method.
     *
     * The format of the array is:
     * <code>
     *   array(
     *    'scope'           => 'public', // public private or protected
     *    'scope_specified' => true,     // true is scope keyword was found.
     *    'is_abstract'     => false,    // true if the abstract keyword was found.
     *    'is_final'        => false,    // true if the final keyword was found.
     *    'is_static'       => false,    // true if the static keyword was found.
     *    'is_closure'      => false,    // true if no name is found.
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
                  T_PUBLIC      => T_PUBLIC,
                  T_PRIVATE     => T_PRIVATE,
                  T_PROTECTED   => T_PROTECTED,
                  T_STATIC      => T_STATIC,
                  T_FINAL       => T_FINAL,
                  T_ABSTRACT    => T_ABSTRACT,
                  T_WHITESPACE  => T_WHITESPACE,
                  T_COMMENT     => T_COMMENT,
                  T_DOC_COMMENT => T_DOC_COMMENT,
                 );

        $scope          = 'public';
        $scopeSpecified = false;
        $isAbstract     = false;
        $isFinal        = false;
        $isStatic       = false;
        $isClosure      = $this->isAnonymousFunction($stackPtr);

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$this->_tokens[$i]['code']]) === false) {
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
                'is_closure'      => $isClosure,
               );

    }//end getMethodProperties()


    /**
     * Returns the visibility and implementation properties of the class member
     * variable found at the specified position in the stack.
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

        $conditions = array_keys($this->_tokens[$stackPtr]['conditions']);
        $ptr        = array_pop($conditions);
        if (isset($this->_tokens[$ptr]) === false
            || $this->_tokens[$ptr]['code'] !== T_CLASS
        ) {
            if (isset($this->_tokens[$ptr]) === true
                && $this->_tokens[$ptr]['code'] === T_INTERFACE
            ) {
                // T_VARIABLEs in interfaces can actually be method arguments
                // but they wont be seen as being inside the method because there
                // are no scope openers and closers for abstract methods. If it is in
                // parentheses, we can be pretty sure it is a method argument.
                if (isset($this->_tokens[$stackPtr]['nested_parenthesis']) === false
                    || empty($this->_tokens[$stackPtr]['nested_parenthesis']) === true
                ) {
                    $error = 'Possible parse error: interfaces may not include member vars';
                    $this->addWarning($error, $stackPtr, 'Internal.ParseError.InterfaceHasMemberVar');
                    return array();
                }
            } else {
                throw new PHP_CodeSniffer_Exception('$stackPtr is not a class member var');
            }
        }

        $valid = array(
                  T_PUBLIC      => T_PUBLIC,
                  T_PRIVATE     => T_PRIVATE,
                  T_PROTECTED   => T_PROTECTED,
                  T_STATIC      => T_STATIC,
                  T_WHITESPACE  => T_WHITESPACE,
                  T_COMMENT     => T_COMMENT,
                  T_DOC_COMMENT => T_DOC_COMMENT,
                  T_VARIABLE    => T_VARIABLE,
                  T_COMMA       => T_COMMA,
                 );

        $scope          = 'public';
        $scopeSpecified = false;
        $isStatic       = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$this->_tokens[$i]['code']]) === false) {
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
     * Returns the visibility and implementation properties of a class.
     *
     * The format of the array is:
     * <code>
     *   array(
     *    'is_abstract' => false, // true if the abstract keyword was found.
     *    'is_final'    => false, // true if the final keyword was found.
     *   );
     * </code>
     *
     * @param int $stackPtr The position in the stack of the T_CLASS token to
     *                      acquire the properties for.
     *
     * @return array
     * @throws PHP_CodeSniffer_Exception If the specified position is not a
     *                                   T_CLASS token.
     */
    public function getClassProperties($stackPtr)
    {
        if ($this->_tokens[$stackPtr]['code'] !== T_CLASS) {
            throw new PHP_CodeSniffer_Exception('$stackPtr must be of type T_CLASS');
        }

        $valid = array(
                  T_FINAL       => T_FINAL,
                  T_ABSTRACT    => T_ABSTRACT,
                  T_WHITESPACE  => T_WHITESPACE,
                  T_COMMENT     => T_COMMENT,
                  T_DOC_COMMENT => T_DOC_COMMENT,
                 );

        $isAbstract = false;
        $isFinal    = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$this->_tokens[$i]['code']]) === false) {
                break;
            }

            switch ($this->_tokens[$i]['code']) {
            case T_ABSTRACT:
                $isAbstract = true;
                break;

            case T_FINAL:
                $isFinal = true;
                break;
            }
        }//end for

        return array(
                'is_abstract' => $isAbstract,
                'is_final'    => $isFinal,
               );

    }//end getClassProperties()


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

        $tokenBefore = $this->findPrevious(
            PHP_CodeSniffer_Tokens::$emptyTokens,
            ($stackPtr - 1),
            null,
            true
        );

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

        if (isset(PHP_CodeSniffer_Tokens::$assignmentTokens[$this->_tokens[$tokenBefore]['code']]) === true) {
            // This is directly after an assignment. It's a reference. Even if
            // it is part of an operation, the other tests will handle it.
            return true;
        }

        if (isset($this->_tokens[$stackPtr]['nested_parenthesis']) === true) {
            $brackets    = $this->_tokens[$stackPtr]['nested_parenthesis'];
            $lastBracket = array_pop($brackets);
            if (isset($this->_tokens[$lastBracket]['parenthesis_owner']) === true) {
                $owner = $this->_tokens[$this->_tokens[$lastBracket]['parenthesis_owner']];
                if ($owner['code'] === T_FUNCTION
                    || $owner['code'] === T_CLOSURE
                    || $owner['code'] === T_ARRAY
                ) {
                    // Inside a function or array declaration, this is a reference.
                    return true;
                }
            } else {
                $prev = false;
                for ($t = ($this->_tokens[$lastBracket]['parenthesis_opener'] - 1); $t >= 0; $t--) {
                    if ($this->_tokens[$t]['code'] !== T_WHITESPACE) {
                        $prev = $t;
                        break;
                    }
                }

                if ($prev !== false && $this->_tokens[$prev]['code'] === T_USE) {
                    return true;
                }
            }//end if
        }//end if

        $tokenAfter = $this->findNext(
            PHP_CodeSniffer_Tokens::$emptyTokens,
            ($stackPtr + 1),
            null,
            true
        );

        if ($this->_tokens[$tokenAfter]['code'] === T_VARIABLE
            && ($this->_tokens[$tokenBefore]['code'] === T_OPEN_PARENTHESIS
            || $this->_tokens[$tokenBefore]['code'] === T_COMMA)
        ) {
            return true;
        }

        return false;

    }//end isReference()


    /**
     * Returns the content of the tokens from the specified start position in
     * the token stack for the specified length.
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
        if ($end > $this->numTokens) {
            $end = $this->numTokens;
        }

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
     *                           If value is omitted, tokens with any value will
     *                           be returned.
     * @param bool      $local   If true, tokens outside the current statement
     *                           will not be checked. IE. checking will stop
     *                           at the next semi-colon found.
     *
     * @return int|bool
     * @see    findNext()
     */
    public function findPrevious(
        $types,
        $start,
        $end=null,
        $exclude=false,
        $value=null,
        $local=false
    ) {
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
     *                           If value is omitted, tokens with any value will
     *                           be returned.
     * @param bool      $local   If true, tokens outside the current statement
     *                           will not be checked. i.e., checking will stop
     *                           at the next semi-colon found.
     *
     * @return int|bool
     * @see    findPrevious()
     */
    public function findNext(
        $types,
        $start,
        $end=null,
        $exclude=false,
        $value=null,
        $local=false
    ) {
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
     *                           If value is omitted, tokens with any value will
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


    /**
     * Return the position of the condition for the passed token.
     *
     * Returns FALSE if the token does not have the condition.
     *
     * @param int $stackPtr The position of the token we are checking.
     * @param int $type     The type of token to search for.
     *
     * @return int
     */
    public function getCondition($stackPtr, $type)
    {
        // Check for the existence of the token.
        if (isset($this->_tokens[$stackPtr]) === false) {
            return false;
        }

        // Make sure the token has conditions.
        if (isset($this->_tokens[$stackPtr]['conditions']) === false) {
            return false;
        }

        $conditions = $this->_tokens[$stackPtr]['conditions'];
        foreach ($conditions as $token => $condition) {
            if ($condition === $type) {
                return $token;
            }
        }

        return false;

    }//end getCondition()


    /**
     * Returns the name of the class that the specified class extends.
     *
     * Returns FALSE on error or if there is no extended class name.
     *
     * @param int $stackPtr The stack position of the class.
     *
     * @return string
     */
    public function findExtendedClassName($stackPtr)
    {
        // Check for the existence of the token.
        if (isset($this->_tokens[$stackPtr]) === false) {
            return false;
        }

        if ($this->_tokens[$stackPtr]['code'] !== T_CLASS) {
            return false;
        }

        if (isset($this->_tokens[$stackPtr]['scope_closer']) === false) {
            return false;
        }

        $classCloserIndex = $this->_tokens[$stackPtr]['scope_closer'];
        $extendsIndex     = $this->findNext(T_EXTENDS, $stackPtr, $classCloserIndex);
        if (false === $extendsIndex) {
            return false;
        }

        $find = array(
                 T_NS_SEPARATOR,
                 T_STRING,
                 T_WHITESPACE,
                );

        $end  = $this->findNext($find, ($extendsIndex + 1), $classCloserIndex, true);
        $name = $this->getTokensAsString(($extendsIndex + 1), ($end - $extendsIndex - 1));
        $name = trim($name);

        if ($name === '') {
            return false;
        }

        return $name;

    }//end findExtendedClassName()


}//end class
