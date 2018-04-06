<?php
/**
 * Checks the nesting level for methods.
 *
 * @author    Johann-Peter Hartmann <hartmann@mayflower.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2007-2014 Mayflower GmbH
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class NestingLevelSniff implements Sniff
{

    /**
     * A nesting level higher than this value will throw a warning.
     *
     * @var integer
     */
    public $nestingLevel = 5;

    /**
     * A nesting level higher than this value will throw an error.
     *
     * @var integer
     */
    public $absoluteNestingLevel = 10;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_FUNCTION];

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

        // Ignore abstract methods.
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        // Detect start and end of this function definition.
        $start = $tokens[$stackPtr]['scope_opener'];
        $end   = $tokens[$stackPtr]['scope_closer'];

        $nestingLevel = 0;

        // Find the maximum nesting level of any token in the function.
        for ($i = ($start + 1); $i < $end; $i++) {
            $level = $tokens[$i]['level'];
            if ($nestingLevel < $level) {
                $nestingLevel = $level;
            }
        }

        // We subtract the nesting level of the function itself.
        $nestingLevel = ($nestingLevel - $tokens[$stackPtr]['level'] - 1);

        if ($nestingLevel > $this->absoluteNestingLevel) {
            $error = 'Function\'s nesting level (%s) exceeds allowed maximum of %s';
            $data  = [
                $nestingLevel,
                $this->absoluteNestingLevel,
            ];
            $phpcsFile->addError($error, $stackPtr, 'MaxExceeded', $data);
        } else if ($nestingLevel > $this->nestingLevel) {
            $warning = 'Function\'s nesting level (%s) exceeds %s; consider refactoring the function';
            $data    = [
                $nestingLevel,
                $this->nestingLevel,
            ];
            $phpcsFile->addWarning($warning, $stackPtr, 'TooHigh', $data);
        }

    }//end process()


}//end class
