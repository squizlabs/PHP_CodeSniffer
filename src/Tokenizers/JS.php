<?php
/**
 * Tokenizes JS code.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tokenizers;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\TokenizerException;
use PHP_CodeSniffer\Util;

class JS extends Tokenizer
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
    public $scopeOpeners = [
        T_IF       => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => false,
            'shared' => false,
            'with'   => [],
        ],
        T_TRY      => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_CATCH    => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_ELSE     => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => false,
            'shared' => false,
            'with'   => [],
        ],
        T_FOR      => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => false,
            'shared' => false,
            'with'   => [],
        ],
        T_CLASS    => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_FUNCTION => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => false,
            'shared' => false,
            'with'   => [],
        ],
        T_WHILE    => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => false,
            'shared' => false,
            'with'   => [],
        ],
        T_DO       => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_SWITCH   => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_CASE     => [
            'start'  => [T_COLON => T_COLON],
            'end'    => [
                T_BREAK    => T_BREAK,
                T_RETURN   => T_RETURN,
                T_CONTINUE => T_CONTINUE,
                T_THROW    => T_THROW,
            ],
            'strict' => true,
            'shared' => true,
            'with'   => [
                T_DEFAULT => T_DEFAULT,
                T_CASE    => T_CASE,
                T_SWITCH  => T_SWITCH,
            ],
        ],
        T_DEFAULT  => [
            'start'  => [T_COLON => T_COLON],
            'end'    => [
                T_BREAK    => T_BREAK,
                T_RETURN   => T_RETURN,
                T_CONTINUE => T_CONTINUE,
                T_THROW    => T_THROW,
            ],
            'strict' => true,
            'shared' => true,
            'with'   => [
                T_CASE   => T_CASE,
                T_SWITCH => T_SWITCH,
            ],
        ],
    ];

    /**
     * A list of tokens that end the scope.
     *
     * This array is just a unique collection of the end tokens
     * from the _scopeOpeners array. The data is duplicated here to
     * save time during parsing of the file.
     *
     * @var array
     */
    public $endScopeTokens = [
        T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
        T_BREAK               => T_BREAK,
    ];

    /**
     * A list of special JS tokens and their types.
     *
     * @var array
     */
    protected $tokenValues = [
        'class'     => 'T_CLASS',
        'function'  => 'T_FUNCTION',
        'prototype' => 'T_PROTOTYPE',
        'try'       => 'T_TRY',
        'catch'     => 'T_CATCH',
        'return'    => 'T_RETURN',
        'throw'     => 'T_THROW',
        'break'     => 'T_BREAK',
        'switch'    => 'T_SWITCH',
        'continue'  => 'T_CONTINUE',
        'if'        => 'T_IF',
        'else'      => 'T_ELSE',
        'do'        => 'T_DO',
        'while'     => 'T_WHILE',
        'for'       => 'T_FOR',
        'var'       => 'T_VAR',
        'case'      => 'T_CASE',
        'default'   => 'T_DEFAULT',
        'true'      => 'T_TRUE',
        'false'     => 'T_FALSE',
        'null'      => 'T_NULL',
        'this'      => 'T_THIS',
        'typeof'    => 'T_TYPEOF',
        '('         => 'T_OPEN_PARENTHESIS',
        ')'         => 'T_CLOSE_PARENTHESIS',
        '{'         => 'T_OPEN_CURLY_BRACKET',
        '}'         => 'T_CLOSE_CURLY_BRACKET',
        '['         => 'T_OPEN_SQUARE_BRACKET',
        ']'         => 'T_CLOSE_SQUARE_BRACKET',
        '?'         => 'T_INLINE_THEN',
        '.'         => 'T_OBJECT_OPERATOR',
        '+'         => 'T_PLUS',
        '-'         => 'T_MINUS',
        '*'         => 'T_MULTIPLY',
        '%'         => 'T_MODULUS',
        '/'         => 'T_DIVIDE',
        '^'         => 'T_LOGICAL_XOR',
        ','         => 'T_COMMA',
        ';'         => 'T_SEMICOLON',
        ':'         => 'T_COLON',
        '<'         => 'T_LESS_THAN',
        '>'         => 'T_GREATER_THAN',
        '<<'        => 'T_SL',
        '>>'        => 'T_SR',
        '>>>'       => 'T_ZSR',
        '<<='       => 'T_SL_EQUAL',
        '>>='       => 'T_SR_EQUAL',
        '>>>='      => 'T_ZSR_EQUAL',
        '<='        => 'T_IS_SMALLER_OR_EQUAL',
        '>='        => 'T_IS_GREATER_OR_EQUAL',
        '=>'        => 'T_DOUBLE_ARROW',
        '!'         => 'T_BOOLEAN_NOT',
        '||'        => 'T_BOOLEAN_OR',
        '&&'        => 'T_BOOLEAN_AND',
        '|'         => 'T_BITWISE_OR',
        '&'         => 'T_BITWISE_AND',
        '!='        => 'T_IS_NOT_EQUAL',
        '!=='       => 'T_IS_NOT_IDENTICAL',
        '='         => 'T_EQUAL',
        '=='        => 'T_IS_EQUAL',
        '==='       => 'T_IS_IDENTICAL',
        '-='        => 'T_MINUS_EQUAL',
        '+='        => 'T_PLUS_EQUAL',
        '*='        => 'T_MUL_EQUAL',
        '/='        => 'T_DIV_EQUAL',
        '%='        => 'T_MOD_EQUAL',
        '++'        => 'T_INC',
        '--'        => 'T_DEC',
        '//'        => 'T_COMMENT',
        '/*'        => 'T_COMMENT',
        '/**'       => 'T_DOC_COMMENT',
        '*/'        => 'T_COMMENT',
    ];

    /**
     * A list string delimiters.
     *
     * @var array
     */
    protected $stringTokens = [
        '\'' => '\'',
        '"'  => '"',
    ];

    /**
     * A list tokens that start and end comments.
     *
     * @var array
     */
    protected $commentTokens = [
        '//'  => null,
        '/*'  => '*/',
        '/**' => '*/',
    ];


    /**
     * Initialise the tokenizer.
     *
     * Pre-checks the content to see if it looks minified.
     *
     * @param string                  $content The content to tokenize,
     * @param \PHP_CodeSniffer\Config $config  The config data for the run.
     * @param string                  $eolChar The EOL char used in the content.
     *
     * @return void
     * @throws \PHP_CodeSniffer\Exceptions\TokenizerException If the file appears to be minified.
     */
    public function __construct($content, Config $config, $eolChar='\n')
    {
        if ($this->isMinifiedContent($content, $eolChar) === true) {
            throw new TokenizerException('File appears to be minified and cannot be processed');
        }

        parent::__construct($content, $config, $eolChar);

    }//end __construct()


    /**
     * Creates an array of tokens when given some JS code.
     *
     * @param string $string The string to tokenize.
     *
     * @return array
     */
    public function tokenize($string)
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START JS TOKENIZING ***".PHP_EOL;
        }

        $maxTokenLength = 0;
        foreach ($this->tokenValues as $token => $values) {
            if (strlen($token) > $maxTokenLength) {
                $maxTokenLength = strlen($token);
            }
        }

        $tokens          = [];
        $inString        = '';
        $stringChar      = null;
        $inComment       = '';
        $buffer          = '';
        $preStringBuffer = '';
        $cleanBuffer     = false;

        $commentTokenizer = new Comment();

        $tokens[] = [
            'code'    => T_OPEN_TAG,
            'type'    => 'T_OPEN_TAG',
            'content' => '',
        ];

        // Convert newlines to single characters for ease of
        // processing. We will change them back later.
        $string = str_replace($this->eolChar, "\n", $string);

        $chars    = str_split($string);
        $numChars = count($chars);
        for ($i = 0; $i < $numChars; $i++) {
            $char = $chars[$i];

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $content       = Util\Common::prepareForOutput($char);
                $bufferContent = Util\Common::prepareForOutput($buffer);

                if ($inString !== '') {
                    echo "\t";
                }

                if ($inComment !== '') {
                    echo "\t";
                }

                echo "\tProcess char $i => $content (buffer: $bufferContent)".PHP_EOL;
            }//end if

            if ($inString === '' && $inComment === '' && $buffer !== '') {
                // If the buffer only has whitespace and we are about to
                // add a character, store the whitespace first.
                if (trim($char) !== '' && trim($buffer) === '') {
                    $tokens[] = [
                        'code'    => T_WHITESPACE,
                        'type'    => 'T_WHITESPACE',
                        'content' => str_replace("\n", $this->eolChar, $buffer),
                    ];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $content = Util\Common::prepareForOutput($buffer);
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
                    $tokens[] = [
                        'code'    => T_STRING,
                        'type'    => 'T_STRING',
                        'content' => str_replace("\n", $this->eolChar, $buffer),
                    ];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $content = Util\Common::prepareForOutput($buffer);
                        echo "\t=> Added token T_STRING ($content)".PHP_EOL;
                    }

                    $buffer = '';
                }
            }//end if

            // Process strings.
            if ($inComment === '' && isset($this->stringTokens[$char]) === true) {
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
                        $tokens[] = [
                            'code'    => T_CONSTANT_ENCAPSED_STRING,
                            'type'    => 'T_CONSTANT_ENCAPSED_STRING',
                            'content' => str_replace("\n", $this->eolChar, $buffer).$char,
                        ];

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t\t* found end of string *".PHP_EOL;
                            $content = Util\Common::prepareForOutput($buffer.$char);
                            echo "\t=> Added token T_CONSTANT_ENCAPSED_STRING ($content)".PHP_EOL;
                        }

                        $buffer          = '';
                        $preStringBuffer = '';
                        $inString        = '';
                        $stringChar      = null;
                        continue;
                    }//end if
                } else if ($inString === '') {
                    $inString        = $char;
                    $stringChar      = $i;
                    $preStringBuffer = $buffer;

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* looking for string closer *".PHP_EOL;
                    }
                }//end if
            }//end if

            if ($inString !== '' && $char === "\n") {
                // Unless this newline character is escaped, the string did not
                // end before the end of the line, which means it probably
                // wasn't a string at all (maybe a regex).
                if ($chars[($i - 1)] !== '\\') {
                    $i      = $stringChar;
                    $buffer = $preStringBuffer;
                    $preStringBuffer = '';
                    $inString        = '';
                    $stringChar      = null;
                    $char            = $chars[$i];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* found newline before end of string, bailing *".PHP_EOL;
                    }
                }
            }

            $buffer .= $char;

            // We don't look for special tokens inside strings,
            // so if we are in a string, we can continue here now
            // that the current char is in the buffer.
            if ($inString !== '') {
                continue;
            }

            // Special case for T_DIVIDE which can actually be
            // the start of a regular expression.
            if ($buffer === $char && $char === '/' && $chars[($i + 1)] !== '*') {
                $regex = $this->getRegexToken($i, $string, $chars, $tokens);
                if ($regex !== null) {
                    $tokens[] = [
                        'code'    => T_REGULAR_EXPRESSION,
                        'type'    => 'T_REGULAR_EXPRESSION',
                        'content' => $regex['content'],
                    ];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $content = Util\Common::prepareForOutput($regex['content']);
                        echo "\t=> Added token T_REGULAR_EXPRESSION ($content)".PHP_EOL;
                    }

                    $i           = $regex['end'];
                    $buffer      = '';
                    $cleanBuffer = false;
                    continue;
                }//end if
            }//end if

            // Check for known tokens, but ignore tokens found that are not at
            // the end of a string, like FOR and this.FORmat.
            if (isset($this->tokenValues[strtolower($buffer)]) === true
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
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* buffer possibly contains token, looking ahead $lookAheadLength chars *".PHP_EOL;
                    }

                    $charBuffer = $buffer;
                    for ($x = 1; $x <= $lookAheadLength; $x++) {
                        if (isset($chars[($i + $x)]) === false) {
                            break;
                        }

                        $charBuffer .= $chars[($i + $x)];

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $content = Util\Common::prepareForOutput($charBuffer);
                            echo "\t\t=> Looking ahead $x chars => $content".PHP_EOL;
                        }

                        if (isset($this->tokenValues[strtolower($charBuffer)]) === true) {
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
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    echo "\t\t* look ahead ignored T_DOC_COMMENT, continuing *".PHP_EOL;
                                }
                            } else {
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    echo "\t\t* look ahead found more specific token ($newType), ignoring $i *".PHP_EOL;
                                }

                                $matchedToken = true;
                                break;
                            }
                        }//end if
                    }//end for
                }//end if

                if ($matchedToken === false) {
                    if (PHP_CODESNIFFER_VERBOSITY > 1 && $lookAheadLength > 0) {
                        echo "\t\t* look ahead found nothing *".PHP_EOL;
                    }

                    $value = $this->tokenValues[strtolower($buffer)];

                    if ($value === 'T_FUNCTION' && $buffer !== 'function') {
                        // The function keyword needs to be all lowercase or else
                        // it is just a function called "Function".
                        $value = 'T_STRING';
                    }

                    $tokens[] = [
                        'code'    => constant($value),
                        'type'    => $value,
                        'content' => $buffer,
                    ];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $content = Util\Common::prepareForOutput($buffer);
                        echo "\t=> Added token $value ($content)".PHP_EOL;
                    }

                    $cleanBuffer = true;
                }//end if
            } else if (isset($this->tokenValues[strtolower($char)]) === true) {
                // No matter what token we end up using, we don't
                // need the content in the buffer any more because we have
                // found a valid token.
                $newContent = substr(str_replace("\n", $this->eolChar, $buffer), 0, -1);
                if ($newContent !== '') {
                    $tokens[] = [
                        'code'    => T_STRING,
                        'type'    => 'T_STRING',
                        'content' => $newContent,
                    ];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $content = Util\Common::prepareForOutput(substr($buffer, 0, -1));
                        echo "\t=> Added token T_STRING ($content)".PHP_EOL;
                    }
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
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

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $content = Util\Common::prepareForOutput($charBuffer);
                        echo "\t\t=> Looking ahead $x chars => $content".PHP_EOL;
                    }

                    if (isset($this->tokenValues[strtolower($charBuffer)]) === true) {
                        // We've found something larger that matches
                        // so we can ignore this char.
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = $this->tokenValues[strtolower($charBuffer)];
                            echo "\t\t* look ahead found more specific token ($type), ignoring $i *".PHP_EOL;
                        }

                        $matchedToken = true;
                        break;
                    }
                }//end for

                if ($matchedToken === false) {
                    $value    = $this->tokenValues[strtolower($char)];
                    $tokens[] = [
                        'code'    => constant($value),
                        'type'    => $value,
                        'content' => $char,
                    ];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* look ahead found nothing *".PHP_EOL;
                        $content = Util\Common::prepareForOutput($char);
                        echo "\t=> Added token $value ($content)".PHP_EOL;
                    }

                    $cleanBuffer = true;
                } else {
                    $buffer = $char;
                }//end if
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
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $value   = $this->tokenValues[strtolower($lastContent)];
                        $content = Util\Common::prepareForOutput($lastContent);
                        echo "\t=> Removed token $value ($content)".PHP_EOL;
                    }

                    $lastChars    = str_split($lastContent);
                    $lastNumChars = count($lastChars);
                    for ($x = 0; $x < $lastNumChars; $x++) {
                        $lastChar = $lastChars[$x];
                        $value    = $this->tokenValues[strtolower($lastChar)];
                        $tokens[] = [
                            'code'    => constant($value),
                            'type'    => $value,
                            'content' => $lastChar,
                        ];

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $content = Util\Common::prepareForOutput($lastChar);
                            echo "\t=> Added token $value ($content)".PHP_EOL;
                        }
                    }
                } else {
                    // We have started a comment.
                    $inComment = $buffer;

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* looking for end of comment *".PHP_EOL;
                    }
                }//end if
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

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    if ($inComment === '') {
                        echo "\t\t* found end of comment *".PHP_EOL;
                    }
                }

                if ($inComment === '' && $cleanBuffer === false) {
                    $tokens[] = [
                        'code'    => T_STRING,
                        'type'    => 'T_STRING',
                        'content' => str_replace("\n", $this->eolChar, $buffer),
                    ];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $content = Util\Common::prepareForOutput($buffer);
                        echo "\t=> Added token T_STRING ($content)".PHP_EOL;
                    }

                    $buffer = '';
                }
            }//end if

            if ($cleanBuffer === true) {
                $buffer      = '';
                $cleanBuffer = false;
            }
        }//end for

        if (empty($buffer) === false) {
            if ($inString !== '') {
                // The string did not end before the end of the file,
                // which means there was probably a syntax error somewhere.
                $tokens[] = [
                    'code'    => T_STRING,
                    'type'    => 'T_STRING',
                    'content' => str_replace("\n", $this->eolChar, $buffer),
                ];

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $content = Util\Common::prepareForOutput($buffer);
                    echo "\t=> Added token T_STRING ($content)".PHP_EOL;
                }
            } else {
                // Buffer contains whitespace from the end of the file.
                $tokens[] = [
                    'code'    => T_WHITESPACE,
                    'type'    => 'T_WHITESPACE',
                    'content' => str_replace("\n", $this->eolChar, $buffer),
                ];

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $content = Util\Common::prepareForOutput($buffer);
                    echo "\t=> Added token T_WHITESPACE ($content)".PHP_EOL;
                }
            }//end if
        }//end if

        $tokens[] = [
            'code'    => T_CLOSE_TAG,
            'type'    => 'T_CLOSE_TAG',
            'content' => '',
        ];

        /*
            Now that we have done some basic tokenizing, we need to
            modify the tokens to join some together and split some apart
            so they match what the PHP tokenizer does.
        */

        $finalTokens = [];
        $newStackPtr = 0;
        $numTokens   = count($tokens);
        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token = $tokens[$stackPtr];

            /*
                Look for comments and join the tokens together.
            */

            if ($token['code'] === T_COMMENT || $token['code'] === T_DOC_COMMENT) {
                $newContent   = '';
                $tokenContent = $token['content'];

                $endContent = null;
                if (isset($this->commentTokens[$tokenContent]) === true) {
                    $endContent = $this->commentTokens[$tokenContent];
                }

                while ($tokenContent !== $endContent) {
                    if ($endContent === null
                        && strpos($tokenContent, $this->eolChar) !== false
                    ) {
                        // A null end token means the comment ends at the end of
                        // the line so we look for newlines and split the token.
                        $tokens[$stackPtr]['content'] = substr(
                            $tokenContent,
                            (strpos($tokenContent, $this->eolChar) + strlen($this->eolChar))
                        );

                        $tokenContent = substr(
                            $tokenContent,
                            0,
                            (strpos($tokenContent, $this->eolChar) + strlen($this->eolChar))
                        );

                        // If the substr failed, skip the token as the content
                        // will now be blank.
                        if ($tokens[$stackPtr]['content'] !== false
                            && $tokens[$stackPtr]['content'] !== ''
                        ) {
                            $stackPtr--;
                        }

                        break;
                    }//end if

                    $stackPtr++;
                    $newContent .= $tokenContent;
                    if (isset($tokens[$stackPtr]) === false) {
                        break;
                    }

                    $tokenContent = $tokens[$stackPtr]['content'];
                }//end while

                if ($token['code'] === T_DOC_COMMENT) {
                    $commentTokens = $commentTokenizer->tokenizeString($newContent.$tokenContent, $this->eolChar, $newStackPtr);
                    foreach ($commentTokens as $commentToken) {
                        $finalTokens[$newStackPtr] = $commentToken;
                        $newStackPtr++;
                    }

                    continue;
                } else {
                    // Save the new content in the current token so
                    // the code below can chop it up on newlines.
                    $token['content'] = $newContent.$tokenContent;
                }
            }//end if

            /*
                If this token has newlines in its content, split each line up
                and create a new token for each line. We do this so it's easier
                to ascertain where errors occur on a line.
                Note that $token[1] is the token's content.
            */

            if (strpos($token['content'], $this->eolChar) !== false) {
                $tokenLines = explode($this->eolChar, $token['content']);
                $numLines   = count($tokenLines);

                for ($i = 0; $i < $numLines; $i++) {
                    $newToken = ['content' => $tokenLines[$i]];
                    if ($i === ($numLines - 1)) {
                        if ($tokenLines[$i] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $this->eolChar;
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
                        $finalTokens[($newStackPtr - 1)]['code'] = constant('T_LNUMBER');
                        $finalTokens[($newStackPtr - 1)]['type'] = 'T_LNUMBER';
                    } else {
                        $finalTokens[($newStackPtr - 1)]['code'] = constant('T_DNUMBER');
                        $finalTokens[($newStackPtr - 1)]['type'] = 'T_DNUMBER';
                    }

                    $stackPtr--;
                    continue;
                } else {
                    $stackPtr = $oldStackPtr;
                }
            }//end if

            // Convert the token after an object operator into a string, in most cases.
            if ($token['code'] === T_OBJECT_OPERATOR) {
                for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                    if (isset(Util\Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                        continue;
                    }

                    if ($tokens[$i]['code'] !== T_PROTOTYPE
                        && $tokens[$i]['code'] !== T_LNUMBER
                        && $tokens[$i]['code'] !== T_DNUMBER
                    ) {
                        $tokens[$i]['code'] = T_STRING;
                        $tokens[$i]['type'] = 'T_STRING';
                    }

                    break;
                }
            }
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END TOKENIZING ***".PHP_EOL;
        }

        return $finalTokens;

    }//end tokenize()


    /**
     * Tokenizes a regular expression if one is found.
     *
     * If a regular expression is not found, NULL is returned.
     *
     * @param string $char   The index of the possible regex start character.
     * @param string $string The complete content of the string being tokenized.
     * @param string $chars  An array of characters being tokenized.
     * @param string $tokens The current array of tokens found in the string.
     *
     * @return array<string, string>|null
     */
    public function getRegexToken($char, $string, $chars, $tokens)
    {
        $beforeTokens = [
            T_EQUAL               => true,
            T_IS_NOT_EQUAL        => true,
            T_IS_IDENTICAL        => true,
            T_IS_NOT_IDENTICAL    => true,
            T_OPEN_PARENTHESIS    => true,
            T_OPEN_SQUARE_BRACKET => true,
            T_RETURN              => true,
            T_BOOLEAN_OR          => true,
            T_BOOLEAN_AND         => true,
            T_BOOLEAN_NOT         => true,
            T_BITWISE_OR          => true,
            T_BITWISE_AND         => true,
            T_COMMA               => true,
            T_COLON               => true,
            T_TYPEOF              => true,
            T_INLINE_THEN         => true,
            T_INLINE_ELSE         => true,
        ];

        $afterTokens = [
            ','            => true,
            ')'            => true,
            ']'            => true,
            ';'            => true,
            ' '            => true,
            '.'            => true,
            ':'            => true,
            $this->eolChar => true,
        ];

        // Find the last non-whitespace token that was added
        // to the tokens array.
        $numTokens = count($tokens);
        for ($prev = ($numTokens - 1); $prev >= 0; $prev--) {
            if (isset(Util\Tokens::$emptyTokens[$tokens[$prev]['code']]) === false) {
                break;
            }
        }

        if (isset($beforeTokens[$tokens[$prev]['code']]) === false) {
            return null;
        }

        // This is probably a regular expression, so look for the end of it.
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t* token possibly starts a regular expression *".PHP_EOL;
        }

        $numChars = count($chars);
        for ($next = ($char + 1); $next < $numChars; $next++) {
            if ($chars[$next] === '/') {
                // Just make sure this is not escaped first.
                if ($chars[($next - 1)] !== '\\') {
                    // In the simple form: /.../ so we found the end.
                    break;
                } else if ($chars[($next - 2)] === '\\') {
                    // In the form: /...\\/ so we found the end.
                    break;
                }
            } else {
                $possibleEolChar = substr($string, $next, strlen($this->eolChar));
                if ($possibleEolChar === $this->eolChar) {
                    // This is the last token on the line and regular
                    // expressions need to be defined on a single line,
                    // so this is not a regular expression.
                    break;
                }
            }
        }

        if ($chars[$next] !== '/') {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo "\t* could not find end of regular expression *".PHP_EOL;
            }

            return null;
        }

        while (preg_match('|[a-zA-Z]|', $chars[($next + 1)]) !== 0) {
            // The token directly after the end of the regex can
            // be modifiers like global and case insensitive
            // (.e.g, /pattern/gi).
            $next++;
        }

        $regexEnd = $next;
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t* found end of regular expression at token $regexEnd *".PHP_EOL;
        }

        for ($next += 1; $next < $numChars; $next++) {
            if ($chars[$next] !== ' ') {
                break;
            } else {
                $possibleEolChar = substr($string, $next, strlen($this->eolChar));
                if ($possibleEolChar === $this->eolChar) {
                    // This is the last token on the line.
                    break;
                }
            }
        }

        if (isset($afterTokens[$chars[$next]]) === false) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo "\t* tokens after regular expression do not look correct *".PHP_EOL;
            }

            return null;
        }

        // This is a regular expression, so join all the tokens together.
        $content = '';
        for ($x = $char; $x <= $regexEnd; $x++) {
            $content .= $chars[$x];
        }

        $token = [
            'start'   => $char,
            'end'     => $regexEnd,
            'content' => $content,
        ];

        return $token;

    }//end getRegexToken()


    /**
     * Performs additional processing after main tokenizing.
     *
     * This additional processing looks for properties, closures, labels and objects.
     *
     * @return void
     */
    public function processAdditional()
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START ADDITIONAL JS PROCESSING ***".PHP_EOL;
        }

        $numTokens  = count($this->tokens);
        $classStack = [];

        for ($i = 0; $i < $numTokens; $i++) {
            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type    = $this->tokens[$i]['type'];
                $content = Util\Common::prepareForOutput($this->tokens[$i]['content']);

                echo str_repeat("\t", count($classStack));
                echo "\tProcess token $i: $type => $content".PHP_EOL;
            }

            // Looking for functions that are actually closures.
            if ($this->tokens[$i]['code'] === T_FUNCTION && isset($this->tokens[$i]['scope_opener']) === true) {
                for ($x = ($i + 1); $x < $numTokens; $x++) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false) {
                        break;
                    }
                }

                if ($this->tokens[$x]['code'] === T_OPEN_PARENTHESIS) {
                    $this->tokens[$i]['code'] = T_CLOSURE;
                    $this->tokens[$i]['type'] = 'T_CLOSURE';
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $line = $this->tokens[$i]['line'];
                        echo str_repeat("\t", count($classStack));
                        echo "\t* token $i on line $line changed from T_FUNCTION to T_CLOSURE *".PHP_EOL;
                    }

                    for ($x = ($this->tokens[$i]['scope_opener'] + 1); $x < $this->tokens[$i]['scope_closer']; $x++) {
                        if (isset($this->tokens[$x]['conditions'][$i]) === false) {
                            continue;
                        }

                        $this->tokens[$x]['conditions'][$i] = T_CLOSURE;
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = $this->tokens[$x]['type'];
                            echo str_repeat("\t", count($classStack));
                            echo "\t\t* cleaned $x ($type) *".PHP_EOL;
                        }
                    }
                }//end if

                continue;
            } else if ($this->tokens[$i]['code'] === T_OPEN_CURLY_BRACKET
                && isset($this->tokens[$i]['scope_condition']) === false
                && isset($this->tokens[$i]['bracket_closer']) === true
            ) {
                $condition = $this->tokens[$i]['conditions'];
                $condition = end($condition);
                if ($condition === T_CLASS) {
                    // Possibly an ES6 method. To be classified as one, the previous
                    // non-empty tokens need to be a set of parenthesis, and then a string
                    // (the method name).
                    for ($parenCloser = ($i - 1); $parenCloser > 0; $parenCloser--) {
                        if (isset(Util\Tokens::$emptyTokens[$this->tokens[$parenCloser]['code']]) === false) {
                            break;
                        }
                    }

                    if ($this->tokens[$parenCloser]['code'] === T_CLOSE_PARENTHESIS) {
                        $parenOpener = $this->tokens[$parenCloser]['parenthesis_opener'];
                        for ($name = ($parenOpener - 1); $name > 0; $name--) {
                            if (isset(Util\Tokens::$emptyTokens[$this->tokens[$name]['code']]) === false) {
                                break;
                            }
                        }

                        if ($this->tokens[$name]['code'] === T_STRING) {
                            // We found a method name.
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $line = $this->tokens[$name]['line'];
                                echo str_repeat("\t", count($classStack));
                                echo "\t* token $name on line $line changed from T_STRING to T_FUNCTION *".PHP_EOL;
                            }

                            $closer = $this->tokens[$i]['bracket_closer'];

                            $this->tokens[$name]['code'] = T_FUNCTION;
                            $this->tokens[$name]['type'] = 'T_FUNCTION';

                            foreach ([$name, $i, $closer] as $token) {
                                $this->tokens[$token]['scope_condition']    = $name;
                                $this->tokens[$token]['scope_opener']       = $i;
                                $this->tokens[$token]['scope_closer']       = $closer;
                                $this->tokens[$token]['parenthesis_opener'] = $parenOpener;
                                $this->tokens[$token]['parenthesis_closer'] = $parenCloser;
                                $this->tokens[$token]['parenthesis_owner']  = $name;
                            }

                            $this->tokens[$parenOpener]['parenthesis_owner'] = $name;
                            $this->tokens[$parenCloser]['parenthesis_owner'] = $name;

                            for ($x = ($i + 1); $x < $closer; $x++) {
                                $this->tokens[$x]['conditions'][$name] = T_FUNCTION;
                                ksort($this->tokens[$x]['conditions'], SORT_NUMERIC);
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    $type = $this->tokens[$x]['type'];
                                    echo str_repeat("\t", count($classStack));
                                    echo "\t\t* added T_FUNCTION condition to $x ($type) *".PHP_EOL;
                                }
                            }

                            continue;
                        }//end if
                    }//end if
                }//end if

                $classStack[] = $i;

                $closer = $this->tokens[$i]['bracket_closer'];
                $this->tokens[$i]['code']      = T_OBJECT;
                $this->tokens[$i]['type']      = 'T_OBJECT';
                $this->tokens[$closer]['code'] = T_CLOSE_OBJECT;
                $this->tokens[$closer]['type'] = 'T_CLOSE_OBJECT';

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo str_repeat("\t", count($classStack));
                    echo "\t* token $i converted from T_OPEN_CURLY_BRACKET to T_OBJECT *".PHP_EOL;
                    echo str_repeat("\t", count($classStack));
                    echo "\t* token $closer converted from T_CLOSE_CURLY_BRACKET to T_CLOSE_OBJECT *".PHP_EOL;
                }

                for ($x = ($i + 1); $x < $closer; $x++) {
                    $this->tokens[$x]['conditions'][$i] = T_OBJECT;
                    ksort($this->tokens[$x]['conditions'], SORT_NUMERIC);
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $this->tokens[$x]['type'];
                        echo str_repeat("\t", count($classStack));
                        echo "\t\t* added T_OBJECT condition to $x ($type) *".PHP_EOL;
                    }
                }
            } else if ($this->tokens[$i]['code'] === T_CLOSE_OBJECT) {
                $opener = array_pop($classStack);
            } else if ($this->tokens[$i]['code'] === T_COLON) {
                // If it is a scope opener, it belongs to a
                // DEFAULT or CASE statement.
                if (isset($this->tokens[$i]['scope_condition']) === true) {
                    continue;
                }

                // Make sure this is not part of an inline IF statement.
                for ($x = ($i - 1); $x >= 0; $x--) {
                    if ($this->tokens[$x]['code'] === T_INLINE_THEN) {
                        $this->tokens[$i]['code'] = T_INLINE_ELSE;
                        $this->tokens[$i]['type'] = 'T_INLINE_ELSE';

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo str_repeat("\t", count($classStack));
                            echo "\t* token $i converted from T_COLON to T_INLINE_THEN *".PHP_EOL;
                        }

                        continue(2);
                    } else if ($this->tokens[$x]['line'] < $this->tokens[$i]['line']) {
                        break;
                    }
                }

                // The string to the left of the colon is either a property or label.
                for ($label = ($i - 1); $label >= 0; $label--) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$label]['code']]) === false) {
                        break;
                    }
                }

                if ($this->tokens[$label]['code'] !== T_STRING
                    && $this->tokens[$label]['code'] !== T_CONSTANT_ENCAPSED_STRING
                ) {
                    continue;
                }

                if (empty($classStack) === false) {
                    $this->tokens[$label]['code'] = T_PROPERTY;
                    $this->tokens[$label]['type'] = 'T_PROPERTY';

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", count($classStack));
                        echo "\t* token $label converted from T_STRING to T_PROPERTY *".PHP_EOL;
                    }
                } else {
                    $this->tokens[$label]['code'] = T_LABEL;
                    $this->tokens[$label]['type'] = 'T_LABEL';

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo str_repeat("\t", count($classStack));
                        echo "\t* token $label converted from T_STRING to T_LABEL *".PHP_EOL;
                    }
                }//end if
            }//end if
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END ADDITIONAL JS PROCESSING ***".PHP_EOL;
        }

    }//end processAdditional()


}//end class
