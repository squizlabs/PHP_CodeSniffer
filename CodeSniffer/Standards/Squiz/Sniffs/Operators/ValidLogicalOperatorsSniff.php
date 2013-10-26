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
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
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
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
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
                T_LOGICAL_XOR,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
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
                         'xor' => '^',
                        );

        $operator = strtolower($tokens[$stackPtr]['content']);
        if (isset($replacements[$operator]) === false) {
            return;
        }

        $error = 'Logical operator "%s" is prohibited; use "%s" instead';
        $data  = array(
                  $tokens[$stackPtr]['content'],
                  $replacements[$operator],
                 );

        // We can only fix this if there are no operators with higher prececende around it
        // See http://www.php.net/manual/en/language.operators.precedence.php
        $problematicOperators = array(T_IS_EQUAL, T_IS_IDENTICAL);

        $fixable = !empty($tokens[$stackPtr]['nested_parenthesis']);
        if ($fixable) {
            foreach ($tokens[$stackPtr]['nested_parenthesis'] as $from => $to) {
                for ($i = ($from + 1); $i < $to; $i++) {
                    $code = $tokens[$i]['code'];
                    //print_r($tokens[$i]);
                    if (in_array($code, $problematicOperators)) {
                        $fixable = false;
                        break;
                    }
                }
            }
        }
        if ($fixable) {
            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NotAllowed', $data);
            if ($fix === true && $phpcsFile->fixer->enabled === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, $replacements[$operator]);
            }
        } else {
            $fix = $phpcsFile->addError($error, $stackPtr, 'NotAllowed', $data);
        }

    }//end process()


}//end class

?>
