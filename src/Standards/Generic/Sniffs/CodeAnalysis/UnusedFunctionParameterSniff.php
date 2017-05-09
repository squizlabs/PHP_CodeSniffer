<?php
/**
 * Checks the for unused function parameters.
 *
 * This sniff checks that all function parameters are used in the function body.
 * One exception is made for empty function bodies or function bodies that only
 * contain comments. This could be useful for the classes that implement an
 * interface that defines multiple methods but the implementation only needs some
 * of them.
 *
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2007-2014 Manuel Pichler. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class UnusedFunctionParameterSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_FUNCTION,
                T_CLOSURE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token  = $tokens[$stackPtr];

        // Skip broken function declarations.
        if (isset($token['scope_opener']) === false || isset($token['parenthesis_opener']) === false) {
            return;
        }

        $params = array();
        foreach ($phpcsFile->getMethodParameters($stackPtr) as $param) {
            $params[$param['name']] = $stackPtr;
        }

        $next = ++$token['scope_opener'];
        $end  = --$token['scope_closer'];

        $foundContent = false;
        $validTokens  = array(
                         T_HEREDOC              => T_HEREDOC,
                         T_NOWDOC               => T_NOWDOC,
                         T_END_HEREDOC          => T_END_HEREDOC,
                         T_END_NOWDOC           => T_END_NOWDOC,
                         T_DOUBLE_QUOTED_STRING => T_DOUBLE_QUOTED_STRING,
                        );
        $validTokens += Tokens::$emptyTokens;

        for (; $next <= $end; ++$next) {
            $token = $tokens[$next];
            $code  = $token['code'];

            // Ignorable tokens.
            if (isset(Tokens::$emptyTokens[$code]) === true) {
                continue;
            }

            if ($foundContent === false) {
                // A throw statement as the first content indicates an interface method.
                if ($code === T_THROW) {
                    return;
                }

                // A return statement as the first content indicates an interface method.
                if ($code === T_RETURN) {
                    $tmp = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);
                    if ($tmp === false) {
                        return;
                    }

                    // There is a return.
                    if ($tokens[$tmp]['code'] === T_SEMICOLON) {
                        return;
                    }

                    $tmp = $phpcsFile->findNext(Tokens::$emptyTokens, ($tmp + 1), null, true);
                    if ($tmp !== false && $tokens[$tmp]['code'] === T_SEMICOLON) {
                        // There is a return <token>.
                        return;
                    }
                }//end if
            }//end if

            $foundContent = true;

            if ($code === T_VARIABLE && isset($params[$token['content']]) === true) {
                unset($params[$token['content']]);
            } else if ($code === T_DOLLAR) {
                $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($next + 1), null, true);
                if ($tokens[$nextToken]['code'] === T_OPEN_CURLY_BRACKET) {
                    $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($nextToken + 1), null, true);
                    if ($tokens[$nextToken]['code'] === T_STRING) {
                        $varContent = '$'.$tokens[$nextToken]['content'];
                        if (isset($params[$varContent]) === true) {
                            unset($params[$varContent]);
                        }
                    }
                }
            } else if ($code === T_DOUBLE_QUOTED_STRING
                || $code === T_START_HEREDOC
                || $code === T_START_NOWDOC
            ) {
                // Tokenize strings that can contain variables.
                // Make sure the string is re-joined if it occurs over multiple lines.
                $content = $token['content'];
                for ($i = ($next + 1); $i <= $end; $i++) {
                    if (isset($validTokens[$tokens[$i]['code']]) === true) {
                        $content .= $tokens[$i]['content'];
                        $next++;
                    } else {
                        break;
                    }
                }

                $stringTokens = token_get_all(sprintf('<?php %s;?>', $content));
                foreach ($stringTokens as $stringPtr => $stringToken) {
                    if (is_array($stringToken) === false) {
                        continue;
                    }

                    $varContent = '';
                    if ($stringToken[0] === T_DOLLAR_OPEN_CURLY_BRACES) {
                        $varContent = '$'.$stringTokens[($stringPtr + 1)][1];
                    } else if ($stringToken[0] === T_VARIABLE) {
                        $varContent = $stringToken[1];
                    }

                    if ($varContent !== '' && isset($params[$varContent]) === true) {
                        unset($params[$varContent]);
                    }
                }
            }//end if
        }//end for

        if ($foundContent === true && count($params) > 0) {
            foreach ($params as $paramName => $position) {
                $error = 'The method parameter %s is never used';
                $data  = array($paramName);
                $phpcsFile->addWarning($error, $position, 'Found', $data);
            }
        }

    }//end process()


}//end class
