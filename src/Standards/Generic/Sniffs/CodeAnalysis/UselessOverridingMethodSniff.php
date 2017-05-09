<?php
/**
 * Detects unnecessary overridden methods that simply call their parent.
 *
 * This rule is based on the PMD rule catalog. The Useless Overriding Method
 * sniff detects the use of methods that only call their parent classes's method
 * with the same name and arguments. These methods are not required.
 *
 * <code>
 * class FooBar {
 *   public function __construct($a, $b) {
 *     parent::__construct($a, $b);
 *   }
 * }
 * </code>
 *
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007-2014 Manuel Pichler. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class UselessOverridingMethodSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return array(T_FUNCTION);

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

        // Skip function without body.
        if (isset($token['scope_opener']) === false) {
            return;
        }

        // Get function name.
        $methodName = $phpcsFile->getDeclarationName($stackPtr);

        // Get all parameters from method signature.
        $signature = array();
        foreach ($phpcsFile->getMethodParameters($stackPtr) as $param) {
            $signature[] = $param['name'];
        }

        $next = ++$token['scope_opener'];
        $end  = --$token['scope_closer'];

        for (; $next <= $end; ++$next) {
            $code = $tokens[$next]['code'];

            if (isset(Tokens::$emptyTokens[$code]) === true) {
                continue;
            } else if ($code === T_RETURN) {
                continue;
            }

            break;
        }

        // Any token except 'parent' indicates correct code.
        if ($tokens[$next]['code'] !== T_PARENT) {
            return;
        }

        // Find next non empty token index, should be double colon.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);

        // Skip for invalid code.
        if ($next === false || $tokens[$next]['code'] !== T_DOUBLE_COLON) {
            return;
        }

        // Find next non empty token index, should be the function name.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);

        // Skip for invalid code or other method.
        if ($next === false || $tokens[$next]['content'] !== $methodName) {
            return;
        }

        // Find next non empty token index, should be the open parenthesis.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);

        // Skip for invalid code.
        if ($next === false || $tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            return;
        }

        $parameters       = array('');
        $parenthesisCount = 1;
        $count            = count($tokens);
        for (++$next; $next < $count; ++$next) {
            $code = $tokens[$next]['code'];

            if ($code === T_OPEN_PARENTHESIS) {
                ++$parenthesisCount;
            } else if ($code === T_CLOSE_PARENTHESIS) {
                --$parenthesisCount;
            } else if ($parenthesisCount === 1 && $code === T_COMMA) {
                $parameters[] = '';
            } else if (isset(Tokens::$emptyTokens[$code]) === false) {
                $parameters[(count($parameters) - 1)] .= $tokens[$next]['content'];
            }

            if ($parenthesisCount === 0) {
                break;
            }
        }//end for

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);
        if ($next === false || $tokens[$next]['code'] !== T_SEMICOLON) {
            return;
        }

        // Check rest of the scope.
        for (++$next; $next <= $end; ++$next) {
            $code = $tokens[$next]['code'];
            // Skip for any other content.
            if (isset(Tokens::$emptyTokens[$code]) === false) {
                return;
            }
        }

        $parameters = array_map('trim', $parameters);
        $parameters = array_filter($parameters);

        if (count($parameters) === count($signature) && $parameters === $signature) {
            $phpcsFile->addWarning('Possible useless method overriding detected', $stackPtr, 'Found');
        }

    }//end process()


}//end class
