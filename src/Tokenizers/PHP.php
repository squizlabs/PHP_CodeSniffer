<?php
/**
 * Tokenizes PHP code.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tokenizers;

use PHP_CodeSniffer\Util;

class PHP extends Tokenizer
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
        T_IF            => [
            'start'  => [
                T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                T_COLON              => T_COLON,
            ],
            'end'    => [
                T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                T_ENDIF               => T_ENDIF,
                T_ELSE                => T_ELSE,
                T_ELSEIF              => T_ELSEIF,
            ],
            'strict' => false,
            'shared' => false,
            'with'   => [
                T_ELSE   => T_ELSE,
                T_ELSEIF => T_ELSEIF,
            ],
        ],
        T_TRY           => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_CATCH         => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_FINALLY       => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_ELSE          => [
            'start'  => [
                T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                T_COLON              => T_COLON,
            ],
            'end'    => [
                T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                T_ENDIF               => T_ENDIF,
            ],
            'strict' => false,
            'shared' => false,
            'with'   => [
                T_IF     => T_IF,
                T_ELSEIF => T_ELSEIF,
            ],
        ],
        T_ELSEIF        => [
            'start'  => [
                T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                T_COLON              => T_COLON,
            ],
            'end'    => [
                T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                T_ENDIF               => T_ENDIF,
                T_ELSE                => T_ELSE,
                T_ELSEIF              => T_ELSEIF,
            ],
            'strict' => false,
            'shared' => false,
            'with'   => [
                T_IF   => T_IF,
                T_ELSE => T_ELSE,
            ],
        ],
        T_FOR           => [
            'start'  => [
                T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                T_COLON              => T_COLON,
            ],
            'end'    => [
                T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                T_ENDFOR              => T_ENDFOR,
            ],
            'strict' => false,
            'shared' => false,
            'with'   => [],
        ],
        T_FOREACH       => [
            'start'  => [
                T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                T_COLON              => T_COLON,
            ],
            'end'    => [
                T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                T_ENDFOREACH          => T_ENDFOREACH,
            ],
            'strict' => false,
            'shared' => false,
            'with'   => [],
        ],
        T_INTERFACE     => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_FUNCTION      => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_CLASS         => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_TRAIT         => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_USE           => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => false,
            'shared' => false,
            'with'   => [],
        ],
        T_DECLARE       => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => false,
            'shared' => false,
            'with'   => [],
        ],
        T_NAMESPACE     => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => false,
            'shared' => false,
            'with'   => [],
        ],
        T_WHILE         => [
            'start'  => [
                T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                T_COLON              => T_COLON,
            ],
            'end'    => [
                T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                T_ENDWHILE            => T_ENDWHILE,
            ],
            'strict' => false,
            'shared' => false,
            'with'   => [],
        ],
        T_DO            => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_SWITCH        => [
            'start'  => [
                T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                T_COLON              => T_COLON,
            ],
            'end'    => [
                T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                T_ENDSWITCH           => T_ENDSWITCH,
            ],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_CASE          => [
            'start'  => [
                T_COLON     => T_COLON,
                T_SEMICOLON => T_SEMICOLON,
            ],
            'end'    => [
                T_BREAK    => T_BREAK,
                T_RETURN   => T_RETURN,
                T_CONTINUE => T_CONTINUE,
                T_THROW    => T_THROW,
                T_EXIT     => T_EXIT,
            ],
            'strict' => true,
            'shared' => true,
            'with'   => [
                T_DEFAULT => T_DEFAULT,
                T_CASE    => T_CASE,
                T_SWITCH  => T_SWITCH,
            ],
        ],
        T_DEFAULT       => [
            'start'  => [
                T_COLON     => T_COLON,
                T_SEMICOLON => T_SEMICOLON,
            ],
            'end'    => [
                T_BREAK    => T_BREAK,
                T_RETURN   => T_RETURN,
                T_CONTINUE => T_CONTINUE,
                T_THROW    => T_THROW,
                T_EXIT     => T_EXIT,
            ],
            'strict' => true,
            'shared' => true,
            'with'   => [
                T_CASE   => T_CASE,
                T_SWITCH => T_SWITCH,
            ],
        ],
        T_START_HEREDOC => [
            'start'  => [T_START_HEREDOC => T_START_HEREDOC],
            'end'    => [T_END_HEREDOC => T_END_HEREDOC],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
        T_START_NOWDOC  => [
            'start'  => [T_START_NOWDOC => T_START_NOWDOC],
            'end'    => [T_END_NOWDOC => T_END_NOWDOC],
            'strict' => true,
            'shared' => false,
            'with'   => [],
        ],
    ];

    /**
     * A list of tokens that end the scope.
     *
     * This array is just a unique collection of the end tokens
     * from the scopeOpeners array. The data is duplicated here to
     * save time during parsing of the file.
     *
     * @var array
     */
    public $endScopeTokens = [
        T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
        T_ENDIF               => T_ENDIF,
        T_ENDFOR              => T_ENDFOR,
        T_ENDFOREACH          => T_ENDFOREACH,
        T_ENDWHILE            => T_ENDWHILE,
        T_ENDSWITCH           => T_ENDSWITCH,
        T_BREAK               => T_BREAK,
        T_END_HEREDOC         => T_END_HEREDOC,
    ];

    /**
     * Known lengths of tokens.
     *
     * @var array<int, int>
     */
    public $knownLengths = [
        T_ABSTRACT                 => 8,
        T_AND_EQUAL                => 2,
        T_ARRAY                    => 5,
        T_AS                       => 2,
        T_BOOLEAN_AND              => 2,
        T_BOOLEAN_OR               => 2,
        T_BREAK                    => 5,
        T_CALLABLE                 => 8,
        T_CASE                     => 4,
        T_CATCH                    => 5,
        T_CLASS                    => 5,
        T_CLASS_C                  => 9,
        T_CLONE                    => 5,
        T_CONCAT_EQUAL             => 2,
        T_CONST                    => 5,
        T_CONTINUE                 => 8,
        T_CURLY_OPEN               => 2,
        T_DEC                      => 2,
        T_DECLARE                  => 7,
        T_DEFAULT                  => 7,
        T_DIR                      => 7,
        T_DIV_EQUAL                => 2,
        T_DO                       => 2,
        T_DOLLAR_OPEN_CURLY_BRACES => 2,
        T_DOUBLE_ARROW             => 2,
        T_DOUBLE_COLON             => 2,
        T_ECHO                     => 4,
        T_ELSE                     => 4,
        T_ELSEIF                   => 6,
        T_EMPTY                    => 5,
        T_ENDDECLARE               => 10,
        T_ENDFOR                   => 6,
        T_ENDFOREACH               => 10,
        T_ENDIF                    => 5,
        T_ENDSWITCH                => 9,
        T_ENDWHILE                 => 8,
        T_EVAL                     => 4,
        T_EXTENDS                  => 7,
        T_FILE                     => 8,
        T_FINAL                    => 5,
        T_FINALLY                  => 7,
        T_FOR                      => 3,
        T_FOREACH                  => 7,
        T_FUNCTION                 => 8,
        T_FUNC_C                   => 12,
        T_GLOBAL                   => 6,
        T_GOTO                     => 4,
        T_HALT_COMPILER            => 15,
        T_IF                       => 2,
        T_IMPLEMENTS               => 10,
        T_INC                      => 2,
        T_INCLUDE                  => 7,
        T_INCLUDE_ONCE             => 12,
        T_INSTANCEOF               => 10,
        T_INSTEADOF                => 9,
        T_INTERFACE                => 9,
        T_ISSET                    => 5,
        T_IS_EQUAL                 => 2,
        T_IS_GREATER_OR_EQUAL      => 2,
        T_IS_IDENTICAL             => 3,
        T_IS_NOT_EQUAL             => 2,
        T_IS_NOT_IDENTICAL         => 3,
        T_IS_SMALLER_OR_EQUAL      => 2,
        T_LINE                     => 8,
        T_LIST                     => 4,
        T_LOGICAL_AND              => 3,
        T_LOGICAL_OR               => 2,
        T_LOGICAL_XOR              => 3,
        T_METHOD_C                 => 10,
        T_MINUS_EQUAL              => 2,
        T_POW_EQUAL                => 3,
        T_MOD_EQUAL                => 2,
        T_MUL_EQUAL                => 2,
        T_NAMESPACE                => 9,
        T_NS_C                     => 13,
        T_NS_SEPARATOR             => 1,
        T_NEW                      => 3,
        T_OBJECT_OPERATOR          => 2,
        T_OPEN_TAG_WITH_ECHO       => 3,
        T_OR_EQUAL                 => 2,
        T_PLUS_EQUAL               => 2,
        T_PRINT                    => 5,
        T_PRIVATE                  => 7,
        T_PUBLIC                   => 6,
        T_PROTECTED                => 9,
        T_REQUIRE                  => 7,
        T_REQUIRE_ONCE             => 12,
        T_RETURN                   => 6,
        T_STATIC                   => 6,
        T_SWITCH                   => 6,
        T_THROW                    => 5,
        T_TRAIT                    => 5,
        T_TRAIT_C                  => 9,
        T_TRY                      => 3,
        T_UNSET                    => 5,
        T_USE                      => 3,
        T_VAR                      => 3,
        T_WHILE                    => 5,
        T_XOR_EQUAL                => 2,
        T_YIELD                    => 5,
        T_OPEN_CURLY_BRACKET       => 1,
        T_CLOSE_CURLY_BRACKET      => 1,
        T_OPEN_SQUARE_BRACKET      => 1,
        T_CLOSE_SQUARE_BRACKET     => 1,
        T_OPEN_PARENTHESIS         => 1,
        T_CLOSE_PARENTHESIS        => 1,
        T_COLON                    => 1,
        T_STRING_CONCAT            => 1,
        T_INLINE_THEN              => 1,
        T_INLINE_ELSE              => 1,
        T_NULLABLE                 => 1,
        T_NULL                     => 4,
        T_FALSE                    => 5,
        T_TRUE                     => 4,
        T_SEMICOLON                => 1,
        T_EQUAL                    => 1,
        T_MULTIPLY                 => 1,
        T_DIVIDE                   => 1,
        T_PLUS                     => 1,
        T_MINUS                    => 1,
        T_MODULUS                  => 1,
        T_POW                      => 2,
        T_SPACESHIP                => 3,
        T_COALESCE                 => 2,
        T_COALESCE_EQUAL           => 3,
        T_BITWISE_AND              => 1,
        T_BITWISE_OR               => 1,
        T_BITWISE_XOR              => 1,
        T_SL                       => 2,
        T_SR                       => 2,
        T_SL_EQUAL                 => 3,
        T_SR_EQUAL                 => 3,
        T_GREATER_THAN             => 1,
        T_LESS_THAN                => 1,
        T_BOOLEAN_NOT              => 1,
        T_SELF                     => 4,
        T_PARENT                   => 6,
        T_COMMA                    => 1,
        T_THIS                     => 4,
        T_CLOSURE                  => 8,
        T_BACKTICK                 => 1,
        T_OPEN_SHORT_ARRAY         => 1,
        T_CLOSE_SHORT_ARRAY        => 1,
    ];

    /**
     * A cache of different token types, resolved into arrays.
     *
     * @var array
     * @see standardiseToken()
     */
    private static $resolveTokenCache = [];


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
    protected function tokenize($string)
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START PHP TOKENIZING ***".PHP_EOL;
            $isWin = false;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $isWin = true;
            }
        }

        $tokens      = @token_get_all($string);
        $finalTokens = [];

        $newStackPtr       = 0;
        $numTokens         = count($tokens);
        $lastNotEmptyToken = 0;

        $insideInlineIf = [];
        $insideUseGroup = false;

        $commentTokenizer = new Comment();

        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            // Special case for tokens we have needed to blank out.
            if ($tokens[$stackPtr] === null) {
                continue;
            }

            $token        = (array) $tokens[$stackPtr];
            $tokenIsArray = isset($token[1]);

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                if ($tokenIsArray === true) {
                    $type    = Util\Tokens::tokenName($token[0]);
                    $content = Util\Common::prepareForOutput($token[1]);
                } else {
                    $newToken = self::resolveSimpleToken($token[0]);
                    $type     = $newToken['type'];
                    $content  = Util\Common::prepareForOutput($token[0]);
                }

                echo "\tProcess token ";
                if ($tokenIsArray === true) {
                    echo "[$stackPtr]";
                } else {
                    echo " $stackPtr ";
                }

                echo ": $type => $content";
            }//end if

            if ($newStackPtr > 0
                && isset(Util\Tokens::$emptyTokens[$finalTokens[($newStackPtr - 1)]['code']]) === false
            ) {
                $lastNotEmptyToken = ($newStackPtr - 1);
            }

            /*
                If we are using \r\n newline characters, the \r and \n are sometimes
                split over two tokens. This normally occurs after comments. We need
                to merge these two characters together so that our line endings are
                consistent for all lines.
            */

            if ($tokenIsArray === true && substr($token[1], -1) === "\r") {
                if (isset($tokens[($stackPtr + 1)]) === true
                    && is_array($tokens[($stackPtr + 1)]) === true
                    && $tokens[($stackPtr + 1)][1][0] === "\n"
                ) {
                    $token[1] .= "\n";
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        if ($isWin === true) {
                            echo '\n';
                        } else {
                            echo "\033[30;1m\\n\033[0m";
                        }
                    }

                    if ($tokens[($stackPtr + 1)][1] === "\n") {
                        // This token's content has been merged into the previous,
                        // so we can skip it.
                        $tokens[($stackPtr + 1)] = '';
                    } else {
                        $tokens[($stackPtr + 1)][1] = substr($tokens[($stackPtr + 1)][1], 1);
                    }
                }
            }//end if

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                echo PHP_EOL;
            }

            /*
                Parse doc blocks into something that can be easily iterated over.
            */

            if ($tokenIsArray === true
                && ($token[0] === T_DOC_COMMENT
                || ($token[0] === T_COMMENT && strpos($token[1], '/**') === 0))
            ) {
                $commentTokens = $commentTokenizer->tokenizeString($token[1], $this->eolChar, $newStackPtr);
                foreach ($commentTokens as $commentToken) {
                    $finalTokens[$newStackPtr] = $commentToken;
                    $newStackPtr++;
                }

                continue;
            }

            /*
                If this is a double quoted string, PHP will tokenize the whole
                thing which causes problems with the scope map when braces are
                within the string. So we need to merge the tokens together to
                provide a single string.
            */

            if ($tokenIsArray === false && ($token[0] === '"' || $token[0] === 'b"')) {
                // Binary casts need a special token.
                if ($token[0] === 'b"') {
                    $finalTokens[$newStackPtr] = [
                        'code'    => T_BINARY_CAST,
                        'type'    => 'T_BINARY_CAST',
                        'content' => 'b',
                    ];
                    $newStackPtr++;
                }

                $tokenContent = '"';
                $nestedVars   = [];
                for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                    $subToken        = (array) $tokens[$i];
                    $subTokenIsArray = isset($subToken[1]);

                    if ($subTokenIsArray === true) {
                        $tokenContent .= $subToken[1];
                        if ($subToken[1] === '{'
                            && $subToken[0] !== T_ENCAPSED_AND_WHITESPACE
                        ) {
                            $nestedVars[] = $i;
                        }
                    } else {
                        $tokenContent .= $subToken[0];
                        if ($subToken[0] === '}') {
                            array_pop($nestedVars);
                        }
                    }

                    if ($subTokenIsArray === false
                        && $subToken[0] === '"'
                        && empty($nestedVars) === true
                    ) {
                        // We found the other end of the double quoted string.
                        break;
                    }
                }//end for

                $stackPtr = $i;

                // Convert each line within the double quoted string to a
                // new token, so it conforms with other multiple line tokens.
                $tokenLines = explode($this->eolChar, $tokenContent);
                $numLines   = count($tokenLines);
                $newToken   = [];

                for ($j = 0; $j < $numLines; $j++) {
                    $newToken['content'] = $tokenLines[$j];
                    if ($j === ($numLines - 1)) {
                        if ($tokenLines[$j] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $this->eolChar;
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
                Detect binary casting and assign the casts their own token.
            */

            if ($tokenIsArray === true
                && $token[0] === T_CONSTANT_ENCAPSED_STRING
                && (substr($token[1], 0, 2) === 'b"'
                || substr($token[1], 0, 2) === "b'")
            ) {
                $finalTokens[$newStackPtr] = [
                    'code'    => T_BINARY_CAST,
                    'type'    => 'T_BINARY_CAST',
                    'content' => 'b',
                ];
                $newStackPtr++;
                $token[1] = substr($token[1], 1);
            }

            if ($tokenIsArray === true
                && $token[0] === T_STRING_CAST
                && preg_match('`^\(\s*binary\s*\)$`i', $token[1]) === 1
            ) {
                $finalTokens[$newStackPtr] = [
                    'code'    => T_BINARY_CAST,
                    'type'    => 'T_BINARY_CAST',
                    'content' => $token[1],
                ];
                $newStackPtr++;
                continue;
            }

            /*
                If this is a heredoc, PHP will tokenize the whole
                thing which causes problems when heredocs don't
                contain real PHP code, which is almost never.
                We want to leave the start and end heredoc tokens
                alone though.
            */

            if ($tokenIsArray === true && $token[0] === T_START_HEREDOC) {
                // Add the start heredoc token to the final array.
                $finalTokens[$newStackPtr] = self::standardiseToken($token);

                // Check if this is actually a nowdoc and use a different token
                // to help the sniffs.
                $nowdoc = false;
                if (strpos($token[1], "'") !== false) {
                    $finalTokens[$newStackPtr]['code'] = T_START_NOWDOC;
                    $finalTokens[$newStackPtr]['type'] = 'T_START_NOWDOC';
                    $nowdoc = true;
                }

                $tokenContent = '';
                for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                    $subTokenIsArray = is_array($tokens[$i]);
                    if ($subTokenIsArray === true
                        && $tokens[$i][0] === T_END_HEREDOC
                    ) {
                        // We found the other end of the heredoc.
                        break;
                    }

                    if ($subTokenIsArray === true) {
                        $tokenContent .= $tokens[$i][1];
                    } else {
                        $tokenContent .= $tokens[$i];
                    }
                }

                if ($i === $numTokens) {
                    // We got to the end of the file and never
                    // found the closing token, so this probably wasn't
                    // a heredoc.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $type = $finalTokens[$newStackPtr]['type'];
                        echo "\t\t* failed to find the end of the here/nowdoc".PHP_EOL;
                        echo "\t\t* token $stackPtr changed from $type to T_STRING".PHP_EOL;
                    }

                    $finalTokens[$newStackPtr]['code'] = T_STRING;
                    $finalTokens[$newStackPtr]['type'] = 'T_STRING';
                    $newStackPtr++;
                    continue;
                }

                $stackPtr = $i;
                $newStackPtr++;

                // Convert each line within the heredoc to a
                // new token, so it conforms with other multiple line tokens.
                $tokenLines = explode($this->eolChar, $tokenContent);
                $numLines   = count($tokenLines);
                $newToken   = [];

                for ($j = 0; $j < $numLines; $j++) {
                    $newToken['content'] = $tokenLines[$j];
                    if ($j === ($numLines - 1)) {
                        if ($tokenLines[$j] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $this->eolChar;
                    }

                    if ($nowdoc === true) {
                        $newToken['code'] = T_NOWDOC;
                        $newToken['type'] = 'T_NOWDOC';
                    } else {
                        $newToken['code'] = T_HEREDOC;
                        $newToken['type'] = 'T_HEREDOC';
                    }

                    $finalTokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                }//end for

                // Add the end heredoc token to the final array.
                $finalTokens[$newStackPtr] = self::standardiseToken($tokens[$stackPtr]);

                if ($nowdoc === true) {
                    $finalTokens[$newStackPtr]['code'] = T_END_NOWDOC;
                    $finalTokens[$newStackPtr]['type'] = 'T_END_NOWDOC';
                }

                $newStackPtr++;

                // Continue, as we're done with this token.
                continue;
            }//end if

            /*
                Before PHP 7.0, the "yield from" was tokenized as
                T_YIELD, T_WHITESPACE and T_STRING. So look for
                and change this token in earlier versions.
            */

            if (PHP_VERSION_ID < 70000
                && PHP_VERSION_ID >= 50500
                && $tokenIsArray === true
                && $token[0] === T_YIELD
                && isset($tokens[($stackPtr + 1)]) === true
                && isset($tokens[($stackPtr + 2)]) === true
                && $tokens[($stackPtr + 1)][0] === T_WHITESPACE
                && $tokens[($stackPtr + 2)][0] === T_STRING
                && strtolower($tokens[($stackPtr + 2)][1]) === 'from'
            ) {
                // Could be multi-line, so just the token stack.
                $token[0]  = T_YIELD_FROM;
                $token[1] .= $tokens[($stackPtr + 1)][1].$tokens[($stackPtr + 2)][1];

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    for ($i = ($stackPtr + 1); $i <= ($stackPtr + 2); $i++) {
                        $type    = Util\Tokens::tokenName($tokens[$i][0]);
                        $content = Util\Common::prepareForOutput($tokens[$i][1]);
                        echo "\t\t* token $i merged into T_YIELD_FROM; was: $type => $content".PHP_EOL;
                    }
                }

                $tokens[($stackPtr + 1)] = null;
                $tokens[($stackPtr + 2)] = null;
            }

            /*
                Before PHP 5.5, the yield keyword was tokenized as
                T_STRING. So look for and change this token in
                earlier versions.
                Checks also if it is just "yield" or "yield from".
            */

            if (PHP_VERSION_ID < 50500
                && $tokenIsArray === true
                && $token[0] === T_STRING
                && strtolower($token[1]) === 'yield'
            ) {
                if (isset($tokens[($stackPtr + 1)]) === true
                    && isset($tokens[($stackPtr + 2)]) === true
                    && $tokens[($stackPtr + 1)][0] === T_WHITESPACE
                    && $tokens[($stackPtr + 2)][0] === T_STRING
                    && strtolower($tokens[($stackPtr + 2)][1]) === 'from'
                ) {
                    // Could be multi-line, so just just the token stack.
                    $token[0]  = T_YIELD_FROM;
                    $token[1] .= $tokens[($stackPtr + 1)][1].$tokens[($stackPtr + 2)][1];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        for ($i = ($stackPtr + 1); $i <= ($stackPtr + 2); $i++) {
                            $type    = Util\Tokens::tokenName($tokens[$i][0]);
                            $content = Util\Common::prepareForOutput($tokens[$i][1]);
                            echo "\t\t* token $i merged into T_YIELD_FROM; was: $type => $content".PHP_EOL;
                        }
                    }

                    $tokens[($stackPtr + 1)] = null;
                    $tokens[($stackPtr + 2)] = null;
                } else {
                    $newToken            = [];
                    $newToken['code']    = T_YIELD;
                    $newToken['type']    = 'T_YIELD';
                    $newToken['content'] = $token[1];
                    $finalTokens[$newStackPtr] = $newToken;

                    $newStackPtr++;
                    continue;
                }//end if
            }//end if

            /*
                Before PHP 5.6, the ... operator was tokenized as three
                T_STRING_CONCAT tokens in a row. So look for and combine
                these tokens in earlier versions.
            */

            if ($tokenIsArray === false
                && $token[0] === '.'
                && isset($tokens[($stackPtr + 1)]) === true
                && isset($tokens[($stackPtr + 2)]) === true
                && $tokens[($stackPtr + 1)] === '.'
                && $tokens[($stackPtr + 2)] === '.'
            ) {
                $newToken            = [];
                $newToken['code']    = T_ELLIPSIS;
                $newToken['type']    = 'T_ELLIPSIS';
                $newToken['content'] = '...';
                $finalTokens[$newStackPtr] = $newToken;

                $newStackPtr++;
                $stackPtr += 2;
                continue;
            }

            /*
                Before PHP 5.6, the ** operator was tokenized as two
                T_MULTIPLY tokens in a row. So look for and combine
                these tokens in earlier versions.
            */

            if ($tokenIsArray === false
                && $token[0] === '*'
                && isset($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)] === '*'
            ) {
                $newToken            = [];
                $newToken['code']    = T_POW;
                $newToken['type']    = 'T_POW';
                $newToken['content'] = '**';
                $finalTokens[$newStackPtr] = $newToken;

                $newStackPtr++;
                $stackPtr++;
                continue;
            }

            /*
                Before PHP 5.6, the **= operator was tokenized as
                T_MULTIPLY followed by T_MUL_EQUAL. So look for and combine
                these tokens in earlier versions.
            */

            if ($tokenIsArray === false
                && $token[0] === '*'
                && isset($tokens[($stackPtr + 1)]) === true
                && is_array($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)][1] === '*='
            ) {
                $newToken            = [];
                $newToken['code']    = T_POW_EQUAL;
                $newToken['type']    = 'T_POW_EQUAL';
                $newToken['content'] = '**=';
                $finalTokens[$newStackPtr] = $newToken;

                $newStackPtr++;
                $stackPtr++;
                continue;
            }

            /*
                Before PHP 7, the ??= operator was tokenized as
                T_INLINE_THEN, T_INLINE_THEN, T_EQUAL.
                Between PHP 7.0 and 7.2, the ??= operator was tokenized as
                T_COALESCE, T_EQUAL.
                So look for and combine these tokens in earlier versions.
            */

            if (($tokenIsArray === false
                && $token[0] === '?'
                && isset($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)][0] === '?'
                && isset($tokens[($stackPtr + 2)]) === true
                && $tokens[($stackPtr + 2)][0] === '=')
                || ($tokenIsArray === true
                && $token[0] === T_COALESCE
                && isset($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)][0] === '=')
            ) {
                $newToken            = [];
                $newToken['code']    = T_COALESCE_EQUAL;
                $newToken['type']    = 'T_COALESCE_EQUAL';
                $newToken['content'] = '??=';
                $finalTokens[$newStackPtr] = $newToken;

                $newStackPtr++;
                $stackPtr++;

                if ($tokenIsArray === false) {
                    // Pre PHP 7.
                    $stackPtr++;
                }

                continue;
            }

            /*
                Before PHP 7, the ?? operator was tokenized as
                T_INLINE_THEN followed by T_INLINE_THEN.
                So look for and combine these tokens in earlier versions.
            */

            if ($tokenIsArray === false
                && $token[0] === '?'
                && isset($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)][0] === '?'
            ) {
                $newToken            = [];
                $newToken['code']    = T_COALESCE;
                $newToken['type']    = 'T_COALESCE';
                $newToken['content'] = '??';
                $finalTokens[$newStackPtr] = $newToken;

                $newStackPtr++;
                $stackPtr++;
                continue;
            }

            /*
                Convert ? to T_NULLABLE OR T_INLINE_THEN
            */

            if ($tokenIsArray === false && $token[0] === '?') {
                $newToken            = [];
                $newToken['content'] = '?';

                $prevNonEmpty     = null;
                $lastSeenNonEmpty = null;

                for ($i = ($stackPtr - 1); $i >= 0; $i--) {
                    if (is_array($tokens[$i]) === true) {
                        $tokenType = $tokens[$i][0];
                    } else {
                        $tokenType = $tokens[$i];
                    }

                    if ($tokenType === T_STATIC && $lastSeenNonEmpty === T_DOUBLE_COLON) {
                        $lastSeenNonEmpty = $tokenType;
                        continue;
                    }

                    if ($prevNonEmpty === null
                        && isset(Util\Tokens::$emptyTokens[$tokenType]) === false
                    ) {
                        // Found the previous non-empty token.
                        if ($tokenType === ':' || $tokenType === ',') {
                            $newToken['code'] = T_NULLABLE;
                            $newToken['type'] = 'T_NULLABLE';
                            break;
                        }

                        $prevNonEmpty = $tokenType;
                    }

                    if ($tokenType === T_FUNCTION
                        || isset(Util\Tokens::$methodPrefixes[$tokenType]) === true
                    ) {
                        $newToken['code'] = T_NULLABLE;
                        $newToken['type'] = 'T_NULLABLE';
                        break;
                    } else if (in_array($tokenType, [T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, '=', '{', ';'], true) === true) {
                        $newToken['code'] = T_INLINE_THEN;
                        $newToken['type'] = 'T_INLINE_THEN';

                        $insideInlineIf[] = $stackPtr;
                        break;
                    }

                    if (isset(Util\Tokens::$emptyTokens[$tokenType]) === false) {
                        $lastSeenNonEmpty = $tokenType;
                    }
                }//end for

                $finalTokens[$newStackPtr] = $newToken;
                $newStackPtr++;
                continue;
            }//end if

            /*
                Tokens after a double colon may be look like scope openers,
                such as when writing code like Foo::NAMESPACE, but they are
                only ever variables or strings.
            */

            if ($stackPtr > 1
                && (is_array($tokens[($stackPtr - 1)]) === true
                && $tokens[($stackPtr - 1)][0] === T_PAAMAYIM_NEKUDOTAYIM)
                && $tokenIsArray === true
                && $token[0] !== T_STRING
                && $token[0] !== T_VARIABLE
                && $token[0] !== T_DOLLAR
                && isset(Util\Tokens::$emptyTokens[$token[0]]) === false
            ) {
                $newToken            = [];
                $newToken['code']    = T_STRING;
                $newToken['type']    = 'T_STRING';
                $newToken['content'] = $token[1];
                $finalTokens[$newStackPtr] = $newToken;

                $newStackPtr++;
                continue;
            }

            /*
                The string-like token after a function keyword should always be
                tokenized as T_STRING even if it appears to be a different token,
                such as when writing code like: function default(): foo
                so go forward and change the token type before it is processed.
            */

            if ($tokenIsArray === true
                && $token[0] === T_FUNCTION
                && $finalTokens[$lastNotEmptyToken]['code'] !== T_USE
            ) {
                for ($x = ($stackPtr + 1); $x < $numTokens; $x++) {
                    if (is_array($tokens[$x]) === false
                        || isset(Util\Tokens::$emptyTokens[$tokens[$x][0]]) === false
                    ) {
                        // Non-empty content.
                        break;
                    }
                }

                if ($x < $numTokens && is_array($tokens[$x]) === true) {
                    $tokens[$x][0] = T_STRING;
                }

                /*
                    This is a special condition for T_ARRAY tokens used for
                    function return types. We want to keep the parenthesis map clean,
                    so let's tag these tokens as T_STRING.
                */

                // Go looking for the colon to start the return type hint.
                // Start by finding the closing parenthesis of the function.
                $parenthesisStack  = [];
                $parenthesisCloser = false;
                for ($x = ($stackPtr + 1); $x < $numTokens; $x++) {
                    if (is_array($tokens[$x]) === false && $tokens[$x] === '(') {
                        $parenthesisStack[] = $x;
                    } else if (is_array($tokens[$x]) === false && $tokens[$x] === ')') {
                        array_pop($parenthesisStack);
                        if (empty($parenthesisStack) === true) {
                            $parenthesisCloser = $x;
                            break;
                        }
                    }
                }

                if ($parenthesisCloser !== false) {
                    for ($x = ($parenthesisCloser + 1); $x < $numTokens; $x++) {
                        if (is_array($tokens[$x]) === false
                            || isset(Util\Tokens::$emptyTokens[$tokens[$x][0]]) === false
                        ) {
                            // Non-empty content.
                            if (is_array($tokens[$x]) === true && $tokens[$x][0] === T_USE) {
                                // Found a use statements, so search ahead for the closing parenthesis.
                                for ($x += 1; $x < $numTokens; $x++) {
                                    if (is_array($tokens[$x]) === false && $tokens[$x] === ')') {
                                        continue(2);
                                    }
                                }
                            }

                            break;
                        }
                    }

                    if (isset($tokens[$x]) === true
                        && is_array($tokens[$x]) === false
                        && $tokens[$x] === ':'
                    ) {
                        $allowed = [
                            T_STRING       => T_STRING,
                            T_ARRAY        => T_ARRAY,
                            T_CALLABLE     => T_CALLABLE,
                            T_SELF         => T_SELF,
                            T_PARENT       => T_PARENT,
                            T_NS_SEPARATOR => T_NS_SEPARATOR,
                        ];

                        $allowed += Util\Tokens::$emptyTokens;

                        // Find the start of the return type.
                        for ($x += 1; $x < $numTokens; $x++) {
                            if (is_array($tokens[$x]) === true
                                && isset(Util\Tokens::$emptyTokens[$tokens[$x][0]]) === true
                            ) {
                                // Whitespace or comments before the return type.
                                continue;
                            }

                            if (is_array($tokens[$x]) === false && $tokens[$x] === '?') {
                                // Found a nullable operator, so skip it.
                                // But also covert the token to save the tokenizer
                                // a bit of time later on.
                                $tokens[$x] = [
                                    T_NULLABLE,
                                    '?',
                                ];

                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    echo "\t\t* token $x changed from ? to T_NULLABLE".PHP_EOL;
                                }

                                continue;
                            }

                            break;
                        }//end for

                        // Any T_ARRAY tokens we find between here and the next
                        // token that can't be part of the return type need to be
                        // converted to T_STRING tokens.
                        for ($x; $x < $numTokens; $x++) {
                            if (is_array($tokens[$x]) === false || isset($allowed[$tokens[$x][0]]) === false) {
                                break;
                            } else if ($tokens[$x][0] === T_ARRAY) {
                                $tokens[$x][0] = T_STRING;

                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    echo "\t\t* token $x changed from T_ARRAY to T_STRING".PHP_EOL;
                                }
                            }
                        }
                    }//end if
                }//end if
            }//end if

            /*
                Before PHP 7, the <=> operator was tokenized as
                T_IS_SMALLER_OR_EQUAL followed by T_GREATER_THAN.
                So look for and combine these tokens in earlier versions.
            */

            if ($tokenIsArray === true
                && $token[0] === T_IS_SMALLER_OR_EQUAL
                && isset($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)][0] === '>'
            ) {
                $newToken            = [];
                $newToken['code']    = T_SPACESHIP;
                $newToken['type']    = 'T_SPACESHIP';
                $newToken['content'] = '<=>';
                $finalTokens[$newStackPtr] = $newToken;

                $newStackPtr++;
                $stackPtr++;
                continue;
            }

            /*
                PHP doesn't assign a token to goto labels, so we have to.
                These are just string tokens with a single colon after them. Double
                colons are already tokenized and so don't interfere with this check.
                But we do have to account for CASE statements, that look just like
                goto labels.
            */

            if ($tokenIsArray === true
                && $token[0] === T_STRING
                && isset($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)] === ':'
                && $tokens[($stackPtr - 1)][0] !== T_PAAMAYIM_NEKUDOTAYIM
            ) {
                $stopTokens = [
                    T_CASE               => true,
                    T_SEMICOLON          => true,
                    T_OPEN_CURLY_BRACKET => true,
                    T_INLINE_THEN        => true,
                ];

                for ($x = ($newStackPtr - 1); $x > 0; $x--) {
                    if (isset($stopTokens[$finalTokens[$x]['code']]) === true) {
                        break;
                    }
                }

                if ($finalTokens[$x]['code'] !== T_CASE
                    && $finalTokens[$x]['code'] !== T_INLINE_THEN
                ) {
                    $finalTokens[$newStackPtr] = [
                        'content' => $token[1].':',
                        'code'    => T_GOTO_LABEL,
                        'type'    => 'T_GOTO_LABEL',
                    ];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* token $stackPtr changed from T_STRING to T_GOTO_LABEL".PHP_EOL;
                        echo "\t\t* skipping T_COLON token ".($stackPtr + 1).PHP_EOL;
                    }

                    $newStackPtr++;
                    $stackPtr++;
                    continue;
                }
            }//end if

            /*
                If this token has newlines in its content, split each line up
                and create a new token for each line. We do this so it's easier
                to ascertain where errors occur on a line.
                Note that $token[1] is the token's content.
            */

            if ($tokenIsArray === true && strpos($token[1], $this->eolChar) !== false) {
                $tokenLines = explode($this->eolChar, $token[1]);
                $numLines   = count($tokenLines);
                $newToken   = [
                    'type'    => Util\Tokens::tokenName($token[0]),
                    'code'    => $token[0],
                    'content' => '',
                ];

                for ($i = 0; $i < $numLines; $i++) {
                    $newToken['content'] = $tokenLines[$i];
                    if ($i === ($numLines - 1)) {
                        if ($tokenLines[$i] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $this->eolChar;
                    }

                    $finalTokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                }
            } else {
                if ($tokenIsArray === true && $token[0] === T_STRING) {
                    // Some T_STRING tokens should remain that way
                    // due to their context.
                    $context = [
                        T_OBJECT_OPERATOR      => true,
                        T_FUNCTION             => true,
                        T_CLASS                => true,
                        T_EXTENDS              => true,
                        T_IMPLEMENTS           => true,
                        T_NEW                  => true,
                        T_CONST                => true,
                        T_NS_SEPARATOR         => true,
                        T_USE                  => true,
                        T_NAMESPACE            => true,
                        T_PAAMAYIM_NEKUDOTAYIM => true,
                    ];

                    if (isset($context[$finalTokens[$lastNotEmptyToken]['code']]) === true) {
                        // Special case for syntax like: return new self
                        // where self should not be a string.
                        if ($finalTokens[$lastNotEmptyToken]['code'] === T_NEW
                            && strtolower($token[1]) === 'self'
                        ) {
                            $finalTokens[$newStackPtr] = [
                                'content' => $token[1],
                                'code'    => T_SELF,
                                'type'    => 'T_SELF',
                            ];
                        } else {
                            $finalTokens[$newStackPtr] = [
                                'content' => $token[1],
                                'code'    => T_STRING,
                                'type'    => 'T_STRING',
                            ];
                        }

                        $newStackPtr++;
                        continue;
                    }//end if
                }//end if

                $newToken = null;
                if ($tokenIsArray === false) {
                    if (isset(self::$resolveTokenCache[$token[0]]) === true) {
                        $newToken = self::$resolveTokenCache[$token[0]];
                    }
                } else {
                    $cacheKey = null;
                    if ($token[0] === T_STRING) {
                        $cacheKey = strtolower($token[1]);
                    } else if ($token[0] !== T_CURLY_OPEN) {
                        $cacheKey = $token[0];
                    }

                    if ($cacheKey !== null && isset(self::$resolveTokenCache[$cacheKey]) === true) {
                        $newToken            = self::$resolveTokenCache[$cacheKey];
                        $newToken['content'] = $token[1];
                    }
                }

                if ($newToken === null) {
                    $newToken = self::standardiseToken($token);
                }

                // Convert colons that are actually the ELSE component of an
                // inline IF statement.
                if (empty($insideInlineIf) === false && $newToken['code'] === T_COLON) {
                    // Make sure this isn't the return type separator of a closure.
                    $isReturnType = false;
                    for ($i = ($stackPtr - 1); $i > 0; $i--) {
                        if (is_array($tokens[$i]) === false
                            || ($tokens[$i][0] !== T_DOC_COMMENT
                            && $tokens[$i][0] !== T_COMMENT
                            && $tokens[$i][0] !== T_WHITESPACE)
                        ) {
                            break;
                        }
                    }

                    if ($tokens[$i] === ')') {
                        $parenCount = 1;
                        for ($i--; $i > 0; $i--) {
                            if ($tokens[$i] === '(') {
                                $parenCount--;
                                if ($parenCount === 0) {
                                    break;
                                }
                            } else if ($tokens[$i] === ')') {
                                $parenCount++;
                            }
                        }

                        // We've found the open parenthesis, so if the previous
                        // non-empty token is FUNCTION or USE, this is a closure.
                        for ($i--; $i > 0; $i--) {
                            if (is_array($tokens[$i]) === false
                                || ($tokens[$i][0] !== T_DOC_COMMENT
                                && $tokens[$i][0] !== T_COMMENT
                                && $tokens[$i][0] !== T_WHITESPACE)
                            ) {
                                break;
                            }
                        }

                        if ($tokens[$i][0] === T_FUNCTION || $tokens[$i][0] === T_USE) {
                            $isReturnType = true;
                        }
                    }//end if

                    if ($isReturnType === false) {
                        array_pop($insideInlineIf);
                        $newToken['code'] = T_INLINE_ELSE;
                        $newToken['type'] = 'T_INLINE_ELSE';
                    }
                }//end if

                // This is a special condition for T_ARRAY tokens used for
                // type hinting function arguments as being arrays. We want to keep
                // the parenthesis map clean, so let's tag these tokens as
                // T_STRING.
                if ($newToken['code'] === T_ARRAY) {
                    for ($i = $stackPtr; $i < $numTokens; $i++) {
                        if ($tokens[$i] === '(') {
                            break;
                        } else if ($tokens[$i][0] === T_VARIABLE) {
                            $newToken['code'] = T_STRING;
                            $newToken['type'] = 'T_STRING';
                            break;
                        }
                    }
                }

                // This is a special case when checking PHP 5.5+ code in PHP < 5.5
                // where "finally" should be T_FINALLY instead of T_STRING.
                if ($newToken['code'] === T_STRING
                    && strtolower($newToken['content']) === 'finally'
                ) {
                    $newToken['code'] = T_FINALLY;
                    $newToken['type'] = 'T_FINALLY';
                }

                // This is a special case for the PHP 5.5 classname::class syntax
                // where "class" should be T_STRING instead of T_CLASS.
                if (($newToken['code'] === T_CLASS
                    || $newToken['code'] === T_FUNCTION)
                    && $finalTokens[$lastNotEmptyToken]['code'] === T_DOUBLE_COLON
                ) {
                    $newToken['code'] = T_STRING;
                    $newToken['type'] = 'T_STRING';
                }

                // This is a special case for PHP 5.6 use function and use const
                // where "function" and "const" should be T_STRING instead of T_FUNCTION
                // and T_CONST.
                if (($newToken['code'] === T_FUNCTION
                    || $newToken['code'] === T_CONST)
                    && ($finalTokens[$lastNotEmptyToken]['code'] === T_USE || $insideUseGroup === true)
                ) {
                    $newToken['code'] = T_STRING;
                    $newToken['type'] = 'T_STRING';
                }

                // This is a special case for use groups in PHP 7+ where leaving
                // the curly braces as their normal tokens would confuse
                // the scope map and sniffs.
                if ($newToken['code'] === T_OPEN_CURLY_BRACKET
                    && $finalTokens[$lastNotEmptyToken]['code'] === T_NS_SEPARATOR
                ) {
                    $newToken['code'] = T_OPEN_USE_GROUP;
                    $newToken['type'] = 'T_OPEN_USE_GROUP';
                    $insideUseGroup   = true;
                }

                if ($insideUseGroup === true && $newToken['code'] === T_CLOSE_CURLY_BRACKET) {
                    $newToken['code'] = T_CLOSE_USE_GROUP;
                    $newToken['type'] = 'T_CLOSE_USE_GROUP';
                    $insideUseGroup   = false;
                }

                $finalTokens[$newStackPtr] = $newToken;
                $newStackPtr++;
            }//end if
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END PHP TOKENIZING ***".PHP_EOL;
        }

        return $finalTokens;

    }//end tokenize()


    /**
     * Performs additional processing after main tokenizing.
     *
     * This additional processing checks for CASE statements that are using curly
     * braces for scope openers and closers. It also turns some T_FUNCTION tokens
     * into T_CLOSURE when they are not standard function definitions. It also
     * detects short array syntax and converts those square brackets into new tokens.
     * It also corrects some usage of the static and class keywords. It also
     * assigns tokens to function return types.
     *
     * @return void
     */
    protected function processAdditional()
    {
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START ADDITIONAL PHP PROCESSING ***".PHP_EOL;
        }

        $numTokens = count($this->tokens);
        for ($i = ($numTokens - 1); $i >= 0; $i--) {
            // Check for any unset scope conditions due to alternate IF/ENDIF syntax.
            if (isset($this->tokens[$i]['scope_opener']) === true
                && isset($this->tokens[$i]['scope_condition']) === false
            ) {
                $this->tokens[$i]['scope_condition'] = $this->tokens[$this->tokens[$i]['scope_opener']]['scope_condition'];
            }

            if ($this->tokens[$i]['code'] === T_FUNCTION) {
                /*
                    Detect functions that are actually closures and
                    assign them a different token.
                */

                if (isset($this->tokens[$i]['scope_opener']) === true) {
                    for ($x = ($i + 1); $x < $numTokens; $x++) {
                        if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false
                            && $this->tokens[$x]['code'] !== T_BITWISE_AND
                        ) {
                            break;
                        }
                    }

                    if ($this->tokens[$x]['code'] === T_OPEN_PARENTHESIS) {
                        $this->tokens[$i]['code'] = T_CLOSURE;
                        $this->tokens[$i]['type'] = 'T_CLOSURE';
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $line = $this->tokens[$i]['line'];
                            echo "\t* token $i on line $line changed from T_FUNCTION to T_CLOSURE".PHP_EOL;
                        }

                        for ($x = ($this->tokens[$i]['scope_opener'] + 1); $x < $this->tokens[$i]['scope_closer']; $x++) {
                            if (isset($this->tokens[$x]['conditions'][$i]) === false) {
                                continue;
                            }

                            $this->tokens[$x]['conditions'][$i] = T_CLOSURE;
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $type = $this->tokens[$x]['type'];
                                echo "\t\t* cleaned $x ($type) *".PHP_EOL;
                            }
                        }
                    }
                }//end if

                continue;
            } else if ($this->tokens[$i]['code'] === T_CLASS && isset($this->tokens[$i]['scope_opener']) === true) {
                /*
                    Detect anonymous classes and assign them a different token.
                */

                for ($x = ($i + 1); $x < $numTokens; $x++) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false) {
                        break;
                    }
                }

                if ($this->tokens[$x]['code'] === T_OPEN_PARENTHESIS
                    || $this->tokens[$x]['code'] === T_OPEN_CURLY_BRACKET
                    || $this->tokens[$x]['code'] === T_EXTENDS
                    || $this->tokens[$x]['code'] === T_IMPLEMENTS
                ) {
                    $this->tokens[$i]['code'] = T_ANON_CLASS;
                    $this->tokens[$i]['type'] = 'T_ANON_CLASS';
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $line = $this->tokens[$i]['line'];
                        echo "\t* token $i on line $line changed from T_CLASS to T_ANON_CLASS".PHP_EOL;
                    }

                    if ($this->tokens[$x]['code'] === T_OPEN_PARENTHESIS
                        && isset($this->tokens[$x]['parenthesis_closer']) === true
                    ) {
                        $closer = $this->tokens[$x]['parenthesis_closer'];

                        $this->tokens[$i]['parenthesis_opener']     = $x;
                        $this->tokens[$i]['parenthesis_closer']     = $closer;
                        $this->tokens[$i]['parenthesis_owner']      = $i;
                        $this->tokens[$x]['parenthesis_owner']      = $i;
                        $this->tokens[$closer]['parenthesis_owner'] = $i;

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $line = $this->tokens[$i]['line'];
                            echo "\t\t* added parenthesis keys to T_ANON_CLASS token $i on line $line".PHP_EOL;
                        }
                    }

                    for ($x = ($this->tokens[$i]['scope_opener'] + 1); $x < $this->tokens[$i]['scope_closer']; $x++) {
                        if (isset($this->tokens[$x]['conditions'][$i]) === false) {
                            continue;
                        }

                        $this->tokens[$x]['conditions'][$i] = T_ANON_CLASS;
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = $this->tokens[$x]['type'];
                            echo "\t\t* cleaned $x ($type) *".PHP_EOL;
                        }
                    }
                }//end if

                continue;
            } else if ($this->tokens[$i]['code'] === T_OPEN_SQUARE_BRACKET) {
                if (isset($this->tokens[$i]['bracket_closer']) === false) {
                    continue;
                }

                // Unless there is a variable or a bracket before this token,
                // it is the start of an array being defined using the short syntax.
                $isShortArray = false;
                $allowed      = [
                    T_CLOSE_SQUARE_BRACKET     => T_CLOSE_SQUARE_BRACKET,
                    T_CLOSE_CURLY_BRACKET      => T_CLOSE_CURLY_BRACKET,
                    T_CLOSE_PARENTHESIS        => T_CLOSE_PARENTHESIS,
                    T_VARIABLE                 => T_VARIABLE,
                    T_OBJECT_OPERATOR          => T_OBJECT_OPERATOR,
                    T_STRING                   => T_STRING,
                    T_CONSTANT_ENCAPSED_STRING => T_CONSTANT_ENCAPSED_STRING,
                ];

                for ($x = ($i - 1); $x >= 0; $x--) {
                    // If we hit a scope opener, the statement has ended
                    // without finding anything, so it's probably an array
                    // using PHP 7.1 short list syntax.
                    if (isset($this->tokens[$x]['scope_opener']) === true) {
                        $isShortArray = true;
                        break;
                    }

                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false) {
                        if (isset($allowed[$this->tokens[$x]['code']]) === false) {
                            $isShortArray = true;
                        }

                        break;
                    }
                }

                if ($isShortArray === true) {
                    $this->tokens[$i]['code'] = T_OPEN_SHORT_ARRAY;
                    $this->tokens[$i]['type'] = 'T_OPEN_SHORT_ARRAY';

                    $closer = $this->tokens[$i]['bracket_closer'];
                    $this->tokens[$closer]['code'] = T_CLOSE_SHORT_ARRAY;
                    $this->tokens[$closer]['type'] = 'T_CLOSE_SHORT_ARRAY';
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $line = $this->tokens[$i]['line'];
                        echo "\t* token $i on line $line changed from T_OPEN_SQUARE_BRACKET to T_OPEN_SHORT_ARRAY".PHP_EOL;
                        $line = $this->tokens[$closer]['line'];
                        echo "\t* token $closer on line $line changed from T_CLOSE_SQUARE_BRACKET to T_CLOSE_SHORT_ARRAY".PHP_EOL;
                    }
                }

                continue;
            } else if ($this->tokens[$i]['code'] === T_STATIC) {
                for ($x = ($i - 1); $x > 0; $x--) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false) {
                        break;
                    }
                }

                if ($this->tokens[$x]['code'] === T_INSTANCEOF) {
                    $this->tokens[$i]['code'] = T_STRING;
                    $this->tokens[$i]['type'] = 'T_STRING';

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $line = $this->tokens[$i]['line'];
                        echo "\t* token $i on line $line changed from T_STATIC to T_STRING".PHP_EOL;
                    }
                }

                continue;
            } else if ($this->tokens[$i]['code'] === T_TRUE
                || $this->tokens[$i]['code'] === T_FALSE
                || $this->tokens[$i]['code'] === T_NULL
            ) {
                for ($x = ($i + 1); $i < $numTokens; $x++) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false) {
                        // Non-whitespace content.
                        break;
                    }
                }

                $context = [
                    T_OBJECT_OPERATOR      => true,
                    T_NS_SEPARATOR         => true,
                    T_PAAMAYIM_NEKUDOTAYIM => true,
                ];
                if (isset($context[$this->tokens[$x]['code']]) === true) {
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $line = $this->tokens[$i]['line'];
                        $type = $this->tokens[$i]['type'];
                        echo "\t* token $i on line $line changed from $type to T_STRING".PHP_EOL;
                    }

                    $this->tokens[$i]['code'] = T_STRING;
                    $this->tokens[$i]['type'] = 'T_STRING';
                }
            } else if ($this->tokens[$i]['code'] === T_CONST) {
                // Context sensitive keywords support.
                for ($x = ($i + 1); $i < $numTokens; $x++) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false) {
                        // Non-whitespace content.
                        break;
                    }
                }

                if ($this->tokens[$x]['code'] !== T_STRING) {
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $line = $this->tokens[$x]['line'];
                        $type = $this->tokens[$x]['type'];
                        echo "\t* token $x on line $line changed from $type to T_STRING".PHP_EOL;
                    }

                    $this->tokens[$x]['code'] = T_STRING;
                    $this->tokens[$x]['type'] = 'T_STRING';
                }
            }//end if

            if (($this->tokens[$i]['code'] !== T_CASE
                && $this->tokens[$i]['code'] !== T_DEFAULT)
                || isset($this->tokens[$i]['scope_opener']) === false
            ) {
                // Only interested in CASE and DEFAULT statements from here on in.
                continue;
            }

            $scopeOpener = $this->tokens[$i]['scope_opener'];
            $scopeCloser = $this->tokens[$i]['scope_closer'];

            // If the first char after the opener is a curly brace
            // and that brace has been ignored, it is actually
            // opening this case statement and the opener and closer are
            // probably set incorrectly.
            for ($x = ($scopeOpener + 1); $x < $numTokens; $x++) {
                if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false) {
                    // Non-whitespace content.
                    break;
                }
            }

            if ($this->tokens[$x]['code'] === T_CASE || $this->tokens[$x]['code'] === T_DEFAULT) {
                // Special case for multiple CASE statements that share the same
                // closer. Because we are going backwards through the file, this next
                // CASE statement is already fixed, so just use its closer and don't
                // worry about fixing anything.
                $newCloser = $this->tokens[$x]['scope_closer'];
                $this->tokens[$i]['scope_closer'] = $newCloser;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $oldType = $this->tokens[$scopeCloser]['type'];
                    $newType = $this->tokens[$newCloser]['type'];
                    $line    = $this->tokens[$i]['line'];
                    echo "\t* token $i (T_CASE) on line $line closer changed from $scopeCloser ($oldType) to $newCloser ($newType)".PHP_EOL;
                }

                continue;
            }

            if ($this->tokens[$x]['code'] !== T_OPEN_CURLY_BRACKET
                || isset($this->tokens[$x]['scope_condition']) === true
            ) {
                // Not a CASE/DEFAULT with a curly brace opener.
                continue;
            }

            // The closer for this CASE/DEFAULT should be the closing curly brace and
            // not whatever it already is. The opener needs to be the opening curly
            // brace so everything matches up.
            $newCloser = $this->tokens[$x]['bracket_closer'];
            foreach ([$i, $x, $newCloser] as $index) {
                $this->tokens[$index]['scope_condition'] = $i;
                $this->tokens[$index]['scope_opener']    = $x;
                $this->tokens[$index]['scope_closer']    = $newCloser;
            }

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $line      = $this->tokens[$i]['line'];
                $tokenType = $this->tokens[$i]['type'];

                $oldType = $this->tokens[$scopeOpener]['type'];
                $newType = $this->tokens[$x]['type'];
                echo "\t* token $i ($tokenType) on line $line opener changed from $scopeOpener ($oldType) to $x ($newType)".PHP_EOL;

                $oldType = $this->tokens[$scopeCloser]['type'];
                $newType = $this->tokens[$newCloser]['type'];
                echo "\t* token $i ($tokenType) on line $line closer changed from $scopeCloser ($oldType) to $newCloser ($newType)".PHP_EOL;
            }

            if ($this->tokens[$scopeOpener]['scope_condition'] === $i) {
                unset($this->tokens[$scopeOpener]['scope_condition']);
                unset($this->tokens[$scopeOpener]['scope_opener']);
                unset($this->tokens[$scopeOpener]['scope_closer']);
            }

            if ($this->tokens[$scopeCloser]['scope_condition'] === $i) {
                unset($this->tokens[$scopeCloser]['scope_condition']);
                unset($this->tokens[$scopeCloser]['scope_opener']);
                unset($this->tokens[$scopeCloser]['scope_closer']);
            } else {
                // We were using a shared closer. All tokens that were
                // sharing this closer with us, except for the scope condition
                // and it's opener, need to now point to the new closer.
                $condition = $this->tokens[$scopeCloser]['scope_condition'];
                $start     = ($this->tokens[$condition]['scope_opener'] + 1);
                for ($y = $start; $y < $scopeCloser; $y++) {
                    if (isset($this->tokens[$y]['scope_closer']) === true
                        && $this->tokens[$y]['scope_closer'] === $scopeCloser
                    ) {
                        $this->tokens[$y]['scope_closer'] = $newCloser;

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $line      = $this->tokens[$y]['line'];
                            $tokenType = $this->tokens[$y]['type'];
                            $oldType   = $this->tokens[$scopeCloser]['type'];
                            $newType   = $this->tokens[$newCloser]['type'];
                            echo "\t\t* token $y ($tokenType) on line $line closer changed from $scopeCloser ($oldType) to $newCloser ($newType)".PHP_EOL;
                        }
                    }
                }
            }//end if

            unset($this->tokens[$x]['bracket_opener']);
            unset($this->tokens[$x]['bracket_closer']);
            unset($this->tokens[$newCloser]['bracket_opener']);
            unset($this->tokens[$newCloser]['bracket_closer']);
            $this->tokens[$scopeCloser]['conditions'][] = $i;

            // Now fix up all the tokens that think they are
            // inside the CASE/DEFAULT statement when they are really outside.
            for ($x = $newCloser; $x < $scopeCloser; $x++) {
                foreach ($this->tokens[$x]['conditions'] as $num => $oldCond) {
                    if ($oldCond === $this->tokens[$i]['code']) {
                        $oldConditions = $this->tokens[$x]['conditions'];
                        unset($this->tokens[$x]['conditions'][$num]);

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type     = $this->tokens[$x]['type'];
                            $oldConds = '';
                            foreach ($oldConditions as $condition) {
                                $oldConds .= Util\Tokens::tokenName($condition).',';
                            }

                            $oldConds = rtrim($oldConds, ',');

                            $newConds = '';
                            foreach ($this->tokens[$x]['conditions'] as $condition) {
                                $newConds .= Util\Tokens::tokenName($condition).',';
                            }

                            $newConds = rtrim($newConds, ',');

                            echo "\t\t* cleaned $x ($type) *".PHP_EOL;
                            echo "\t\t\t=> conditions changed from $oldConds to $newConds".PHP_EOL;
                        }

                        break;
                    }//end if
                }//end foreach
            }//end for
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END ADDITIONAL PHP PROCESSING ***".PHP_EOL;
        }

    }//end processAdditional()


    /**
     * Takes a token produced from <code>token_get_all()</code> and produces a
     * more uniform token.
     *
     * @param string|array $token The token to convert.
     *
     * @return array The new token.
     */
    public static function standardiseToken($token)
    {
        if (isset($token[1]) === false) {
            if (isset(self::$resolveTokenCache[$token[0]]) === true) {
                return self::$resolveTokenCache[$token[0]];
            }
        } else {
            $cacheKey = null;
            if ($token[0] === T_STRING) {
                $cacheKey = strtolower($token[1]);
            } else if ($token[0] !== T_CURLY_OPEN) {
                $cacheKey = $token[0];
            }

            if ($cacheKey !== null && isset(self::$resolveTokenCache[$cacheKey]) === true) {
                $newToken            = self::$resolveTokenCache[$cacheKey];
                $newToken['content'] = $token[1];
                return $newToken;
            }
        }

        if (isset($token[1]) === false) {
            return self::resolveSimpleToken($token[0]);
        }

        if ($token[0] === T_STRING) {
            switch ($cacheKey) {
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

            self::$resolveTokenCache[$cacheKey] = $newToken;
        } else if ($token[0] === T_CURLY_OPEN) {
            $newToken = [
                'code' => T_OPEN_CURLY_BRACKET,
                'type' => 'T_OPEN_CURLY_BRACKET',
            ];
        } else {
            $newToken = [
                'code' => $token[0],
                'type' => Util\Tokens::tokenName($token[0]),
            ];

            self::$resolveTokenCache[$token[0]] = $newToken;
        }//end if

        $newToken['content'] = $token[1];
        return $newToken;

    }//end standardiseToken()


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
    public static function resolveSimpleToken($token)
    {
        $newToken = [];

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
            $newToken['type'] = 'T_BITWISE_XOR';
            break;
        case '&':
            $newToken['type'] = 'T_BITWISE_AND';
            break;
        case '|':
            $newToken['type'] = 'T_BITWISE_OR';
            break;
        case '~':
            $newToken['type'] = 'T_BITWISE_NOT';
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

        self::$resolveTokenCache[$token] = $newToken;
        return $newToken;

    }//end resolveSimpleToken()


}//end class
