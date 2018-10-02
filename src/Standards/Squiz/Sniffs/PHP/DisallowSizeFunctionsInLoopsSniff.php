<?php
/**
 * Bans the use of size-based functions in loop conditions.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowSizeFunctionsInLoopsSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];

    /**
     * An array of functions we don't want in the condition of loops.
     *
     * @var array
     */
    protected $forbiddenFunctions = [
        'PHP' => [
            'sizeof' => true,
            'strlen' => true,
            'count'  => true,
        ],
        'JS'  => ['length' => true],
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_WHILE,
            T_FOR,
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
        $tokens       = $phpcsFile->getTokens();
        $tokenizer    = $phpcsFile->tokenizerType;
        $openBracket  = $tokens[$stackPtr]['parenthesis_opener'];
        $closeBracket = $tokens[$stackPtr]['parenthesis_closer'];

        if ($tokens[$stackPtr]['code'] === T_FOR) {
            // We only want to check the condition in FOR loops.
            $start = $phpcsFile->findNext(T_SEMICOLON, ($openBracket + 1));
            $end   = $phpcsFile->findPrevious(T_SEMICOLON, ($closeBracket - 1));
        } else {
            $start = $openBracket;
            $end   = $closeBracket;
        }

        for ($i = ($start + 1); $i < $end; $i++) {
            if ($tokens[$i]['code'] === T_STRING
                && isset($this->forbiddenFunctions[$tokenizer][$tokens[$i]['content']]) === true
            ) {
                $functionName = $tokens[$i]['content'];
                if ($tokenizer === 'JS') {
                    // Needs to be in the form object.function to be valid.
                    $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($i - 1), null, true);
                    if ($prev === false || $tokens[$prev]['code'] !== T_OBJECT_OPERATOR) {
                        continue;
                    }

                    $functionName = 'object.'.$functionName;
                } else {
                    // Make sure it isn't a member var.
                    if ($tokens[($i - 1)]['code'] === T_OBJECT_OPERATOR) {
                        continue;
                    }

                    $functionName .= '()';
                }

                $error = 'The use of %s inside a loop condition is not allowed; assign the return value to a variable and use the variable in the loop condition instead';
                $data  = [$functionName];
                $phpcsFile->addError($error, $i, 'Found', $data);
            }//end if
        }//end for

    }//end process()


}//end class
