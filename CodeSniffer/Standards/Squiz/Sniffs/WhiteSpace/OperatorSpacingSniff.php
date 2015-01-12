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
     * Whether a newline should be treated as whitespace for the purpose of
     * operator spacing.
     *
     * @var boolean
     */
    public $treatNewlineAsWhitespace = false;

    /**
     * The first part of the message to display when an unexpected amount of
     * whitespace is encountered. Depends on whther $treatNewlineAsWhitespace
     * is set.
     *
     * @var string
     */
    public $expectedSpaceMessage = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $comparison = PHP_CodeSniffer_Tokens::$comparisonTokens;
        $operators  = PHP_CodeSniffer_Tokens::$operators;
        $assignment = PHP_CodeSniffer_Tokens::$assignmentTokens;
        $inlineIf   = array(
                       T_INLINE_THEN,
                       T_INLINE_ELSE,
                      );

        if ($this->treatNewlineAsWhitespace === true) {
            $this->expectedSpaceMessage = 'Expected 1 space or newline';
        } else {
            $this->expectedSpaceMessage = 'Expected 1 space';
        }

        return array_unique(
            array_merge($comparison, $operators, $assignment, $inlineIf)
        );

    }//end register()


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
                $error ='%s before "&" operator; 0 found';
                $data  = array($this->expectedSpaceMessage);
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBeforeAmp', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
                }

                $phpcsFile->recordMetric($stackPtr, 'Space before operator', 0);
            } else {
                if ($tokens[($stackPtr - 2)]['line'] !== $tokens[$stackPtr]['line']) {
                    $foundToken  = 'newline';
                    $foundLength = 1;
                    $foundError  = $foundToken;
                } else {
                    $foundToken  = 'space';
                    $foundLength = $tokens[($stackPtr - 1)]['length'];
                    $foundError  = $foundLength;
                }

                $phpcsFile->recordMetric(
                    $stackPtr,
                    sprintf('%s before operator', ucfirst($foundToken)),
                    $foundLength
                );

                $hasProblem = ($this->treatNewlineAsWhitespace === false && $foundToken === 'newline') ||
                    ($foundToken === 'space' && $foundLength > 1);

                if ($hasProblem === true) {
                    $error = '%s before "&" operator; %s found';
                    $data  = array(
                              $this->expectedSpaceMessage,
                              $foundError,
                             );
                    $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBeforeAmp', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                    }
                }
            }//end if

            // Check there is one space after the & operator.
            if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
                $error = '%s after "&" operator; 0 found';
                $data  = array($this->expectedSpaceMessage);
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfterAmp', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->addContent($stackPtr, ' ');
                }

                $phpcsFile->recordMetric($stackPtr, 'Space after operator', 0);
            } else {
                if ($tokens[($stackPtr + 2)]['line'] !== $tokens[$stackPtr]['line']) {
                    $foundToken  = 'newline';
                    $foundLength = 1;
                    $foundError  = $foundToken;
                } else {
                    $foundToken  = 'space';
                    $foundLength = $tokens[($stackPtr + 1)]['length'];
                    $foundError  = $foundLength;
                }

                $phpcsFile->recordMetric(
                    $stackPtr,
                    sprintf('%s before operator', ucfirst($foundToken)),
                    $foundLength
                );

                $hasProblem = ($this->treatNewlineAsWhitespace === false && $foundToken === 'newline') ||
                    ($foundToken === 'space' && $foundLength > 1);

                if ($hasProblem === true) {
                    $error = '%s after "&" operator; %s found';
                    $data  = array(
                              $this->expectedSpaceMessage,
                              $foundError,
                             );
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
            if ($tokens[$prev]['code'] === T_RETURN) {
                // Just returning a negative value; eg. (return -1).
                return;
            }

            if (isset(PHP_CodeSniffer_Tokens::$operators[$tokens[$prev]['code']]) === true) {
                // Just trying to operate on a negative value; eg. ($var * -1).
                return;
            }

            if (isset(PHP_CodeSniffer_Tokens::$comparisonTokens[$tokens[$prev]['code']]) === true) {
                // Just trying to compare a negative value; eg. ($var === -1).
                return;
            }

            if (isset(PHP_CodeSniffer_Tokens::$assignmentTokens[$tokens[$prev]['code']]) === true) {
                // Just trying to assign a negative value; eg. ($var = -1).
                return;
            }

            // A list of tokens that indicate that the token is not
            // part of an arithmetic operation.
            $invalidTokens = array(
                              T_COMMA               => true,
                              T_OPEN_PARENTHESIS    => true,
                              T_OPEN_SQUARE_BRACKET => true,
                              T_DOUBLE_ARROW        => true,
                              T_COLON               => true,
                              T_INLINE_THEN         => true,
                              T_INLINE_ELSE         => true,
                              T_CASE                => true,
                             );

            if (isset($invalidTokens[$tokens[$prev]['code']]) === true) {
                // Just trying to use a negative value; eg. myFunction($var, -2).
                return;
            }
        }//end if

        $operator = $tokens[$stackPtr]['content'];

        if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
            $error = $this->expectedSpaceMessage . " before \"$operator\"; 0 found";
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceBefore');
            if ($fix === true) {
                $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
            }

            $phpcsFile->recordMetric($stackPtr, 'Space before operator', 0);
        } else if (isset(PHP_CodeSniffer_Tokens::$assignmentTokens[$tokens[$stackPtr]['code']]) === false) {
            // Don't throw an error for assignments, because other standards allow
            // multiple spaces there to align multiple assignments.
            if ($tokens[($stackPtr - 2)]['line'] !== $tokens[$stackPtr]['line']) {
                $foundToken  = 'newline';
                $foundLength = 1;
                $foundError  = $foundToken;
            } else {
                $foundToken  = 'space';
                $foundLength = $tokens[($stackPtr - 1)]['length'];
                $foundError  = $foundLength;
            }

            $phpcsFile->recordMetric(
                $stackPtr,
                sprintf('%s before operator', ucfirst($foundToken)),
                $foundLength
            );

            $hasProblem = ($this->treatNewlineAsWhitespace === false && $foundToken === 'newline') ||
                ($foundToken === 'space' && $foundLength > 1);

            if ($hasProblem === true) {
                $error = '%s before "%s"; %s found';
                $data  = array(
                          $this->expectedSpaceMessage,
                          $operator,
                          $foundError,
                         );
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBefore', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                }
            }
        }//end if

        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $error = $this->expectedSpaceMessage . " after \"$operator\"; 0 found";
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpaceAfter');
            if ($fix === true) {
                $phpcsFile->fixer->addContent($stackPtr, ' ');
            }

            $phpcsFile->recordMetric($stackPtr, 'Space after operator', 0);
        } else {
            if ($tokens[($stackPtr + 2)]['line'] !== $tokens[$stackPtr]['line']) {
                $foundToken  = 'newline';
                $foundLength = 1;
                $foundError  = $foundToken;
            } else {
                $foundToken  = 'space';
                $foundLength = $tokens[($stackPtr + 1)]['length'];
                $foundError  = $foundLength;
            }

            $phpcsFile->recordMetric(
                $stackPtr,
                sprintf('%s after operator', ucfirst($foundToken)),
                $foundLength
            );

            $hasProblem = ($this->treatNewlineAsWhitespace === false && $foundToken === 'newline') ||
                ($foundToken === 'space' && $foundLength > 1);

            if ($hasProblem === true) {
                $error = '%s after "%s"; %s found';
                $data  = array(
                          $this->expectedSpaceMessage,
                          $operator,
                          $foundError,
                         );
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfter', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                }
            }
        }//end if

    }//end process()


}//end class
