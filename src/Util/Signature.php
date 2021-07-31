<?php
/**
 * Function for generating the code signature used in the baseline.
 *
 * @author    Frank Dekker <fdekker@123inkt.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util;

class Signature
{


    /**
     * Generate the sha1 code signature for the tokens around the given line.
     *
     * @param array<int|string> $tokens All tokens of a given file.
     * @param int               $lineNr The lineNr to search for tokens.
     *
     * @return string The sha1 hash of the tokens around the given line
     */
    public static function createSignature(array $tokens, $lineNr)
    {
        // get all tokens one line before and after
        $start = ($lineNr - 1);
        $end   = ($lineNr + 1);

        $line = '';
        foreach ($tokens as $token) {
            if ($token['line'] > $end) {
                break;
            }

            if ($token['line'] >= $start) {
                // concat content excluding line endings.
                $line .= isset($token['content']) ? trim($token['content'], "\r\n") : '';
            }
        }

        // generate hash
        return hash('sha1', $line);

    }//end createSignature()


}//end class
