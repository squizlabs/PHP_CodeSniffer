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
require_once dirname(__FILE__).'/Tokens.php';

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
    protected $tabWidth = 4;

    /**
     * Flag for the verbose setting.
     *
     * @var integer
     */
    protected $verbose = 0;

    /**
     * The encoding of the file.
     *
     * @var string
     */
    protected $encoding = 'utf-8';

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

        // If we know the width of each tab, convert tabs
        // into spaces so sniffs can use one method of checking.
        if ($this->tabWidth > 0) {
            $this->_convertTabs($tokens, $eolChar);
        }

        $this->_createTokenMap($tokens, $eolChar);
        $this->_createParenthesisNestingMap($tokens, $eolChar);
        $this->_createScopeMap($tokens, $eolChar);
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
     * Returns the encoding setting.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;

    }//end getEncoding()


    /**
     * Set the encoding setting.
     *
     * @param string $encoding The encoding setting.
     *
     * @return void
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

    }//end setEncoding()


    /**
     * Returns the tabWidth setting.
     *
     * @return integer
     */
    public function getTabWidth()
    {
        return $this->tabWidth;

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
        $this->tabWidth = (int) $tabWidth;

    }//end setTabWidth()


    /**
     * Returns the verbose setting.
     *
     * @return integer
     */
    public function getVerbose()
    {
        return $this->verbose;

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
        $this->verbose = (int) $verbose;

    }//end setVerbose()


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
    private function _convertTabs(&$tokens, $eolChar)
    {
        $currColumn = 1;
        $count      = count($tokens);
        $eolLen     = (strlen($eolChar) * -1);

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
                        if (($currColumn % $this->getTabWidth()) === 0) {
                            // This is the first tab, and we are already at a
                            // tab stop, so this tab counts as a single space.
                            $currColumn++;
                        } else {
                            $currColumn++;
                            while (($currColumn % $this->getTabWidth()) != 0) {
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

            if (substr($tokens[$i]['content'], $eolLen) === $eolChar) {
                $currColumn = 1;
            }
        }//end for

    }//end _convertTabs()


    /**
    * Creates a map of brackets positions.
    *
    * @param array  &$tokens The array of tokens to process.
    * @param string $eolChar The EOL character to use for splitting strings.
    *
    * @return void
    */
    private function _createTokenMap(&$tokens, $eolChar)
    {
        if ($this->getVerbose() > 1) {
            echo "\t*** START TOKEN MAP ***".PHP_EOL;
        }

        $squareOpeners = array();
        $curlyOpeners = array();
        $numTokens = count($tokens);

        $openers = array();
        $openOwner = null;

        $currColumn = 1;
        $lineNumber = 1;
        $eolLen = (strlen($eolChar) * -1);

        for ($i = 0; $i < $numTokens; $i++) {
            /*
                Column and line values.
            */

            $tokens[$i]['line']   = $lineNumber;
            $tokens[$i]['column'] = $currColumn;

            if (isset(PHP_CodeSniffer_Tokens::$knownLengths[$tokens[$i]['code']]) === true) {
                $length = PHP_CodeSniffer_Tokens::$knownLengths[$tokens[$i]['code']];
            } else {
                if ($this->getEncoding() !== 'iso-8859-1') {
                    // Not using the default encoding, so take a bit more care.
                    $length = iconv_strlen($tokens[$i]['content'], $this->getEncoding());
                    if ($length === false) {
                        // String contained invalid characters, so revert to default.
                        $length = strlen($tokens[$i]['content']);
                    }
                } else {
                    $length = strlen($tokens[$i]['content']);
                }
            }

            $tokens[$i]['length'] = $length;
            $currColumn += $length;

            if (substr($tokens[$i]['content'], $eolLen) === $eolChar) {
                $lineNumber++;
                $currColumn = 1;
                $tokens[$i]['length'] += $eolLen;
            }

            /*
                Parenthesis mapping.
            */

            if (isset(PHP_CodeSniffer_Tokens::$parenthesisOpeners[$tokens[$i]['code']]) === true) {
                $tokens[$i]['parenthesis_opener'] = null;
                $tokens[$i]['parenthesis_closer'] = null;
                $tokens[$i]['parenthesis_owner'] = $i;
                $openOwner = $i;
            } else if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $openers[] = $i;
                $tokens[$i]['parenthesis_opener'] = $i;
                if ($openOwner !== null) {
                    $tokens[$openOwner]['parenthesis_opener'] = $i;
                    $tokens[$i]['parenthesis_owner'] = $openOwner;
                    $openOwner = null;
                }
            } else if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                // Did we set an owner for this set of parenthesis?
                $numOpeners = count($openers);
                if ($numOpeners !== 0) {
                    $opener = array_pop($openers);
                    if (isset($tokens[$opener]['parenthesis_owner']) === true) {
                        $owner = $tokens[$opener]['parenthesis_owner'];

                        $tokens[$owner]['parenthesis_closer'] = $i;
                        $tokens[$i]['parenthesis_owner'] = $owner;
                    }

                    $tokens[$i]['parenthesis_opener'] = $opener;
                    $tokens[$i]['parenthesis_closer'] = $i;
                    $tokens[$opener]['parenthesis_closer'] = $i;
                }
            }//end if

            /*
                Bracket mapping.
            */

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
                    $opener = array_pop($squareOpeners);
                    $tokens[$i]['bracket_opener'] = $opener;
                    $tokens[$i]['bracket_closer'] = $i;
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
                    $opener = array_pop($curlyOpeners);
                    $tokens[$i]['bracket_opener'] = $opener;
                    $tokens[$i]['bracket_closer'] = $i;
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
            echo "\t*** END TOKEN MAP ***".PHP_EOL;
        }

    }//end _createTokenMap()


    /**
    * Creates a map for the parenthesis tokens that surround other tokens.
    *
    * @param array  &$tokens The array of tokens to process.
    * @param string $eolChar The EOL character to use for splitting strings.
    *
    * @return void
    */
    private function _createParenthesisNestingMap(&$tokens, $eolChar)
    {
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
    * @param array  &$tokens The array of tokens to process.
    * @param string $eolChar The EOL character to use for splitting strings.
    *
    * @return void
    * @see    _recurseScopeMap()
    */
    private function _createScopeMap(&$tokens, $eolChar)
    {
        if ($this->getVerbose() > 1) {
            echo "\t*** START SCOPE MAP ***".PHP_EOL;
            $isWin = false;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $isWin = true;
            }
        }

        $numTokens = count($tokens);
        for ($i = 0; $i < $numTokens; $i++) {
            // Check to see if the current token starts a new scope.
            if (isset($this->scopeOpeners[$tokens[$i]['code']]) === true) {
                if ($this->getVerbose() > 1) {
                    $type = $tokens[$i]['type'];
                    if ($isWin === true) {
                        $content = str_replace($eolChar, '\n', $tokens[$i]['content']);
                    } else {
                        $content = str_replace($eolChar, "\033[30;1m\\n\033[0m", $tokens[$i]['content']);
                        $content = str_replace(' ', "\033[30;1m·\033[0m", $content);
                    }

                    echo "\tStart scope map at $i:$type => $content".PHP_EOL;
                }

                $i = $this->_recurseScopeMap(
                    $tokens,
                    $numTokens,
                    $eolChar,
                    $i
                );
            }//end if
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
        if ($this->getVerbose() > 1) {
            echo "\t*** START SCOPE MAP ***".PHP_EOL;
            $isWin = false;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $isWin = true;
            }
        }

        $opener    = null;
        $currType  = $tokens[$stackPtr]['code'];
        $startLine = $tokens[$stackPtr]['line'];

        // We will need this to restore the value if we end up
        // returning a token ID that causes our calling function to go back
        // over already ignored braces.
        $originalIgnore = $ignore;

        // If the start token for this scope opener is the same as
        // the scope token, we have already found our opener.
        if (isset($this->scopeOpeners[$currType]['start'][$currType]) === true) {
            $opener = $stackPtr;
        }

        for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
            $tokenType = $tokens[$i]['code'];

            if ($this->getVerbose() > 1) {
                $type = $tokens[$i]['type'];
                if ($isWin === true) {
                    $content = str_replace($eolChar, '\n', $tokens[$i]['content']);
                } else {
                    $content = str_replace($eolChar, "\033[30;1m\\n\033[0m", $tokens[$i]['content']);
                    $content = str_replace(' ', "\033[30;1m·\033[0m", $content);
                }

                echo str_repeat("\t", $depth);
                echo "Process token $i [";
                if ($opener !== null) {
                    echo "opener:$opener;";
                }

                if ($ignore > 0) {
                    echo "ignore=$ignore;";
                }

                echo "]: $type => $content".PHP_EOL;
            }//end if

            // Very special case for IF statements in PHP that can be defined without
            // scope tokens. E.g., if (1) 1; 1 ? (1 ? 1 : 1) : 1;
            // If an IF statement below this one has an opener but no
            // keyword, the opener will be incorrectly assigned to this IF statement.
            if (($currType === T_IF || $currType === T_ELSE) && $opener === null && $tokens[$i]['code'] === T_SEMICOLON) {
                if ($this->getVerbose() > 1) {
                    $type = $tokens[$stackPtr]['type'];
                    echo str_repeat("\t", $depth);
                    echo "=> Found semicolon before scope opener for $stackPtr:$type, bailing".PHP_EOL;
                }

                return $i;
            }

            if ($opener !== null
                && (isset($tokens[$i]['scope_opener']) === false
                || $this->scopeOpeners[$tokens[$stackPtr]['code']]['shared'] === true)
                && isset($this->scopeOpeners[$currType]['end'][$tokenType]) === true
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
                        $type = $tokens[$stackPtr]['type'];
                        $closerType = $tokens[$i]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Found scope closer ($i:$closerType) for $stackPtr:$type".PHP_EOL;
                    }

                    foreach (array($stackPtr, $opener, $i) as $token) {
                        $tokens[$token]['scope_condition'] = $stackPtr;
                        $tokens[$token]['scope_opener'] = $opener;
                        $tokens[$token]['scope_closer'] = $i;
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

                    if (isset($this->scopeOpeners[$tokenType]['end'][T_CLOSE_CURLY_BRACKET]) === true) {
                        $oldIgnore = $ignore;
                        $ignore = 0;
                    }

                    $i = $this->_recurseScopeMap(
                        $tokens,
                        $numTokens,
                        $eolChar,
                        $i,
                        ($depth + 1),
                        $ignore
                    );

                    if (isset($this->scopeOpeners[$tokenType]['end'][T_CLOSE_CURLY_BRACKET]) === true) {
                        $ignore = $oldIgnore;
                    }
                }//end if
            }//end if

            if (isset($this->scopeOpeners[$currType]['start'][$tokenType]) === true
                && $opener === null
            ) {
                if ($tokenType === T_OPEN_CURLY_BRACKET) {
                    // Make sure this is actually an opener and not a
                    // string offset (e.g., $var{0}).
                    for ($x = ($i - 1); $x > 0; $x--) {
                        if (isset(PHP_CodeSniffer_Tokens::$emptyTokens[$tokens[$x]['code']]) === true) {
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
                    if (isset(PHP_CodeSniffer_Tokens::$scopeOpeners[$tokens[$owner]['code']]) === true
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
                    && isset(PHP_CodeSniffer_Tokens::$emptyTokens[$tokens[($i - 1)]['code']]) === false
                ) {
                    if ($this->scopeOpeners[$currType]['strict'] === true) {
                        if ($this->getVerbose() > 1) {
                            $type = $tokens[$stackPtr]['type'];
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
                && isset($this->endScopeTokens[$tokenType]) === true
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
                            $tokens[$token]['scope_opener'] = $opener;
                            $tokens[$token]['scope_closer'] = $i;
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
    private function _createLevelMap(&$tokens, $eolChar)
    {
        if ($this->getVerbose() > 1) {
            echo "\t*** START LEVEL MAP ***".PHP_EOL;
            $isWin = false;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $isWin = true;
            }
        }

        $numTokens  = count($tokens);
        $level      = 0;
        $conditions = array();
        $lastOpener = null;
        $openers    = array();

        for ($i = 0; $i < $numTokens; $i++) {
            if ($this->getVerbose() > 1) {
                $type = $tokens[$i]['type'];
                $line = $tokens[$i]['line'];
                if ($isWin === true) {
                    $content = str_replace($eolChar, '\n', $tokens[$i]['content']);
                } else {
                    $content = str_replace($eolChar, "\033[30;1m\\n\033[0m", $tokens[$i]['content']);
                    $content = str_replace(' ', "\033[30;1m·\033[0m", $content);
                }

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
            }//end if

            $tokens[$i]['level'] = $level;
            $tokens[$i]['conditions'] = $conditions;

            if (isset($tokens[$i]['scope_condition']) === true) {
                // Check to see if this token opened the scope.
                if ($tokens[$i]['scope_opener'] === $i) {
                    $stackPtr = $tokens[$i]['scope_condition'];
                    if ($this->getVerbose() > 1) {
                        $type = $tokens[$stackPtr]['type'];
                        echo str_repeat("\t", ($level + 1));
                        echo "=> Found scope opener for $stackPtr:$type".PHP_EOL;
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
                        $opener = $tokens[$lastOpener]['scope_condition'];

                        $isShared = isset($this->scopeOpeners[$thisType]['with'][$tokens[$opener]['code']]);

                        reset($this->scopeOpeners[$thisType]['end']);
                        reset($this->scopeOpeners[$tokens[$opener]['code']]['end']);
                        $sameEnd = (current($this->scopeOpeners[$thisType]['end']) === current($this->scopeOpeners[$tokens[$opener]['code']]['end']));
                        if ($isShared === true && $sameEnd === true) {
                            $badToken = $opener;
                            if ($this->getVerbose() > 1) {
                                $type = $tokens[$badToken]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "* shared closer, cleaning up $badToken:$type *".PHP_EOL;
                            }

                            for ($x = $tokens[$i]['scope_condition']; $x <= $i; $x++) {
                                $oldConditions = $tokens[$x]['conditions'];
                                $oldLevel = $tokens[$x]['level'];
                                $tokens[$x]['level']--;
                                unset($tokens[$x]['conditions'][$badToken]);
                                if ($this->getVerbose() > 1) {
                                    $type = $tokens[$x]['type'];
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
                                    echo "* cleaned $x:$type *".PHP_EOL;
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
                                echo "* token $badToken:$type removed from conditions array *".PHP_EOL;
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
                        echo "* token $stackPtr:$type added to conditions array *".PHP_EOL;
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
                                $lastOpener = array_pop($openers);
                                $openers[$lastOpener] = $lastOpener;
                            } else {
                                $lastOpener = null;
                            }

                            if ($this->getVerbose() > 1) {
                                $type = $tokens[$oldOpener]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "=> Found scope closer for $oldOpener:$type".PHP_EOL;
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
                                if (isset($this->scopeOpeners[$oldCondition]['with'][$condition]) === false) {
                                    $badToken = $tokens[$oldOpener]['scope_condition'];

                                    if ($this->getVerbose() > 1) {
                                        $type = token_name($oldCondition);
                                        echo str_repeat("\t", ($level + 1));
                                        echo "* scope closer was bad, cleaning up $badToken:$type *".PHP_EOL;
                                    }

                                    for ($x = ($oldOpener + 1); $x <= $i; $x++) {
                                        $oldConditions = $tokens[$x]['conditions'];
                                        $oldLevel = $tokens[$x]['level'];
                                        $tokens[$x]['level']--;
                                        unset($tokens[$x]['conditions'][$badToken]);
                                        if ($this->getVerbose() > 1) {
                                            $type = $tokens[$x]['type'];
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
                                            echo "* cleaned $x:$type *".PHP_EOL;
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

                            $tokens[$i]['level'] = $level;
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
