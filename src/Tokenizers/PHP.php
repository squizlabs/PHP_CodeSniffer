<?php
/**
 * Tokenizes PHP code.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/Symplify\PHP7_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace Symplify\PHP7_CodeSniffer\Tokenizers;

use Symplify\PHP7_CodeSniffer\Util;

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
    public $scopeOpeners = array(
                            T_IF            => array(
                                                'start'  => array(
                                                             T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                                                             T_COLON              => T_COLON,
                                                            ),
                                                'end'    => array(
                                                             T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                                                             T_ENDIF               => T_ENDIF,
                                                             T_ELSE                => T_ELSE,
                                                             T_ELSEIF              => T_ELSEIF,
                                                            ),
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(
                                                             T_ELSE   => T_ELSE,
                                                             T_ELSEIF => T_ELSEIF,
                                                            ),
                                               ),
                            T_TRY           => array(
                                                'start'  => array(T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET),
                                                'end'    => array(T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET),
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_CATCH         => array(
                                                'start'  => array(T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET),
                                                'end'    => array(T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET),
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_FINALLY       => array(
                                                'start'  => array(T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET),
                                                'end'    => array(T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET),
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_ELSE          => array(
                                                'start'  => array(
                                                             T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                                                             T_COLON              => T_COLON,
                                                            ),
                                                'end'    => array(
                                                             T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                                                             T_ENDIF               => T_ENDIF,
                                                            ),
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(
                                                             T_IF     => T_IF,
                                                             T_ELSEIF => T_ELSEIF,
                                                            ),
                                               ),
                            T_ELSEIF        => array(
                                                'start'  => array(
                                                             T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                                                             T_COLON              => T_COLON,
                                                            ),
                                                'end'    => array(
                                                             T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                                                             T_ENDIF               => T_ENDIF,
                                                             T_ELSE                => T_ELSE,
                                                             T_ELSEIF              => T_ELSEIF,
                                                            ),
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(
                                                             T_IF   => T_IF,
                                                             T_ELSE => T_ELSE,
                                                            ),
                                               ),
                            T_FOR           => array(
                                                'start'  => array(
                                                             T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                                                             T_COLON              => T_COLON,
                                                            ),
                                                'end'    => array(
                                                             T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                                                             T_ENDFOR              => T_ENDFOR,
                                                            ),
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_FOREACH       => array(
                                                'start'  => array(
                                                             T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                                                             T_COLON              => T_COLON,
                                                            ),
                                                'end'    => array(
                                                             T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                                                             T_ENDFOREACH          => T_ENDFOREACH,
                                                            ),
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_INTERFACE     => array(
                                                'start'  => array(T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET),
                                                'end'    => array(T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET),
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_FUNCTION      => array(
                                                'start'  => array(T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET),
                                                'end'    => array(T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET),
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_CLASS         => array(
                                                'start'  => array(T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET),
                                                'end'    => array(T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET),
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_TRAIT         => array(
                                                'start'  => array(T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET),
                                                'end'    => array(T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET),
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_USE           => array(
                                                'start'  => array(T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET),
                                                'end'    => array(T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET),
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_DECLARE       => array(
                                                'start'  => array(T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET),
                                                'end'    => array(T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET),
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_NAMESPACE     => array(
                                                'start'  => array(T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET),
                                                'end'    => array(T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET),
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_WHILE         => array(
                                                'start'  => array(
                                                             T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                                                             T_COLON              => T_COLON,
                                                            ),
                                                'end'    => array(
                                                             T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                                                             T_ENDWHILE            => T_ENDWHILE,
                                                            ),
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_DO            => array(
                                                'start'  => array(T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET),
                                                'end'    => array(T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET),
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_SWITCH        => array(
                                                'start'  => array(
                                                             T_OPEN_CURLY_BRACKET => T_OPEN_CURLY_BRACKET,
                                                             T_COLON              => T_COLON,
                                                            ),
                                                'end'    => array(
                                                             T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                                                             T_ENDSWITCH           => T_ENDSWITCH,
                                                            ),
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_CASE          => array(
                                                'start'  => array(
                                                             T_COLON     => T_COLON,
                                                             T_SEMICOLON => T_SEMICOLON,
                                                            ),
                                                'end'    => array(
                                                             T_BREAK    => T_BREAK,
                                                             T_RETURN   => T_RETURN,
                                                             T_CONTINUE => T_CONTINUE,
                                                             T_THROW    => T_THROW,
                                                             T_EXIT     => T_EXIT,
                                                            ),
                                                'strict' => true,
                                                'shared' => true,
                                                'with'   => array(
                                                             T_DEFAULT => T_DEFAULT,
                                                             T_CASE    => T_CASE,
                                                             T_SWITCH  => T_SWITCH,
                                                            ),
                                               ),
                            T_DEFAULT       => array(
                                                'start'  => array(
                                                             T_COLON     => T_COLON,
                                                             T_SEMICOLON => T_SEMICOLON,
                                                            ),
                                                'end'    => array(
                                                             T_BREAK    => T_BREAK,
                                                             T_RETURN   => T_RETURN,
                                                             T_CONTINUE => T_CONTINUE,
                                                             T_THROW    => T_THROW,
                                                             T_EXIT     => T_EXIT,
                                                            ),
                                                'strict' => true,
                                                'shared' => true,
                                                'with'   => array(
                                                             T_CASE   => T_CASE,
                                                             T_SWITCH => T_SWITCH,
                                                            ),
                                               ),
                            T_START_HEREDOC => array(
                                                'start'  => array(T_START_HEREDOC => T_START_HEREDOC),
                                                'end'    => array(T_END_HEREDOC => T_END_HEREDOC),
                                                'strict' => true,
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
                              T_CLOSE_CURLY_BRACKET => T_CLOSE_CURLY_BRACKET,
                              T_ENDIF               => T_ENDIF,
                              T_ENDFOR              => T_ENDFOR,
                              T_ENDFOREACH          => T_ENDFOREACH,
                              T_ENDWHILE            => T_ENDWHILE,
                              T_ENDSWITCH           => T_ENDSWITCH,
                              T_BREAK               => T_BREAK,
                              T_END_HEREDOC         => T_END_HEREDOC,
                             );

    /**
     * Known lengths of tokens.
     *
     * @var array<int, int>
     */
    public $knownLengths = array(
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
                            T_BITWISE_AND              => 1,
                            T_BITWISE_OR               => 1,
                            T_BITWISE_XOR              => 1,
                            T_SL                       => 2,
                            T_SR                       => 2,
                            T_SL_EQUAL                 => 3,
                            T_SR_EQUAL                 => 3,
                            T_ARRAY_HINT               => 5,
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
                           );


    /**
     * A cache of different token types, resolved into arrays.
     *
     * @var array
     * @see standardiseToken()
     */
    private static $_resolveTokenCache = array();


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
        $tokens      = @token_get_all($string);
        $finalTokens = array();

        $newStackPtr       = 0;
        $numTokens         = count($tokens);
        $lastNotEmptyToken = 0;

        $insideInlineIf = array();
        $insideUseGroup = false;

        $commentTokenizer = new Comment();

        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token        = (array) $tokens[$stackPtr];
            $tokenIsArray = isset($token[1]);

            if ($newStackPtr > 0 && $finalTokens[($newStackPtr - 1)]['code'] !== T_WHITESPACE) {
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

                    if ($tokens[($stackPtr + 1)][1] === "\n") {
                        // This token's content has been merged into the previous,
                        // so we can skip it.
                        $tokens[($stackPtr + 1)] = '';
                    } else {
                        $tokens[($stackPtr + 1)][1] = substr($tokens[($stackPtr + 1)][1], 1);
                    }
                }
            }//end if

            /*
                Parse doc blocks into something that can be easily iterated over.
            */

            if ($tokenIsArray === true && $token[0] === T_DOC_COMMENT) {
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
                    $finalTokens[$newStackPtr] = array(
                                                  'code'    => T_BINARY_CAST,
                                                  'type'    => 'T_BINARY_CAST',
                                                  'content' => 'b',
                                                 );
                    $newStackPtr++;
                }

                $tokenContent = '"';
                $nestedVars   = array();
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
                $newToken   = array();

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
                if ($token[1][3] === "'") {
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
                $newToken   = array();

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
                    $nowdoc = true;
                }

                $newStackPtr++;

                // Continue, as we're done with this token.
                continue;
            }//end if


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
                $stopTokens = array(
                               T_CASE               => true,
                               T_SEMICOLON          => true,
                               T_OPEN_CURLY_BRACKET => true,
                               T_INLINE_THEN        => true,
                              );

                for ($x = ($newStackPtr - 1); $x > 0; $x--) {
                    if (isset($stopTokens[$finalTokens[$x]['code']]) === true) {
                        break;
                    }
                }

                if ($finalTokens[$x]['code'] !== T_CASE
                    && $finalTokens[$x]['code'] !== T_INLINE_THEN
                ) {
                    $finalTokens[$newStackPtr] = array(
                                                  'content' => $token[1].':',
                                                  'code'    => T_GOTO_LABEL,
                                                  'type'    => 'T_GOTO_LABEL',
                                                 );

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
                $newToken   = array(
                               'type'    => token_name($token[0]),
                               'code'    => $token[0],
                               'content' => '',
                              );

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
                    $context = array(
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
                               );
                    if (isset($context[$finalTokens[$lastNotEmptyToken]['code']]) === true) {
                        $finalTokens[$newStackPtr] = array(
                                                      'content' => $token[1],
                                                      'code'    => T_STRING,
                                                      'type'    => 'T_STRING',
                                                     );
                        $newStackPtr++;
                        continue;
                    }
                }//end if

                $newToken = null;
                if ($tokenIsArray === false) {
                    if (isset(self::$_resolveTokenCache[$token[0]]) === true) {
                        $newToken = self::$_resolveTokenCache[$token[0]];
                    }
                } else {
                    $cacheKey = null;
                    if ($token[0] === T_STRING) {
                        $cacheKey = strtolower($token[1]);
                    } else if ($token[0] !== T_CURLY_OPEN) {
                        $cacheKey = $token[0];
                    }

                    if ($cacheKey !== null && isset(self::$_resolveTokenCache[$cacheKey]) === true) {
                        $newToken            = self::$_resolveTokenCache[$cacheKey];
                        $newToken['content'] = $token[1];
                    }
                }

                if ($newToken === null) {
                    $newToken = self::standardiseToken($token);
                }

                // Convert colons that are actually the ELSE component of an
                // inline IF statement.
                if ($newToken['code'] === T_INLINE_THEN) {
                    $insideInlineIf[] = $stackPtr;
                } else if (empty($insideInlineIf) === false && $newToken['code'] === T_COLON) {
                    array_pop($insideInlineIf);
                    $newToken['code'] = T_INLINE_ELSE;
                    $newToken['type'] = 'T_INLINE_ELSE';
                }

                // This is a special condition for T_ARRAY tokens used for
                // type hinting function arguments as being arrays. We want to keep
                // the parenthesis map clean, so let's tag these tokens as
                // T_ARRAY_HINT.
                if ($newToken['code'] === T_ARRAY) {
                    // Recalculate number of tokens.
                    for ($i = $stackPtr; $i < $numTokens; $i++) {
                        if ($tokens[$i] === '(') {
                            break;
                        } else if ($tokens[$i][0] === T_VARIABLE) {
                            $newToken['code'] = T_ARRAY_HINT;
                            $newToken['type'] = 'T_ARRAY_HINT';
                            break;
                        }
                    }
                }

                // This is a special case for the PHP 5.5 classname::class syntax
                // where "class" should be T_STRING instead of T_CLASS.
                if ($newToken['code'] === T_CLASS
                    && $finalTokens[($newStackPtr - 1)]['code'] === T_DOUBLE_COLON
                ) {
                    $newToken['code'] = T_STRING;
                    $newToken['type'] = 'T_STRING';
                }

                // This is a special case for PHP 5.6 use function and use const
                // where "function" and "const" should be T_STRING instead of T_FUNCTION
                // and T_CONST.
                if (($newToken['code'] === T_FUNCTION
                    || $newToken['code'] === T_CONST)
                    && $finalTokens[$lastNotEmptyToken]['code'] === T_USE
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
        $numTokens = count($this->tokens);
        for ($i = ($numTokens - 1); $i >= 0; $i--) {
            // Check for any unset scope conditions due to alternate IF/ENDIF syntax.
            if (isset($this->tokens[$i]['scope_opener']) === true
                && isset($this->tokens[$i]['scope_condition']) === false
            ) {
                $this->tokens[$i]['scope_condition'] = $this->tokens[$this->tokens[$i]['scope_opener']]['scope_condition'];
            }

            if ($this->tokens[$i]['code'] === T_FUNCTION) {
                // Context sensitive keywords support.
                for ($x = ($i + 1); $x < $numTokens; $x++) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false) {
                        // Non-whitespace content.
                        break;
                    }
                }

                if ($x === $numTokens) {
                    // We got to the end without finding any more
                    // non-whitespace content.
                    continue;
                }

                if (in_array($this->tokens[$x]['code'], array(T_STRING, T_OPEN_PARENTHESIS, T_BITWISE_AND), true) === false) {
                    $this->tokens[$x]['code'] = T_STRING;
                    $this->tokens[$x]['type'] = 'T_STRING';
                }

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

                        for ($x = ($this->tokens[$i]['scope_opener'] + 1); $x < $this->tokens[$i]['scope_closer']; $x++) {
                            if (isset($this->tokens[$x]['conditions'][$i]) === false) {
                                continue;
                            }

                            $this->tokens[$x]['conditions'][$i] = T_CLOSURE;
                        }
                    }

                    $tokenAfterReturnTypeHint = $this->tokens[$i]['scope_opener'];
                } else if (isset($this->tokens[$i]['parenthesis_closer']) === true) {
                    $tokenAfterReturnTypeHint = null;
                    for ($x = ($this->tokens[$i]['parenthesis_closer'] + 1); $x < $numTokens; $x++) {
                        if ($this->tokens[$x]['code'] === T_SEMICOLON) {
                            $tokenAfterReturnTypeHint = $x;
                            break;
                        }
                    }

                    if ($tokenAfterReturnTypeHint === null) {
                        // Probably a syntax error.
                        continue;
                    }
                } else {
                    // Probably a syntax error.
                    continue;
                }//end if

                /*
                    Detect function return values and assign them
                    a special token, because PHP doesn't.
                */

                for ($x = ($tokenAfterReturnTypeHint - 1); $x > $i; $x--) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false) {
                        if (in_array($this->tokens[$x]['code'], array(T_STRING, T_ARRAY, T_CALLABLE, T_SELF, T_PARENT), true) === true) {
                            $this->tokens[$x]['code'] = T_RETURN_TYPE;
                            $this->tokens[$x]['type'] = 'T_RETURN_TYPE';
                        }

                        break;
                    }
                }

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

                    for ($x = ($this->tokens[$i]['scope_opener'] + 1); $x < $this->tokens[$i]['scope_closer']; $x++) {
                        if (isset($this->tokens[$x]['conditions'][$i]) === false) {
                            continue;
                        }

                        $this->tokens[$x]['conditions'][$i] = T_ANON_CLASS;
                    }
                }

                continue;
            } else if ($this->tokens[$i]['code'] === T_OPEN_SQUARE_BRACKET) {
                // Unless there is a variable or a bracket before this token,
                // it is the start of an array being defined using the short syntax.
                for ($x = ($i - 1); $x > 0; $x--) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false) {
                        break;
                    }
                }

                $allowed = array(
                            T_CLOSE_CURLY_BRACKET  => T_CLOSE_CURLY_BRACKET,
                            T_CLOSE_SQUARE_BRACKET => T_CLOSE_SQUARE_BRACKET,
                            T_CLOSE_PARENTHESIS    => T_CLOSE_PARENTHESIS,
                            T_VARIABLE             => T_VARIABLE,
                            T_STRING               => T_STRING,
                           );

                if (isset($allowed[$this->tokens[$x]['code']]) === false
                    && isset($this->tokens[$i]['bracket_closer']) === true
                ) {
                    $this->tokens[$i]['code'] = T_OPEN_SHORT_ARRAY;
                    $this->tokens[$i]['type'] = 'T_OPEN_SHORT_ARRAY';

                    $closer = $this->tokens[$i]['bracket_closer'];
                    $this->tokens[$closer]['code'] = T_CLOSE_SHORT_ARRAY;
                    $this->tokens[$closer]['type'] = 'T_CLOSE_SHORT_ARRAY';
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
                }

                continue;
            } else if ($this->tokens[$i]['code'] === T_ECHO && $this->tokens[$i]['content'] === '<?=') {
                // HHVM tokenizes <?= as T_ECHO but it should be T_OPEN_TAG_WITH_ECHO.
                $this->tokens[$i]['code'] = T_OPEN_TAG_WITH_ECHO;
                $this->tokens[$i]['type'] = 'T_OPEN_TAG_WITH_ECHO';

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

                $context = array(
                            T_OBJECT_OPERATOR      => true,
                            T_NS_SEPARATOR         => true,
                            T_PAAMAYIM_NEKUDOTAYIM => true,
                           );
                if (isset($context[$this->tokens[$x]['code']]) === true) {
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

                    $this->tokens[$x]['code'] = T_STRING;
                    $this->tokens[$x]['type'] = 'T_STRING';
                }
            } else if ($this->tokens[$i]['code'] === T_PAAMAYIM_NEKUDOTAYIM) {
                // Context sensitive keywords support.
                for ($x = ($i + 1); $i < $numTokens; $x++) {
                    if (isset(Util\Tokens::$emptyTokens[$this->tokens[$x]['code']]) === false) {
                        // Non-whitespace content.
                        break;
                    }
                }

                if (in_array($this->tokens[$x]['code'], array(T_STRING, T_VARIABLE, T_DOLLAR), true) === false) {
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
            foreach (array($i, $x, $newCloser) as $index) {
                $this->tokens[$index]['scope_condition'] = $i;
                $this->tokens[$index]['scope_opener']    = $x;
                $this->tokens[$index]['scope_closer']    = $newCloser;
            }

            unset($this->tokens[$scopeOpener]['scope_condition']);
            unset($this->tokens[$scopeOpener]['scope_opener']);
            unset($this->tokens[$scopeOpener]['scope_closer']);
            unset($this->tokens[$scopeCloser]['scope_condition']);
            unset($this->tokens[$scopeCloser]['scope_opener']);
            unset($this->tokens[$scopeCloser]['scope_closer']);
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

                        break;
                    }//end if
                }//end foreach
            }//end for
        }//end for
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
            if (isset(self::$_resolveTokenCache[$token[0]]) === true) {
                return self::$_resolveTokenCache[$token[0]];
            }
        } else {
            $cacheKey = null;
            if ($token[0] === T_STRING) {
                $cacheKey = strtolower($token[1]);
            } else if ($token[0] !== T_CURLY_OPEN) {
                $cacheKey = $token[0];
            }

            if ($cacheKey !== null && isset(self::$_resolveTokenCache[$cacheKey]) === true) {
                $newToken            = self::$_resolveTokenCache[$cacheKey];
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

            self::$_resolveTokenCache[$cacheKey] = $newToken;
        } else if ($token[0] === T_CURLY_OPEN) {
            $newToken = array(
                         'code' => T_OPEN_CURLY_BRACKET,
                         'type' => 'T_OPEN_CURLY_BRACKET',
                        );
        } else {
            $newToken = array(
                         'code' => $token[0],
                         'type' => token_name($token[0]),
                        );

            self::$_resolveTokenCache[$token[0]] = $newToken;
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
            $newToken['type'] = 'T_BITWISE_XOR';
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

        self::$_resolveTokenCache[$token] = $newToken;
        return $newToken;

    }//end resolveSimpleToken()


}//end class
