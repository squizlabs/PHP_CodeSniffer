<?php
/**
 * Sniffs_Squiz_WhiteSpace_OperatorSpacingSniff.
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

 * Sniffs_Squiz_WhiteSpace_OperatorSpacingSniff.
 *
 * Verifies that operators have valid spacing surrounding them.
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
class Squiz_Sniffs_WhiteSpace_OperatorSpacingSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
    */
    public $supportedTokenizers = array(
          'PHP',
          'JS',
    );
    
    /**
     * Allow newline before the operator. 
     * - false means deny the newline before all of checked operators.
     * - true means allow the newline before all of checked operators.
     * - string with regexp allows the newline only before operators matching this regexp.
     * Examples: 
     *      /(?[=]|^\+\+)$/ - allow the newline before the increment and after any kind of assignment.
     *      /[^*]/ - allow the newline before any operator except *.
     *      /^(?!([+-])\1)/ - allow the newline before any operator except ++ and --.
     * 
     * If switched on then the indentation spaces between the newline and Operator will not be incorrect.
     * So 
     *  $val = 7 * 38
     *          + 25 / 99
     *   ;
     * is correct.
     * 
     * @var bool|string
     */
    public $allowNewLineBefore = false;
    
    /**
     * Allow newLine after the operator.
     * - false means deny the newline after all of checked operators.
     * - true means allow the newline after all of checked operators.
     * - string with regexp allows the newline only after operators matching this regexp.
     * Examples: 
     *      /(?(?<!=)[=]|^\+\+)$/ - allow the newline after the increment and after any kind of assignment (ends with =, but not ==).
     *      /[^*]/ - allow the newline after any operator except *.
     *      /^(?!([+-])\1)/ - allow the newline after any operator except ++ and --.
     * 
     * If switched on then the newline characted is allowed right after Operator.
     * So 
     *  $val = 7 * 38 +
     *          25 / 99
     *   ;
     * is correct.
     * 
     * @var bool|string
     */
    public $allowNewLineAfter = '/(?<!=)=$/';
	
	/**
     * Allow newlines instead of spaces for all cases.
     *
     * @var boolean
	*/
	public $ignoreNewlines = false;
    
    /**
     * Allows multispaces before the assignment operators (for the alignment purpose).
     * So
     *  $veryLongVariable = 123;
     *  $shortvariable    = 456;
     * is correct;
     * 
     * @var bool
     */
    public $allowMultispaceForAssignmentAlignment = true;
    
    /**
     * The list of tokens that says that there must not be a space after minus.
     * For example:
     *      return -1;
     *      $a * -1;
     *      $someBool || -1 * $v > 10
     * Here is the unary minus. It must not require space after himself.
     * @var array
     */
    protected $tokensThatMayGoBeforeUnaryMinus = [];
    

    public function __construct() {
        $this->tokensThatMayGoBeforeUnaryMinus = array_merge(
            array(
                T_RETURN, // return -1;
                T_COMMA, // foo(1, -1);
                T_OPEN_PARENTHESIS, // foo(-1)
                T_OPEN_SQUARE_BRACKET, // $arr[-1]
                T_DOUBLE_ARROW, // [1 => -1]
                T_COLON, // : -1;
                T_INLINE_THEN, // ? -1
                T_INLINE_ELSE, // : -1;
                T_CASE, // case -1:
            ), 
            PHP_CodeSniffer_Tokens::$operators, // $a * -1
            PHP_CodeSniffer_Tokens::$booleanOperators, //$a || -1 === $b
            PHP_CodeSniffer_Tokens::$comparisonTokens, // $a === -1
            PHP_CodeSniffer_Tokens::$assignmentTokens // $a = -1
        );
    }

    public function register()
    {
        $comparison = PHP_CodeSniffer_Tokens::$comparisonTokens;
        $operators  = PHP_CodeSniffer_Tokens::$operators;
        $assignment = PHP_CodeSniffer_Tokens::$assignmentTokens;
        $inlineIf   = array(
           T_INLINE_THEN,
           T_INLINE_ELSE,
        
        );
        return array_unique(
            array_merge($comparison, $operators, $assignment, $inlineIf)
        );
    } //end register()



    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Skip default values in function declarations.
        if ($tokens[$stackPtr]['code'] === T_EQUAL
            || $tokens[$stackPtr]['code'] === T_MINUS
        ) {
            if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
                $parenthesis = array_keys($tokens[$stackPtr]['nested_parenthesis']);
                $bracket     = array_pop($parenthesis);
                if (isset($tokens[$bracket]['parenthesis_owner']) === true) {
                    $function = $tokens[$bracket]['parenthesis_owner'];
                    if ($tokens[$function]['code'] === T_FUNCTION
                        || $tokens[$function]['code'] === T_CLOSURE
                    ) {
                        return;
                    }
                }
            }
        }

        if ($tokens[$stackPtr]['code'] === T_EQUAL) {
            // Skip for '=&' case.
            if (isset($tokens[($stackPtr + 1)]) === true
                && $tokens[($stackPtr + 1)]['code'] === T_BITWISE_AND
            ) {
                return;
            }
        }

        // Skip short ternary such as: "$foo = $bar ?: true;".
        if (($tokens[$stackPtr]['code'] === T_INLINE_THEN
            && $tokens[($stackPtr + 1)]['code'] === T_INLINE_ELSE)
            || ($tokens[($stackPtr - 1)]['code'] === T_INLINE_THEN
            && $tokens[$stackPtr]['code'] === T_INLINE_ELSE)
        ) {
                return;
        }

        if ($tokens[$stackPtr]['code'] === T_BITWISE_AND) {
            // If it's not a reference, then we expect one space either side of the
            // bitwise operator.
            if ($phpcsFile->isReference($stackPtr) === true) {
                return;
            }

            // Check there is one space before the & operator.
            if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space before "&" operator; 0 found';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBeforeAmp');
                if ($fix === true) {
                    $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
                }

                $phpcsFile->recordMetric($stackPtr, 'Space before operator', 0);
            } else {
                if ($tokens[($stackPtr - 2)]['line'] !== $tokens[$stackPtr]['line']) {
                    $found = 'newline';
                } else {
                    $found = $tokens[($stackPtr - 1)]['length'];
                }

                $phpcsFile->recordMetric($stackPtr, 'Space before operator', $found);
                if (!$this->isSpaceCountOk('&', $found, $this->allowNewLineBefore)) {
                    $error = 'Expected 1 space before "&" operator; %s found';
                    $data  = array($found);
                    $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBeforeAmp', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                    }
                }
            }//end if

            // Check there is one space after the & operator.
            if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space after "&" operator; 0 found';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfterAmp');
                if ($fix === true) {
                    $phpcsFile->fixer->addContent($stackPtr, ' ');
                }

                $phpcsFile->recordMetric($stackPtr, 'Space after operator', 0);
            } else {
                if ($tokens[($stackPtr + 2)]['line'] !== $tokens[$stackPtr]['line']) {
                    $found = 'newline';
                } else {
                    $found = $tokens[($stackPtr + 1)]['length'];
                }

                $phpcsFile->recordMetric($stackPtr, 'Space after operator', $found);
                if (!$this->isSpaceCountOk('&', $found, $this->allowNewLineAfter)) {
                    $error = 'Expected 1 space after "&" operator; %s found';
                    $data  = array($found);
                    $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterAmp', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                    }
                }
            }//end if

            return;
        }//end if

        if ($tokens[$stackPtr]['code'] === T_MINUS) {
            // Check minus spacing, but make sure we aren't just assigning
            // a minus value or returning one.
            $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
            
            // It is a unary minus.
            if (false !== array_search($tokens[$prev]['code'], $this->tokensThatMayGoBeforeUnaryMinus)) {
                return;
            }

        }//end if

        $operator = $tokens[$stackPtr]['content'];

        if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
            $error = "Expected 1 space before \"$operator\"; 0 found";
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBefore');
            if ($fix === true) {
                $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
            }

            $phpcsFile->recordMetric($stackPtr, 'Space before operator', 0);
        } elseif (
            // Don't throw an error for assignments, because other standards allow
            // multiple spaces there to align multiple assignments.
            !isset(PHP_CodeSniffer_Tokens::$assignmentTokens[$tokens[$stackPtr]['code']]) 
            || !$this->allowMultispaceForAssignmentAlignment
        ) {
            if ($tokens[($stackPtr - 2)]['line'] !== $tokens[$stackPtr]['line']) {
                $found = 'newline';
            } else {
                $found = $tokens[($stackPtr - 1)]['length'];
            }

            $phpcsFile->recordMetric($stackPtr, 'Space before operator', $found);
            if (!$this->isSpaceCountOk($operator, $found, $this->allowNewLineBefore)) {
                $error = 'Expected 1 space before "%s"; %s found';
                $data  = array(
                          $operator,
                          $found,
                         );
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBefore', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                }
            }
        }//end if

        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $error = "Expected 1 space after \"$operator\"; 0 found";
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfter');
            if ($fix === true) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }

            $phpcsFile->recordMetric($stackPtr, 'Space after operator', 0);
        } else {
            if ($tokens[($stackPtr + 2)]['line'] !== $tokens[$stackPtr]['line']) {
                $found = 'newline';
            } else {
                $found = $tokens[($stackPtr + 1)]['length'];
            }

            $phpcsFile->recordMetric($stackPtr, 'Space after operator', $found);
            if (!$this->isSpaceCountOk($operator, $found, $this->allowNewLineAfter)) {
                $error = 'Expected 1 space after "%s"; %s found';
                $data  = array(
                          $operator,
                          $found,
                         );
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfter', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                }
            }
        }//end if

    }//end process()
    
    /**
     * Checks if spaces count around an operator is ok.
     * 
     * @param string $operator
     * @param int $found
     * @param bool $isNewlineAllowed
     * @return bool
     */
    protected function isSpaceCountOk($operator, $found, $isNewlineAllowed) {
        if ($found === 1) {
            return true;
        }
        if ($found !== 'newline') {
            return false;
        }
		
        if (is_bool($isNewlineAllowed)) {
            return $isNewlineAllowed || $this->ignoreNewlines;
        }
        return preg_match($isNewlineAllowed, $operator);
    }//end isSpaceCountOk()


}//end class
