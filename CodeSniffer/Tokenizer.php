<?php
/**
 * The Tokenizer class contains shared code between the different tokenizers.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Benjamin Pearson <bpearson@squiz.com.au>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * The Tokenizer class contains shared code between the different tokenizers.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Benjamin Pearson <bpearson@squiz.com.au>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Tokenizer
{

    /**
     * The tabWidth setting.
     *
     * @var integer
     */
    protected $_tabWidth = 4;

    /**
     * Flag for the verbose setting.
     *
     * @var integer
     */
    protected $_verbose = 0;

    /**
     * A cache of different token types, resolved into arrays.
     *
     * @var array()
     * @see standardiseToken()
     */
    private $_resolveTokenCache = array();


    /**
     * Tokenize a string.
     *
     * @param string $string  The string to tokenize.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return array
     */
    public function tokenizeString($string, $eolChar='\n')
    {
        $tokens = $this->tokenize($string, $eolChar);

        $this->_createLineMap($tokens, $eolChar);
        $this->_createBracketMap($tokens, $eolChar);
        $this->_createParenthesisMap($tokens, $eolChar);
        $this->_createParenthesisNestingMap($tokens, $eolChar);
        $this->_createScopeMap($tokens, $eolChar);

        // If we know the width of each tab, convert tabs
        // into spaces so sniffs can use one method of checking.
        if ($this->_tabWidth > 0) {
            $this->_convertTabs($tokens, $eolChar);
        }

        // Column map requires the line map to be complete.
        $this->_createColumnMap($tokens, $eolChar);
        $this->_createLevelMap($tokens, $eolChar);

        // Allow the tokenizer to do additional processing if required.
        $this->processAdditional($tokens, $eolChar);

        return $tokens;

    }//end tokenizeString()


    /**
     * Creates an array of tokens (children to override).
     *
     * @param string $string  The string to tokenize.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return array
     */
    protected function tokenize($string, $eolChar='\n')
    {
        $finalTokens = array();

        return $finalTokens;

    }//end tokenize()


    /**
     * Performs additional processing after main tokenizing (children to override).
     *
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     */
    protected function processAdditional(&$tokens, $eolChar)
    {

    }//end processAdditional()


    /**
     * Takes a token produced from <code>token_get_all()</code> and produces a
     * more uniform token.
     *
     * Note that this method also resolves T_STRING tokens into more discrete
     * types, therefore there is no need to call resolveTstringToken()
     *
     * @param string|array $token The token to convert.
     *
     * @return array The new token.
     */
    public function standardiseToken($token)
    {
        if (is_array($token) === false) {
            if (isset($this->_resolveTokenCache[$token]) === true) {
                $newToken = $this->_resolveTokenCache[$token];
            } else {
                $newToken = $this->resolveSimpleToken($token);
            }
        } else {
            switch ($token[0]) {
            case T_STRING:
                // Some T_STRING tokens can be more specific.
                $tokenType = strtolower($token[1]);
                if (isset($this->_resolveTokenCache[$tokenType]) === true) {
                    $newToken = $this->_resolveTokenCache[$tokenType];
                } else {
                    $newToken = $this->resolveTstringToken($tokenType);
                }

                break;
            case T_CURLY_OPEN:
                $newToken = array(
                             'code' => T_OPEN_CURLY_BRACKET,
                             'type' => 'T_OPEN_CURLY_BRACKET',
                            );
                break;
            default:
                $newToken = array(
                             'code' => $token[0],
                             'type' => token_name($token[0]),
                            );
                break;
            }//end switch

            $newToken['content'] = $token[1];
        }//end if

        return $newToken;

    }//end standardiseToken()


    /**
     * Converts T_STRING tokens into more usable token names.
     *
     * The token should be produced using the token_get_all() function.
     * Currently, not all T_STRING tokens are converted.
     *
     * @param string $token The T_STRING token to convert as constructed
     *                      by token_get_all().
     *
     * @return array The new token.
     */
    public function resolveTstringToken($token)
    {
        $newToken = array();
        switch ($token) {
        case 'false':
            $newToken['type'] = 'T_FALSE';
            break;
        case 'true':
            $newToken['type'] = 'T_TRUE';
            break;
        case 'null':
            $newToken['type'] = 'T_NULL';
            break;
        case 'self':
            $newToken['type'] = 'T_SELF';
            break;
        case 'parent':
            $newToken['type'] = 'T_PARENT';
            break;
        default:
            $newToken['type'] = 'T_STRING';
            break;
        }

        $newToken['code'] = constant($newToken['type']);

        $this->_resolveTokenCache[$token] = $newToken;
        return $newToken;

    }//end resolveTstringToken()


    /**
     * Converts simple tokens into a format that conforms to complex tokens
     * produced by token_get_all().
     *
     * Simple tokens are tokens that are not in array form when produced from
     * token_get_all().
     *
     * @param string $token The simple token to convert.
     *
     * @return array The new token in array format.
     */
    public function resolveSimpleToken($token)
    {
        $newToken = array();

        switch ($token) {
        case '{':
            $newToken['type'] = 'T_OPEN_CURLY_BRACKET';
            break;
        case '}':
            $newToken['type'] = 'T_CLOSE_CURLY_BRACKET';
            break;
        case '[':
            $newToken['type'] = 'T_OPEN_SQUARE_BRACKET';
            break;
        case ']':
            $newToken['type'] = 'T_CLOSE_SQUARE_BRACKET';
            break;
        case '(':
            $newToken['type'] = 'T_OPEN_PARENTHESIS';
            break;
        case ')':
            $newToken['type'] = 'T_CLOSE_PARENTHESIS';
            break;
        case ':':
            $newToken['type'] = 'T_COLON';
            break;
        case '.':
            $newToken['type'] = 'T_STRING_CONCAT';
            break;
        case '?':
            $newToken['type'] = 'T_INLINE_THEN';
            break;
        case ';':
            $newToken['type'] = 'T_SEMICOLON';
            break;
        case '=':
            $newToken['type'] = 'T_EQUAL';
            break;
        case '*':
            $newToken['type'] = 'T_MULTIPLY';
            break;
        case '/':
            $newToken['type'] = 'T_DIVIDE';
            break;
        case '+':
            $newToken['type'] = 'T_PLUS';
            break;
        case '-':
            $newToken['type'] = 'T_MINUS';
            break;
        case '%':
            $newToken['type'] = 'T_MODULUS';
            break;
        case '^':
            $newToken['type'] = 'T_POWER';
            break;
        case '&':
            $newToken['type'] = 'T_BITWISE_AND';
            break;
        case '|':
            $newToken['type'] = 'T_BITWISE_OR';
            break;
        case '<':
            $newToken['type'] = 'T_LESS_THAN';
            break;
        case '>':
            $newToken['type'] = 'T_GREATER_THAN';
            break;
        case '!':
            $newToken['type'] = 'T_BOOLEAN_NOT';
            break;
        case ',':
            $newToken['type'] = 'T_COMMA';
            break;
        case '@':
            $newToken['type'] = 'T_ASPERAND';
            break;
        case '$':
            $newToken['type'] = 'T_DOLLAR';
            break;
        case '`':
            $newToken['type'] = 'T_BACKTICK';
            break;
        default:
            $newToken['type'] = 'T_NONE';
            break;
        }//end switch

        $newToken['code']    = constant($newToken['type']);
        $newToken['content'] = $token;

        $this->_resolveTokenCache[$token] = $newToken;
        return $newToken;

    }//end resolveSimpleToken()


    /**
     * Returns the tabWidth setting.
     *
     * @return integer
     */
    public function getTabWidth()
    {
        return $this->_tabWidth;

    }//end getTabWidth()


    /**
     * Set the tabWidth setting.
     *
     * @param integer $tabWidth The tabWidth setting.
     *
     * @return void
     */
    public function setTabWidth($tabWidth)
    {
        $this->_tabWidth = (int) $tabWidth;

    }//end setTabWidth()


    /**
     * Returns the verbose setting.
     *
     * @return integer
     */
    public function getVerbose()
    {
        return $this->_verbose;

    }//end getVerbose()


    /**
     * Set the verbose setting.
     *
     * @param integer $verbose Verbose information.
     *
     * @return void
     */
    public function setVerbose($verbose)
    {
        $this->_verbose = (int) $verbose;

    }//end setVerbose()


    /**
     * Creates a map of tokens => line numbers for each token.
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private function _createLineMap(&$tokens, $eolChar)
    {
        $lineNumber = 1;
        $count      = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $tokens[$i]['line'] = $lineNumber;
            if ($tokens[$i]['content'] === '') {
                continue;
            }

            $lineNumber += substr_count($tokens[$i]['content'], $eolChar);
        }

    }//end _createLineMap()


    /**
     * Converts tabs into spaces.
     *
     * Each tab can represent between 1 and $width spaces, so
     * this cannot be a straight string replace.
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private function _convertTabs(&$tokens, $eolChar)
    {
        $currColumn = 1;
        $count      = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $tokenContent = $tokens[$i]['content'];

            if (strpos($tokenContent, "\t") === false) {
                // There are no tabs in this content.
                $currColumn += strlen($tokenContent);
            } else {
                // We need to determine the length of each tab.
                $tabs = preg_split(
                    "|(\t)|",
                    $tokenContent,
                    -1,
                    PREG_SPLIT_DELIM_CAPTURE
                );

                $tabNum       = 0;
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
                        if (($currColumn % $this->_tabWidth) === 0) {
                            // This is the first tab, and we are already at a
                            // tab stop, so this tab counts as a single space.
                            $currColumn++;
                        } else {
                            $currColumn++;
                            while (($currColumn % $this->_tabWidth) != 0) {
                                $currColumn++;
                            }

                            $currColumn++;
                        }

                        $length      = ($currColumn - $lastCurrColumn);
                        $newContent .= str_repeat(' ', $length);
                    }//end if
                }//end foreach

                $tokens[$i]['content'] = $newContent;
            }//end if

            if (isset($tokens[($i + 1)]['line']) === true
                && $tokens[($i + 1)]['line'] !== $tokens[$i]['line']
            ) {
                $currColumn = 1;
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
    private function _createColumnMap(&$tokens, $eolChar)
    {
        $currColumn = 1;
        $count      = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $tokens[$i]['column'] = $currColumn;
            if (isset($tokens[($i + 1)]['line']) === true
                && $tokens[($i + 1)]['line'] !== $tokens[$i]['line']
            ) {
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
     * @param array  &$tokens   The array of tokens to process.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private function _createBracketMap(&$tokens, $eolChar)
    {
        if ($this->getVerbose() > 1) {
            echo "\t*** START BRACKET MAP ***".PHP_EOL;
        }

        $squareOpeners = array();
        $curlyOpeners  = array();
        $numTokens     = count($tokens);

        for ($i = 0; $i < $numTokens; $i++) {
            switch ($tokens[$i]['code']) {
            case T_OPEN_SQUARE_BRACKET:
                $squareOpeners[] = $i;

                if ($this->getVerbose() > 1) {
                    echo str_repeat("\t", count($squareOpeners));
                    echo str_repeat("\t", count($curlyOpeners));
                    echo "=> Found square bracket opener at $i".PHP_EOL;
                }

                break;
            case T_OPEN_CURLY_BRACKET:
                if (isset($tokens[$i]['scope_closer']) === false) {
                    $curlyOpeners[] = $i;

                    if ($this->getVerbose() > 1) {
                        echo str_repeat("\t", count($squareOpeners));
                        echo str_repeat("\t", count($curlyOpeners));
                        echo "=> Found curly bracket opener at $i".PHP_EOL;
                    }
                }
                break;
            case T_CLOSE_SQUARE_BRACKET:
                if (empty($squareOpeners) === false) {
                    $opener                            = array_pop($squareOpeners);
                    $tokens[$i]['bracket_opener']      = $opener;
                    $tokens[$i]['bracket_closer']      = $i;
                    $tokens[$opener]['bracket_opener'] = $opener;
                    $tokens[$opener]['bracket_closer'] = $i;

                    if ($this->getVerbose() > 1) {
                        echo str_repeat("\t", count($squareOpeners));
                        echo str_repeat("\t", count($curlyOpeners));
                        echo "\t=> Found square bracket closer at $i for $opener".PHP_EOL;
                    }
                }
                break;
            case T_CLOSE_CURLY_BRACKET:
                if (empty($curlyOpeners) === false
                    && isset($tokens[$i]['scope_opener']) === false
                ) {
                    $opener                            = array_pop($curlyOpeners);
                    $tokens[$i]['bracket_opener']      = $opener;
                    $tokens[$i]['bracket_closer']      = $i;
                    $tokens[$opener]['bracket_opener'] = $opener;
                    $tokens[$opener]['bracket_closer'] = $i;

                    if ($this->getVerbose() > 1) {
                        echo str_repeat("\t", count($squareOpeners));
                        echo str_repeat("\t", count($curlyOpeners));
                        echo "\t=> Found curly bracket closer at $i for $opener".PHP_EOL;
                    }
                }
                break;
            default:
                continue;
            }//end switch
        }//end for

        if ($this->getVerbose() > 1) {
            echo "\t*** END BRACKET MAP ***".PHP_EOL;
        }

    }//end _createBracketMap()


    /**
     * Creates a map for opening and closing of parenthesis.
     *
     * Each parenthesis token (T_OPEN_PARENTHESIS and T_CLOSE_PARENTHESIS) has a
     * reference to their opening and closing parenthesis (parenthesis_opener
     * and parenthesis_closer).
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private function _createParenthesisMap(&$tokens, $eolChar)
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
     * @param array  &$tokens   The array of tokens to process.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private function _createParenthesisNestingMap(
        &$tokens,
        $eolChar
    ) {
        $numTokens = count($tokens);
        $map       = array();
        for ($i = 0; $i < $numTokens; $i++) {
            if (isset($tokens[$i]['parenthesis_opener']) === true
                && $i === $tokens[$i]['parenthesis_opener']
            ) {
                if (empty($map) === false) {
                    $tokens[$i]['nested_parenthesis'] = $map;
                }

                if (isset($tokens[$i]['parenthesis_closer']) === true) {
                    $map[$tokens[$i]['parenthesis_opener']]
                        = $tokens[$i]['parenthesis_closer'];
                }
            } else if (isset($tokens[$i]['parenthesis_closer']) === true
                && $i === $tokens[$i]['parenthesis_closer']
            ) {
                array_pop($map);
                if (empty($map) === false) {
                    $tokens[$i]['nested_parenthesis'] = $map;
                }
            } else {
                if (empty($map) === false) {
                    $tokens[$i]['nested_parenthesis'] = $map;
                }
            }//end if
        }//end for

    }//end _createParenthesisNestingMap()


    /**
     * Creates a scope map of tokens that open scopes.
     *
     * @param array  &$tokens   The array of tokens to process.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     * @see _recurseScopeMap()
     */
    private function _createScopeMap(&$tokens, $eolChar)
    {
        if ($this->getVerbose() > 1) {
            echo "\t*** START SCOPE MAP ***".PHP_EOL;
        }

        $numTokens = count($tokens);
        for ($i = 0; $i < $numTokens; $i++) {
            // Check to see if the current token starts a new scope.
            if (isset($this->scopeOpeners[$tokens[$i]['code']]) === true) {
                if ($this->getVerbose() > 1) {
                    $type    = $tokens[$i]['type'];
                    $content = str_replace($eolChar, '\n', $tokens[$i]['content']);
                    echo "\tStart scope map at $i: $type => $content".PHP_EOL;
                }

                $i = $this->_recurseScopeMap(
                    $tokens,
                    $numTokens,
                    $eolChar,
                    $i
                );
            }
        }//end for

        if ($this->getVerbose() > 1) {
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
     * @param int    &$ignore   How many curly braces we are ignoring.
     *
     * @return int The position in the stack that closed the scope.
     */
    private function _recurseScopeMap(
        &$tokens,
        $numTokens,
        $eolChar,
        $stackPtr,
        $depth=1,
        &$ignore=0
    ) {
        $opener    = null;
        $currType  = $tokens[$stackPtr]['code'];
        $startLine = $tokens[$stackPtr]['line'];

        // We will need this to restore the value if we end up
        // returning a token ID that causes our calling function to go back
        // over already ignored braces.
        $originalIgnore = $ignore;

        // If the start token for this scope opener is the same as
        // the scope token, we have already found our opener.
        if (in_array($currType, $this->scopeOpeners[$currType]['start']) === true) {
            $opener = $stackPtr;
        }

        for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
            $tokenType = $tokens[$i]['code'];

            if ($this->getVerbose() > 1) {
                $type    = $tokens[$i]['type'];
                $content = str_replace($eolChar, '\n', $tokens[$i]['content']);
                echo str_repeat("\t", $depth);
                echo "Process token $i [";
                if ($opener !== null) {
                    echo "opener:$opener;";
                }

                if ($ignore > 0) {
                    echo "ignore=$ignore;";
                }

                echo "]: $type => $content".PHP_EOL;
            }

            // Very special case for IF statements in PHP that can be defined without
            // scope tokens. If an IF statement below this one has an opener but no
            // keyword, the opener will be incorrectly assigned to this IF statement.
            // E.g., if (1) 1; 1 ? (1 ? 1 : 1) : 1;
            if (($currType === T_IF || $currType === T_ELSE) && $opener === null && $tokens[$i]['code'] === T_SEMICOLON) {
                if ($this->getVerbose() > 1) {
                    $type = $tokens[$stackPtr]['type'];
                    echo str_repeat("\t", $depth);
                    echo "=> Found semicolon before scope opener for $stackPtr ($type), bailing".PHP_EOL;
                }

                return $i;
            }

            if ($opener !== null
                && (isset($tokens[$i]['scope_opener']) === false
                || $this->scopeOpeners[$tokens[$stackPtr]['code']]['shared'] === true)
                && in_array($tokenType, $this->scopeOpeners[$currType]['end']) === true
            ) {
                if ($ignore > 0 && $tokenType === T_CLOSE_CURLY_BRACKET) {
                    // The last opening bracket must have been for a string
                    // offset or alike, so let's ignore it.
                    if ($this->getVerbose() > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* finished ignoring curly brace *'.PHP_EOL;
                    }

                    $ignore--;
                    continue;
                } else if ($tokens[$opener]['code'] === T_OPEN_CURLY_BRACKET
                    && $tokenType !== T_CLOSE_CURLY_BRACKET
                ) {
                    // The opener is a curly bracket so the closer must be a curly bracket as well.
                    // We ignore this closer to handle cases such as T_ELSE or T_ELSEIF being considered
                    // a closer of T_IF when it should not.
                    if ($this->getVerbose() > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Ignoring non-culry scope closer for $stackPtr:$type".PHP_EOL;
                    }
                } else {
                    if ($this->getVerbose() > 1) {
                        $type       = $tokens[$stackPtr]['type'];
                        $closerType = $tokens[$i]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Found scope closer ($i:$closerType) for $stackPtr:$type".PHP_EOL;
                    }

                    foreach (array($stackPtr, $opener, $i) as $token) {
                        $tokens[$token]['scope_condition'] = $stackPtr;
                        $tokens[$token]['scope_opener']    = $opener;
                        $tokens[$token]['scope_closer']    = $i;
                    }

                    if ($this->scopeOpeners[$tokens[$stackPtr]['code']]['shared'] === true) {
                        // As we are going back to where we started originally, restore
                        // the ignore value back to its original value.
                        $ignore = $originalIgnore;
                        return $opener;
                    } else if (isset($this->scopeOpeners[$tokenType]) === true) {
                        // Unset scope_condition here or else the token will appear to have
                        // already been processed, and it will be skipped. Normally we want that,
                        // but in this case, the token is both a closer and an opener, so
                        // it needs to act like an opener. This is also why we return the
                        // token before this one; so the closer has a chance to be processed
                        // a second time, but as an opener.
                        unset($tokens[$i]['scope_condition']);
                        return ($i - 1);
                    } else {
                        return $i;
                    }
                }//end if
            }//end if

            // Is this an opening condition ?
            if (isset($this->scopeOpeners[$tokenType]) === true) {
                if ($opener === null) {
                    // Found another opening condition but still haven't
                    // found our opener, so we are never going to find one.
                    if ($this->getVerbose() > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Couldn't find scope opener for $stackPtr:$type, bailing".PHP_EOL;
                    }

                    return $stackPtr;
                }

                if ($this->getVerbose() > 1) {
                    echo str_repeat("\t", $depth);
                    echo '* token is an opening condition *'.PHP_EOL;
                }

                $isShared = ($this->scopeOpeners[$tokenType]['shared'] === true);

                if (isset($tokens[$i]['scope_condition']) === true) {
                    // We've been here before.
                    if ($this->getVerbose() > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* already processed, skipping *'.PHP_EOL;
                    }

                    if ($isShared === false
                        && isset($tokens[$i]['scope_closer']) === true
                    ) {
                        $i = $tokens[$i]['scope_closer'];
                    }

                    continue;
                } else if ($currType === $tokenType
                    && $isShared === false
                    && $opener === null
                ) {
                    // We haven't yet found our opener, but we have found another
                    // scope opener which is the same type as us, and we don't
                    // share openers, so we will never find one.
                    if ($this->getVerbose() > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* it was another token\'s opener, bailing *'.PHP_EOL;
                    }

                    return $stackPtr;
                } else {
                    if ($this->getVerbose() > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* searching for opener *'.PHP_EOL;
                    }

                    if (in_array(T_CLOSE_CURLY_BRACKET, $this->scopeOpeners[$tokenType]['end']) === true) {
                        $oldIgnore = $ignore;
                        $ignore    = 0;
                    }

                    $i = $this->_recurseScopeMap(
                        $tokens,
                        $numTokens,
                        $eolChar,
                        $i,
                        ($depth + 1),
                        $ignore
                    );

                    if (in_array(T_CLOSE_CURLY_BRACKET, $this->scopeOpeners[$tokenType]['end']) === true) {
                        $ignore = $oldIgnore;
                    }
                }//end if
            }//end if

            if (in_array($tokenType, $this->scopeOpeners[$currType]['start']) === true
                && $opener === null
            ) {
                if ($tokenType === T_OPEN_CURLY_BRACKET) {
                    // Make sure this is actually an opener and not a
                    // string offset (e.g., $var{0}).
                    for ($x = ($i - 1); $x > 0; $x--) {
                        if (in_array($tokens[$x]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                            continue;
                        } else {
                            // If the first non-whitespace/comment token is a
                            // variable or object operator then this is an opener
                            // for a string offset and not a scope.
                            if ($tokens[$x]['code'] === T_VARIABLE
                                || $tokens[$x]['code'] === T_OBJECT_OPERATOR
                            ) {
                                if ($this->getVerbose() > 1) {
                                    echo str_repeat("\t", $depth);
                                    echo '* ignoring curly brace *'.PHP_EOL;
                                }

                                $ignore++;
                            }//end if

                            break;
                        }//end if
                    }//end for
                }//end if

                if ($ignore === 0) {
                    // We found the opening scope token for $currType.
                    if ($this->getVerbose() > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Found scope opener for $stackPtr:$type".PHP_EOL;
                    }

                    $opener = $i;
                }
            } else if ($tokenType === T_OPEN_PARENTHESIS) {
                if (isset($tokens[$i]['parenthesis_owner']) === true) {
                    $owner = $tokens[$i]['parenthesis_owner'];
                    if (in_array($tokens[$owner]['code'], PHP_CodeSniffer_Tokens::$scopeOpeners) === true
                        && isset($tokens[$i]['parenthesis_closer']) === true
                    ) {
                        // If we get into here, then we opened a parenthesis for
                        // a scope (eg. an if or else if). We can just skip to
                        // the closing parenthesis.
                        $i = $tokens[$i]['parenthesis_closer'];

                        // Update the start of the line so that when we check to see
                        // if the closing parenthesis is more than 3 lines away from
                        // the statement, we check from the closing parenthesis.
                        $startLine
                            = $tokens[$tokens[$i]['parenthesis_closer']]['line'];

                        if ($this->getVerbose() > 1) {
                            echo str_repeat("\t", $depth);
                            echo '* skipping parenthesis *'.PHP_EOL;
                        }
                    }
                }//end if
            } else if ($tokenType === T_OPEN_CURLY_BRACKET && $opener !== null) {
                // We opened something that we don't have a scope opener for.
                // Examples of this are curly brackets for string offsets etc.
                // We want to ignore this so that we don't have an invalid scope
                // map.
                if ($this->getVerbose() > 1) {
                    echo str_repeat("\t", $depth);
                    echo '* ignoring curly brace *'.PHP_EOL;
                }

                $ignore++;
            } else if ($opener === null
                && isset($this->scopeOpeners[$currType]) === true
            ) {
                // If we still haven't found the opener after 3 lines,
                // we're not going to find it, unless we know it requires
                // an opener (in which case we better keep looking) or the last
                // token was empty (in which case we'll just confirm there is
                // more code in this file and not just a big comment).
                if ($tokens[$i]['line'] >= ($startLine + 3)
                    && in_array($tokens[($i - 1)]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false
                ) {
                    if ($this->scopeOpeners[$currType]['strict'] === true) {
                        if ($this->getVerbose() > 1) {
                            $type  = $tokens[$stackPtr]['type'];
                            $lines = ($tokens[$i]['line'] - $startLine);
                            echo str_repeat("\t", $depth);
                            echo "=> Still looking for $stackPtr:$type scope opener after $lines lines".PHP_EOL;
                        }
                    } else {
                        if ($this->getVerbose() > 1) {
                            $type = $tokens[$stackPtr]['type'];
                            echo str_repeat("\t", $depth);
                            echo "=> Couldn't find scope opener for $stackPtr:$type, bailing".PHP_EOL;
                        }

                        return $stackPtr;
                    }
                }
            } else if ($opener !== null
                && $tokenType !== T_BREAK
                && in_array($tokenType, $this->endScopeTokens) === true
            ) {
                if (isset($tokens[$i]['scope_condition']) === false) {
                    if ($ignore > 0) {
                        // We found the end token for the opener we were ignoring.
                        if ($this->getVerbose() > 1) {
                            echo str_repeat("\t", $depth);
                            echo '* finished ignoring curly brace *'.PHP_EOL;
                        }

                        $ignore--;
                    } else {
                        // We found a token that closes the scope but it doesn't
                        // have a condition, so it belongs to another token and
                        // our token doesn't have a closer, so pretend this is
                        // the closer.
                        if ($this->getVerbose() > 1) {
                            $type = $tokens[$stackPtr]['type'];
                            echo str_repeat("\t", $depth);
                            echo "=> Found (unexpected) scope closer for $stackPtr:$type".PHP_EOL;
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
     * @param array  &$tokens   The array of tokens to process.
     * @param string $eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private function _createLevelMap(&$tokens, $eolChar)
    {
        if ($this->getVerbose() > 1) {
            echo "\t*** START LEVEL MAP ***".PHP_EOL;
        }

        $numTokens  = count($tokens);
        $level      = 0;
        $conditions = array();
        $lastOpener = null;
        $openers    = array();

        for ($i = 0; $i < $numTokens; $i++) {
            if ($this->getVerbose() > 1) {
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
                    if ($this->getVerbose() > 1) {
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

                        $isShared = in_array(
                            $tokens[$opener]['code'],
                            $this->scopeOpeners[$thisType]['with']
                        );

                        $sameEnd = ($this->scopeOpeners[$thisType]['end'][0] === $this->scopeOpeners[$tokens[$opener]['code']]['end'][0]);
                        if ($isShared === true && $sameEnd === true) {
                            $badToken = $opener;
                            if ($this->getVerbose() > 1) {
                                $type = $tokens[$badToken]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "* shared closer, cleaning up $badToken ($type) *".PHP_EOL;
                            }

                            for ($x = $tokens[$i]['scope_condition']; $x <= $i; $x++) {
                                $oldConditions = $tokens[$x]['conditions'];
                                $oldLevel      = $tokens[$x]['level'];
                                $tokens[$x]['level']--;
                                unset($tokens[$x]['conditions'][$badToken]);
                                if ($this->getVerbose() > 1) {
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
                            if ($this->getVerbose() > 1) {
                                $type = $tokens[$badToken]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "* token $badToken ($type) removed from conditions array *".PHP_EOL;
                            }

                            unset ($openers[$lastOpener]);

                            $level--;
                            if ($this->getVerbose() > 1) {
                                echo str_repeat("\t", ($level + 2));
                                echo '* level decreased *'.PHP_EOL;
                            }
                        }//end if
                    }//end if

                    $level++;
                    if ($this->getVerbose() > 1) {
                        echo str_repeat("\t", ($level + 1));
                        echo '* level increased *'.PHP_EOL;
                    }

                    $conditions[$stackPtr] = $tokens[$stackPtr]['code'];
                    if ($this->getVerbose() > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", ($level + 1));
                        echo "* token $stackPtr ($type) added to conditions array *".PHP_EOL;
                    }

                    $lastOpener = $tokens[$i]['scope_opener'];
                    if ($lastOpener !== null) {
                        $openers[$lastOpener] = $lastOpener;
                    }
                } else if ($lastOpener !== null && $tokens[$lastOpener]['scope_closer'] === $i) {
                    foreach (array_reverse($openers) as $opener) {
                        if ($tokens[$opener]['scope_closer'] === $i) {
                            $oldOpener = array_pop($openers);
                            if (empty($openers) === false) {
                                $lastOpener           = array_pop($openers);
                                $openers[$lastOpener] = $lastOpener;
                            } else {
                                $lastOpener = null;
                            }

                            if ($this->getVerbose() > 1) {
                                $type = $tokens[$oldOpener]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "=> Found scope closer for $oldOpener ($type)".PHP_EOL;
                            }

                            $oldCondition = array_pop($conditions);
                            if ($this->getVerbose() > 1) {
                                echo str_repeat("\t", ($level + 1));
                                echo '* token '.token_name($oldCondition).' removed from conditions array *'.PHP_EOL;
                            }

                            // Make sure this closer actually belongs to us.
                            // Either the condition also has to think this is the
                            // closer, or it has to allow sharing with us.
                            $condition
                                = $tokens[$tokens[$i]['scope_condition']]['code'];
                            if ($condition !== $oldCondition) {
                                if (in_array($condition, $this->scopeOpeners[$oldCondition]['with']) === false) {
                                    $badToken = $tokens[$oldOpener]['scope_condition'];

                                    if ($this->getVerbose() > 1) {
                                        $type = token_name($oldCondition);
                                        echo str_repeat("\t", ($level + 1));
                                        echo "* scope closer was bad, cleaning up $badToken ($type) *".PHP_EOL;
                                    }

                                    for ($x = ($oldOpener + 1); $x <= $i; $x++) {
                                        $oldConditions = $tokens[$x]['conditions'];
                                        $oldLevel      = $tokens[$x]['level'];
                                        $tokens[$x]['level']--;
                                        unset($tokens[$x]['conditions'][$badToken]);
                                        if ($this->getVerbose() > 1) {
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
                                }//end if
                            }//end if

                            $level--;
                            if ($this->getVerbose() > 1) {
                                echo str_repeat("\t", ($level + 2));
                                echo '* level decreased *'.PHP_EOL;
                            }

                            $tokens[$i]['level']      = $level;
                            $tokens[$i]['conditions'] = $conditions;
                        }//end if
                    }//end foreach
                }//end if
            }//end if
        }//end for

        if ($this->getVerbose() > 1) {
            echo "\t*** END LEVEL MAP ***".PHP_EOL;
        }

    }//end _createLevelMap()


}//end class

?>
