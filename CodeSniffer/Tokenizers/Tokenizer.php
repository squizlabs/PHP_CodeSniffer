<?php
/**
 * Tokenizes PHP code.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Tokenizes PHP code.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
abstract class PHP_CodeSniffer_Tokenizers_Tokenizer
{
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
    public $scopeOpeners;

    /**
     * A list of tokens that end the scope.
     *
     * This array is just a unique collection of the end tokens
     * from the _scopeOpeners array. The data is duplicated here to
     * save time during parsing of the file.
     *
     * @var array
     */
    public $endScopeTokens;

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
    public $tokens;
    
    /**
     *
     * @var string
     */
    public $eolChar;

    /**
     * Return all tokens in an array
     *
     * @param string $string  The string to tokenize.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return type
     */
    public function tokenize($string, $eolChar='\n')
    {
        $this->eolChar = $eolChar;
        
        $this->tokenizeString($string);
        $this->createLineMap();
        $this->createBracketMap();
        $this->createParenthesisMap();
        $this->createParenthesisNestingMap();
        $this->createScopeMap();
        // If we know the width of each tab, convert tabs
        // into spaces so sniffs can use one method of checking.
        if (PHP_CODESNIFFER_TAB_WIDTH > 0) {
            $this->convertTabs();
        }
        // Column map requires the line map to be complete.
        $this->createColumnMap();
        $this->createLevelMap();
        $this->processAdditional();

        return $this->tokens;
    }

    /**
     * Creates an array of tokens when given some PHP code.
     *
     * Starts by using token_get_all() but does a lot of extra processing
     * to insert information about the context of the token.
     *
     * @param string $string The string to tokenize.
     *
     * @return array
     */
    abstract  public function tokenizeString($string);

    /**
     * Performs additional processing after main tokenizing.
     *
     * This additional processing checks for CASE statements that are using curly
     * braces for scope openers and closers. It also turns some T_FUNCTION tokens
     * into T_CLOSURE when they are not standard function definitions. It also
     * detects short array syntax and converts those square brackets into new tokens.
     * It also corrects some usage of the static and class keywords.
     *
     * @return void
     */
    abstract public function processAdditional();

        /**
     * Creates a map of tokens => line numbers for each token.
     *
     * @return void
     */
    protected function createLineMap()
    {
        $lineNumber = 1;
        $count      = count($this->tokens);

        for ($i = 0; $i < $count; $i++) {
            $this->tokens[$i]['line'] = $lineNumber;
            if ($this->tokens[$i]['content'] === '') {
                continue;
            }

            $lineNumber += substr_count($this->tokens[$i]['content'], $this->eolChar);
        }

        
    }//end _createLineMap()

       /**
     * Converts tabs into spaces.
     *
     * Each tab can represent between 1 and $width spaces, so
     * this cannot be a straight string replace.
     *
     * @return void
     */
    protected function convertTabs()
    {
        $currColumn = 1;
        $count      = count($this->tokens);

        for ($i = 0; $i < $count; $i++) {
            $tokenContent = $this->tokens[$i]['content'];

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
                        } else {
                            $currColumn++;
                            while (($currColumn % PHP_CODESNIFFER_TAB_WIDTH) != 0) {
                                $currColumn++;
                            }

                            $currColumn++;
                        }

                        $length      = ($currColumn - $lastCurrColumn);
                        $newContent .= str_repeat(' ', $length);
                    }//end if
                }//end foreach

                $this->tokens[$i]['content'] = $newContent;
            }//end if

            if (isset($this->tokens[($i + 1)]['line']) === true
                && $this->tokens[($i + 1)]['line'] !== $this->tokens[$i]['line']
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
     * @return void
     */
    protected function createColumnMap()
    {
        $currColumn = 1;
        $count      = count($this->tokens);

        for ($i = 0; $i < $count; $i++) {
            $this->tokens[$i]['column'] = $currColumn;
            if (isset($this->tokens[($i + 1)]['line']) === true
                && $this->tokens[($i + 1)]['line'] !== $this->tokens[$i]['line']
            ) {
                $currColumn = 1;
            } else {
                $currColumn += strlen($this->tokens[$i]['content']);
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
     * @return void
     */
    protected function createBracketMap()
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START BRACKET MAP ***".PHP_EOL;
        }

        $squareOpeners = array();
        $curlyOpeners  = array();
        $numTokens     = count($this->tokens);

        for ($i = 0; $i < $numTokens; $i++) {
            switch ($this->tokens[$i]['code']) {
            case T_OPEN_SQUARE_BRACKET:
                $squareOpeners[] = $i;

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", count($squareOpeners));
                    echo str_repeat("\t", count($curlyOpeners));
                    echo "=> Found square bracket opener at $i".PHP_EOL;
                }

                break;
            case T_OPEN_CURLY_BRACKET:
                if (isset($this->tokens[$i]['scope_closer']) === false) {
                    $curlyOpeners[] = $i;

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", count($squareOpeners));
                        echo str_repeat("\t", count($curlyOpeners));
                        echo "=> Found curly bracket opener at $i".PHP_EOL;
                    }
                }
                break;
            case T_CLOSE_SQUARE_BRACKET:
                if (empty($squareOpeners) === false) {
                    $opener                            = array_pop($squareOpeners);
                    $this->tokens[$i]['bracket_opener']      = $opener;
                    $this->tokens[$i]['bracket_closer']      = $i;
                    $this->tokens[$opener]['bracket_opener'] = $opener;
                    $this->tokens[$opener]['bracket_closer'] = $i;

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", count($squareOpeners));
                        echo str_repeat("\t", count($curlyOpeners));
                        echo "\t=> Found square bracket closer at $i for $opener".PHP_EOL;
                    }
                }
                break;
            case T_CLOSE_CURLY_BRACKET:
                if (empty($curlyOpeners) === false
                    && isset($this->tokens[$i]['scope_opener']) === false
                ) {
                    $opener                            = array_pop($curlyOpeners);
                    $this->tokens[$i]['bracket_opener']      = $opener;
                    $this->tokens[$i]['bracket_closer']      = $i;
                    $this->tokens[$opener]['bracket_opener'] = $opener;
                    $this->tokens[$opener]['bracket_closer'] = $i;

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
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

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
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
     * @return void
     */
    protected function createParenthesisMap()
    {
        $openers   = array();
        $numTokens = count($this->tokens);
        $openOwner = null;

        for ($i = 0; $i < $numTokens; $i++) {
            if (in_array($this->tokens[$i]['code'], PHP_CodeSniffer_Tokens::$parenthesisOpeners) === true) {
                $this->tokens[$i]['parenthesis_opener'] = null;
                $this->tokens[$i]['parenthesis_closer'] = null;
                $this->tokens[$i]['parenthesis_owner']  = $i;
                $openOwner                        = $i;
            } else if ($this->tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $openers[]                        = $i;
                $this->tokens[$i]['parenthesis_opener'] = $i;
                if ($openOwner !== null) {
                    $this->tokens[$openOwner]['parenthesis_opener'] = $i;
                    $this->tokens[$i]['parenthesis_owner']          = $openOwner;
                    $openOwner                                = null;
                }
            } else if ($this->tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                // Did we set an owner for this set of parenthesis?
                $numOpeners = count($openers);
                if ($numOpeners !== 0) {
                    $opener = array_pop($openers);
                    if (isset($this->tokens[$opener]['parenthesis_owner']) === true) {
                        $owner = $this->tokens[$opener]['parenthesis_owner'];

                        $this->tokens[$owner]['parenthesis_closer'] = $i;
                        $this->tokens[$i]['parenthesis_owner']      = $owner;
                    }

                    $this->tokens[$i]['parenthesis_opener']      = $opener;
                    $this->tokens[$i]['parenthesis_closer']      = $i;
                    $this->tokens[$opener]['parenthesis_closer'] = $i;
                }
            }//end if
        }//end for

        
    }//end _createParenthesisMap()


    /**
     * Creates a map for the parenthesis tokens that surround other tokens.
     *
     * @return void
     */
    protected function createParenthesisNestingMap()
    {
        $numTokens = count($this->tokens);
        $map       = array();
        for ($i = 0; $i < $numTokens; $i++) {
            if (isset($this->tokens[$i]['parenthesis_opener']) === true
                && $i === $this->tokens[$i]['parenthesis_opener']
            ) {
                if (empty($map) === false) {
                    $this->tokens[$i]['nested_parenthesis'] = $map;
                }

                if (isset($this->tokens[$i]['parenthesis_closer']) === true) {
                    $map[$this->tokens[$i]['parenthesis_opener']]
                        = $this->tokens[$i]['parenthesis_closer'];
                }
            } else if (isset($this->tokens[$i]['parenthesis_closer']) === true
                && $i === $this->tokens[$i]['parenthesis_closer']
            ) {
                array_pop($map);
                if (empty($map) === false) {
                    $this->tokens[$i]['nested_parenthesis'] = $map;
                }
            } else {
                if (empty($map) === false) {
                    $this->tokens[$i]['nested_parenthesis'] = $map;
                }
            }//end if
        }//end for

        
    }//end _createParenthesisNestingMap()


    /**
     * Creates a scope map of tokens that open scopes.
     *
     * @return void
     * @see _recurseScopeMap()
     */
    protected function createScopeMap()
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START SCOPE MAP ***".PHP_EOL;
        }

        $numTokens = count($this->tokens);
        for ($i = 0; $i < $numTokens; $i++) {
            // Check to see if the current token starts a new scope.
            if (isset($this->scopeOpeners[$this->tokens[$i]['code']]) === true) {
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $type    = $this->tokens[$i]['type'];
                    $content = str_replace($this->eolChar, '\n', $this->tokens[$i]['content']);
                    echo "\tStart scope map at $i: $type => $content".PHP_EOL;
                }

                $i = $this->recurseScopeMap(
                    $numTokens,
                    $i
                );
            }
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END SCOPE MAP ***".PHP_EOL;
        }

        
    }//end _createScopeMap()


    /**
     * Recurses though the scope openers to build a scope map.
     *
     * @param int $numTokens The size of the tokens array.
     * @param int $stackPtr  The position in the stack of the token that
     *                          opened the scope (eg. an IF token or FOR token).
     * @param int $depth     How many scope levels down we are.
     * @param int &$ignore   How many curly braces we are ignoring.
     *
     * @return int The position in the stack that closed the scope.
     */
    protected function recurseScopeMap(
        $numTokens,
        $stackPtr,
        $depth=1,
        &$ignore=0
    ) {
        $opener    = null;
        $currType  = $this->tokens[$stackPtr]['code'];
        $startLine = $this->tokens[$stackPtr]['line'];

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
            $tokenType = $this->tokens[$i]['code'];

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type    = $this->tokens[$i]['type'];
                $content = str_replace($this->eolChar, '\n', $this->tokens[$i]['content']);
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
            if (($currType === T_IF || $currType === T_ELSE) && $opener === null && $this->tokens[$i]['code'] === T_SEMICOLON) {
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $type = $this->tokens[$stackPtr]['type'];
                    echo str_repeat("\t", $depth);
                    echo "=> Found semicolon before scope opener for $stackPtr ($type), bailing".PHP_EOL;
                }

                return $i;
            }

            // Is this an opening condition ?
            if (isset($this->scopeOpeners[$tokenType]) === true) {
                if ($opener === null) {
                    // Found another opening condition but still haven't
                    // found our opener, so we are never going to find one.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $this->tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Couldn't find scope opener for $stackPtr ($type), bailing".PHP_EOL;
                    }

                    return $stackPtr;
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo '* token is an opening condition *'.PHP_EOL;
                }

                $isShared
                    = ($this->scopeOpeners[$tokenType]['shared'] === true);

                if (isset($this->tokens[$i]['scope_condition']) === true) {
                    // We've been here before.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* already processed, skipping *'.PHP_EOL;
                    }

                    if ($isShared === false
                        && isset($this->tokens[$i]['scope_closer']) === true
                    ) {
                        $i = $this->tokens[$i]['scope_closer'];
                    }

                    continue;
                } else if ($currType === $tokenType
                    && $isShared === false
                    && $opener === null
                ) {
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

                    if (in_array(T_CLOSE_CURLY_BRACKET, $this->scopeOpeners[$tokenType]['end']) === true) {
                        $oldIgnore = $ignore;
                        $ignore    = 0;
                    }

                    $i = $this->recurseScopeMap(
                        $numTokens,
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
                        if (in_array($this->tokens[$x]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                            continue;
                        } else {
                            // If the first non-whitespace/comment token is a
                            // variable or object operator then this is an opener
                            // for a string offset and not a scope.
                            if ($this->tokens[$x]['code'] === T_VARIABLE
                                || $this->tokens[$x]['code'] === T_OBJECT_OPERATOR
                            ) {
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
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
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $this->tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Found scope opener for $stackPtr ($type)".PHP_EOL;
                    }

                    $opener = $i;
                }
            } else if (in_array($tokenType, $this->scopeOpeners[$currType]['end']) === true
                && $opener !== null
            ) {
                if ($ignore > 0 && $tokenType === T_CLOSE_CURLY_BRACKET) {
                    // The last opening bracket must have been for a string
                    // offset or alike, so let's ignore it.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* finished ignoring curly brace *'.PHP_EOL;
                    }

                    $ignore--;
                } else {
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $this->tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Found scope closer for $stackPtr ($type)".PHP_EOL;
                    }

                    foreach (array($stackPtr, $opener, $i) as $token) {
                        $this->tokens[$token]['scope_condition'] = $stackPtr;
                        $this->tokens[$token]['scope_opener']    = $opener;
                        $this->tokens[$token]['scope_closer']    = $i;
                    }

                    if ($this->scopeOpeners[$this->tokens[$stackPtr]['code']]['shared'] === true) {
                        // As we are going back to where we started originally, restore
                        // the ignore value back to its original value.
                        $ignore = $originalIgnore;
                        return $opener;
                    } else {
                        return $i;
                    }
                }//end if
            } else if ($tokenType === T_OPEN_PARENTHESIS) {
                if (isset($this->tokens[$i]['parenthesis_owner']) === true) {
                    $owner = $this->tokens[$i]['parenthesis_owner'];
                    if (in_array($this->tokens[$owner]['code'], PHP_CodeSniffer_Tokens::$scopeOpeners) === true
                        && isset($this->tokens[$i]['parenthesis_closer']) === true
                    ) {
                        // If we get into here, then we opened a parenthesis for
                        // a scope (eg. an if or else if). We can just skip to
                        // the closing parenthesis.
                        $i = $this->tokens[$i]['parenthesis_closer'];

                        // Update the start of the line so that when we check to see
                        // if the closing parenthesis is more than 3 lines away from
                        // the statement, we check from the closing parenthesis.
                        $startLine
                            = $this->tokens[$this->tokens[$i]['parenthesis_closer']]['line'];

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
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
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
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
                if ($this->tokens[$i]['line'] >= ($startLine + 3)
                    && in_array($this->tokens[($i - 1)]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false
                ) {
                    if ($this->scopeOpeners[$currType]['strict'] === true) {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type  = $this->tokens[$stackPtr]['type'];
                            $lines = ($this->tokens[$i]['line'] - $startLine);
                            echo str_repeat("\t", $depth);
                            echo "=> Still looking for $stackPtr ($type) scope opener after $lines lines".PHP_EOL;
                        }
                    } else {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = $this->tokens[$stackPtr]['type'];
                            echo str_repeat("\t", $depth);
                            echo "=> Couldn't find scope opener for $stackPtr ($type), bailing".PHP_EOL;
                        }

                        return $stackPtr;
                    }
                }
            } else if ($opener !== null
                && $tokenType !== T_BREAK
                && in_array($tokenType, $this->endScopeTokens) === true
            ) {
                if (isset($this->tokens[$i]['scope_condition']) === false) {
                    if ($ignore > 0) {
                        // We found the end token for the opener we were ignoring.
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo '* finished ignoring curly brace *'.PHP_EOL;
                        }

                        $ignore--;
                    } else {
                        // We found a token that closes the scope but it doesn't
                        // have a condition, so it belongs to another token and
                        // our token doesn't have a closer, so pretend this is
                        // the closer.
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = $this->tokens[$stackPtr]['type'];
                            echo str_repeat("\t", $depth);
                            echo "=> Found (unexpected) scope closer for $stackPtr ($type)".PHP_EOL;
                        }

                        foreach (array($stackPtr, $opener) as $token) {
                            $this->tokens[$token]['scope_condition'] = $stackPtr;
                            $this->tokens[$token]['scope_opener']    = $opener;
                            $this->tokens[$token]['scope_closer']    = $i;
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
     * @return void
     */
    protected function createLevelMap()
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START LEVEL MAP ***".PHP_EOL;
        }

        $numTokens  = count($this->tokens);
        $level      = 0;
        $conditions = array();
        $lastOpener = null;
        $openers    = array();

        for ($i = 0; $i < $numTokens; $i++) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type    = $this->tokens[$i]['type'];
                $line    = $this->tokens[$i]['line'];
                $content = str_replace($this->eolChar, '\n', $this->tokens[$i]['content']);
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

            $this->tokens[$i]['level']      = $level;
            $this->tokens[$i]['conditions'] = $conditions;

            if (isset($this->tokens[$i]['scope_condition']) === true) {
                // Check to see if this token opened the scope.
                if ($this->tokens[$i]['scope_opener'] === $i) {
                    $stackPtr = $this->tokens[$i]['scope_condition'];
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $this->tokens[$stackPtr]['type'];
                        echo str_repeat("\t", ($level + 1));
                        echo "=> Found scope opener for $stackPtr ($type)".PHP_EOL;
                    }

                    $stackPtr = $this->tokens[$i]['scope_condition'];

                    // If we find a scope opener that has a shared closer,
                    // then we need to go back over the condition map that we
                    // just created and fix ourselves as we just added some
                    // conditions where there was none. This happens for T_CASE
                    // statements that are using the same break statement.
                    if ($lastOpener !== null && $this->tokens[$lastOpener]['scope_closer'] === $this->tokens[$i]['scope_closer']) {
                        // This opener shares its closer with the previous opener,
                        // but we still need to check if the two openers share their
                        // closer with each other directly (like CASE and DEFAULT)
                        // or if they are just sharing because one doesn't have a
                        // closer (like CASE with no BREAK using a SWITCHes closer).
                        $thisType = $this->tokens[$this->tokens[$i]['scope_condition']]['code'];
                        $opener   = $this->tokens[$lastOpener]['scope_condition'];

                        $isShared = in_array(
                            $this->tokens[$opener]['code'],
                            $this->scopeOpeners[$thisType]['with']
                        );

                        $sameEnd = ($this->scopeOpeners[$thisType]['end'][0] === $this->scopeOpeners[$this->tokens[$opener]['code']]['end'][0]);
                        if ($isShared === true && $sameEnd === true) {
                            $badToken = $opener;
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $type = $this->tokens[$badToken]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "* shared closer, cleaning up $badToken ($type) *".PHP_EOL;
                            }

                            for ($x = $this->tokens[$i]['scope_condition']; $x <= $i; $x++) {
                                $oldConditions = $this->tokens[$x]['conditions'];
                                $oldLevel      = $this->tokens[$x]['level'];
                                $this->tokens[$x]['level']--;
                                unset($this->tokens[$x]['conditions'][$badToken]);
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    $type     = $this->tokens[$x]['type'];
                                    $oldConds = '';
                                    foreach ($oldConditions as $condition) {
                                        $oldConds .= token_name($condition).',';
                                    }

                                    $oldConds = rtrim($oldConds, ',');

                                    $newConds = '';
                                    foreach ($this->tokens[$x]['conditions'] as $condition) {
                                        $newConds .= token_name($condition).',';
                                    }

                                    $newConds = rtrim($newConds, ',');

                                    $newLevel = $this->tokens[$x]['level'];
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
                                $type = $this->tokens[$badToken]['type'];
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

                    $conditions[$stackPtr] = $this->tokens[$stackPtr]['code'];
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $this->tokens[$stackPtr]['type'];
                        echo str_repeat("\t", ($level + 1));
                        echo "* token $stackPtr ($type) added to conditions array *".PHP_EOL;
                    }

                    $lastOpener = $this->tokens[$i]['scope_opener'];
                    if ($lastOpener !== null) {
                        $openers[$lastOpener] = $lastOpener;
                    }
                } else if ($this->tokens[$i]['scope_closer'] === $i) {
                    foreach (array_reverse($openers) as $opener) {
                        if ($this->tokens[$opener]['scope_closer'] === $i) {
                            $oldOpener = array_pop($openers);
                            if (empty($openers) === false) {
                                $lastOpener           = array_pop($openers);
                                $openers[$lastOpener] = $lastOpener;
                            } else {
                                $lastOpener = null;
                            }

                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $type = $this->tokens[$oldOpener]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "=> Found scope closer for $oldOpener ($type)".PHP_EOL;
                            }

                            $oldCondition = array_pop($conditions);
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                echo str_repeat("\t", ($level + 1));
                                echo '* token '.token_name($oldCondition).' removed from conditions array *'.PHP_EOL;
                            }

                            // Make sure this closer actually belongs to us.
                            // Either the condition also has to think this is the
                            // closer, or it has to allow sharing with us.
                            $condition
                                = $this->tokens[$this->tokens[$i]['scope_condition']]['code'];
                            if ($condition !== $oldCondition) {
                                if (in_array($condition, $this->scopeOpeners[$oldCondition]['with']) === false) {
                                    $badToken = $this->tokens[$oldOpener]['scope_condition'];

                                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                        $type = token_name($oldCondition);
                                        echo str_repeat("\t", ($level + 1));
                                        echo "* scope closer was bad, cleaning up $badToken ($type) *".PHP_EOL;
                                    }

                                    for ($x = ($oldOpener + 1); $x <= $i; $x++) {
                                        $oldConditions = $this->tokens[$x]['conditions'];
                                        $oldLevel      = $this->tokens[$x]['level'];
                                        $this->tokens[$x]['level']--;
                                        unset($this->tokens[$x]['conditions'][$badToken]);
                                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                            $type     = $this->tokens[$x]['type'];
                                            $oldConds = '';
                                            foreach ($oldConditions as $condition) {
                                                $oldConds .= token_name($condition).',';
                                            }

                                            $oldConds = rtrim($oldConds, ',');

                                            $newConds = '';
                                            foreach ($this->tokens[$x]['conditions'] as $condition) {
                                                $newConds .= token_name($condition).',';
                                            }

                                            $newConds = rtrim($newConds, ',');

                                            $newLevel = $this->tokens[$x]['level'];
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
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                echo str_repeat("\t", ($level + 2));
                                echo '* level decreased *'.PHP_EOL;
                            }

                            $this->tokens[$i]['level']      = $level;
                            $this->tokens[$i]['conditions'] = $conditions;
                        }//end if
                    }//end foreach
                }//end if
            }//end if
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END LEVEL MAP ***".PHP_EOL;
        }

    }//end _createLevelMap()
}//end class

?>
