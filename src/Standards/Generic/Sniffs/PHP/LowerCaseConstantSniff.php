<?php
/**
 * Checks that all uses of true, false and null are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class LowerCaseConstantSniff implements Sniff
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
     * The tokens this sniff is targetting.
     *
     * @var array
     */
    private $targets = [
        T_TRUE  => T_TRUE,
        T_FALSE => T_FALSE,
        T_NULL  => T_NULL,
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $targets = $this->targets;

        // Register function keywords to filter out type declarations.
        $targets[] = T_FUNCTION;
        $targets[] = T_CLOSURE;
        $targets[] = T_FN;

        return $targets;

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Handle function declarations separately as they may contain the keywords in type declarations.
        if ($tokens[$stackPtr]['code'] === T_FUNCTION
            || $tokens[$stackPtr]['code'] === T_CLOSURE
            || $tokens[$stackPtr]['code'] === T_FN
        ) {
            if (isset($tokens[$stackPtr]['parenthesis_closer']) === false) {
                return;
            }

            $end = $tokens[$stackPtr]['parenthesis_closer'];
            if (isset($tokens[$stackPtr]['scope_opener']) === true) {
                $end = $tokens[$stackPtr]['scope_opener'];
            }

            // Do a quick check if any of the targets exist in the declaration.
            $found = $phpcsFile->findNext($this->targets, $tokens[$stackPtr]['parenthesis_opener'], $end);
            if ($found === false) {
                // Skip forward, no need to examine these tokens again.
                return $end;
            }

            // Handle the whole function declaration in one go.
            $params = $phpcsFile->getMethodParameters($stackPtr);
            foreach ($params as $param) {
                if (isset($param['default_token']) === false) {
                    continue;
                }

                $paramEnd = $param['comma_token'];
                if ($param['comma_token'] === false) {
                    $paramEnd = $tokens[$stackPtr]['parenthesis_closer'];
                }

                for ($i = $param['default_token']; $i < $paramEnd; $i++) {
                    if (isset($this->targets[$tokens[$i]['code']]) === true) {
                        $this->processConstant($phpcsFile, $i);
                    }
                }
            }

            // Skip over return type declarations.
            return $end;
        }//end if

        // Handle property declarations separately as they may contain the keywords in type declarations.
        if (isset($tokens[$stackPtr]['conditions']) === true) {
            $conditions    = $tokens[$stackPtr]['conditions'];
            $lastCondition = end($conditions);
            if (isset(Tokens::$ooScopeTokens[$lastCondition]) === true) {
                // This can only be an OO constant or property declaration as methods are handled above.
                $equals = $phpcsFile->findPrevious(T_EQUAL, ($stackPtr - 1), null, false, null, true);
                if ($equals !== false) {
                    $this->processConstant($phpcsFile, $stackPtr);
                }

                return;
            }
        }

        // Handle everything else.
        $this->processConstant($phpcsFile, $stackPtr);

    }//end process()


    /**
     * Processes a non-type declaration constant.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    protected function processConstant(File $phpcsFile, $stackPtr)
    {
        $tokens   = $phpcsFile->getTokens();
        $keyword  = $tokens[$stackPtr]['content'];
        $expected = strtolower($keyword);

        if ($keyword !== $expected) {
            if ($keyword === strtoupper($keyword)) {
                $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'upper');
            } else {
                $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'mixed');
            }

            $error = 'TRUE, FALSE and NULL must be lowercase; expected "%s" but found "%s"';
            $data  = [
                $expected,
                $keyword,
            ];

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found', $data);
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, $expected);
            }
        } else {
            $phpcsFile->recordMetric($stackPtr, 'PHP constant case', 'lower');
        }

    }//end processConstant()


}//end class
