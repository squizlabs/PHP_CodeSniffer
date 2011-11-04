<?php
/**
 * This file is part of the CodeAnalysis addon for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Checks the for unused function parameters.
 *
 * This sniff checks that all function parameters are used in the function body.
 * One exception is made for empty function bodies or function bodies that only
 * contain comments. This could be usefull for the classes that implement an
 * interface that defines multiple methods but the implementation only needs some
 * of them.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2008 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_CodeAnalysis_UnusedFunctionParameterSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FUNCTION);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
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

        $emptyBody = true;

        for (; $next <= $end; ++$next) {
            $token = $tokens[$next];
            $code  = $token['code'];

            // Ingorable tokens.
            if (in_array($code, PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                continue;
            } else if ($code === T_THROW && $emptyBody === true) {
                // Throw statement and an empty body indicate an interface method.
                return;
            } else if ($code === T_RETURN && $emptyBody === true) {
                // Return statement and an empty body indicate an interface method.
                $tmp = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($next + 1), null, true);
                if ($tmp === false) {
                    return;
                }

                // There is a return.
                if ($tokens[$tmp]['code'] === T_SEMICOLON) {
                    return;
                }

                $tmp = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($tmp + 1), null, true);

                // There is a return <token>.
                if ($tmp !== false && $tokens[$tmp]['code'] === T_SEMICOLON) {
                     return;
                }
            }//end if

            $emptyBody = false;

            if ($code === T_VARIABLE && isset($params[$token['content']]) === true) {
                unset($params[$token['content']]);
            } else if ($code === T_DOUBLE_QUOTED_STRING || $code === T_HEREDOC) {
                // Tokenize strings that can contain variables.
                // Make sure the string is re-joined if it occurs over multiple lines.
                $string = $token['content'];
                for ($i = ($next + 1); $i <= $end; $i++) {
                    if ($tokens[$i]['code'] === $code) {
                        $string .= $tokens[$i]['content'];
                        $next++;
                    }
                }

                $strTokens = token_get_all(sprintf('<?php %s;?>', $string));

                foreach ($strTokens as $tok) {
                    if (is_array($tok) === false || $tok[0] !== T_VARIABLE ) {
                        continue;
                    }

                    if (isset($params[$tok[1]]) === true) {
                        unset($params[$tok[1]]);
                    }
                }
            }//end if
        }//end for

        if ($emptyBody === false && count($params) > 0) {
            foreach ($params as $paramName => $position) {
                $error = 'The method parameter %s is never used';
                $data  = array($paramName);
                $phpcsFile->addWarning($error, $position, 'Found', $data);
            }
        }

    }//end process()


}//end class

?>