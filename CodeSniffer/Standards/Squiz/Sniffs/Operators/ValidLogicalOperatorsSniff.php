<?php
/**
 * Squiz_Sniffs_Operators_ValidLogicalOperatorsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Squiz_Sniffs_Operators_ValidLogicalOperatorsSniff.
 *
 * Checks to ensure that the logical operators 'and' and 'or' are not used.
 * Use the && and || operators instead.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Operators_ValidLogicalOperatorsSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_LOGICAL_AND,
                T_LOGICAL_OR,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     * Tokens can get autoreplaced if statements don't contain an operator with a priority between and/or and &&/||
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $replacements = array(
                         'and' => '&&',
                         'or'  => '||',
                        );

        $operator = strtolower($tokens[$stackPtr]['content']);
        if (isset($replacements[$operator]) === false) {
            return;
        }

        $error = 'Logical operator "%s" is prohibited; use "%s" instead';
        $data  = array(
                  $operator,
                  $replacements[$operator],
                 );

        $blackList = array(
                      T_EQUAL,
                      T_PLUS_EQUAL,
                      T_MINUS_EQUAL,
                      T_MUL_EQUAL,
                      T_POW_EQUAL,
                      T_DIV_EQUAL,
                      T_CONCAT_EQUAL,
                      T_MOD_EQUAL,
                      T_AND_EQUAL,
                      T_OR_EQUAL,
                      T_XOR_EQUAL,
                      T_SL_EQUAL,
                      T_SR_EQUAL,
                      T_LOGICAL_XOR,
                      T_COALESCE_EQUAL,
                      T_INLINE_THEN,
                      T_INLINE_ELSE,
                     );

        $start = $phpcsFile->findStartOfStatement($stackPtr);
        $end   = $phpcsFile->findEndOfStatement($stackPtr);
        for ($index = $start; $index <= $end; ++$index) {
            if (in_array($tokens[$index]['code'], $blackList, true)) {
                $phpcsFile->addError($error, $stackPtr, 'NotAllowed', $data);
                break;
            }
        }

        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NotAllowed', $data);
        if ($fix === true) {
            $phpcsFile->fixer->replaceToken($stackPtr, $replacements[$operator]);
        }

    }//end process()


}//end class
