<?php
/**
 * Tokenizes PHP code.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Tokenizes PHP code.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Tokenizers_PHP
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
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_TRY           => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_CATCH         => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_ELSE          => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_ELSEIF        => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_FOR           => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_FOREACH       => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_INTERFACE     => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_FUNCTION      => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_CLASS         => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_WHILE         => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => false,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_DO            => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_SWITCH        => array(
                                                'start'  => T_OPEN_CURLY_BRACKET,
                                                'end'    => T_CLOSE_CURLY_BRACKET,
                                                'strict' => true,
                                                'shared' => false,
                                                'with'   => array(),
                                               ),
                            T_CASE          => array(
                                                'start'  => T_COLON,
                                                'end'    => T_BREAK,
                                                'strict' => true,
                                                'shared' => true,
                                                'with'   => array(
                                                             T_DEFAULT,
                                                             T_CASE,
                                                             T_SWITCH,
                                                            ),
                                               ),
                            T_DEFAULT       => array(
                                                'start'  => T_COLON,
                                                'end'    => T_BREAK,
                                                'strict' => true,
                                                'shared' => true,
                                                'with'   => array(
                                                             T_CASE,
                                                             T_SWITCH,
                                                            ),
                                               ),
                            T_START_HEREDOC => array(
                                                'start'  => T_START_HEREDOC,
                                                'end'    => T_END_HEREDOC,
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
                              T_CLOSE_CURLY_BRACKET,
                              T_BREAK,
                              T_END_HEREDOC,
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
    public function tokenizeString($string, $eolChar='\n')
    {
        $tokens      = @token_get_all($string);
        $finalTokens = array();

        $newStackPtr = 0;
        $numTokens   = count($tokens);
        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token        = $tokens[$stackPtr];
            $tokenIsArray = is_array($token);

            /*
                If we are using \r\n newline characters, the \r and \n are sometimes
                split over two tokens. This normally occurs after comments. We need
                to merge these two characters together so that our line endings are
                consistent for all lines.
            */

            if ($tokenIsArray === true && substr($token[1], -1) === "\r") {
                if (isset($tokens[($stackPtr + 1)]) === true && is_array($tokens[($stackPtr + 1)]) === true && $tokens[($stackPtr + 1)][1][0] === "\n") {
                    $token[1] .= "\n";

                    if ($tokens[($stackPtr + 1)][1] === "\n") {
                        // The next token's content has been merged into this token,
                        // so we can skip it.
                        $stackPtr++;
                    } else {
                        $tokens[($stackPtr + 1)][1] = substr($tokens[($stackPtr + 1)][1], 1);
                    }
                }
            }//end if

            /*
                If this is a double quoted string, PHP will tokenise the whole
                thing which causes problems with the scope map when braces are
                within the string. So we need to merge the tokens together to
                provide a single string.
            */

            if ($tokenIsArray === false && $token === '"') {
                $tokenContent = '"';
                $nestedVars   = array();
                for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                    $subTokenIsArray = is_array($tokens[$i]);

                    if ($subTokenIsArray === true) {
                        $tokenContent .= $tokens[$i][1];
                        if ($tokens[$i][1] === '{') {
                            $nestedVars[] = $i;
                        }
                    } else {
                        $tokenContent .= $tokens[$i];
                        if ($tokens[$i] === '}') {
                            array_pop($nestedVars);
                        }
                    }

                    if ($subTokenIsArray === false && $tokens[$i] === '"' && empty($nestedVars) === true) {
                        // We found the other end of the double quoted string.
                        break;
                    }
                }

                $stackPtr = $i;

                // Convert each line within the double quoted string to a
                // new token, so it conforms with other multiple line tokens.
                $tokenLines = explode($eolChar, $tokenContent);
                $numLines   = count($tokenLines);
                $newToken   = array();

                for ($j = 0; $j < $numLines; $j++) {
                    $newToken['content'] = $tokenLines[$j];
                    if ($j === ($numLines - 1)) {
                        if ($tokenLines[$j] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $eolChar;
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
                If this is a heredoc, PHP will tokenise the whole
                thing which causes problems when heredocs don't
                contain real PHP code, which is almost never.
                We want to leave the start and end heredoc tokens
                alone though.
            */

            if ($tokenIsArray === true && $token[0] === T_START_HEREDOC) {
                // Add the start heredoc token to the final array.
                $finalTokens[$newStackPtr] = PHP_CodeSniffer::standardiseToken($token);
                $newStackPtr++;

                $tokenContent = '';
                for ($i = ($stackPtr + 1); $i < $numTokens; $i++) {
                    $subTokenIsArray = is_array($tokens[$i]);
                    if ($subTokenIsArray === true && $tokens[$i][0] === T_END_HEREDOC) {
                        // We found the other end of the heredoc.
                        break;
                    }

                    if ($subTokenIsArray === true) {
                        $tokenContent .= $tokens[$i][1];
                    } else {
                        $tokenContent .= $tokens[$i];
                    }
                }

                $stackPtr = $i;

                // Convert each line within the heredoc to a
                // new token, so it conforms with other multiple line tokens.
                $tokenLines = explode($eolChar, $tokenContent);
                $numLines   = count($tokenLines);
                $newToken   = array();

                for ($j = 0; $j < $numLines; $j++) {
                    $newToken['content'] = $tokenLines[$j];
                    if ($j === ($numLines - 1)) {
                        if ($tokenLines[$j] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $eolChar;
                    }

                    $newToken['code']          = T_HEREDOC;
                    $newToken['type']          = 'T_HEREDOC';
                    $finalTokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                }

                // Add the end heredoc token to the final array.
                $finalTokens[$newStackPtr] = PHP_CodeSniffer::standardiseToken($tokens[$stackPtr]);
                $newStackPtr++;

                // Continue, as we're done with this token.
                continue;
            }//end if

            /*
                If this token has newlines in its content, split each line up
                and create a new token for each line. We do this so it's easier
                to asertain where errors occur on a line.
                Note that $token[1] is the token's content.
            */

            if ($tokenIsArray === true && strpos($token[1], $eolChar) !== false) {
                $tokenLines = explode($eolChar, $token[1]);
                $numLines   = count($tokenLines);
                $tokenName  = token_name($token[0]);

                for ($i = 0; $i < $numLines; $i++) {
                    $newToken['content'] = $tokenLines[$i];
                    if ($i === ($numLines - 1)) {
                        if ($tokenLines[$i] === '') {
                            break;
                        }
                    } else {
                        $newToken['content'] .= $eolChar;
                    }

                    $newToken['type']          = $tokenName;
                    $newToken['code']          = $token[0];
                    $finalTokens[$newStackPtr] = $newToken;
                    $newStackPtr++;
                }
            } else {
                $newToken = PHP_CodeSniffer::standardiseToken($token);

                // This is a special condition for T_ARRAY tokens use to
                // type hint function arguments as being arrays. We want to keep
                // the parenthsis map clean, so let's tag these tokens as
                // T_ARRAY_HINT.
                if ($newToken['code'] === T_ARRAY) {
                    // Recalculate number of tokens.
                    $numTokens = count($tokens);
                    for ($i = $stackPtr; $i < $numTokens; $i++) {
                        if (is_array($tokens[$i]) === false) {
                            if ($tokens[$i] === '(') {
                                break;
                            }
                        } else if ($tokens[$i][0] === T_VARIABLE) {
                            $newToken['code'] = T_ARRAY_HINT;
                            $newToken['type'] = 'T_ARRAY_HINT';
                            break;
                        }
                    }
                }

                $finalTokens[$newStackPtr] = $newToken;
                $newStackPtr++;
            }//end if
        }//end for

        return $finalTokens;

    }//end tokenizeString()


    /**
     * Performs additional processing after main tokenizing.
     *
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return array
     */
    public function processAdditional(&$tokens, $eolChar)
    {

    }//end processAdditional()


}//end class

?>
