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
            'start'  => [
                T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                T_COLON              => T_COLON,
            ],
            'end'    => [
                T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                T_ENDDECLARE          => T_ENDDECLARE,
            ],
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
        T_MATCH         => [
            'start'  => [T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET],
            'end'    => [T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET],
            'strict' => true,
            'shared' => false,
            'with'   => [],
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
        T_ENDDECLARE          => T_ENDDECLARE,
        T_BREAK               => T_BREAK,
        T_END_HEREDOC         => T_END_HEREDOC,
        T_END_NOWDOC          => T_END_NOWDOC,
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
        T_ELLIPSIS                 => 3,
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
        T_FN                       => 2,
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
        T_MATCH                    => 5,
        T_MATCH_ARROW              => 2,
        T_MATCH_DEFAULT            => 7,
        T_METHOD_C                 => 10,
        T_MINUS_EQUAL              => 2,
        T_POW_EQUAL                => 3,
        T_MOD_EQUAL                => 2,
        T_MUL_EQUAL                => 2,
        T_NAMESPACE                => 9,
        T_NS_C                     => 13,
        T_NS_SEPARATOR             => 1,
        T_NEW                      => 3,
        T_NULLSAFE_OBJECT_OPERATOR => 3,
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
        T_TYPE_UNION               => 1,
    ];

    /**
     * Contexts in which keywords should always be tokenized as T_STRING.
     *
     * @var array
     */
    protected $tstringContexts = [
        T_OBJECT_OPERATOR          => true,
        T_NULLSAFE_OBJECT_OPERATOR => true,
        T_FUNCTION                 => true,
        T_CLASS                    => true,
        T_INTERFACE                => true,
        T_TRAIT                    => true,
        T_EXTENDS                  => true,
        T_IMPLEMENTS               => true,
        T_ATTRIBUTE                => true,
        T_NEW                      => true,
        T_CONST                    => true,
        T_NS_SEPARATOR             => true,
        T_USE                      => true,
        T_NAMESPACE                => true,
        T_PAAMAYIM_NEKUDOTAYIM     => true,
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
                PHP 8 tokenizes a new line after a slash and hash comment to the next whitespace token.
            */

            if (PHP_VERSION_ID >= 80000
                && $tokenIsArray === true
                && ($token[0] === T_COMMENT && (strpos($token[1], '//') === 0 || strpos($token[1], '#') === 0))
                && isset($tokens[($stackPtr + 1)]) === true
                && is_array($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)][0] === T_WHITESPACE
            ) {
                $nextToken = $tokens[($stackPtr + 1)];

                // If the next token is a single new line, merge it into the comment token
                // and set to it up to be skipped.
                if ($nextToken[1] === "\n" || $nextToken[1] === "\r\n" || $nextToken[1] === "\n\r") {
                    $token[1] .= $nextToken[1];
                    $tokens[($stackPtr + 1)] = null;

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* merged newline after comment into comment token $stackPtr".PHP_EOL;
                    }
                } else {
                    // This may be a whitespace token consisting of multiple new lines.
                    if (strpos($nextToken[1], "\r\n") === 0) {
                        $token[1] .= "\r\n";
                        $tokens[($stackPtr + 1)][1] = substr($nextToken[1], 2);
                    } else if (strpos($nextToken[1], "\n\r") === 0) {
                        $token[1] .= "\n\r";
                        $tokens[($stackPtr + 1)][1] = substr($nextToken[1], 2);
                    } else if (strpos($nextToken[1], "\n") === 0) {
                        $token[1] .= "\n";
                        $tokens[($stackPtr + 1)][1] = substr($nextToken[1], 1);
                    }

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* stripped first newline after comment and added it to comment token $stackPtr".PHP_EOL;
                    }
                }//end if
            }//end if

            /*
                PHP 8.1 introduced two dedicated tokens for the & character.
                Retokenizing both of these to T_BITWISE_AND, which is the
                token PHPCS already tokenized them as.
            */

            if ($tokenIsArray === true
                && ($token[0] === T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG
                || $token[0] === T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG)
            ) {
                $finalTokens[$newStackPtr] = [
                    'code'    => T_BITWISE_AND,
                    'type'    => 'T_BITWISE_AND',
                    'content' => $token[1],
                ];
                $newStackPtr++;
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
                As of PHP 8.0 fully qualified, partially qualified and namespace relative
                identifier names are tokenized differently.
                This "undoes" the new tokenization so the tokenization will be the same in
                in PHP 5, 7 and 8.
            */

            if (PHP_VERSION_ID >= 80000
                && $tokenIsArray === true
                && ($token[0] === T_NAME_QUALIFIED
                || $token[0] === T_NAME_FULLY_QUALIFIED
                || $token[0] === T_NAME_RELATIVE)
            ) {
                $name = $token[1];

                if ($token[0] === T_NAME_FULLY_QUALIFIED) {
                    $newToken            = [];
                    $newToken['code']    = T_NS_SEPARATOR;
                    $newToken['type']    = 'T_NS_SEPARATOR';
                    $newToken['content'] = '\\';
                    $finalTokens[$newStackPtr] = $newToken;
                    ++$newStackPtr;

                    $name = ltrim($name, '\\');
                }

                if ($token[0] === T_NAME_RELATIVE) {
                    $newToken            = [];
                    $newToken['code']    = T_NAMESPACE;
                    $newToken['type']    = 'T_NAMESPACE';
                    $newToken['content'] = substr($name, 0, 9);
                    $finalTokens[$newStackPtr] = $newToken;
                    ++$newStackPtr;

                    $newToken            = [];
                    $newToken['code']    = T_NS_SEPARATOR;
                    $newToken['type']    = 'T_NS_SEPARATOR';
                    $newToken['content'] = '\\';
                    $finalTokens[$newStackPtr] = $newToken;
                    ++$newStackPtr;

                    $name = substr($name, 10);
                }

                $parts     = explode('\\', $name);
                $partCount = count($parts);
                $lastPart  = ($partCount - 1);

                foreach ($parts as $i => $part) {
                    $newToken            = [];
                    $newToken['code']    = T_STRING;
                    $newToken['type']    = 'T_STRING';
                    $newToken['content'] = $part;
                    $finalTokens[$newStackPtr] = $newToken;
                    ++$newStackPtr;

                    if ($i !== $lastPart) {
                        $newToken            = [];
                        $newToken['code']    = T_NS_SEPARATOR;
                        $newToken['type']    = 'T_NS_SEPARATOR';
                        $newToken['content'] = '\\';
                        $finalTokens[$newStackPtr] = $newToken;
                        ++$newStackPtr;
                    }
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $type    = Util\Tokens::tokenName($token[0]);
                    $content = Util\Common::prepareForOutput($token[1]);
                    echo "\t\t* token $stackPtr split into individual tokens; was: $type => $content".PHP_EOL;
                }

                continue;
            }//end if

            /*
                PHP 8.0 Attributes
            */

            if (PHP_VERSION_ID < 80000
                && $token[0] === T_COMMENT
                && strpos($token[1], '#[') === 0
            ) {
                $subTokens = $this->parsePhpAttribute($tokens, $stackPtr);
                if ($subTokens !== null) {
                    array_splice($tokens, $stackPtr, 1, $subTokens);
                    $numTokens = count($tokens);

                    $tokenIsArray = true;
                    $token        = $tokens[$stackPtr];
                } else {
                    $token[0] = T_ATTRIBUTE;
                }
            }

            if ($tokenIsArray === true
                && $token[0] === T_ATTRIBUTE
            ) {
                // Go looking for the close bracket.
                $bracketCloser = $this->findCloser($tokens, ($stackPtr + 1), ['[', '#['], ']');

                $newToken            = [];
                $newToken['code']    = T_ATTRIBUTE;
                $newToken['type']    = 'T_ATTRIBUTE';
                $newToken['content'] = '#[';
                $finalTokens[$newStackPtr] = $newToken;

                $tokens[$bracketCloser]    = [];
                $tokens[$bracketCloser][0] = T_ATTRIBUTE_END;
                $tokens[$bracketCloser][1] = ']';

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo "\t\t* token $bracketCloser changed from T_CLOSE_SQUARE_BRACKET to T_ATTRIBUTE_END".PHP_EOL;
                }

                $newStackPtr++;
                continue;
            }//end if

            /*
                Tokenize the parameter labels for PHP 8.0 named parameters as a special T_PARAM_NAME
                token and ensure that the colon after it is always T_COLON.
            */

            if ($tokenIsArray === true
                && ($token[0] === T_STRING
                || preg_match('`^[a-zA-Z_\x80-\xff]`', $token[1]) === 1)
            ) {
                // Get the next non-empty token.
                for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                    if (is_array($tokens[$i]) === false
                        || isset(Util\Tokens::$emptyTokens[$tokens[$i][0]]) === false
                    ) {
                        break;
                    }
                }

                if (isset($tokens[$i]) === true
                    && is_array($tokens[$i]) === false
                    && $tokens[$i] === ':'
                ) {
                    // Get the previous non-empty token.
                    for ($j = ($stackPtr - 1); $j > 0; $j--) {
                        if (is_array($tokens[$j]) === false
                            || isset(Util\Tokens::$emptyTokens[$tokens[$j][0]]) === false
                        ) {
                            break;
                        }
                    }

                    if (is_array($tokens[$j]) === false
                        && ($tokens[$j] === '('
                        || $tokens[$j] === ',')
                    ) {
                        $newToken            = [];
                        $newToken['code']    = T_PARAM_NAME;
                        $newToken['type']    = 'T_PARAM_NAME';
                        $newToken['content'] = $token[1];
                        $finalTokens[$newStackPtr] = $newToken;

                        $newStackPtr++;

                        // Modify the original token stack so that future checks, like
                        // determining T_COLON vs T_INLINE_ELSE can handle this correctly.
                        $tokens[$stackPtr][0] = T_PARAM_NAME;

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $type = Util\Tokens::tokenName($token[0]);
                            echo "\t\t* token $stackPtr changed from $type to T_PARAM_NAME".PHP_EOL;
                        }

                        continue;
                    }
                }//end if
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
                // Could be multi-line, so adjust the token stack.
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
                Between PHP 7.0 and 7.3, the ??= operator was tokenized as
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
                Before PHP 8, the ?-> operator was tokenized as
                T_INLINE_THEN followed by T_OBJECT_OPERATOR.
                So look for and combine these tokens in earlier versions.
            */

            if ($tokenIsArray === false
                && $token[0] === '?'
                && isset($tokens[($stackPtr + 1)]) === true
                && is_array($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)][0] === T_OBJECT_OPERATOR
            ) {
                $newToken            = [];
                $newToken['code']    = T_NULLSAFE_OBJECT_OPERATOR;
                $newToken['type']    = 'T_NULLSAFE_OBJECT_OPERATOR';
                $newToken['content'] = '?->';
                $finalTokens[$newStackPtr] = $newToken;

                $newStackPtr++;
                $stackPtr++;
                continue;
            }

            /*
                Before PHP 7.4, underscores inside T_LNUMBER and T_DNUMBER
                tokens split the token with a T_STRING. So look for
                and change these tokens in earlier versions.
            */

            if (PHP_VERSION_ID < 70400
                && ($tokenIsArray === true
                && ($token[0] === T_LNUMBER
                || $token[0] === T_DNUMBER)
                && isset($tokens[($stackPtr + 1)]) === true
                && is_array($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)][0] === T_STRING
                && $tokens[($stackPtr + 1)][1][0] === '_')
            ) {
                $newContent = $token[1];
                $newType    = $token[0];
                for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                    if (is_array($tokens[$i]) === false) {
                        break;
                    }

                    if ($tokens[$i][0] === T_LNUMBER
                        || $tokens[$i][0] === T_DNUMBER
                    ) {
                        $newContent .= $tokens[$i][1];
                        continue;
                    }

                    if ($tokens[$i][0] === T_STRING
                        && $tokens[$i][1][0] === '_'
                        && ((strpos($newContent, '0x') === 0
                        && preg_match('`^((?<!\.)_[0-9A-F][0-9A-F\.]*)+$`iD', $tokens[$i][1]) === 1)
                        || (strpos($newContent, '0x') !== 0
                        && substr($newContent, -1) !== '.'
                        && substr(strtolower($newContent), -1) !== 'e'
                        && preg_match('`^(?:(?<![\.e])_[0-9][0-9e\.]*)+$`iD', $tokens[$i][1]) === 1))
                    ) {
                        $newContent .= $tokens[$i][1];

                        // Support floats.
                        if (substr(strtolower($tokens[$i][1]), -1) === 'e'
                            && ($tokens[($i + 1)] === '-'
                            || $tokens[($i + 1)] === '+')
                        ) {
                            $newContent .= $tokens[($i + 1)];
                            $i++;
                        }

                        continue;
                    }//end if

                    break;
                }//end for

                if ($newType === T_LNUMBER
                    && ((stripos($newContent, '0x') === 0 && hexdec(str_replace('_', '', $newContent)) > PHP_INT_MAX)
                    || (stripos($newContent, '0b') === 0 && bindec(str_replace('_', '', $newContent)) > PHP_INT_MAX)
                    || (stripos($newContent, '0x') !== 0
                    && stripos($newContent, 'e') !== false || strpos($newContent, '.') !== false)
                    || (strpos($newContent, '0') === 0 && stripos($newContent, '0x') !== 0
                    && stripos($newContent, '0b') !== 0 && octdec(str_replace('_', '', $newContent)) > PHP_INT_MAX)
                    || (strpos($newContent, '0') !== 0 && str_replace('_', '', $newContent) > PHP_INT_MAX))
                ) {
                    $newType = T_DNUMBER;
                }

                $newToken            = [];
                $newToken['code']    = $newType;
                $newToken['type']    = Util\Tokens::tokenName($newType);
                $newToken['content'] = $newContent;
                $finalTokens[$newStackPtr] = $newToken;

                $newStackPtr++;
                $stackPtr = ($i - 1);
                continue;
            }//end if

            /*
                Backfill the T_MATCH token for PHP versions < 8.0 and
                do initial correction for non-match expression T_MATCH tokens
                to T_STRING for PHP >= 8.0.
                A final check for non-match expression T_MATCH tokens is done
                in PHP::processAdditional().
            */

            if ($tokenIsArray === true
                && (($token[0] === T_STRING
                && strtolower($token[1]) === 'match')
                || $token[0] === T_MATCH)
            ) {
                $isMatch = false;
                for ($x = ($stackPtr + 1); $x < $numTokens; $x++) {
                    if (isset($tokens[$x][0], Util\Tokens::$emptyTokens[$tokens[$x][0]]) === true) {
                        continue;
                    }

                    if ($tokens[$x] !== '(') {
                        // This is not a match expression.
                        break;
                    }

                    if (isset($this->tstringContexts[$finalTokens[$lastNotEmptyToken]['code']]) === true) {
                        // Also not a match expression.
                        break;
                    }

                    $isMatch = true;
                    break;
                }//end for

                if ($isMatch === true && $token[0] === T_STRING) {
                    $newToken            = [];
                    $newToken['code']    = T_MATCH;
                    $newToken['type']    = 'T_MATCH';
                    $newToken['content'] = $token[1];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* token $stackPtr changed from T_STRING to T_MATCH".PHP_EOL;
                    }

                    $finalTokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                    continue;
                } else if ($isMatch === false && $token[0] === T_MATCH) {
                    // PHP 8.0, match keyword, but not a match expression.
                    $newToken            = [];
                    $newToken['code']    = T_STRING;
                    $newToken['type']    = 'T_STRING';
                    $newToken['content'] = $token[1];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* token $stackPtr changed from T_MATCH to T_STRING".PHP_EOL;
                    }

                    $finalTokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                    continue;
                }//end if
            }//end if

            /*
                Retokenize the T_DEFAULT in match control structures as T_MATCH_DEFAULT
                to prevent scope being set and the scope for switch default statements
                breaking.
            */

            if ($tokenIsArray === true
                && $token[0] === T_DEFAULT
            ) {
                if (isset($this->tstringContexts[$finalTokens[$lastNotEmptyToken]['code']]) === false) {
                    for ($x = ($stackPtr + 1); $x < $numTokens; $x++) {
                        if ($tokens[$x] === ',') {
                            // Skip over potential trailing comma (supported in PHP).
                            continue;
                        }

                        if (is_array($tokens[$x]) === false
                            || isset(Util\Tokens::$emptyTokens[$tokens[$x][0]]) === false
                        ) {
                            // Non-empty, non-comma content.
                            break;
                        }
                    }

                    if (isset($tokens[$x]) === true
                        && is_array($tokens[$x]) === true
                        && $tokens[$x][0] === T_DOUBLE_ARROW
                    ) {
                        // Modify the original token stack for the double arrow so that
                        // future checks can disregard the double arrow token more easily.
                        // For match expression "case" statements, this is handled
                        // in PHP::processAdditional().
                        $tokens[$x][0] = T_MATCH_ARROW;
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t\t* token $x changed from T_DOUBLE_ARROW to T_MATCH_ARROW".PHP_EOL;
                        }

                        $newToken            = [];
                        $newToken['code']    = T_MATCH_DEFAULT;
                        $newToken['type']    = 'T_MATCH_DEFAULT';
                        $newToken['content'] = $token[1];

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t\t* token $stackPtr changed from T_DEFAULT to T_MATCH_DEFAULT".PHP_EOL;
                        }

                        $finalTokens[$newStackPtr] = $newToken;
                        $newStackPtr++;
                        continue;
                    }//end if
                } else {
                    // Definitely not the "default" keyword.
                    $newToken            = [];
                    $newToken['code']    = T_STRING;
                    $newToken['type']    = 'T_STRING';
                    $newToken['content'] = $token[1];

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* token $stackPtr changed from T_DEFAULT to T_STRING".PHP_EOL;
                    }

                    $finalTokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                    continue;
                }//end if
            }//end if

            /*
                Convert ? to T_NULLABLE OR T_INLINE_THEN
            */

            if ($tokenIsArray === false && $token[0] === '?') {
                $newToken            = [];
                $newToken['content'] = '?';

                /*
                 * Check if the next non-empty token is one of the tokens which can be used
                 * in type declarations. If not, it's definitely a ternary.
                 * At this point, the only token types which need to be taken into consideration
                 * as potential type declarations are identifier names, T_ARRAY, T_CALLABLE and T_NS_SEPARATOR.
                 */

                $lastRelevantNonEmpty = null;

                for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                    if (is_array($tokens[$i]) === true) {
                        $tokenType = $tokens[$i][0];
                    } else {
                        $tokenType = $tokens[$i];
                    }

                    if (isset(Util\Tokens::$emptyTokens[$tokenType]) === true) {
                        continue;
                    }

                    if ($tokenType === T_STRING
                        || $tokenType === T_NAME_FULLY_QUALIFIED
                        || $tokenType === T_NAME_RELATIVE
                        || $tokenType === T_NAME_QUALIFIED
                        || $tokenType === T_ARRAY
                        || $tokenType === T_NAMESPACE
                        || $tokenType === T_NS_SEPARATOR
                    ) {
                        $lastRelevantNonEmpty = $tokenType;
                        continue;
                    }

                    if (($tokenType !== T_CALLABLE
                        && isset($lastRelevantNonEmpty) === false)
                        || ($lastRelevantNonEmpty === T_ARRAY
                        && $tokenType === '(')
                        || (($lastRelevantNonEmpty === T_STRING
                        || $lastRelevantNonEmpty === T_NAME_FULLY_QUALIFIED
                        || $lastRelevantNonEmpty === T_NAME_RELATIVE
                        || $lastRelevantNonEmpty === T_NAME_QUALIFIED)
                        && ($tokenType === T_DOUBLE_COLON
                        || $tokenType === '('
                        || $tokenType === ':'))
                    ) {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t\t* token $stackPtr changed from ? to T_INLINE_THEN".PHP_EOL;
                        }

                        $newToken['code'] = T_INLINE_THEN;
                        $newToken['type'] = 'T_INLINE_THEN';

                        $insideInlineIf[] = $stackPtr;

                        $finalTokens[$newStackPtr] = $newToken;
                        $newStackPtr++;
                        continue 2;
                    }

                    break;
                }//end for

                /*
                 * This can still be a nullable type or a ternary.
                 * Do additional checking.
                 */

                $prevNonEmpty     = null;
                $lastSeenNonEmpty = null;

                for ($i = ($stackPtr - 1); $i >= 0; $i--) {
                    if (is_array($tokens[$i]) === true) {
                        $tokenType = $tokens[$i][0];
                    } else {
                        $tokenType = $tokens[$i];
                    }

                    if ($tokenType === T_STATIC
                        && ($lastSeenNonEmpty === T_DOUBLE_COLON
                        || $lastSeenNonEmpty === '(')
                    ) {
                        $lastSeenNonEmpty = $tokenType;
                        continue;
                    }

                    if ($prevNonEmpty === null
                        && isset(Util\Tokens::$emptyTokens[$tokenType]) === false
                    ) {
                        // Found the previous non-empty token.
                        if ($tokenType === ':' || $tokenType === ',' || $tokenType === T_ATTRIBUTE_END) {
                            $newToken['code'] = T_NULLABLE;
                            $newToken['type'] = 'T_NULLABLE';

                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                echo "\t\t* token $stackPtr changed from ? to T_NULLABLE".PHP_EOL;
                            }

                            break;
                        }

                        $prevNonEmpty = $tokenType;
                    }

                    if ($tokenType === T_FUNCTION
                        || $tokenType === T_FN
                        || isset(Util\Tokens::$methodPrefixes[$tokenType]) === true
                        || $tokenType === T_VAR
                    ) {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t\t* token $stackPtr changed from ? to T_NULLABLE".PHP_EOL;
                        }

                        $newToken['code'] = T_NULLABLE;
                        $newToken['type'] = 'T_NULLABLE';
                        break;
                    } else if (in_array($tokenType, [T_DOUBLE_ARROW, T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, '=', '{', ';'], true) === true) {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t\t* token $stackPtr changed from ? to T_INLINE_THEN".PHP_EOL;
                        }

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
                Tokens after a double colon may look like scope openers,
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
                Backfill the T_FN token for PHP versions < 7.4.
            */

            if ($tokenIsArray === true
                && $token[0] === T_STRING
                && strtolower($token[1]) === 'fn'
            ) {
                // Modify the original token stack so that
                // future checks (like looking for T_NULLABLE) can
                // detect the T_FN token more easily.
                $tokens[$stackPtr][0] = T_FN;
                $token[0] = T_FN;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo "\t\t* token $stackPtr changed from T_STRING to T_FN".PHP_EOL;
                }
            }

            /*
                The string-like token after a function keyword should always be
                tokenized as T_STRING even if it appears to be a different token,
                such as when writing code like: function default(): foo
                so go forward and change the token type before it is processed.

                Note: this should not be done for `function Level\Name` within a
                group use statement for the PHP 8 identifier name tokens as it
                would interfere with the re-tokenization of those.
            */

            if ($tokenIsArray === true
                && ($token[0] === T_FUNCTION
                || $token[0] === T_FN)
                && $finalTokens[$lastNotEmptyToken]['code'] !== T_USE
            ) {
                if ($token[0] === T_FUNCTION) {
                    for ($x = ($stackPtr + 1); $x < $numTokens; $x++) {
                        if (is_array($tokens[$x]) === false
                            || (isset(Util\Tokens::$emptyTokens[$tokens[$x][0]]) === false
                            && $tokens[$x][1] !== '&')
                        ) {
                            // Non-empty content.
                            break;
                        }
                    }

                    if ($x < $numTokens
                        && is_array($tokens[$x]) === true
                        && $tokens[$x][0] !== T_STRING
                        && $tokens[$x][0] !== T_NAME_QUALIFIED
                    ) {
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $oldType = Util\Tokens::tokenName($tokens[$x][0]);
                            echo "\t\t* token $x changed from $oldType to T_STRING".PHP_EOL;
                        }

                        $tokens[$x][0] = T_STRING;
                    }
                }//end if

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
                            T_STRING               => T_STRING,
                            T_NAME_FULLY_QUALIFIED => T_NAME_FULLY_QUALIFIED,
                            T_NAME_RELATIVE        => T_NAME_RELATIVE,
                            T_NAME_QUALIFIED       => T_NAME_QUALIFIED,
                            T_ARRAY                => T_ARRAY,
                            T_CALLABLE             => T_CALLABLE,
                            T_SELF                 => T_SELF,
                            T_PARENT               => T_PARENT,
                            T_NAMESPACE            => T_NAMESPACE,
                            T_STATIC               => T_STATIC,
                            T_NS_SEPARATOR         => T_NS_SEPARATOR,
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
                                // But also convert the token to save the tokenizer
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
                && (is_array($tokens[($stackPtr - 1)]) === false
                || $tokens[($stackPtr - 1)][0] !== T_PAAMAYIM_NEKUDOTAYIM)
            ) {
                $stopTokens = [
                    T_CASE               => true,
                    T_SEMICOLON          => true,
                    T_OPEN_TAG           => true,
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
                    if (isset($this->tstringContexts[$finalTokens[$lastNotEmptyToken]['code']]) === true) {
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
                    $isInlineIf = true;

                    // Make sure this isn't a named parameter label.
                    // Get the previous non-empty token.
                    for ($i = ($stackPtr - 1); $i > 0; $i--) {
                        if (is_array($tokens[$i]) === false
                            || isset(Util\Tokens::$emptyTokens[$tokens[$i][0]]) === false
                        ) {
                            break;
                        }
                    }

                    if ($tokens[$i][0] === T_PARAM_NAME) {
                        $isInlineIf = false;
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t\t* token is parameter label, not T_INLINE_ELSE".PHP_EOL;
                        }
                    }

                    if ($isInlineIf === true) {
                        // Make sure this isn't a return type separator.
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
                            // non-empty token is FUNCTION or USE, this is a return type.
                            // Note that we need to skip T_STRING tokens here as these
                            // can be function names.
                            for ($i--; $i > 0; $i--) {
                                if (is_array($tokens[$i]) === false
                                    || ($tokens[$i][0] !== T_DOC_COMMENT
                                    && $tokens[$i][0] !== T_COMMENT
                                    && $tokens[$i][0] !== T_WHITESPACE
                                    && $tokens[$i][0] !== T_STRING)
                                ) {
                                    break;
                                }
                            }

                            if ($tokens[$i][0] === T_FUNCTION || $tokens[$i][0] === T_FN || $tokens[$i][0] === T_USE) {
                                $isInlineIf = false;
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    echo "\t\t* token is return type, not T_INLINE_ELSE".PHP_EOL;
                                }
                            }
                        }//end if
                    }//end if

                    // Check to see if this is a CASE or DEFAULT opener.
                    if ($isInlineIf === true) {
                        $inlineIfToken = $insideInlineIf[(count($insideInlineIf) - 1)];
                        for ($i = $stackPtr; $i > $inlineIfToken; $i--) {
                            if (is_array($tokens[$i]) === true
                                && ($tokens[$i][0] === T_CASE
                                || $tokens[$i][0] === T_DEFAULT)
                            ) {
                                $isInlineIf = false;
                                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                    echo "\t\t* token is T_CASE or T_DEFAULT opener, not T_INLINE_ELSE".PHP_EOL;
                                }

                                break;
                            }

                            if (is_array($tokens[$i]) === false
                                && ($tokens[$i] === ';'
                                || $tokens[$i] === '{')
                            ) {
                                break;
                            }
                        }
                    }//end if

                    if ($isInlineIf === true) {
                        array_pop($insideInlineIf);
                        $newToken['code'] = T_INLINE_ELSE;
                        $newToken['type'] = 'T_INLINE_ELSE';

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t\t* token changed from T_COLON to T_INLINE_ELSE".PHP_EOL;
                        }
                    }
                }//end if

                // This is a special condition for T_ARRAY tokens used for anything else
                // but array declarations, like type hinting function arguments as
                // being arrays.
                // We want to keep the parenthesis map clean, so let's tag these tokens as
                // T_STRING.
                if ($newToken['code'] === T_ARRAY) {
                    for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                        if (is_array($tokens[$i]) === false
                            || isset(Util\Tokens::$emptyTokens[$tokens[$i][0]]) === false
                        ) {
                            // Non-empty content.
                            break;
                        }
                    }

                    if ($tokens[$i] !== '(' && $i !== $numTokens) {
                        $newToken['code'] = T_STRING;
                        $newToken['type'] = 'T_STRING';
                    }
                }

                // This is a special case when checking PHP 5.5+ code in PHP < 5.5
                // where "finally" should be T_FINALLY instead of T_STRING.
                if ($newToken['code'] === T_STRING
                    && strtolower($newToken['content']) === 'finally'
                    && $finalTokens[$lastNotEmptyToken]['code'] === T_CLOSE_CURLY_BRACKET
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

        $this->createAttributesNestingMap();

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
            } else if ($this->tokens[$i]['code'] === T_FN && isset($this->tokens[($i + 1)]) === true) {
                // Possible arrow function.
                for ($x = ($i + 1); $x < $numTokens; $x++) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false
                        && $this->tokens[$x]['code'] !== T_BITWISE_AND
                    ) {
                        // Non-whitespace content.
                        break;
                    }
                }

                if (isset($this->tokens[$x]) === true && $this->tokens[$x]['code'] === T_OPEN_PARENTHESIS) {
                    $ignore  = Util\Tokens::$emptyTokens;
                    $ignore += [
                        T_ARRAY        => T_ARRAY,
                        T_CALLABLE     => T_CALLABLE,
                        T_COLON        => T_COLON,
                        T_NAMESPACE    => T_NAMESPACE,
                        T_NS_SEPARATOR => T_NS_SEPARATOR,
                        T_NULL         => T_NULL,
                        T_NULLABLE     => T_NULLABLE,
                        T_PARENT       => T_PARENT,
                        T_SELF         => T_SELF,
                        T_STATIC       => T_STATIC,
                        T_STRING       => T_STRING,
                        T_TYPE_UNION   => T_TYPE_UNION,
                    ];

                    $closer = $this->tokens[$x]['parenthesis_closer'];
                    for ($arrow = ($closer + 1); $arrow < $numTokens; $arrow++) {
                        if (isset($ignore[$this->tokens[$arrow]['code']]) === false) {
                            break;
                        }
                    }

                    if ($this->tokens[$arrow]['code'] === T_DOUBLE_ARROW) {
                        $endTokens = [
                            T_COLON                => true,
                            T_COMMA                => true,
                            T_SEMICOLON            => true,
                            T_CLOSE_PARENTHESIS    => true,
                            T_CLOSE_SQUARE_BRACKET => true,
                            T_CLOSE_CURLY_BRACKET  => true,
                            T_CLOSE_SHORT_ARRAY    => true,
                            T_OPEN_TAG             => true,
                            T_CLOSE_TAG            => true,
                        ];

                        $inTernary    = false;
                        $lastEndToken = null;

                        for ($scopeCloser = ($arrow + 1); $scopeCloser < $numTokens; $scopeCloser++) {
                            // Arrow function closer should never be shared with the closer of a match
                            // control structure.
                            if (isset($this->tokens[$scopeCloser]['scope_closer'], $this->tokens[$scopeCloser]['scope_condition']) === true
                                && $scopeCloser === $this->tokens[$scopeCloser]['scope_closer']
                                && $this->tokens[$this->tokens[$scopeCloser]['scope_condition']]['code'] === T_MATCH
                            ) {
                                if ($arrow < $this->tokens[$scopeCloser]['scope_condition']) {
                                    // Match in return value of arrow function. Move on to the next token.
                                    continue;
                                }

                                // Arrow function as return value for the last match case without trailing comma.
                                if ($lastEndToken !== null) {
                                    $scopeCloser = $lastEndToken;
                                    break;
                                }

                                for ($lastNonEmpty = ($scopeCloser - 1); $lastNonEmpty > $arrow; $lastNonEmpty--) {
                                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$lastNonEmpty]['code']]) === false) {
                                        $scopeCloser = $lastNonEmpty;
                                        break 2;
                                    }
                                }
                            }

                            if (isset($endTokens[$this->tokens[$scopeCloser]['code']]) === true) {
                                if ($lastEndToken !== null
                                    && ((isset($this->tokens[$scopeCloser]['parenthesis_opener']) === true
                                    && $this->tokens[$scopeCloser]['parenthesis_opener'] < $arrow)
                                    || (isset($this->tokens[$scopeCloser]['bracket_opener']) === true
                                    && $this->tokens[$scopeCloser]['bracket_opener'] < $arrow))
                                ) {
                                    for ($lastNonEmpty = ($scopeCloser - 1); $lastNonEmpty > $arrow; $lastNonEmpty--) {
                                        if (isset(Util\Tokens::$emptyTokens[$this->tokens[$lastNonEmpty]['code']]) === false) {
                                            $scopeCloser = $lastNonEmpty;
                                            break;
                                        }
                                    }
                                }

                                break;
                            }

                            if ($inTernary === false
                                && isset($this->tokens[$scopeCloser]['scope_closer'], $this->tokens[$scopeCloser]['scope_condition']) === true
                                && $scopeCloser === $this->tokens[$scopeCloser]['scope_closer']
                                && $this->tokens[$this->tokens[$scopeCloser]['scope_condition']]['code'] === T_FN
                            ) {
                                // Found a nested arrow function that already has the closer set and is in
                                // the same scope as us, so we can use its closer.
                                break;
                            }

                            if (isset($this->tokens[$scopeCloser]['scope_closer']) === true
                                && $this->tokens[$scopeCloser]['code'] !== T_INLINE_ELSE
                                && $this->tokens[$scopeCloser]['code'] !== T_END_HEREDOC
                                && $this->tokens[$scopeCloser]['code'] !== T_END_NOWDOC
                            ) {
                                // We minus 1 here in case the closer can be shared with us.
                                $scopeCloser = ($this->tokens[$scopeCloser]['scope_closer'] - 1);
                                continue;
                            }

                            if (isset($this->tokens[$scopeCloser]['parenthesis_closer']) === true) {
                                $scopeCloser  = $this->tokens[$scopeCloser]['parenthesis_closer'];
                                $lastEndToken = $scopeCloser;
                                continue;
                            }

                            if (isset($this->tokens[$scopeCloser]['bracket_closer']) === true) {
                                $scopeCloser  = $this->tokens[$scopeCloser]['bracket_closer'];
                                $lastEndToken = $scopeCloser;
                                continue;
                            }

                            if ($this->tokens[$scopeCloser]['code'] === T_INLINE_THEN) {
                                $inTernary = true;
                                continue;
                            }

                            if ($this->tokens[$scopeCloser]['code'] === T_INLINE_ELSE) {
                                if ($inTernary === false) {
                                    break;
                                }

                                $inTernary = false;
                                continue;
                            }
                        }//end for

                        if ($scopeCloser !== $numTokens) {
                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $line = $this->tokens[$i]['line'];
                                echo "\t=> token $i on line $line processed as arrow function".PHP_EOL;
                                echo "\t\t* scope opener set to $arrow *".PHP_EOL;
                                echo "\t\t* scope closer set to $scopeCloser *".PHP_EOL;
                                echo "\t\t* parenthesis opener set to $x *".PHP_EOL;
                                echo "\t\t* parenthesis closer set to $closer *".PHP_EOL;
                            }

                            $this->tokens[$i]['code']            = T_FN;
                            $this->tokens[$i]['type']            = 'T_FN';
                            $this->tokens[$i]['scope_condition'] = $i;
                            $this->tokens[$i]['scope_opener']    = $arrow;
                            $this->tokens[$i]['scope_closer']    = $scopeCloser;
                            $this->tokens[$i]['parenthesis_owner']  = $i;
                            $this->tokens[$i]['parenthesis_opener'] = $x;
                            $this->tokens[$i]['parenthesis_closer'] = $closer;

                            $this->tokens[$arrow]['code'] = T_FN_ARROW;
                            $this->tokens[$arrow]['type'] = 'T_FN_ARROW';

                            $this->tokens[$arrow]['scope_condition']       = $i;
                            $this->tokens[$arrow]['scope_opener']          = $arrow;
                            $this->tokens[$arrow]['scope_closer']          = $scopeCloser;
                            $this->tokens[$scopeCloser]['scope_condition'] = $i;
                            $this->tokens[$scopeCloser]['scope_opener']    = $arrow;
                            $this->tokens[$scopeCloser]['scope_closer']    = $scopeCloser;

                            $opener = $this->tokens[$i]['parenthesis_opener'];
                            $closer = $this->tokens[$i]['parenthesis_closer'];
                            $this->tokens[$opener]['parenthesis_owner'] = $i;
                            $this->tokens[$closer]['parenthesis_owner'] = $i;

                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                $line = $this->tokens[$arrow]['line'];
                                echo "\t\t* token $arrow on line $line changed from T_DOUBLE_ARROW to T_FN_ARROW".PHP_EOL;
                            }
                        }//end if
                    }//end if
                }//end if

                // If after all that, the extra tokens are not set, this is not an arrow function.
                if (isset($this->tokens[$i]['scope_closer']) === false) {
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $line = $this->tokens[$i]['line'];
                        echo "\t=> token $i on line $line is not an arrow function".PHP_EOL;
                        echo "\t\t* token changed from T_FN to T_STRING".PHP_EOL;
                    }

                    $this->tokens[$i]['code'] = T_STRING;
                    $this->tokens[$i]['type'] = 'T_STRING';
                }
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
                    T_NULLSAFE_OBJECT_OPERATOR => T_NULLSAFE_OBJECT_OPERATOR,
                    T_STRING                   => T_STRING,
                    T_CONSTANT_ENCAPSED_STRING => T_CONSTANT_ENCAPSED_STRING,
                    T_DOUBLE_QUOTED_STRING     => T_DOUBLE_QUOTED_STRING,
                ];
                $allowed     += Util\Tokens::$magicConstants;

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
            } else if ($this->tokens[$i]['code'] === T_MATCH) {
                if (isset($this->tokens[$i]['scope_opener'], $this->tokens[$i]['scope_closer']) === false) {
                    // Not a match expression after all.
                    $this->tokens[$i]['code'] = T_STRING;
                    $this->tokens[$i]['type'] = 'T_STRING';

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* token $i changed from T_MATCH to T_STRING".PHP_EOL;
                    }

                    if (isset($this->tokens[$i]['parenthesis_opener'], $this->tokens[$i]['parenthesis_closer']) === true) {
                        $opener = $this->tokens[$i]['parenthesis_opener'];
                        $closer = $this->tokens[$i]['parenthesis_closer'];
                        unset(
                            $this->tokens[$opener]['parenthesis_owner'],
                            $this->tokens[$closer]['parenthesis_owner']
                        );
                        unset(
                            $this->tokens[$i]['parenthesis_opener'],
                            $this->tokens[$i]['parenthesis_closer'],
                            $this->tokens[$i]['parenthesis_owner']
                        );

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t\t* cleaned parenthesis of token $i *".PHP_EOL;
                        }
                    }
                } else {
                    // Retokenize the double arrows for match expression cases to `T_MATCH_ARROW`.
                    $searchFor  = [
                        T_OPEN_CURLY_BRACKET  => T_OPEN_CURLY_BRACKET,
                        T_OPEN_SQUARE_BRACKET => T_OPEN_SQUARE_BRACKET,
                        T_OPEN_PARENTHESIS    => T_OPEN_PARENTHESIS,
                        T_OPEN_SHORT_ARRAY    => T_OPEN_SHORT_ARRAY,
                        T_DOUBLE_ARROW        => T_DOUBLE_ARROW,
                    ];
                    $searchFor += Util\Tokens::$scopeOpeners;

                    for ($x = ($this->tokens[$i]['scope_opener'] + 1); $x < $this->tokens[$i]['scope_closer']; $x++) {
                        if (isset($searchFor[$this->tokens[$x]['code']]) === false) {
                            continue;
                        }

                        if (isset($this->tokens[$x]['scope_closer']) === true) {
                            $x = $this->tokens[$x]['scope_closer'];
                            continue;
                        }

                        if (isset($this->tokens[$x]['parenthesis_closer']) === true) {
                            $x = $this->tokens[$x]['parenthesis_closer'];
                            continue;
                        }

                        if (isset($this->tokens[$x]['bracket_closer']) === true) {
                            $x = $this->tokens[$x]['bracket_closer'];
                            continue;
                        }

                        // This must be a double arrow, but make sure anyhow.
                        if ($this->tokens[$x]['code'] === T_DOUBLE_ARROW) {
                            $this->tokens[$x]['code'] = T_MATCH_ARROW;
                            $this->tokens[$x]['type'] = 'T_MATCH_ARROW';

                            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                                echo "\t\t* token $x changed from T_DOUBLE_ARROW to T_MATCH_ARROW".PHP_EOL;
                            }
                        }
                    }//end for
                }//end if

                continue;
            } else if ($this->tokens[$i]['code'] === T_BITWISE_OR) {
                /*
                    Convert "|" to T_TYPE_UNION or leave as T_BITWISE_OR.
                */

                $allowed = [
                    T_STRING       => T_STRING,
                    T_CALLABLE     => T_CALLABLE,
                    T_SELF         => T_SELF,
                    T_PARENT       => T_PARENT,
                    T_STATIC       => T_STATIC,
                    T_FALSE        => T_FALSE,
                    T_NULL         => T_NULL,
                    T_NAMESPACE    => T_NAMESPACE,
                    T_NS_SEPARATOR => T_NS_SEPARATOR,
                ];

                $suspectedType  = null;
                $typeTokenCount = 0;

                for ($x = ($i + 1); $x < $numTokens; $x++) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === true) {
                        continue;
                    }

                    if (isset($allowed[$this->tokens[$x]['code']]) === true) {
                        ++$typeTokenCount;
                        continue;
                    }

                    if ($typeTokenCount > 0
                        && ($this->tokens[$x]['code'] === T_BITWISE_AND
                        || $this->tokens[$x]['code'] === T_ELLIPSIS)
                    ) {
                        // Skip past reference and variadic indicators for parameter types.
                        continue;
                    }

                    if ($this->tokens[$x]['code'] === T_VARIABLE) {
                        // Parameter/Property defaults can not contain variables, so this could be a type.
                        $suspectedType = 'property or parameter';
                        break;
                    }

                    if ($this->tokens[$x]['code'] === T_DOUBLE_ARROW) {
                        // Possible arrow function.
                        $suspectedType = 'return';
                        break;
                    }

                    if ($this->tokens[$x]['code'] === T_SEMICOLON) {
                        // Possible abstract method or interface method.
                        $suspectedType = 'return';
                        break;
                    }

                    if ($this->tokens[$x]['code'] === T_OPEN_CURLY_BRACKET
                        && isset($this->tokens[$x]['scope_condition']) === true
                        && $this->tokens[$this->tokens[$x]['scope_condition']]['code'] === T_FUNCTION
                    ) {
                        $suspectedType = 'return';
                    }

                    break;
                }//end for

                if ($typeTokenCount === 0 || isset($suspectedType) === false) {
                    // Definitely not a union type, move on.
                    continue;
                }

                $typeTokenCount = 0;
                $unionOperators = [$i];
                $confirmed      = false;

                for ($x = ($i - 1); $x >= 0; $x--) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === true) {
                        continue;
                    }

                    if (isset($allowed[$this->tokens[$x]['code']]) === true) {
                        ++$typeTokenCount;
                        continue;
                    }

                    // Union types can't use the nullable operator, but be tolerant to parse errors.
                    if ($typeTokenCount > 0 && $this->tokens[$x]['code'] === T_NULLABLE) {
                        continue;
                    }

                    if ($this->tokens[$x]['code'] === T_BITWISE_OR) {
                        $unionOperators[] = $x;
                        continue;
                    }

                    if ($suspectedType === 'return' && $this->tokens[$x]['code'] === T_COLON) {
                        $confirmed = true;
                        break;
                    }

                    if ($suspectedType === 'property or parameter'
                        && (isset(Util\Tokens::$scopeModifiers[$this->tokens[$x]['code']]) === true
                        || $this->tokens[$x]['code'] === T_VAR)
                    ) {
                        // This will also confirm constructor property promotion parameters, but that's fine.
                        $confirmed = true;
                    }

                    break;
                }//end for

                if ($confirmed === false
                    && $suspectedType === 'property or parameter'
                    && isset($this->tokens[$i]['nested_parenthesis']) === true
                ) {
                    $parens = $this->tokens[$i]['nested_parenthesis'];
                    $last   = end($parens);

                    if (isset($this->tokens[$last]['parenthesis_owner']) === true
                        && $this->tokens[$this->tokens[$last]['parenthesis_owner']]['code'] === T_FUNCTION
                    ) {
                        $confirmed = true;
                    } else {
                        // No parenthesis owner set, this may be an arrow function which has not yet
                        // had additional processing done.
                        if (isset($this->tokens[$last]['parenthesis_opener']) === true) {
                            for ($x = ($this->tokens[$last]['parenthesis_opener'] - 1); $x >= 0; $x--) {
                                if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === true) {
                                    continue;
                                }

                                break;
                            }

                            if ($this->tokens[$x]['code'] === T_FN) {
                                for (--$x; $x >= 0; $x--) {
                                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === true
                                        || $this->tokens[$x]['code'] === T_BITWISE_AND
                                    ) {
                                        continue;
                                    }

                                    break;
                                }

                                if ($this->tokens[$x]['code'] !== T_FUNCTION) {
                                    $confirmed = true;
                                }
                            }
                        }//end if
                    }//end if

                    unset($parens, $last);
                }//end if

                if ($confirmed === false) {
                    // Not a union type after all, move on.
                    continue;
                }

                foreach ($unionOperators as $x) {
                    $this->tokens[$x]['code'] = T_TYPE_UNION;
                    $this->tokens[$x]['type'] = 'T_TYPE_UNION';

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        $line = $this->tokens[$x]['line'];
                        echo "\t* token $x on line $line changed from T_BITWISE_OR to T_TYPE_UNION".PHP_EOL;
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

                if (isset($this->tstringContexts[$this->tokens[$x]['code']]) === true) {
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


    /**
     * Finds a "closer" token (closing parenthesis or square bracket for example)
     * Handle parenthesis balancing while searching for closing token
     *
     * @param array           $tokens       The list of tokens to iterate searching the closing token (as returned by token_get_all)
     * @param int             $start        The starting position
     * @param string|string[] $openerTokens The opening character
     * @param string          $closerChar   The closing character
     *
     * @return int|null The position of the closing token, if found. NULL otherwise.
     */
    private function findCloser(array &$tokens, $start, $openerTokens, $closerChar)
    {
        $numTokens    = count($tokens);
        $stack        = [0];
        $closer       = null;
        $openerTokens = (array) $openerTokens;

        for ($x = $start; $x < $numTokens; $x++) {
            if (in_array($tokens[$x], $openerTokens, true) === true
                || (is_array($tokens[$x]) === true && in_array($tokens[$x][1], $openerTokens, true) === true)
            ) {
                $stack[] = $x;
            } else if ($tokens[$x] === $closerChar) {
                array_pop($stack);
                if (empty($stack) === true) {
                    $closer = $x;
                    break;
                }
            }
        }

        return $closer;

    }//end findCloser()


    /**
     * PHP 8 attributes parser for PHP < 8
     * Handles single-line and multiline attributes.
     *
     * @param array $tokens   The original array of tokens (as returned by token_get_all)
     * @param int   $stackPtr The current position in token array
     *
     * @return array|null The array of parsed attribute tokens
     */
    private function parsePhpAttribute(array &$tokens, $stackPtr)
    {

        $token = $tokens[$stackPtr];

        $commentBody = substr($token[1], 2);
        $subTokens   = @token_get_all('<?php '.$commentBody);

        foreach ($subTokens as $i => $subToken) {
            if (is_array($subToken) === true
                && $subToken[0] === T_COMMENT
                && strpos($subToken[1], '#[') === 0
            ) {
                $reparsed = $this->parsePhpAttribute($subTokens, $i);
                if ($reparsed !== null) {
                    array_splice($subTokens, $i, 1, $reparsed);
                } else {
                    $subToken[0] = T_ATTRIBUTE;
                }
            }
        }

        array_splice($subTokens, 0, 1, [[T_ATTRIBUTE, '#[']]);

        // Go looking for the close bracket.
        $bracketCloser = $this->findCloser($subTokens, 1, '[', ']');
        if (PHP_VERSION_ID < 80000 && $bracketCloser === null) {
            foreach (array_slice($tokens, ($stackPtr + 1)) as $token) {
                if (is_array($token) === true) {
                    $commentBody .= $token[1];
                } else {
                    $commentBody .= $token;
                }
            }

            $subTokens = @token_get_all('<?php '.$commentBody);
            array_splice($subTokens, 0, 1, [[T_ATTRIBUTE, '#[']]);

            $bracketCloser = $this->findCloser($subTokens, 1, '[', ']');
            if ($bracketCloser !== null) {
                array_splice($tokens, ($stackPtr + 1), count($tokens), array_slice($subTokens, ($bracketCloser + 1)));
                $subTokens = array_slice($subTokens, 0, ($bracketCloser + 1));
            }
        }

        if ($bracketCloser === null) {
            return null;
        }

        return $subTokens;

    }//end parsePhpAttribute()


    /**
     * Creates a map for the attributes tokens that surround other tokens.
     *
     * @return void
     */
    private function createAttributesNestingMap()
    {
        $map = [];
        for ($i = 0; $i < $this->numTokens; $i++) {
            if (isset($this->tokens[$i]['attribute_opener']) === true
                && $i === $this->tokens[$i]['attribute_opener']
            ) {
                if (empty($map) === false) {
                    $this->tokens[$i]['nested_attributes'] = $map;
                }

                if (isset($this->tokens[$i]['attribute_closer']) === true) {
                    $map[$this->tokens[$i]['attribute_opener']]
                        = $this->tokens[$i]['attribute_closer'];
                }
            } else if (isset($this->tokens[$i]['attribute_closer']) === true
                && $i === $this->tokens[$i]['attribute_closer']
            ) {
                array_pop($map);
                if (empty($map) === false) {
                    $this->tokens[$i]['nested_attributes'] = $map;
                }
            } else {
                if (empty($map) === false) {
                    $this->tokens[$i]['nested_attributes'] = $map;
                }
            }//end if
        }//end for

    }//end createAttributesNestingMap()


}//end class
