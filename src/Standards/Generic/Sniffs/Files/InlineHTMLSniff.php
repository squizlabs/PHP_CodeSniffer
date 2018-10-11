<?php
/**
 * Ensures the whole file is PHP only, with no whitespace or inline HTML.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Files;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class InlineHTMLSniff implements Sniff
{

    /**
     * List of supported BOM definitions.
     *
     * Use encoding names as keys and hex BOM representations as values.
     *
     * @var array
     */
    protected $bomDefinitions = [
        'UTF-8'       => 'efbbbf',
        'UTF-16 (BE)' => 'feff',
        'UTF-16 (LE)' => 'fffe',
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_INLINE_HTML];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        // Allow a byte-order mark.
        $tokens = $phpcsFile->getTokens();
        foreach ($this->bomDefinitions as $bomName => $expectedBomHex) {
            $bomByteLength = (strlen($expectedBomHex) / 2);
            $htmlBomHex    = bin2hex(substr($tokens[0]['content'], 0, $bomByteLength));
            if ($htmlBomHex === $expectedBomHex && strlen($tokens[0]['content']) === $bomByteLength) {
                return;
            }
        }

        // Ignore shebang lines.
        $tokens = $phpcsFile->getTokens();
        if (substr($tokens[$stackPtr]['content'], 0, 2) === '#!') {
            return;
        }

        $error = 'PHP files must only contain PHP code';
        $phpcsFile->addError($error, $stackPtr, 'Found');

        return $phpcsFile->numTokens;

    }//end process()


}//end class
