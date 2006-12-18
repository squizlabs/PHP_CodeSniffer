<?php
/**
 * A test to ensure that arrays conform to the array coding standard.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

require_once 'PHP/CodeSniffer/Sniff.php';

/**
 * A test to ensure that arrays conform to the array coding standard.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Sniffs_Arrays_ArrayDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_ARRAY,
               );

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Array keyword should be lower case.
        if (strtolower($tokens[$stackPtr]['content']) !== $tokens[$stackPtr]['content']) {
            $error = 'Array keyword should be lower case. Expected "array" but found "'.$tokens[$stackPtr]['content'].'".';
            $phpcsFile->addError($error, $stackPtr);
        }

        $arrayStart   = $tokens[$stackPtr]['parenthesis_opener'];
        $arrayEnd     = $tokens[$arrayStart]['parenthesis_closer'];
        $keywordStart = $tokens[$stackPtr]['column'];
        if ($tokens[$arrayStart]['line'] === $tokens[$arrayEnd]['line']) {
            // Single line array.
            // Find the next non-whitespace character.
            $content = $phpcsFile->findNext(array(T_WHITESPACE), $arrayStart + 1, $arrayEnd + 1, true);
            if ($content === $arrayEnd) {
                // Empty array, but if the brackets aren't together, there's a problem.
                if (($arrayEnd - $arrayStart) !== 1) {
                    $error = 'Empty array initialisation should have no space between the parentheses';
                    $phpcsFile->addError($error, $stackPtr);
                }
            }

            // Check if there are multiple values. If so, then it has to be multiple lines
            // unless it is contained inside a function call or condition.
            $nextComma  = $arrayStart;
            $valueCount = 0;
            $commas     = array();
            while (($nextComma = $phpcsFile->findNext(array(T_COMMA), $nextComma + 1, $arrayEnd)) !== false) {
                $valueCount++;
                $commas[] = $nextComma;
            }

            // Now check each of the double arrows (if any).
            $nextArrow = $arrayStart;
            while (($nextArrow = $phpcsFile->findNext(T_DOUBLE_ARROW, $nextArrow + 1, $arrayEnd)) !== false) {
                if ($tokens[$nextArrow - 1]['code'] !== T_WHITESPACE) {
                    $error = 'Space required before double arrow in array.';
                    $phpcsFile->addError($error, $nextArrow);
                } else {
                    $spaceLength = strlen($tokens[$nextArrow - 1]['content']);
                    if ($spaceLength !== 1) {
                        $error  = $spaceLength.' spaces found before ';
                        $error .= 'double arrow. Expected 1.';
                        $phpcsFile->addError($error, $nextArrow);
                    }
                }

                if ($tokens[$nextArrow + 1]['code'] !== T_WHITESPACE) {
                    $error = 'Space required after double arrow in array.';
                    $phpcsFile->addError($error, $nextArrow);
                } else {
                    $spaceLength = strlen($tokens[$nextArrow + 1]['content']);
                    if ($spaceLength !== 1) {
                        $error  = $spaceLength.' spaces found after ';
                        $error .= 'double arrow. Expected 1.';
                        $phpcsFile->addError($error, $nextArrow);
                    }
                }
            }//end while

            if ($valueCount > 0) {
                $conditionCheck = $phpcsFile->findPrevious(array(T_OPEN_PARENTHESIS, T_SEMICOLON), $stackPtr - 1, null, false);

                if (($conditionCheck === false) || ($tokens[$conditionCheck]['line'] !== $tokens[$stackPtr]['line'])) {
                    $error = 'Array with multiple values cannot be on a single line.';
                    $phpcsFile->addError($error, $stackPtr);
                    return;
                }

                // We have a multiple value array that is inside a condition or
                // function. Check its spacing is correct.
                foreach ($commas as $comma) {
                    if ($tokens[$comma + 1]['code'] !== T_WHITESPACE) {
                        $error = 'Space required after commas in inline array declaration. Expected ", '.$tokens[($comma + 1)]['content'].'" but found ",'.$tokens[($comma + 1)]['content'].'".';
                        $phpcsFile->addError($error, $comma);
                    }
                }
            }

            return;
        }//end if

        // Check the closing bracket is on a new line..
        $lastContent = $phpcsFile->findPrevious(array(T_WHITESPACE), $arrayEnd - 1, $arrayStart, true);
        if ($tokens[$lastContent]['line'] !== ($tokens[$arrayEnd]['line'] - 1)) {
            $phpcsFile->addError('Closing Parenthesis should be on a new line', $arrayEnd);
        } else if ($tokens[$arrayEnd]['column'] !== $keywordStart) {
            // Check the closing bracket is lined up under the a in array.
            $expected  = $keywordStart;
            $expected .= ($keywordStart === 0) ? ' space' : ' spaces';
            $found     = $tokens[$arrayEnd]['column'];
            $found    .= ($found === 0) ? ' space' : ' spaces';
            $phpcsFile->addError('Closing Parenthesis not aligned correctly. Expected '.$expected.' but found '.$found.'.', $arrayEnd);
        }


        $nextToken  = $stackPtr;
        $lastComma  = $stackPtr;
        $keyUsed    = false;
        $singleUsed = false;
        $lastToken  = '';
        $indices    = array();
        $maxLength  = 0;

        // Find all the double arrows that reside in this scope.
        while (($nextToken = $phpcsFile->findNext(array(T_DOUBLE_ARROW, T_COMMA, T_ARRAY), $nextToken + 1, $arrayEnd)) !== false) {
            $currentEntry = array();
            if ($tokens[$nextToken]['code'] === T_ARRAY) {
                // Let subsequent calls of this test handle nested arrays.
                $nextToken = $tokens[$tokens[$nextToken]['parenthesis_opener']]['parenthesis_closer'];
                continue;
            }

            if ($tokens[$nextToken]['code'] === T_COMMA) {
                if ($keyUsed === true && $lastToken === T_COMMA) {
                    $error = 'No key specified for entry when other array entries have keys.';
                    $phpcsFile->addError($error, $nextToken);
                    return;
                }

                if ($keyUsed === false) {
                    if ($tokens[$nextToken - 1]['code'] === T_WHITESPACE) {
                        $error = 'Whitespace found before comma in array declaration.';
                        $phpcsFile->addError($error, $nextToken);
                        return;
                    }

                    $valueContent = $phpcsFile->findPrevious(array(T_WHITESPACE), $nextToken);
                    $indices[]    = array(
                                     'value' => ($valueContent + 1),
                                    );
                    $singleUsed = true;
                }

                $lastToken = T_COMMA;
                continue;
            }//end if

            if ($tokens[$nextToken]['code'] === T_DOUBLE_ARROW) {
                if ($singleUsed === true) {
                    $error = 'Key used for array entry, when other entries have none.';
                    $phpcsFile->addError($error, $nextToken);
                    return;
                }

                $currentEntry['arrow'] = $nextToken;
                $keyUsed               = true;
                // Find the index that uses this double arrow.
                $index                 = $phpcsFile->findPrevious(array(T_WHITESPACE), $nextToken - 1, $arrayStart, true);
                $currentEntry['index'] = $index;
                if ($maxLength < strlen($tokens[$index]['content'])) {
                    $maxLength = strlen($tokens[$index]['content']);
                }

                // Find the value of this index.
                $nextContent           = $phpcsFile->findNext(array(T_WHITESPACE), $nextToken + 1, $arrayEnd, true);
                $currentEntry['value'] = $nextContent;
                $indices[]             = $currentEntry;
                $lastToken             = T_DOUBLE_ARROW;
            }//end if
        }//end while

        /*
            This section checks for arrays that don't specify keys.

            Arrays such as:
               array(
                'aaa',
                'bbb',
                'd',
               );
        */

        if ($keyUsed === false) {
            $count     = count($indices);
            $lastIndex = $indices[$count - 1]['value'];

            $trailingContent = $phpcsFile->findNext(array(T_WHITESPACE, T_COMMA), $lastIndex + 1, $arrayEnd, true);
            if ($trailingContent !== false) {
                $indices[] = array('value' => $trailingContent);
                $error     = 'Comma required after last value in array.';
                $phpcsFile->addError($error, $trailingContent);
            }

            foreach ($indices as $value) {
                if ($tokens[$value['value']]['column'] !== ($keywordStart + 1)) {
                    $error = 'Array value not aligned correctly. Expected '.($keywordStart + 1).' spaces but found '.$tokens[$value['value']]['column'].'.';
                    $phpcsFile->addError($error, $value['value']);
                }
            }

            return;
        }

        /*
            Below the actual indentation of the array is checked.
            Errors will be thrown when a key is not aligned, when
            a double arrow is not aligned, and when a value is not
            aligned correctly.
            If an error is found in one of the above areas, then errors
            are not reported for the rest of the line to avoid reporting
            spaces and columns incorrectly. Often fixing the first
            problem will fix the other 2 anyway.

            For example:

            $a = array(
                  'index'  => '2',
                 );

            In this array, the double arrow is indented too far, but this
            will also cause an error in the value's alignment. If the arrow were
            to be moved back one space however, then both errors would be fixed.
        */

        $numValues = count($indices);

        $indicesStart = ($keywordStart + 1);
        $arrowStart   = ($indicesStart + $maxLength + 1);
        $valueStart   = ($arrowStart + 3);
        foreach ($indices as $index) {
            if (($tokens[$index['index']]['line'] === $tokens[$stackPtr]['line']) && ($numValues > 1)) {
                $phpcsFile->addError('The first value in a multi-value array must be on a new line.', $stackPtr);
                continue;
            }

            if ($tokens[$index['index']]['column'] !== $indicesStart) {
                $phpcsFile->addError('Array key not aligned correctly. Expected '.$indicesStart.' spaces but found '.$tokens[$index['index']]['column'].'.', $index['index']);
                continue;
            }

            if ($tokens[$index['arrow']]['column'] !== $arrowStart) {
                $expected  = ($arrowStart - (strlen($tokens[$index['index']]['content']) + $tokens[$index['index']]['column']));
                $expected .= ($expected === 1) ? ' space' : ' spaces';
                $found     = ($tokens[$index['arrow']]['column'] - (strlen($tokens[$index['index']]['content']) + $tokens[$index['index']]['column']));
                $phpcsFile->addError('Array Double Arrow not aligned correctly. Expected '.$expected.' but found '.$found.'.', $index['arrow']);
                continue;
            }

            if ($tokens[$index['value']]['column'] !== $valueStart) {
                $expected  = ($valueStart - (strlen($tokens[$index['arrow']]['content']) + $tokens[$index['arrow']]['column']));
                $expected .= ($expected === 1) ? ' space' : ' spaces';
                $found     = ($tokens[$index['value']]['column'] - (strlen($tokens[$index['arrow']]['content']) + $tokens[$index['arrow']]['column']));
                $phpcsFile->addError('Value not aligned correctly. Expected '.$expected.' but found '.$found.'.', $index['arrow']);
            }

            // Check each line ends in a comma.
            if ($tokens[$index['value']]['code'] !== T_ARRAY) {
                $nextComma = $phpcsFile->findNext(array(T_COMMA), $index['value'] + 1);
                if (($nextComma === false) || ($tokens[$nextComma]['line'] !== $tokens[$index['value']]['line'])) {
                    $error = 'Each line in the array must end in a comma.';
                    $phpcsFile->addError($error, $index['value']);
                }
            }
        }//end foreach

    }//end process()


}//end class

?>
