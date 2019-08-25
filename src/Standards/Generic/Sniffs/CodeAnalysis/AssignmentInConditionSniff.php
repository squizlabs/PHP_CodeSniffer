<?php
/**
 * Detects variable assignments being made within conditions.
 *
 * This is a typical code smell and more often than not a comparison was intended.
 *
 * Note: this sniff does not detect variable assignments in the conditional part of ternaries!
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2017 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class AssignmentInConditionSniff implements Sniff
{

    /**
     * Assignment tokens to trigger on.
     *
     * Set in the register() method.
     *
     * @var array
     */
    protected $assignmentTokens = [];

    /**
     * The tokens that indicate the start of a condition.
     *
     * @var array
     */
    protected $conditionStartTokens = [];


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        $this->assignmentTokens = Tokens::$assignmentTokens;
        unset($this->assignmentTokens[T_DOUBLE_ARROW]);

        $starters = Tokens::$booleanOperators;
        $starters[T_SEMICOLON]        = T_SEMICOLON;
        $starters[T_OPEN_PARENTHESIS] = T_OPEN_PARENTHESIS;

        $this->conditionStartTokens = $starters;

        return [
            T_IF,
            T_ELSEIF,
            T_FOR,
            T_SWITCH,
            T_CASE,
            T_WHILE,
        ];

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

        // Find the condition opener/closer.
        if ($token['code'] === T_FOR) {
            if (isset($token['parenthesis_opener'], $token['parenthesis_closer']) === false) {
                return;
            }

            $semicolon = $phpcsFile->findNext(T_SEMICOLON, ($token['parenthesis_opener'] + 1), ($token['parenthesis_closer']));
            if ($semicolon === false) {
                return;
            }

            $opener = $semicolon;

            $semicolon = $phpcsFile->findNext(T_SEMICOLON, ($opener + 1), ($token['parenthesis_closer']));
            if ($semicolon === false) {
                return;
            }

            $closer = $semicolon;
            unset($semicolon);
        } else if ($token['code'] === T_CASE) {
            if (isset($token['scope_opener']) === false) {
                return;
            }

            $opener = $stackPtr;
            $closer = $token['scope_opener'];
        } else {
            if (isset($token['parenthesis_opener'], $token['parenthesis_closer']) === false) {
                return;
            }

            $opener = $token['parenthesis_opener'];
            $closer = $token['parenthesis_closer'];
        }//end if

        $startPos = $opener;

        do {
            $hasAssignment = $phpcsFile->findNext($this->assignmentTokens, ($startPos + 1), $closer);
            if ($hasAssignment === false) {
                return;
            }

            // Examine whether the left side is a variable.
            $hasVariable       = false;
            $conditionStart    = $startPos;
            $altConditionStart = $phpcsFile->findPrevious($this->conditionStartTokens, ($hasAssignment - 1), $startPos);
            if ($altConditionStart !== false) {
                $conditionStart = $altConditionStart;
            }

            for ($i = $hasAssignment; $i > $conditionStart; $i--) {
                if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                    continue;
                }

                // If this is a variable or array, we've seen all we need to see.
                if ($tokens[$i]['code'] === T_VARIABLE || $tokens[$i]['code'] === T_CLOSE_SQUARE_BRACKET) {
                    $hasVariable = true;
                    break;
                }

                // If this is a function call or something, we are OK.
                if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                    break;
                }
            }

            if ($hasVariable === true) {
                $errorCode = 'Found';
                if ($token['code'] === T_WHILE) {
                    $errorCode = 'FoundInWhileCondition';
                }

                $phpcsFile->addWarning(
                    'Variable assignment found within a condition. Did you mean to do a comparison ?',
                    $hasAssignment,
                    $errorCode
                );
            }

            $startPos = $hasAssignment;
        } while ($startPos < $closer);

    }//end process()


}//end class
