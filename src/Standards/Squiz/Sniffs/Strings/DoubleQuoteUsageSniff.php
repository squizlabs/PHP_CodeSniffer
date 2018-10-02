<?php
/**
 * Makes sure that any use of double quotes strings are warranted.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DoubleQuoteUsageSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_CONSTANT_ENCAPSED_STRING,
            T_DOUBLE_QUOTED_STRING,
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

        // If tabs are being converted to spaces by the tokeniser, the
        // original content should be used instead of the converted content.
        if (isset($tokens[$stackPtr]['orig_content']) === true) {
            $workingString = $tokens[$stackPtr]['orig_content'];
        } else {
            $workingString = $tokens[$stackPtr]['content'];
        }

        $lastStringToken = $stackPtr;

        $i = ($stackPtr + 1);
        if (isset($tokens[$i]) === true) {
            while ($i < $phpcsFile->numTokens
                && $tokens[$i]['code'] === $tokens[$stackPtr]['code']
            ) {
                if (isset($tokens[$i]['orig_content']) === true) {
                    $workingString .= $tokens[$i]['orig_content'];
                } else {
                    $workingString .= $tokens[$i]['content'];
                }

                $lastStringToken = $i;
                $i++;
            }
        }

        $skipTo = ($lastStringToken + 1);

        // Check if it's a double quoted string.
        if ($workingString[0] !== '"' || substr($workingString, -1) !== '"') {
            return $skipTo;
        }

        // The use of variables in double quoted strings is not allowed.
        if ($tokens[$stackPtr]['code'] === T_DOUBLE_QUOTED_STRING) {
            $stringTokens = token_get_all('<?php '.$workingString);
            foreach ($stringTokens as $token) {
                if (is_array($token) === true && $token[0] === T_VARIABLE) {
                    $error = 'Variable "%s" not allowed in double quoted string; use concatenation instead';
                    $data  = [$token[1]];
                    $phpcsFile->addError($error, $stackPtr, 'ContainsVar', $data);
                }
            }

            return $skipTo;
        }//end if

        $allowedChars = [
            '\0',
            '\1',
            '\2',
            '\3',
            '\4',
            '\5',
            '\6',
            '\7',
            '\n',
            '\r',
            '\f',
            '\t',
            '\v',
            '\x',
            '\b',
            '\e',
            '\u',
            '\'',
        ];

        foreach ($allowedChars as $testChar) {
            if (strpos($workingString, $testChar) !== false) {
                return $skipTo;
            }
        }

        $error = 'String %s does not require double quotes; use single quotes instead';
        $data  = [str_replace(["\r", "\n"], ['\r', '\n'], $workingString)];
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NotRequired', $data);

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();
            $innerContent = substr($workingString, 1, -1);
            $innerContent = str_replace('\"', '"', $innerContent);
            $innerContent = str_replace('\\$', '$', $innerContent);
            $phpcsFile->fixer->replaceToken($stackPtr, "'$innerContent'");
            while ($lastStringToken !== $stackPtr) {
                $phpcsFile->fixer->replaceToken($lastStringToken, '');
                $lastStringToken--;
            }

            $phpcsFile->fixer->endChangeset();
        }

        return $skipTo;

    }//end process()


}//end class
