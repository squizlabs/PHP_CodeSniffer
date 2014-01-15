<?php
/**
 * Tokenizes SQL code.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Benjamin Pearson <bpearson@squiz.com.au>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
require_once dirname(dirname(__FILE__)).'/Tokenizer.php';

/**
 * Tokenizes SQL code.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Benjamin Pearson <bpearson@squiz.com.au>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Tokenizers_SQL extends PHP_CodeSniffer_Tokenizer
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
    public $scopeOpeners = array(
                            T_IF       => array(
                                           'start'  => array(T_OPEN_CURLY_BRACKET),
                                           'end'    => array(T_CLOSE_CURLY_BRACKET),
                                           'strict' => false,
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
    public $endScopeTokens = array(
                              T_CLOSE_CURLY_BRACKET,
                             );

    /**
     * A list of special JS tokens and their types.
     *
     * @var array
     */
    protected $tokenValues = array(
                              'select'    => 'T_SELECT',
                              'from'      => 'T_FROM',
                              'where'     => 'T_WHERE',
                              'join'      => 'T_JOIN',
                              '('         => 'T_OPEN_PARENTHESIS',
                              ')'         => 'T_CLOSE_PARENTHESIS',
                              '['         => 'T_OPEN_SQUARE_BRACKET',
                              ']'         => 'T_CLOSE_SQUARE_BRACKET',
                              '.'         => 'T_OBJECT_OPERATOR',
                              '+'         => 'T_PLUS',
                              '-'         => 'T_MINUS',
                              ','         => 'T_COMMA',
                              ';'         => 'T_SEMICOLON',
                              ':'         => 'T_COLON',
                              '<'         => 'T_LESS_THAN',
                              '>'         => 'T_GREATER_THAN',
                              '<='        => 'T_IS_SMALLER_OR_EQUAL',
                              '>='        => 'T_IS_GREATER_OR_EQUAL',
                              '!'         => 'T_BOOLEAN_NOT',
                              '!='        => 'T_IS_NOT_EQUAL',
                              '='         => 'T_EQUAL',
                              '=='        => 'T_IS_EQUAL',
                              '--'        => 'T_COMMENT',
                             );

    /**
     * A list string delimiters.
     *
     * @var array
     */
    protected $stringTokens = array('\'');

    /**
     * A list tokens that start and end comments.
     *
     * @var array
     */
    protected $commentTokens = array(
                                '--' => null,
                               );


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
    protected function tokenize($string, $eolChar='\n')
    {
        if ($this->getVerbose() > 1) {
            echo "\t*** START SQL TOKENIZING ***".PHP_EOL;
        }

        $tokenTypes = array_keys($this->tokenValues);

        $maxTokenLength = 0;
        foreach ($tokenTypes as $token) {
            if (strlen($token) > $maxTokenLength) {
                $maxTokenLength = strlen($token);
            }
        }

        $tokens          = array();
        $inString        = '';
        $stringChar      = null;
        $inComment       = '';
        $buffer          = '';
        $preStringBuffer = '';
        $cleanBuffer     = false;

        // Convert newlines to single characters for ease of
        // processing. We will change them back later.
        $string = str_replace($eolChar, "\n", $string);

        $chars    = str_split($string);
        $numChars = count($chars);
        for ($i = 0; $i < $numChars; $i++) {
            $char = $chars[$i];

            if ($this->getVerbose() > 1) {
                $content = str_replace("\n", '\n', $char);
                $bufferContent = str_replace("\n", '\n', $buffer);
                if ($inString !== '') {
                    echo "\t";
                }

                if ($inComment !== '') {
                    echo "\t";
                }

                echo "\tProcess char $i => $content (buffer: $bufferContent)".PHP_EOL;
            }

            if ($inString === '' && $inComment === '' && $buffer !== '') {
                // If the buffer only has whitespace and we are about to
                // add a character, store the whitespace first.
                if (trim($char) !== '' && trim($buffer) === '') {
                    $tokens[] = array(
                                 'code'    => T_WHITESPACE,
                                 'type'    => 'T_WHITESPACE',
                                 'content' => str_replace("\n", $eolChar, $buffer),
                                );

                    if ($this->getVerbose() > 1) {
                        $content = str_replace("\n", '\n', $buffer);
                        echo "\t=> Added token T_WHITESPACE ($content)".PHP_EOL;
                    }

                    $buffer = '';
                }

                // If the buffer is not whitespace and we are about to
                // add a whitespace character, store the content first.
                if ($inString === ''
                    && $inComment === ''
                    && trim($char) === ''
                    && trim($buffer) !== ''
                ) {
                    $tokens[] = array(
                                 'code'    => T_STRING,
                                 'type'    => 'T_STRING',
                                 'content' => str_replace("\n", $eolChar, $buffer),
                                );

                    if ($this->getVerbose() > 1) {
                        $content = str_replace("\n", '\n', $buffer);
                        echo "\t=> Added token T_STRING ($content)".PHP_EOL;
                    }

                    $buffer = '';
                }
            }//end if

            // Process strings.
            if ($inComment === '' && in_array($char, $this->stringTokens) === true) {
                if ($inString === $char) {
                    // This could be the end of the string, but make sure it
                    // is not escaped first.
                    $escapes = 0;
                    for ($x = ($i - 1); $x >= 0; $x--) {
                        if ($chars[$x] !== '\\') {
                            break;
                        }

                        $escapes++;
                    }

                    if ($escapes === 0 || ($escapes % 2) === 0) {
                        // There is an even number escape chars,
                        // so this is not escaped, it is the end of the string.
                        $tokens[] = array(
                                     'code'    => T_CONSTANT_ENCAPSED_STRING,
                                     'type'    => 'T_CONSTANT_ENCAPSED_STRING',
                                     'content' => str_replace("\n", $eolChar, $buffer).$char,
                                    );

                        if ($this->getVerbose() > 1) {
                            echo "\t\t* found end of string *".PHP_EOL;
                            $content = str_replace("\n", '\n', $buffer.$char);
                            echo "\t=> Added token T_CONSTANT_ENCAPSED_STRING ($content)".PHP_EOL;
                        }

                        $buffer          = '';
                        $preStringBuffer = '';
                        $inString        = '';
                        $stringChar      = null;
                        continue;
                    }
                } else if ($inString === '') {
                    $inString        = $char;
                    $stringChar      = $i;
                    $preStringBuffer = $buffer;

                    if ($this->getVerbose() > 1) {
                        echo "\t\t* looking for string closer *".PHP_EOL;
                    }
                }//end if
            }//end if

            $buffer .= $char;

            // We don't look for special tokens inside strings,
            // so if we are in a string, we can continue here now
            // that the current char is in the buffer.
            if ($inString !== '') {
                continue;
            }

            // Check for known tokens, but ignore tokens found that are not at
            // the end of a string, like FOR and this.FORmat.
            if (in_array(strtolower($buffer), $tokenTypes) === true
                && (preg_match('|[a-zA-z0-9_]|', $char) === 0
                || isset($chars[($i + 1)]) === false
                || preg_match('|[a-zA-z0-9_]|', $chars[($i + 1)]) === 0)
            ) {
                $matchedToken    = false;
                $lookAheadLength = ($maxTokenLength - strlen($buffer));

                if ($lookAheadLength > 0) {
                    // The buffer contains a token type, but we need
                    // to look ahead at the next chars to see if this is
                    // actually part of a larger token. For example,
                    // FOR and FOREACH.
                    if ($this->getVerbose() > 1) {
                        echo "\t\t* buffer possibly contains token, looking ahead $lookAheadLength chars *".PHP_EOL;
                    }

                    $charBuffer = $buffer;
                    for ($x = 1; $x <= $lookAheadLength; $x++) {
                        if (isset($chars[($i + $x)]) === false) {
                            break;
                        }

                        $charBuffer .= $chars[($i + $x)];

                        if ($this->getVerbose() > 1) {
                            $content = str_replace("\n", '\n', $charBuffer);
                            echo "\t\t=> Looking ahead $x chars => $content".PHP_EOL;
                        }

                        if (in_array(strtolower($charBuffer), $tokenTypes) === true) {
                            // We've found something larger that matches
                            // so we can ignore this char. Except for 1 very specific
                            // case where a comment like /**/ needs to tokenize as
                            // T_COMMENT and not T_DOC_COMMENT.
                            $oldType = $this->tokenValues[strtolower($buffer)];
                            $newType = $this->tokenValues[strtolower($charBuffer)];
                            if ($oldType === 'T_COMMENT'
                                && $newType === 'T_DOC_COMMENT'
                                && $chars[($i + $x + 1)] === '/'
                            ) {
                                if ($this->getVerbose() > 1) {
                                    echo "\t\t* look ahead ignored T_DOC_COMMENT, continuing *".PHP_EOL;
                                }
                            } else {
                                if ($this->getVerbose() > 1) {
                                    echo "\t\t* look ahead found more specific token ($newType), ignoring $i *".PHP_EOL;
                                }

                                $matchedToken = true;
                                break;
                            }
                        }
                    }//end for
                }//end if

                if ($matchedToken === false) {
                    if ($this->getVerbose() > 1 && $lookAheadLength > 0) {
                        echo "\t\t* look ahead found nothing *".PHP_EOL;
                    }

                    $value    = $this->tokenValues[strtolower($buffer)];
                    $tokens[] = array(
                                 'code'    => constant($value),
                                 'type'    => $value,
                                 'content' => $buffer,
                                );

                    if ($this->getVerbose() > 1) {
                        $content = str_replace("\n", '\n', $buffer);
                        echo "\t=> Added token $value ($content)".PHP_EOL;
                    }

                    $cleanBuffer = true;
                }//end if
            } else if (in_array(strtolower($char), $tokenTypes) === true) {
                // No matter what token we end up using, we don't
                // need the content in the buffer any more because we have
                // found a valid token.
                $newContent = substr(str_replace("\n", $eolChar, $buffer), 0, -1);
                if ($newContent !== '') {
                    $tokens[] = array(
                                 'code'    => T_STRING,
                                 'type'    => 'T_STRING',
                                 'content' => $newContent,
                                );

                    if ($this->getVerbose() > 1) {
                        $content = str_replace("\n", '\n', substr($buffer, 0, -1));
                        echo "\t=> Added token T_STRING ($content)".PHP_EOL;
                    }
                }

                if ($this->getVerbose() > 1) {
                    echo "\t\t* char is token, looking ahead ".($maxTokenLength - 1).' chars *'.PHP_EOL;
                }

                // The char is a token type, but we need to look ahead at the
                // next chars to see if this is actually part of a larger token.
                // For example, = and ===.
                $charBuffer   = $char;
                $matchedToken = false;
                for ($x = 1; $x <= $maxTokenLength; $x++) {
                    if (isset($chars[($i + $x)]) === false) {
                        break;
                    }

                    $charBuffer .= $chars[($i + $x)];

                    if ($this->getVerbose() > 1) {
                        $content = str_replace("\n", '\n', $charBuffer);
                        echo "\t\t=> Looking ahead $x chars => $content".PHP_EOL;
                    }

                    if (in_array(strtolower($charBuffer), $tokenTypes) === true) {
                        // We've found something larger that matches
                        // so we can ignore this char.
                        if ($this->getVerbose() > 1) {
                            $type = $this->tokenValues[strtolower($charBuffer)];
                            echo "\t\t* look ahead found more specific token ($type), ignoring $i *".PHP_EOL;
                        }

                        $matchedToken = true;
                        break;
                    }
                }//end for

                if ($matchedToken === false) {
                    $value    = $this->tokenValues[strtolower($char)];
                    $tokens[] = array(
                                 'code'    => constant($value),
                                 'type'    => $value,
                                 'content' => $char,
                                );

                    if ($this->getVerbose() > 1) {
                        echo "\t\t* look ahead found nothing *".PHP_EOL;
                        $content = str_replace("\n", '\n', $char);
                        echo "\t=> Added token $value ($content)".PHP_EOL;
                    }

                    $cleanBuffer = true;
                } else {
                    $buffer = $char;
                }
            }//end if

            // Keep track of content inside comments.
            if ($inComment === ''
                && array_key_exists($buffer, $this->commentTokens) === true
            ) {
                // This is not really a comment if the content
                // looks like \// (i.e., it is escaped).
                if (isset($chars[($i - 2)]) === true && $chars[($i - 2)] === '\\') {
                    $lastToken   = array_pop($tokens);
                    $lastContent = $lastToken['content'];
                    if ($this->getVerbose() > 1) {
                        $value   = $this->tokenValues[strtolower($lastContent)];
                        $content = str_replace("\n", '\n', $lastContent);
                        echo "\t=> Removed token $value ($content)".PHP_EOL;
                    }

                    $lastChars    = str_split($lastContent);
                    $lastNumChars = count($lastChars);
                    for ($x = 0; $x < $lastNumChars; $x++) {
                        $lastChar = $lastChars[$x];
                        $value    = $this->tokenValues[strtolower($lastChar)];
                        $tokens[] = array(
                                     'code'    => constant($value),
                                     'type'    => $value,
                                     'content' => $lastChar,
                                    );

                        if ($this->getVerbose() > 1) {
                            $content = str_replace("\n", '\n', $lastChar);
                            echo "\t=> Added token $value ($content)".PHP_EOL;
                        }
                    }
                } else {
                    // We have started a comment.
                    $inComment = $buffer;

                    if ($this->getVerbose() > 1) {
                        echo "\t\t* looking for end of comment *".PHP_EOL;
                    }
                }
            } else if ($inComment !== '') {
                if ($this->commentTokens[$inComment] === null) {
                    // Comment ends at the next newline.
                    if (strpos($buffer, "\n") !== false) {
                        $inComment = '';
                    }
                } else {
                    if ($this->commentTokens[$inComment] === $buffer) {
                        $inComment = '';
                    }
                }

                if ($this->getVerbose() > 1) {
                    if ($inComment === '') {
                        echo "\t\t* found end of comment *".PHP_EOL;
                    }
                }

                if ($inComment === '' && $cleanBuffer === false) {
                    $tokens[] = array(
                                 'code'    => T_STRING,
                                 'type'    => 'T_STRING',
                                 'content' => str_replace("\n", $eolChar, $buffer),
                                );

                    if ($this->getVerbose() > 1) {
                        $content = str_replace("\n", '\n', $buffer);
                        echo "\t=> Added token T_STRING ($content)".PHP_EOL;
                    }

                    $buffer = '';
                }
            }//end if

            if ($cleanBuffer === true) {
                $buffer      = '';
                $cleanBuffer = false;
            }
        }//end foreach

        if (empty($buffer) === false) {
            // Buffer contains whitespace from the end of the file.
            $tokens[] = array(
                         'code'    => T_WHITESPACE,
                         'type'    => 'T_WHITESPACE',
                         'content' => str_replace("\n", $eolChar, $buffer),
                        );

            if ($this->getVerbose() > 1) {
                $content = str_replace($eolChar, '\n', $buffer);
                echo "\t=> Added token T_WHITESPACE ($content)".PHP_EOL;
            }
        }

        $tokens[] = array(
                     'code'    => T_CLOSE_TAG,
                     'type'    => 'T_CLOSE_TAG',
                     'content' => '',
                    );

        /*
            Now that we have done some basic tokenizing, we need to
            modify the tokens to join some together and split some apart
            so they match what the PHP tokenizer does.
        */

        $finalTokens = array();
        $newStackPtr = 0;
        $numTokens   = count($tokens);
        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token = $tokens[$stackPtr];

            /*
                Look for comments and join the tokens together.
            */

            if (array_key_exists($token['content'], $this->commentTokens) === true) {
                $newContent   = '';
                $tokenContent = $token['content'];
                $endContent   = $this->commentTokens[$tokenContent];
                while ($tokenContent !== $endContent) {
                    if ($endContent === null
                        && strpos($tokenContent, $eolChar) !== false
                    ) {
                        // A null end token means the comment ends at the end of
                        // the line so we look for newlines and split the token.
                        $tokens[$stackPtr]['content'] = substr(
                            $tokenContent,
                            (strpos($tokenContent, $eolChar) + strlen($eolChar))
                        );

                        $tokenContent = substr(
                            $tokenContent,
                            0,
                            (strpos($tokenContent, $eolChar) + strlen($eolChar))
                        );

                        // If the substr failed, skip the token as the content
                        // will now be blank.
                        if ($tokens[$stackPtr]['content'] !== false) {
                            $stackPtr--;
                        }

                        break;
                    }//end if

                    $stackPtr++;
                    $newContent  .= $tokenContent;
                    if (isset($tokens[$stackPtr]) === false) {
                        break;
                    }

                    $tokenContent = $tokens[$stackPtr]['content'];
                }//end while

                // Save the new content in the current token so
                // the code below can chop it up on newlines.
                $token['content'] = $newContent.$tokenContent;
            }//end if

            /*
                If this token has newlines in its content, split each line up
                and create a new token for each line. We do this so it's easier
                to ascertain where errors occur on a line.
                Note that $token[1] is the token's content.
            */

            if (strpos($token['content'], $eolChar) !== false) {
                $tokenLines = explode($eolChar, $token['content']);
                $numLines   = count($tokenLines);

                for ($i = 0; $i < $numLines; $i++) {
                    $newToken['content'] = $tokenLines[$i];
                    if ($i === ($numLines - 1)) {
                        if ($tokenLines[$i] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $eolChar;
                    }

                    $newToken['type']          = $token['type'];
                    $newToken['code']          = $token['code'];
                    $finalTokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                }
            } else {
                $finalTokens[$newStackPtr] = $token;
                $newStackPtr++;
            }//end if

            // Convert numbers, including decimals.
            if ($token['code'] === T_STRING
                || $token['code'] === T_OBJECT_OPERATOR
            ) {
                $newContent  = '';
                $oldStackPtr = $stackPtr;
                while (preg_match('|^[0-9\.]+$|', $tokens[$stackPtr]['content']) !== 0) {
                    $newContent .= $tokens[$stackPtr]['content'];
                    $stackPtr++;
                }

                if ($newContent !== '' && $newContent !== '.') {
                    $finalTokens[($newStackPtr - 1)]['content'] = $newContent;
                    if (ctype_digit($newContent) === true) {
                        $finalTokens[($newStackPtr - 1)]['code']
                            = constant('T_LNUMBER');
                        $finalTokens[($newStackPtr - 1)]['type'] = 'T_LNUMBER';
                    } else {
                        $finalTokens[($newStackPtr - 1)]['code']
                            = constant('T_DNUMBER');
                        $finalTokens[($newStackPtr - 1)]['type'] = 'T_DNUMBER';
                    }

                    $stackPtr--;
                } else {
                    $stackPtr = $oldStackPtr;
                }
            }//end if
        }//end for

        if ($this->getVerbose() > 1) {
            echo "\t*** END SQL TOKENIZING ***".PHP_EOL;
        }

        return $finalTokens;

    }//end tokenize()


    /**
     * Performs additional processing after main tokenizing.
     *
     * This additional processing looks for properties, labels and objects.
     *
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     */
    protected function processAdditional(&$tokens, $eolChar)
    {

    }//end processAdditional()


}//end class

?>
