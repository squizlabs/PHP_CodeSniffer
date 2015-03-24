<?php

namespace PHP_CodeSniffer\Tokenizers;

use PHP_CodeSniffer\RuntimeException;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Util;

/**
 * Tokenizes PHP code.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Tokenizes PHP code.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
abstract class Tokenizer
{

    protected $eolChar = array();
    protected $config = null;
    protected $tokens = array();
    public $knownLengths = array();

    public function __construct($content, Config $config, $eolChar='\n')
    {
        $this->eolChar = $eolChar;
        $this->config = $config;

        $this->tokens = $this->tokenize($content);

        $this->createPositionMap();
        $this->createTokenMap();
        $this->createParenthesisNestingMap();
        $this->createScopeMap();

        $this->createLevelMap();

        // Allow the tokenizer to do additional processing if required.
        $this->processAdditional();
    }


    public function getTokens()
    {
        return $this->tokens;
    }



    abstract protected function tokenize($string);
    abstract protected function processAdditional();


    /**
     * Sets token position information.
     *
     * Can also convert tabs into spaces. Each tab can represent between
     * 1 and $width spaces, so this cannot be a straight string replace.
     *
     * @param array  $this->tokens    The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $this->eolChar   The EOL character to use for splitting strings.
     * @param string $this->config->encoding  The charset of the sniffed file.
     * @param int    $this->config->tabWidth  The number of spaces that each tab represents.
     *                          Set to 0 to disable tab replacement.
     *
     * @return void
     */
    private function createPositionMap()
    {
        $currColumn    = 1;
        $lineNumber    = 1;
        $eolLen        = (strlen($this->eolChar) * -1);
        $ignoring      = false;
        $inTests       = defined('PHP_CODESNIFFER_IN_TESTS');

        $checkEncoding = false;
        if ($this->config->encoding !== 'iso-8859-1' && function_exists('iconv_strlen') === true) {
            $checkEncoding = true;
        }

        $this->tokensWithTabs = array(
                           T_WHITESPACE               => true,
                           T_COMMENT                  => true,
                           T_DOC_COMMENT              => true,
                           T_DOC_COMMENT_WHITESPACE   => true,
                           T_DOC_COMMENT_STRING       => true,
                           T_CONSTANT_ENCAPSED_STRING => true,
                           T_DOUBLE_QUOTED_STRING     => true,
                           T_HEREDOC                  => true,
                           T_NOWDOC                   => true,
                           T_INLINE_HTML              => true,
                          );

        $this->numTokens = count($this->tokens);
        for ($i = 0; $i < $this->numTokens; $i++) {
            $this->tokens[$i]['line']   = $lineNumber;
            $this->tokens[$i]['column'] = $currColumn;

            if (isset($this->knownLengths[$this->tokens[$i]['code']]) === true) {
                // There are no tabs in the tokens we know the length of.
                $length      = $this->knownLengths[$this->tokens[$i]['code']];
                $currColumn += $length;
            } else if ($this->config->tabWidth === 0
                || isset($this->tokensWithTabs[$this->tokens[$i]['code']]) === false
                || strpos($this->tokens[$i]['content'], "\t") === false
            ) {
                // There are no tabs in this content, or we aren't replacing them.
                if ($checkEncoding === true) {
                    // Not using the default encoding, so take a bit more care.
                    $length = iconv_strlen($this->tokens[$i]['content'], $this->config->encoding);
                    if ($length === false) {
                        // String contained invalid characters, so revert to default.
                        $length = strlen($this->tokens[$i]['content']);
                    }
                } else {
                    $length = strlen($this->tokens[$i]['content']);
                }

                $currColumn += $length;
            } else {
                if (str_replace("\t", '', $this->tokens[$i]['content']) === '') {
                    // String only contains tabs, so we can shortcut the process.
                    $numTabs = strlen($this->tokens[$i]['content']);

                    $newContent   = '';
                    $firstTabSize = ($this->config->tabWidth - ($currColumn % $this->config->tabWidth) + 1);
                    $length       = ($firstTabSize + ($this->config->tabWidth * ($numTabs - 1)));
                    $currColumn  += $length;
                    $newContent   = str_repeat(' ', $length);
                } else {
                    // We need to determine the length of each tab.
                    $tabs = explode("\t", $this->tokens[$i]['content']);

                    $numTabs    = (count($tabs) - 1);
                    $tabNum     = 0;
                    $newContent = '';
                    $length     = 0;

                    foreach ($tabs as $content) {
                        if ($content !== '') {
                            $newContent .= $content;
                            if ($checkEncoding === true) {
                                // Not using the default encoding, so take a bit more care.
                                $contentLength = iconv_strlen($content, $this->config->encoding);
                                if ($contentLength === false) {
                                    // String contained invalid characters, so revert to default.
                                    $contentLength = strlen($content);
                                }
                            } else {
                                $contentLength = strlen($content);
                            }

                            $currColumn += $contentLength;
                            $length     += $contentLength;
                        }

                        // The last piece of content does not have a tab after it.
                        if ($tabNum === $numTabs) {
                            break;
                        }

                        // Process the tab that comes after the content.
                        $lastCurrColumn = $currColumn;
                        $tabNum++;

                        // Move the pointer to the next tab stop.
                        if (($currColumn % $this->config->tabWidth) === 0) {
                            // This is the first tab, and we are already at a
                            // tab stop, so this tab counts as a single space.
                            $currColumn++;
                        } else {
                            $currColumn++;
                            while (($currColumn % $this->config->tabWidth) !== 0) {
                                $currColumn++;
                            }

                            $currColumn++;
                        }

                        $length     += ($currColumn - $lastCurrColumn);
                        $newContent .= str_repeat(' ', ($currColumn - $lastCurrColumn));
                    }//end foreach
                }//end if

                $this->tokens[$i]['orig_content'] = $this->tokens[$i]['content'];
                $this->tokens[$i]['content']      = $newContent;
            }//end if

            $this->tokens[$i]['length'] = $length;

            if (isset($this->knownLengths[$this->tokens[$i]['code']]) === false
                && strpos($this->tokens[$i]['content'], $this->eolChar) !== false
            ) {
                $lineNumber++;
                $currColumn = 1;

                // Newline chars are not counted in the token length.
                $this->tokens[$i]['length'] += $eolLen;
            }

            if ($this->tokens[$i]['code'] === T_COMMENT
                || $this->tokens[$i]['code'] === T_DOC_COMMENT
                || ($inTests === true && $this->tokens[$i]['code'] === T_INLINE_HTML)
            ) {
                if (strpos($this->tokens[$i]['content'], '@codingStandards') !== false) {
                    if ($ignoring === false
                        && strpos($this->tokens[$i]['content'], '@codingStandardsIgnoreStart') !== false
                    ) {
                        $ignoring = true;
                    } else if ($ignoring === true
                        && strpos($this->tokens[$i]['content'], '@codingStandardsIgnoreEnd') !== false
                    ) {
                        $ignoring = false;
                        // Ignore this comment too.
                        $this->ignoredLines[$this->tokens[$i]['line']] = true;
                    } else if ($ignoring === false
                        && strpos($this->tokens[$i]['content'], '@codingStandardsIgnoreLine') !== false
                    ) {
                        $this->ignoredLines[($this->tokens[$i]['line'] + 1)] = true;
                        // Ignore this comment too.
                        $this->ignoredLines[$this->tokens[$i]['line']] = true;
                    }
                }
            }//end if

            if ($ignoring === true) {
                $this->ignoredLines[$this->tokens[$i]['line']] = true;
            }
        }//end for

    }//end _createPositionMap()


    /**
     * Creates a map of brackets positions.
     *
     * @param array  $this->tokens    The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $this->eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private function createTokenMap()
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START TOKEN MAP ***".PHP_EOL;
        }

        $squareOpeners = array();
        $curlyOpeners  = array();
        $this->numTokens     = count($this->tokens);

        $openers   = array();
        $openOwner = null;

        for ($i = 0; $i < $this->numTokens; $i++) {
            /*
                Parenthesis mapping.
            */

            if (isset(Util\Tokens::$parenthesisOpeners[$this->tokens[$i]['code']]) === true) {
                $this->tokens[$i]['parenthesis_opener'] = null;
                $this->tokens[$i]['parenthesis_closer'] = null;
                $this->tokens[$i]['parenthesis_owner']  = $i;
                $openOwner = $i;
            } else if ($this->tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $openers[] = $i;
                $this->tokens[$i]['parenthesis_opener'] = $i;
                if ($openOwner !== null) {
                    $this->tokens[$openOwner]['parenthesis_opener'] = $i;
                    $this->tokens[$i]['parenthesis_owner']          = $openOwner;
                    $openOwner = null;
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

            /*
                Bracket mapping.
            */

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
                    $opener = array_pop($squareOpeners);
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
                    $opener = array_pop($curlyOpeners);
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
            echo "\t*** END TOKEN MAP ***".PHP_EOL;
        }

    }//end _createTokenMap()


    /**
     * Creates a map for the parenthesis tokens that surround other tokens.
     *
     * @param array  $this->tokens    The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $this->eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private function createParenthesisNestingMap()
    {
        $map       = array();
        for ($i = 0; $i < $this->numTokens; $i++) {
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
     * @param array  $this->tokens    The array of tokens to process.
     *
     * @return void
     * @see    recurseScopeMap()
     */
    private function createScopeMap()
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START SCOPE MAP ***".PHP_EOL;
            $isWin = false;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $isWin = true;
            }
        }

        for ($i = 0; $i < $this->numTokens; $i++) {
            // Check to see if the current token starts a new scope.
            if (isset($this->scopeOpeners[$this->tokens[$i]['code']]) === true) {
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $type    = $this->tokens[$i]['type'];
                    $content = Util\Common::prepareForOutput($this->tokens[$i]['content']);
                    echo "\tStart scope map at $i:$type => $content".PHP_EOL;
                }

                $i = $this->recurseScopeMap($i);
            }//end if
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END SCOPE MAP ***".PHP_EOL;
        }

    }//end _createScopeMap()


    /**
     * Recurses though the scope openers to build a scope map.
     *
     * @param array  $this->tokens    The array of tokens to process.
     * @param int    $this->numTokens The size of the tokens array.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $this->eolChar   The EOL character to use for splitting strings.
     * @param int    $stackPtr  The position in the stack of the token that
     *                          opened the scope (eg. an IF token or FOR token).
     * @param int    $depth     How many scope levels down we are.
     * @param int    $ignore    How many curly braces we are ignoring.
     *
     * @return int The position in the stack that closed the scope.
     */
    private function recurseScopeMap($stackPtr, $depth=1, &$ignore=0) {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            if ($depth === 1) {
                echo "\t*** START SCOPE MAP ***".PHP_EOL;
            }

            $isWin = false;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $isWin = true;
            }
        }

        $opener    = null;
        $currType  = $this->tokens[$stackPtr]['code'];
        $startLine = $this->tokens[$stackPtr]['line'];

        // We will need this to restore the value if we end up
        // returning a token ID that causes our calling function to go back
        // over already ignored braces.
        $originalIgnore = $ignore;

        // If the start token for this scope opener is the same as
        // the scope token, we have already found our opener.
        if (isset($this->scopeOpeners[$currType]['start'][$currType]) === true) {
            $opener = $stackPtr;
        }

        for ($i = ($stackPtr + 1); $i < $this->numTokens; $i++) {
            $tokenType = $this->tokens[$i]['code'];

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type    = $this->tokens[$i]['type'];
                $line    = $this->tokens[$i]['line'];
                $content = Util\Common::prepareForOutput($this->tokens[$i]['content']);

                echo str_repeat("\t", $depth);
                echo "Process token $i on line $line [";
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
            if (($currType === T_IF || $currType === T_ELSE)
                && $opener === null
                && $this->tokens[$i]['code'] === T_SEMICOLON
            ) {
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $type = $this->tokens[$stackPtr]['type'];
                    echo str_repeat("\t", $depth);
                    echo "=> Found semicolon before scope opener for $stackPtr:$type, bailing".PHP_EOL;
                }

                return $i;
            }

            if ($opener !== null
                && (isset($this->tokens[$i]['scope_opener']) === false
                || $this->scopeOpeners[$this->tokens[$stackPtr]['code']]['shared'] === true)
                && isset($this->scopeOpeners[$currType]['end'][$tokenType]) === true
            ) {
                if ($ignore > 0 && $tokenType === T_CLOSE_CURLY_BRACKET) {
                    // The last opening bracket must have been for a string
                    // offset or alike, so let's ignore it.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", $depth);
                        echo '* finished ignoring curly brace *'.PHP_EOL;
                    }

                    $ignore--;
                    continue;
                } else if ($this->tokens[$opener]['code'] === T_OPEN_CURLY_BRACKET
                    && $tokenType !== T_CLOSE_CURLY_BRACKET
                ) {
                    // The opener is a curly bracket so the closer must be a curly bracket as well.
                    // We ignore this closer to handle cases such as T_ELSE or T_ELSEIF being considered
                    // a closer of T_IF when it should not.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $this->tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Ignoring non-curly scope closer for $stackPtr:$type".PHP_EOL;
                    }
                } else {
                    $scopeCloser = $i;
                    $todo        = array(
                                    $stackPtr,
                                    $opener,
                                   );

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type       = $this->tokens[$stackPtr]['type'];
                        $closerType = $this->tokens[$scopeCloser]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Found scope closer ($scopeCloser:$closerType) for $stackPtr:$type".PHP_EOL;
                    }

                    $validCloser = true;
                    if (($this->tokens[$stackPtr]['code'] === T_IF || $this->tokens[$stackPtr]['code'] === T_ELSEIF)
                        && ($tokenType === T_ELSE || $tokenType === T_ELSEIF)
                    ) {
                        // To be a closer, this token must have an opener.
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo "* closer needs to be tested *".PHP_EOL;
                        }

                        $i = self::recurseScopeMap($i, ($depth + 1), $ignore);

                        if (isset($this->tokens[$scopeCloser]['scope_opener']) === false) {
                            $validCloser = false;
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                echo str_repeat("\t", $depth);
                                echo "* closer is not valid (no opener found) *".PHP_EOL;
                            }
                        } else if ($this->tokens[$this->tokens[$scopeCloser]['scope_opener']]['code'] !== $this->tokens[$opener]['code']) {
                            $validCloser = false;
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                echo str_repeat("\t", $depth);
                                $type       = $this->tokens[$this->tokens[$scopeCloser]['scope_opener']]['type'];
                                $openerType = $this->tokens[$opener]['type'];
                                echo "* closer is not valid (mismatched opener type; $type != $openerType) *".PHP_EOL;
                            }
                        } else if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo "* closer was valid *".PHP_EOL;
                        }
                    } else {
                        // The closer was not processed, so we need to
                        // complete that token as well.
                        $todo[] = $scopeCloser;
                    }//end if

                    if ($validCloser === true) {
                        foreach ($todo as $token) {
                            $this->tokens[$token]['scope_condition'] = $stackPtr;
                            $this->tokens[$token]['scope_opener']    = $opener;
                            $this->tokens[$token]['scope_closer']    = $scopeCloser;
                        }

                        if ($this->scopeOpeners[$this->tokens[$stackPtr]['code']]['shared'] === true) {
                            // As we are going back to where we started originally, restore
                            // the ignore value back to its original value.
                            $ignore = $originalIgnore;
                            return $opener;
                        } else if ($scopeCloser === $i
                            && isset($this->scopeOpeners[$tokenType]) === true
                        ) {
                            // Unset scope_condition here or else the token will appear to have
                            // already been processed, and it will be skipped. Normally we want that,
                            // but in this case, the token is both a closer and an opener, so
                            // it needs to act like an opener. This is also why we return the
                            // token before this one; so the closer has a chance to be processed
                            // a second time, but as an opener.
                            unset($this->tokens[$scopeCloser]['scope_condition']);
                            return ($i - 1);
                        } else {
                            return $i;
                        }
                    } else {
                        continue;
                    }//end if
                }//end if
            }//end if

            // Is this an opening condition ?
            if (isset($this->scopeOpeners[$tokenType]) === true) {
                if ($opener === null) {
                    if ($tokenType === T_USE) {
                        // PHP use keywords are special because they can be
                        // used as blocks but also inline in function definitions.
                        // So if we find them nested inside another opener, just skip them.
                        continue;
                    }

                    // Found another opening condition but still haven't
                    // found our opener, so we are never going to find one.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $this->tokens[$stackPtr]['type'];
                        echo str_repeat("\t", $depth);
                        echo "=> Found new opening condition before scope opener for $stackPtr:$type, bailing".PHP_EOL;
                    }

                    return $stackPtr;
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", $depth);
                    echo '* token is an opening condition *'.PHP_EOL;
                }

                $isShared = ($this->scopeOpeners[$tokenType]['shared'] === true);

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

                    if (isset($this->scopeOpeners[$tokenType]['end'][T_CLOSE_CURLY_BRACKET]) === true) {
                        $oldIgnore = $ignore;
                        $ignore    = 0;
                    }

                    // PHP has a max nesting level for functions. Stop before we hit that limit
                    // because too many loops means we've run into trouble anyway.
                    if ($depth > 50) {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", $depth);
                            echo '* reached maximum nesting level; aborting *'.PHP_EOL;
                        }

                        throw new RuntimeException('Maximum nesting level reached; file could not be processed');
                    }

                    $i = self::recurseScopeMap($i, ($depth + 1), $ignore);

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
                        if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === true) {
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
                        echo "=> Found scope opener for $stackPtr:$type".PHP_EOL;
                    }

                    $opener = $i;
                }
            } else if ($tokenType === T_OPEN_PARENTHESIS) {
                if (isset($this->tokens[$i]['parenthesis_owner']) === true) {
                    $owner = $this->tokens[$i]['parenthesis_owner'];
                    if (isset(Util\Tokens::$scopeOpeners[$this->tokens[$owner]['code']]) === true
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
                    && isset(Util\Tokens::$emptyTokens[$this->tokens[($i - 1)]['code']]) === false
                ) {
                    if ($this->scopeOpeners[$currType]['strict'] === true) {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type  = $this->tokens[$stackPtr]['type'];
                            $lines = ($this->tokens[$i]['line'] - $startLine);
                            echo str_repeat("\t", $depth);
                            echo "=> Still looking for $stackPtr:$type scope opener after $lines lines".PHP_EOL;
                        }
                    } else {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = $this->tokens[$stackPtr]['type'];
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
                            echo "=> Found (unexpected) scope closer for $stackPtr:$type".PHP_EOL;
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
     * The level map adds a 'level' index to each token which indicates the
     * depth that a token within a set of scope blocks. It also adds a
     * 'condition' index which is an array of the scope conditions that opened
     * each of the scopes - position 0 being the first scope opener.
     *
     * @param array  $this->tokens    The array of tokens to process.
     * @param object $tokenizer The tokenizer being used to process this file.
     * @param string $this->eolChar   The EOL character to use for splitting strings.
     *
     * @return void
     */
    private function createLevelMap()
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START LEVEL MAP ***".PHP_EOL;
        }

        $this->numTokens  = count($this->tokens);
        $level      = 0;
        $conditions = array();
        $lastOpener = null;
        $openers    = array();

        for ($i = 0; $i < $this->numTokens; $i++) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type = $this->tokens[$i]['type'];
                $line = $this->tokens[$i]['line'];
                $len  = $this->tokens[$i]['length'];
                $col  = $this->tokens[$i]['column'];

                $content = Util\Common::prepareForOutput($this->tokens[$i]['content']);

                echo str_repeat("\t", ($level + 1));
                echo "Process token $i on line $line [col:$col;len:$len;lvl:$level;";
                if (empty($conditions) !== true) {
                    $condString = 'conds;';
                    foreach ($conditions as $condition) {
                        $condString .= token_name($condition).',';
                    }

                    echo rtrim($condString, ',').';';
                }

                echo "]: $type => $content".PHP_EOL;
            }//end if

            $this->tokens[$i]['level']      = $level;
            $this->tokens[$i]['conditions'] = $conditions;

            if (isset($this->tokens[$i]['scope_condition']) === true) {
                // Check to see if this token opened the scope.
                if ($this->tokens[$i]['scope_opener'] === $i) {
                    $stackPtr = $this->tokens[$i]['scope_condition'];
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $this->tokens[$stackPtr]['type'];
                        echo str_repeat("\t", ($level + 1));
                        echo "=> Found scope opener for $stackPtr:$type".PHP_EOL;
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

                        $isShared = isset($this->scopeOpeners[$thisType]['with'][$this->tokens[$opener]['code']]);

                        reset($this->scopeOpeners[$thisType]['end']);
                        reset($this->scopeOpeners[$this->tokens[$opener]['code']]['end']);
                        $sameEnd = (current($this->scopeOpeners[$thisType]['end']) === current($this->scopeOpeners[$this->tokens[$opener]['code']]['end']));

                        if ($isShared === true && $sameEnd === true) {
                            $badToken = $opener;
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $type = $this->tokens[$badToken]['type'];
                                echo str_repeat("\t", ($level + 1));
                                echo "* shared closer, cleaning up $badToken:$type *".PHP_EOL;
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
                                    echo "* cleaned $x:$type *".PHP_EOL;
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
                                echo "* token $badToken:$type removed from conditions array *".PHP_EOL;
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
                        echo "* token $stackPtr:$type added to conditions array *".PHP_EOL;
                    }

                    $lastOpener = $this->tokens[$i]['scope_opener'];
                    if ($lastOpener !== null) {
                        $openers[$lastOpener] = $lastOpener;
                    }
                } else if ($lastOpener !== null && $this->tokens[$lastOpener]['scope_closer'] === $i) {
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
                                echo "=> Found scope closer for $oldOpener:$type".PHP_EOL;
                            }

                            $oldCondition = array_pop($conditions);
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                echo str_repeat("\t", ($level + 1));
                                echo '* token '.token_name($oldCondition).' removed from conditions array *'.PHP_EOL;
                            }

                            // Make sure this closer actually belongs to us.
                            // Either the condition also has to think this is the
                            // closer, or it has to allow sharing with us.
                            $condition = $this->tokens[$this->tokens[$i]['scope_condition']]['code'];
                            if ($condition !== $oldCondition) {
                                if (isset($this->scopeOpeners[$oldCondition]['with'][$condition]) === false) {
                                    $badToken = $this->tokens[$oldOpener]['scope_condition'];

                                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                        $type = token_name($oldCondition);
                                        echo str_repeat("\t", ($level + 1));
                                        echo "* scope closer was bad, cleaning up $badToken:$type *".PHP_EOL;
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
