<?php
/**
 * Checks that the opening PHP tag is the first content in a file.
 *
 * @author    Andy Grunwald <andygrunwald@gmail.com>
 * @copyright 2010-2014 Andy Grunwald
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class CharacterBeforePHPOpeningTagSniff implements Sniff
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
        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $expected = 0;
        if ($stackPtr > 0) {
            // Allow a byte-order mark.
            $tokens = $phpcsFile->getTokens();
            foreach ($this->bomDefinitions as $bomName => $expectedBomHex) {
                $bomByteLength = (strlen($expectedBomHex) / 2);
                $htmlBomHex    = bin2hex(substr($tokens[0]['content'], 0, $bomByteLength));
                if ($htmlBomHex === $expectedBomHex) {
                    $expected++;
                    break;
                }
            }

            // Allow a shebang line.
            if (substr($tokens[0]['content'], 0, 2) === '#!') {
                $expected++;
            }
        }

        if ($stackPtr !== $expected) {
            $error = 'The opening PHP tag must be the first content in the file';
            $phpcsFile->addError($error, $stackPtr, 'Found');
        }

        // Skip the rest of the file so we don't pick up additional
        // open tags, typically embedded in HTML.
        return $phpcsFile->numTokens;

    }//end process()


}//end class
