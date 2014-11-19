<?php
/**
 * Generic_Sniffs_Whitespace_ScopeIndentSniff.
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
 * Generic_Sniffs_Whitespace_ScopeIndentSniff.
 *
 * Checks that control structures are structured correctly, and their content
 * is indented correctly. This sniff will throw errors if tabs are used
 * for indentation rather than spaces.
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
class Generic_Sniffs_WhiteSpace_ScopeIndentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
                                   'PHP',
                                   'JS',
                                  );

    /**
     * The number of spaces code should be indented.
     *
     * @var int
     */
    public $indent = 4;

    /**
     * Does the indent need to be exactly right?
     *
     * If TRUE, indent needs to be exactly $indent spaces. If FALSE,
     * indent needs to be at least $indent spaces (but can be more).
     *
     * @var bool
     */
    public $exact = false;

    /**
     * Should tabs be used for indenting?
     *
     * If TRUE, fixes will be made using tabs instead of spaces.
     * The size of each tab is important, so it should be specified
     * using the --tab-width CLI argument.
     *
     * @var bool
     */
    public $tabIndent = false;

    /**
     * The --tab-width CLI value that is being used.
     *
     * @var int
     */
    private $_tabWidth = null;

    /**
     * List of tokens not needing to be checked for indentation.
     *
     * Useful to allow Sniffs based on this to easily ignore/skip some
     * tokens from verification. For example, inline html sections
     * or php open/close tags can escape from here and have their own
     * rules elsewhere.
     *
     * @var int[]
     */
    public $ignoreIndentationTokens = array();

    /**
     * List of tokens not needing to be checked for indentation.
     *
     * This is a cached copy of the public version of this var, which
     * can be set in a ruleset file, and some core ignored tokens.
     *
     * @var int[]
     */
    private $_ignoreIndentationTokens = array();

    /**
     * Any scope openers that should not cause an indent.
     *
     * @var int[]
     */
    protected $nonIndentingScopes = array();


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
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($this->_tabWidth === null) {
            $cliValues = $phpcsFile->phpcs->cli->getCommandLineValues();
            if (isset($cliValues['tabWidth']) === false || $cliValues['tabWidth'] === 0) {
                // We have no idea how wide tabs are, so assume 4 spaces for fixing.
                // It shouldn't really matter because indent checks elsewhere in the
                // standard should fix things up.
                $this->_tabWidth = 4;
            } else {
                $this->_tabWidth = $cliValues['tabWidth'];
            }
        }

        $currentIndent = 0;
        $lastOpenTag   = $stackPtr;
        $lastCloseTag  = null;
        $openScopes    = array();
        $adjustments   = array();

        $tokens        = $phpcsFile->getTokens();
        $currentIndent = ($tokens[$stackPtr]['column'] - 1);

        if (empty($this->_ignoreIndentationTokens) === true) {
            $this->_ignoreIndentationTokens = array(
                                               T_OPEN_TAG           => true,
                                               T_OPEN_TAG_WITH_ECHO => true,
                                               T_CLOSE_TAG          => true,
                                               T_INLINE_HTML        => true,
                                              );

            foreach ($this->ignoreIndentationTokens as $token) {
                if (is_int($token) === false) {
                    if (defined($token) === false) {
                        continue;
                    }

                    $token = constant($token);
                }

                $this->_ignoreIndentationTokens[$token] = true;
            }
        }//end if

        $this->exact     = (bool) $this->exact;
        $this->tabIndent = (bool) $this->tabIndent;

        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            $checkToken  = null;
            $checkIndent = null;
            $exact       = $this->exact;

            // Detect line changes and figure out where the indent is.
            if ($tokens[$i]['column'] === 1) {
                $trimmed = ltrim($tokens[$i]['content']);
                if ($trimmed === '') {
                    if (isset($tokens[($i + 1)]) === true
                        && $tokens[$i]['line'] === $tokens[($i + 1)]['line']
                    ) {
                        $checkToken  = ($i + 1);
                        $tokenIndent = ($tokens[($i + 1)]['column'] - 1);
                    }
                } else {
                    $checkToken  = $i;
                    $tokenIndent = (strlen($tokens[$i]['content']) - strlen($trimmed));
                }
            }

            // Special case for closing parenthesis, which should just be indented
            // to at least the same level as where they were opened (but can be more).
            if ($checkToken !== null
                && $tokens[$checkToken]['code'] === T_CLOSE_PARENTHESIS
            ) {
                $first       = $phpcsFile->findFirstOnLine(T_WHITESPACE, $tokens[$checkToken]['parenthesis_opener'], true);
                $checkIndent = ($tokens[$first]['column'] - 1);
                if (isset($adjustments[$first]) === true) {
                    $checkIndent += $adjustments[$first];
                }

                // Make sure it is divisable by our expected indent.
                $checkIndent = (int) (ceil($checkIndent / $this->indent) * $this->indent);
                $exact       = false;
            }//end if

            // Adjust lines within scopes while auto-fixing.
            if ($checkToken !== null
                && isset($tokens[$checkToken]['conditions']) === true
                && empty($tokens[$checkToken]['conditions']) === false
                && $exact === false
            ) {
                end($tokens[$checkToken]['conditions']);
                $condition = key($tokens[$checkToken]['conditions']);
                $first     = $phpcsFile->findFirstOnLine(T_WHITESPACE, $condition, true);

                if (isset($adjustments[$first]) === true
                    && (($adjustments[$first] < 0 && $tokenIndent > $currentIndent)
                    || ($adjustments[$first] > 0 && $tokenIndent < $currentIndent))
                ) {
                    $padding = ($tokenIndent + $adjustments[$first]);
                    if ($padding > 0) {
                        if ($this->tabIndent === true) {
                            $numTabs   = floor($padding / $this->_tabWidth);
                            $numSpaces = ($padding - ($numTabs * $this->_tabWidth));
                            $padding   = str_repeat("\t", $numTabs).str_repeat(' ', $numSpaces);
                        } else {
                            $padding = str_repeat(' ', $padding);
                        }
                    } else {
                        $padding = '';
                    }

                    if ($checkToken === $i) {
                        $phpcsFile->fixer->replaceToken($checkToken, $padding.$trimmed);
                    } else {
                        // Easier to just replace the entire indent.
                        $phpcsFile->fixer->replaceToken(($checkToken - 1), $padding);
                    }

                    $adjustments[$checkToken] = $adjustments[$first];
                }//end if
            }//end if

            // Scope closers reset the required indent to the same level as the opening condition.
            if (($checkToken !== null
                && isset($tokens[$checkToken]['scope_condition']) === true
                && isset($tokens[$checkToken]['scope_closer']) === true
                && $tokens[$checkToken]['scope_closer'] === $checkToken)
                || ($checkToken === null
                && isset($tokens[$i]['scope_condition']) === true
                && isset($tokens[$i]['scope_closer']) === true
                && $tokens[$i]['scope_closer'] === $i)
            ) {
                $scopeCloser = $checkToken;
                if ($scopeCloser === null) {
                    $scopeCloser = $i;
                } else {
                    array_pop($openScopes);
                }

                $first         = $phpcsFile->findFirstOnLine(T_WHITESPACE, $tokens[$scopeCloser]['scope_condition'], true);
                $currentIndent = ($tokens[$first]['column'] - 1);
                if (isset($adjustments[$first]) === true) {
                    $currentIndent += $adjustments[$first];
                }

                // Make sure it is divisable by our expected indent.
                $currentIndent = (int) (ceil($currentIndent / $this->indent) * $this->indent);

                // We only check the indent of scope closers if they are
                // curly braces because other constructs tend to have different rules.
                if ($tokens[$scopeCloser]['code'] === T_CLOSE_CURLY_BRACKET) {
                    $exact = true;
                } else {
                    $checkToken = null;
                }
            }//end if

            // Handle scope for JS object notation.
            if ($phpcsFile->tokenizerType === 'JS'
                && (($checkToken !== null
                && $tokens[$checkToken]['code'] === T_CLOSE_CURLY_BRACKET
                && isset($tokens[$checkToken]['scope_condition']) === false
                && isset($tokens[$checkToken]['bracket_closer']) === true
                && $tokens[$checkToken]['bracket_closer'] === $checkToken)
                || ($checkToken === null
                && $tokens[$i]['code'] === T_CLOSE_CURLY_BRACKET
                && isset($tokens[$i]['scope_condition']) === false
                && isset($tokens[$i]['bracket_closer']) === true
                && $tokens[$i]['bracket_closer'] === $i))
            ) {
                $scopeCloser = $checkToken;
                if ($scopeCloser === null) {
                    $scopeCloser = $i;
                } else {
                    array_pop($openScopes);
                }

                if (isset($tokens[$scopeCloser]['nested_parenthesis']) === true) {
                    reset($tokens[$scopeCloser]['nested_parenthesis']);
                    $parens = key($tokens[$scopeCloser]['nested_parenthesis']);
                    $first  = $phpcsFile->findFirstOnLine(T_WHITESPACE, $parens, true);
                } else {
                    $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $tokens[$scopeCloser]['bracket_opener'], true);
                }

                $currentIndent = ($tokens[$first]['column'] - 1);
                if (isset($adjustments[$first]) === true) {
                    $currentIndent += $adjustments[$first];
                }

                if (isset($tokens[$scopeCloser]['nested_parenthesis']) === true) {
                    $first       = $phpcsFile->findFirstOnLine(T_WHITESPACE, $tokens[$scopeCloser]['bracket_opener'], true);
                    $checkIndent = ($tokens[$first]['column'] - 1);
                    if (isset($adjustments[$first]) === true) {
                        $checkIndent += $adjustments[$first];
                    }
                } else {
                    $checkIndent = $currentIndent;
                }

                // Make sure it is divisable by our expected indent.
                $currentIndent = (int) (ceil($currentIndent / $this->indent) * $this->indent);
                $checkIndent   = (int) (ceil($checkIndent / $this->indent) * $this->indent);
                $exact         = true;
            }//end if

            if ($checkToken !== null
                && isset(PHP_CodeSniffer_Tokens::$scopeOpeners[$tokens[$checkToken]['code']]) === true
                && in_array($tokens[$checkToken]['code'], $this->nonIndentingScopes) === false
                && isset($tokens[$checkToken]['scope_opener']) === true
            ) {
                $exact = true;

                $lastOpener = null;
                if (empty($openScopes) === false) {
                    end($openScopes);
                    $lastOpener = current($openScopes);
                }

                // A scope opener that shares a closer with another token (like multiple
                // CASEs using the same BREAK) needs to reduce the indent level so its
                // indent is checked correctly. It will then increase the indent again
                // (as all openers do) after being checked.
                if ($lastOpener !== null
                    && isset($tokens[$lastOpener]['scope_closer']) === true
                    && $tokens[$lastOpener]['level'] === $tokens[$checkToken]['level']
                    && $tokens[$lastOpener]['scope_closer'] === $tokens[$checkToken]['scope_closer']
                ) {
                    $currentIndent -= $this->indent;
                }

                if ($tokens[$checkToken]['code'] === T_CLOSURE
                    && $tokenIndent > $currentIndent
                ) {
                    // The opener is indented more than needed, which is fine.
                    // But just check that it is divisble by our expected indent.
                    $currentIndent = (int) (ceil($tokenIndent / $this->indent) * $this->indent);
                    $exact         = false;
                }
            }//end if

            // JS property indentation has to be exact or else if will break
            // things like function and object indentation.
            if ($checkToken !== null && $tokens[$checkToken]['code'] === T_PROPERTY) {
                $exact = true;
            }

            // Check the line indent.
            if ($checkIndent === null) {
                $checkIndent = $currentIndent;
            }

            $adjusted = false;
            if ($checkToken !== null
                && isset($this->_ignoreIndentationTokens[$tokens[$checkToken]['code']]) === false
                && (($tokenIndent !== $checkIndent && $exact === true)
                || ($tokenIndent < $checkIndent && $exact === false))
            ) {
                $type  = 'IncorrectExact';
                $error = 'Line indented incorrectly; expected ';
                if ($exact === false) {
                    $error .= 'at least ';
                    $type   = 'Incorrect';
                }

                if ($this->tabIndent === true) {
                    $error .= '%s tabs, found %s';
                    $data   = array(
                               floor($checkIndent / $this->_tabWidth),
                               floor($tokenIndent / $this->_tabWidth),
                              );
                } else {
                    $error .= '%s spaces, found %s';
                    $data   = array(
                               $checkIndent,
                               $tokenIndent,
                              );
                }

                $fix = $phpcsFile->addFixableError($error, $checkToken, $type, $data);
                if ($fix === true) {
                    if ($this->tabIndent === true) {
                        $numTabs   = floor($checkIndent / $this->_tabWidth);
                        $numSpaces = ($checkIndent - ($numTabs * $this->_tabWidth));
                        $padding   = str_repeat("\t", $numTabs).str_repeat(' ', $numSpaces);
                    } else {
                        $padding = str_repeat(' ', $checkIndent);
                    }

                    if ($checkToken === $i) {
                        $accepted = $phpcsFile->fixer->replaceToken($checkToken, $padding.$trimmed);
                    } else {
                        // Easier to just replace the entire indent.
                        $accepted = $phpcsFile->fixer->replaceToken(($checkToken - 1), $padding);
                    }

                    if ($accepted === true) {
                        $adjustments[$checkToken] = ($checkIndent - $tokenIndent);
                    }
                }
            }//end if

            if ($checkToken !== null) {
                $i = $checkToken;
            }

            // Completely skip here/now docs as the indent is a part of the
            // content itself.
            if ($tokens[$i]['code'] === T_START_HEREDOC
                || $tokens[$i]['code'] === T_START_NOWDOC
            ) {
                $i = $phpcsFile->findNext(array(T_END_HEREDOC, T_END_NOWDOC), ($i + 1));
                continue;
            }

            // Completely skip multi-line strings as the indent is a part of the
            // content itself.
            if ($tokens[$i]['code'] === T_CONSTANT_ENCAPSED_STRING
                || $tokens[$i]['code'] === T_DOUBLE_QUOTED_STRING
            ) {
                $i = $phpcsFile->findNext($tokens[$i]['code'], ($i + 1), null, true);
                $i--;
                continue;
            }

            // Completely skip doc comments as they tend to have complex
            // indentation rules.
            if ($tokens[$i]['code'] === T_DOC_COMMENT_OPEN_TAG) {
                $i = $tokens[$i]['comment_closer'];
                continue;
            }

            // Open tags reset the indent level.
            if ($tokens[$i]['code'] == T_OPEN_TAG
                || $tokens[$i]['code'] == T_OPEN_TAG_WITH_ECHO
            ) {
                if ($checkToken === null) {
                    $first         = $phpcsFile->findFirstOnLine(T_WHITESPACE, $i, true);
                    $currentIndent = (strlen($tokens[$first]['content']) - strlen(ltrim($tokens[$first]['content'])));
                } else {
                    $currentIndent = ($tokens[$i]['column'] - 1);
                }

                $lastOpenTag = $i;

                if (isset($adjustments[$i]) === true) {
                    $currentIndent += $adjustments[$i];
                }

                // Make sure it is divisable by our expected indent.
                $currentIndent = (int) (ceil($currentIndent / $this->indent) * $this->indent);
                continue;
            }

            // Close tags reset the indent level, unless they are closing a tag
            // opened on the same line.
            if ($tokens[$i]['code'] === T_CLOSE_TAG) {
                if ($tokens[$lastOpenTag]['line'] !== $tokens[$i]['line']) {
                    $currentIndent = ($tokens[$i]['column'] - 1);
                    $lastCloseTag  = $i;
                } else {
                    if ($lastCloseTag === null) {
                        $currentIndent = 0;
                    } else {
                        $currentIndent = ($tokens[$lastCloseTag]['column'] - 1);
                    }
                }

                if (isset($adjustments[$i]) === true) {
                    $currentIndent += $adjustments[$i];
                }

                // Make sure it is divisable by our expected indent.
                $currentIndent = (int) (ceil($currentIndent / $this->indent) * $this->indent);
                continue;
            }

            // Closures set the indent based on their own indent level.
            if ($tokens[$i]['code'] === T_CLOSURE) {
                $first         = $phpcsFile->findFirstOnLine(T_WHITESPACE, $i, true);
                $currentIndent = (($tokens[$first]['column'] - 1) + $this->indent);

                if (isset($adjustments[$first]) === true) {
                    $currentIndent += $adjustments[$first];
                }

                // Make sure it is divisable by our expected indent.
                $currentIndent = (int) (ceil($currentIndent / $this->indent) * $this->indent);

                $i = $tokens[$i]['scope_opener'];
                continue;
            }

            // Scope openers increase the indent level.
            if (isset($tokens[$i]['scope_condition']) === true
                && isset($tokens[$i]['scope_opener']) === true
                && $tokens[$i]['scope_opener'] === $i
            ) {
                $condition = $tokens[$tokens[$i]['scope_condition']]['code'];
                if (isset(PHP_CodeSniffer_Tokens::$scopeOpeners[$condition]) === true
                    && in_array($condition, $this->nonIndentingScopes) === false
                ) {
                    $currentIndent += $this->indent;
                    $openScopes[]   = $tokens[$i]['scope_condition'];
                    continue;
                }
            }

            // JS objects set the indent level.
            if ($phpcsFile->tokenizerType === 'JS'
                && $tokens[$i]['code'] === T_OPEN_CURLY_BRACKET
                && isset($tokens[$i]['scope_condition']) === false
                && isset($tokens[$i]['bracket_opener']) === true
                && $tokens[$i]['bracket_opener'] === $i
            ) {
                $first         = $phpcsFile->findFirstOnLine(T_WHITESPACE, $i, true);
                $currentIndent = (($tokens[$first]['column'] - 1) + $this->indent);

                // Make sure it is divisable by our expected indent.
                $currentIndent = (int) (ceil($currentIndent / $this->indent) * $this->indent);
                $openScopes[]  = $i;
                continue;
            }

            // Closing a closure.
            if (isset($tokens[$i]['scope_condition']) === true
                && $tokens[$i]['scope_closer'] === $i
                && $tokens[$tokens[$i]['scope_condition']]['code'] === T_CLOSURE
            ) {
                $prev = false;
                if (isset($tokens[$i]['nested_parenthesis']) === true) {
                    end($tokens[$i]['nested_parenthesis']);
                    $parens = key($tokens[$i]['nested_parenthesis']);

                    $condition = 0;
                    if (isset($tokens[$i]['conditions']) === true) {
                        end($tokens[$i]['conditions']);
                        $condition = key($tokens[$i]['conditions']);
                    }

                    if ($condition < $parens) {
                        $prev = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, ($parens - 1), null, true);
                    }
                }

                if ($prev === false) {
                    $prev = $phpcsFile->findPrevious(array(T_EQUAL, T_RETURN), ($tokens[$i]['scope_condition'] - 1));
                    if ($prev === false) {
                        $prev = $i;
                    }
                }

                $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $prev, true);
                if ($tokens[$first]['code'] === T_OBJECT_OPERATOR) {
                    // This is not the start of the statement.
                    $prev = $phpcsFile->findPrevious(T_VARIABLE, $first);
                    if ($prev !== false) {
                        $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $prev, true);
                    }
                }

                $currentIndent = ($tokens[$first]['column'] - 1);

                // Make sure it is divisable by our expected indent.
                $currentIndent = (int) (ceil($currentIndent / $this->indent) * $this->indent);
            }//end if
        }//end for

        // Don't process the rest of the file.
        return ($phpcsFile->numTokens - 1);

    }//end process()


}//end class
