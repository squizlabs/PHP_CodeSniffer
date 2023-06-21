<?php
/**
 * Ensures that arrays conform to the array coding standard.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ArrayDeclarationSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_ARRAY,
            T_OPEN_SHORT_ARRAY,
        ];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being checked.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_ARRAY) {
            $phpcsFile->recordMetric($stackPtr, 'Short array syntax used', 'no');

            // Array keyword should be lower case.
            if ($tokens[$stackPtr]['content'] !== strtolower($tokens[$stackPtr]['content'])) {
                if ($tokens[$stackPtr]['content'] === strtoupper($tokens[$stackPtr]['content'])) {
                    $phpcsFile->recordMetric($stackPtr, 'Array keyword case', 'upper');
                } else {
                    $phpcsFile->recordMetric($stackPtr, 'Array keyword case', 'mixed');
                }

                $error = 'Array keyword should be lower case; expected "array" but found "%s"';
                $data  = [$tokens[$stackPtr]['content']];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NotLowerCase', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($stackPtr, 'array');
                }
            } else {
                $phpcsFile->recordMetric($stackPtr, 'Array keyword case', 'lower');
            }

            $arrayStart = $tokens[$stackPtr]['parenthesis_opener'];
            if (isset($tokens[$arrayStart]['parenthesis_closer']) === false) {
                return;
            }

            $arrayEnd = $tokens[$arrayStart]['parenthesis_closer'];

            if ($arrayStart !== ($stackPtr + 1)) {
                $error = 'There must be no space between the "array" keyword and the opening parenthesis';

                $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), $arrayStart, true);
                if (isset(Tokens::$commentTokens[$tokens[$next]['code']]) === true) {
                    // We don't have anywhere to put the comment, so don't attempt to fix it.
                    $phpcsFile->addError($error, $stackPtr, 'SpaceAfterKeyword');
                } else {
                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterKeyword');
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($stackPtr + 1); $i < $arrayStart; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Short array syntax used', 'yes');
            $arrayStart = $stackPtr;
            $arrayEnd   = $tokens[$stackPtr]['bracket_closer'];
        }//end if

        // Check for empty arrays.
        $content = $phpcsFile->findNext(T_WHITESPACE, ($arrayStart + 1), ($arrayEnd + 1), true);
        if ($content === $arrayEnd) {
            // Empty array, but if the brackets aren't together, there's a problem.
            if (($arrayEnd - $arrayStart) !== 1) {
                $error = 'Empty array declaration must have no space between the parentheses';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceInEmptyArray');

                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($arrayStart + 1); $i < $arrayEnd; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }

            // We can return here because there is nothing else to check. All code
            // below can assume that the array is not empty.
            return;
        }

        if ($tokens[$arrayStart]['line'] === $tokens[$arrayEnd]['line']) {
            $this->processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd);
        } else {
            $this->processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd);
        }

    }//end process()


    /**
     * Processes a single-line array definition.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile  The current file being checked.
     * @param int                         $stackPtr   The position of the current token
     *                                                in the stack passed in $tokens.
     * @param int                         $arrayStart The token that starts the array definition.
     * @param int                         $arrayEnd   The token that ends the array definition.
     *
     * @return void
     */
    public function processSingleLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd)
    {
        $tokens = $phpcsFile->getTokens();

        // Check if there are multiple values. If so, then it has to be multiple lines
        // unless it is contained inside a function call or condition.
        $valueCount = 0;
        $commas     = [];
        for ($i = ($arrayStart + 1); $i < $arrayEnd; $i++) {
            // Skip bracketed statements, like function calls.
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $i = $tokens[$i]['parenthesis_closer'];
                continue;
            }

            if ($tokens[$i]['code'] === T_COMMA) {
                // Before counting this comma, make sure we are not
                // at the end of the array.
                $next = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), $arrayEnd, true);
                if ($next !== false) {
                    $valueCount++;
                    $commas[] = $i;
                } else {
                    // There is a comma at the end of a single line array.
                    $error = 'Comma not allowed after last value in single-line array declaration';
                    $fix   = $phpcsFile->addFixableError($error, $i, 'CommaAfterLast');
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                }
            }
        }//end for

        // Now check each of the double arrows (if any).
        $nextArrow = $arrayStart;
        while (($nextArrow = $phpcsFile->findNext(T_DOUBLE_ARROW, ($nextArrow + 1), $arrayEnd)) !== false) {
            if ($tokens[($nextArrow - 1)]['code'] !== T_WHITESPACE) {
                $content = $tokens[($nextArrow - 1)]['content'];
                $error   = 'Expected 1 space between "%s" and double arrow; 0 found';
                $data    = [$content];
                $fix     = $phpcsFile->addFixableError($error, $nextArrow, 'NoSpaceBeforeDoubleArrow', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->addContentBefore($nextArrow, ' ');
                }
            } else {
                $spaceLength = $tokens[($nextArrow - 1)]['length'];
                if ($spaceLength !== 1) {
                    $content = $tokens[($nextArrow - 2)]['content'];
                    $error   = 'Expected 1 space between "%s" and double arrow; %s found';
                    $data    = [
                        $content,
                        $spaceLength,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $nextArrow, 'SpaceBeforeDoubleArrow', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($nextArrow - 1), ' ');
                    }
                }
            }//end if

            if ($tokens[($nextArrow + 1)]['code'] !== T_WHITESPACE) {
                $content = $tokens[($nextArrow + 1)]['content'];
                $error   = 'Expected 1 space between double arrow and "%s"; 0 found';
                $data    = [$content];
                $fix     = $phpcsFile->addFixableError($error, $nextArrow, 'NoSpaceAfterDoubleArrow', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->addContent($nextArrow, ' ');
                }
            } else {
                $spaceLength = $tokens[($nextArrow + 1)]['length'];
                if ($spaceLength !== 1) {
                    $content = $tokens[($nextArrow + 2)]['content'];
                    $error   = 'Expected 1 space between double arrow and "%s"; %s found';
                    $data    = [
                        $content,
                        $spaceLength,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $nextArrow, 'SpaceAfterDoubleArrow', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($nextArrow + 1), ' ');
                    }
                }
            }//end if
        }//end while

        if ($valueCount > 0) {
            $nestedParenthesis = false;
            if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
                $nested            = $tokens[$stackPtr]['nested_parenthesis'];
                $nestedParenthesis = array_pop($nested);
            }

            if ($nestedParenthesis === false
                || $tokens[$nestedParenthesis]['line'] !== $tokens[$stackPtr]['line']
            ) {
                $error = 'Array with multiple values cannot be declared on a single line';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SingleLineNotAllowed');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->addNewline($arrayStart);

                    if ($tokens[($arrayEnd - 1)]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken(($arrayEnd - 1), $phpcsFile->eolChar);
                    } else {
                        $phpcsFile->fixer->addNewlineBefore($arrayEnd);
                    }

                    $phpcsFile->fixer->endChangeset();
                }

                return;
            }

            // We have a multiple value array that is inside a condition or
            // function. Check its spacing is correct.
            foreach ($commas as $comma) {
                if ($tokens[($comma + 1)]['code'] !== T_WHITESPACE) {
                    $content = $tokens[($comma + 1)]['content'];
                    $error   = 'Expected 1 space between comma and "%s"; 0 found';
                    $data    = [$content];
                    $fix     = $phpcsFile->addFixableError($error, $comma, 'NoSpaceAfterComma', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->addContent($comma, ' ');
                    }
                } else {
                    $spaceLength = $tokens[($comma + 1)]['length'];
                    if ($spaceLength !== 1) {
                        $content = $tokens[($comma + 2)]['content'];
                        $error   = 'Expected 1 space between comma and "%s"; %s found';
                        $data    = [
                            $content,
                            $spaceLength,
                        ];

                        $fix = $phpcsFile->addFixableError($error, $comma, 'SpaceAfterComma', $data);
                        if ($fix === true) {
                            $phpcsFile->fixer->replaceToken(($comma + 1), ' ');
                        }
                    }
                }//end if

                if ($tokens[($comma - 1)]['code'] === T_WHITESPACE) {
                    $content     = $tokens[($comma - 2)]['content'];
                    $spaceLength = $tokens[($comma - 1)]['length'];
                    $error       = 'Expected 0 spaces between "%s" and comma; %s found';
                    $data        = [
                        $content,
                        $spaceLength,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $comma, 'SpaceBeforeComma', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($comma - 1), '');
                    }
                }
            }//end foreach
        }//end if

    }//end processSingleLineArray()


    /**
     * Processes a multi-line array definition.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile  The current file being checked.
     * @param int                         $stackPtr   The position of the current token
     *                                                in the stack passed in $tokens.
     * @param int                         $arrayStart The token that starts the array definition.
     * @param int                         $arrayEnd   The token that ends the array definition.
     *
     * @return void
     */
    public function processMultiLineArray($phpcsFile, $stackPtr, $arrayStart, $arrayEnd)
    {
        $tokens       = $phpcsFile->getTokens();
        $keywordStart = $tokens[$stackPtr]['column'];

        // Check the closing bracket is on a new line.
        $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($arrayEnd - 1), $arrayStart, true);
        if ($tokens[$lastContent]['line'] === $tokens[$arrayEnd]['line']) {
            $error = 'Closing parenthesis of array declaration must be on a new line';
            $fix   = $phpcsFile->addFixableError($error, $arrayEnd, 'CloseBraceNewLine');
            if ($fix === true) {
                $phpcsFile->fixer->addNewlineBefore($arrayEnd);
            }
        } else if ($tokens[$arrayEnd]['column'] !== $keywordStart) {
            // Check the closing bracket is lined up under the "a" in array.
            $expected = ($keywordStart - 1);
            $found    = ($tokens[$arrayEnd]['column'] - 1);
            $error    = 'Closing parenthesis not aligned correctly; expected %s space(s) but found %s';
            $data     = [
                $expected,
                $found,
            ];

            $fix = $phpcsFile->addFixableError($error, $arrayEnd, 'CloseBraceNotAligned', $data);
            if ($fix === true) {
                if ($found === 0) {
                    $phpcsFile->fixer->addContent(($arrayEnd - 1), str_repeat(' ', $expected));
                } else {
                    $phpcsFile->fixer->replaceToken(($arrayEnd - 1), str_repeat(' ', $expected));
                }
            }
        }//end if

        $keyUsed    = false;
        $singleUsed = false;
        $indices    = [];
        $maxLength  = 0;

        if ($tokens[$stackPtr]['code'] === T_ARRAY) {
            $lastToken = $tokens[$stackPtr]['parenthesis_opener'];
        } else {
            $lastToken = $stackPtr;
        }

        // Find all the double arrows that reside in this scope.
        for ($nextToken = ($stackPtr + 1); $nextToken < $arrayEnd; $nextToken++) {
            // Skip bracketed statements, like function calls.
            if ($tokens[$nextToken]['code'] === T_OPEN_PARENTHESIS
                && (isset($tokens[$nextToken]['parenthesis_owner']) === false
                || $tokens[$nextToken]['parenthesis_owner'] !== $stackPtr)
            ) {
                $nextToken = $tokens[$nextToken]['parenthesis_closer'];
                continue;
            }

            if ($tokens[$nextToken]['code'] === T_ARRAY
                || $tokens[$nextToken]['code'] === T_OPEN_SHORT_ARRAY
                || $tokens[$nextToken]['code'] === T_CLOSURE
                || $tokens[$nextToken]['code'] === T_FN
                || $tokens[$nextToken]['code'] === T_MATCH
            ) {
                // Let subsequent calls of this test handle nested arrays.
                if ($tokens[$lastToken]['code'] !== T_DOUBLE_ARROW) {
                    $indices[] = ['value' => $nextToken];
                    $lastToken = $nextToken;
                }

                if ($tokens[$nextToken]['code'] === T_ARRAY) {
                    $nextToken = $tokens[$tokens[$nextToken]['parenthesis_opener']]['parenthesis_closer'];
                } else if ($tokens[$nextToken]['code'] === T_OPEN_SHORT_ARRAY) {
                    $nextToken = $tokens[$nextToken]['bracket_closer'];
                } else {
                    // T_CLOSURE.
                    $nextToken = $tokens[$nextToken]['scope_closer'];
                }

                $nextToken = $phpcsFile->findNext(T_WHITESPACE, ($nextToken + 1), null, true);
                if ($tokens[$nextToken]['code'] !== T_COMMA) {
                    $nextToken--;
                } else {
                    $lastToken = $nextToken;
                }

                continue;
            }//end if

            if ($tokens[$nextToken]['code'] !== T_DOUBLE_ARROW && $tokens[$nextToken]['code'] !== T_COMMA) {
                continue;
            }

            $currentEntry = [];

            if ($tokens[$nextToken]['code'] === T_COMMA) {
                $stackPtrCount = 0;
                if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
                    $stackPtrCount = count($tokens[$stackPtr]['nested_parenthesis']);
                }

                $commaCount = 0;
                if (isset($tokens[$nextToken]['nested_parenthesis']) === true) {
                    $commaCount = count($tokens[$nextToken]['nested_parenthesis']);
                    if ($tokens[$stackPtr]['code'] === T_ARRAY) {
                        // Remove parenthesis that are used to define the array.
                        $commaCount--;
                    }
                }

                if ($commaCount > $stackPtrCount) {
                    // This comma is inside more parenthesis than the ARRAY keyword,
                    // then there it is actually a comma used to separate arguments
                    // in a function call.
                    continue;
                }

                if ($keyUsed === true && $tokens[$lastToken]['code'] === T_COMMA) {
                    $nextToken = $phpcsFile->findNext(Tokens::$emptyTokens, ($lastToken + 1), null, true);
                    // Allow for PHP 7.4+ array unpacking within an array declaration.
                    if ($tokens[$nextToken]['code'] !== T_ELLIPSIS) {
                        $error = 'No key specified for array entry; first entry specifies key';
                        $phpcsFile->addError($error, $nextToken, 'NoKeySpecified');
                        return;
                    }
                }

                if ($keyUsed === false) {
                    if ($tokens[($nextToken - 1)]['code'] === T_WHITESPACE) {
                        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($nextToken - 1), null, true);
                        if (($tokens[$prev]['code'] !== T_END_HEREDOC
                            && $tokens[$prev]['code'] !== T_END_NOWDOC)
                            || $tokens[($nextToken - 1)]['line'] === $tokens[$nextToken]['line']
                        ) {
                            if ($tokens[($nextToken - 1)]['content'] === $phpcsFile->eolChar) {
                                $spaceLength = 'newline';
                            } else {
                                $spaceLength = $tokens[($nextToken - 1)]['length'];
                            }

                            $error = 'Expected 0 spaces before comma; %s found';
                            $data  = [$spaceLength];

                            // The error is only fixable if there is only whitespace between the tokens.
                            if ($prev === $phpcsFile->findPrevious(T_WHITESPACE, ($nextToken - 1), null, true)) {
                                $fix = $phpcsFile->addFixableError($error, $nextToken, 'SpaceBeforeComma', $data);
                                if ($fix === true) {
                                    $phpcsFile->fixer->replaceToken(($nextToken - 1), '');
                                }
                            } else {
                                $phpcsFile->addError($error, $nextToken, 'SpaceBeforeComma', $data);
                            }
                        }
                    }//end if

                    $valueContent = $phpcsFile->findNext(
                        Tokens::$emptyTokens,
                        ($lastToken + 1),
                        $nextToken,
                        true
                    );

                    $indices[]          = ['value' => $valueContent];
                    $usesArrayUnpacking = $phpcsFile->findPrevious(
                        Tokens::$emptyTokens,
                        ($nextToken - 2),
                        null,
                        true
                    );
                    if ($tokens[$usesArrayUnpacking]['code'] !== T_ELLIPSIS) {
                        // Don't decide if an array is key => value indexed or not when PHP 7.4+ array unpacking is used.
                        $singleUsed = true;
                    }
                }//end if

                $lastToken = $nextToken;
                continue;
            }//end if

            if ($tokens[$nextToken]['code'] === T_DOUBLE_ARROW) {
                if ($singleUsed === true) {
                    $error = 'Key specified for array entry; first entry has no key';
                    $phpcsFile->addError($error, $nextToken, 'KeySpecified');
                    return;
                }

                $currentEntry['arrow'] = $nextToken;
                $keyUsed = true;

                // Find the start of index that uses this double arrow.
                $indexEnd   = $phpcsFile->findPrevious(T_WHITESPACE, ($nextToken - 1), $arrayStart, true);
                $indexStart = $phpcsFile->findStartOfStatement($indexEnd);

                if ($indexStart === $indexEnd) {
                    $currentEntry['index']         = $indexEnd;
                    $currentEntry['index_content'] = $tokens[$indexEnd]['content'];
                    $currentEntry['index_length']  = $tokens[$indexEnd]['length'];
                } else {
                    $currentEntry['index']         = $indexStart;
                    $currentEntry['index_content'] = '';
                    $currentEntry['index_length']  = 0;
                    for ($i = $indexStart; $i <= $indexEnd; $i++) {
                        $currentEntry['index_content'] .= $tokens[$i]['content'];
                        $currentEntry['index_length']  += $tokens[$i]['length'];
                    }
                }

                if ($maxLength < $currentEntry['index_length']) {
                    $maxLength = $currentEntry['index_length'];
                }

                // Find the value of this index.
                $nextContent = $phpcsFile->findNext(
                    Tokens::$emptyTokens,
                    ($nextToken + 1),
                    $arrayEnd,
                    true
                );

                $currentEntry['value'] = $nextContent;
                $indices[] = $currentEntry;
                $lastToken = $nextToken;
            }//end if
        }//end for

        // Check for multi-line arrays that should be single-line.
        $singleValue = false;

        if (empty($indices) === true) {
            $singleValue = true;
        } else if (count($indices) === 1 && $tokens[$lastToken]['code'] === T_COMMA) {
            // There may be another array value without a comma.
            $exclude     = Tokens::$emptyTokens;
            $exclude[]   = T_COMMA;
            $nextContent = $phpcsFile->findNext($exclude, ($indices[0]['value'] + 1), $arrayEnd, true);
            if ($nextContent === false) {
                $singleValue = true;
            }
        }

        if ($singleValue === true) {
            // Before we complain, make sure the single value isn't a here/nowdoc.
            $next = $phpcsFile->findNext(Tokens::$heredocTokens, ($arrayStart + 1), ($arrayEnd - 1));
            if ($next === false) {
                // Array cannot be empty, so this is a multi-line array with
                // a single value. It should be defined on single line.
                $error     = 'Multi-line array contains a single value; use single-line array instead';
                $errorCode = 'MultiLineNotAllowed';

                $find    = Tokens::$phpcsCommentTokens;
                $find[]  = T_COMMENT;
                $comment = $phpcsFile->findNext($find, ($arrayStart + 1), $arrayEnd);
                if ($comment === false) {
                    $fix = $phpcsFile->addFixableError($error, $stackPtr, $errorCode);
                } else {
                    $fix = false;
                    $phpcsFile->addError($error, $stackPtr, $errorCode);
                }

                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($arrayStart + 1); $i < $arrayEnd; $i++) {
                        if ($tokens[$i]['code'] !== T_WHITESPACE) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    for ($i = ($arrayEnd - 1); $i > $arrayStart; $i--) {
                        if ($tokens[$i]['code'] !== T_WHITESPACE) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }

                return;
            }//end if
        }//end if

        /*
            This section checks for arrays that don't specify keys.

            Arrays such as:
               array(
                'aaa',
                'bbb',
                'd',
               );
        */

        if ($keyUsed === false && empty($indices) === false) {
            $count     = count($indices);
            $lastIndex = $indices[($count - 1)]['value'];

            $trailingContent = $phpcsFile->findPrevious(
                Tokens::$emptyTokens,
                ($arrayEnd - 1),
                $lastIndex,
                true
            );

            if ($tokens[$trailingContent]['code'] !== T_COMMA) {
                $phpcsFile->recordMetric($stackPtr, 'Array end comma', 'no');
                $error = 'Comma required after last value in array declaration';
                $fix   = $phpcsFile->addFixableError($error, $trailingContent, 'NoCommaAfterLast');
                if ($fix === true) {
                    $phpcsFile->fixer->addContent($trailingContent, ',');
                }
            } else {
                $phpcsFile->recordMetric($stackPtr, 'Array end comma', 'yes');
            }

            foreach ($indices as $valuePosition => $value) {
                if (empty($value['value']) === true) {
                    // Array was malformed and we couldn't figure out
                    // the array value correctly, so we have to ignore it.
                    // Other parts of this sniff will correct the error.
                    continue;
                }

                $valuePointer = $value['value'];

                $ignoreTokens  = [
                    T_WHITESPACE => T_WHITESPACE,
                    T_COMMA      => T_COMMA,
                ];
                $ignoreTokens += Tokens::$castTokens;

                if ($tokens[$valuePointer]['code'] === T_CLOSURE
                    || $tokens[$valuePointer]['code'] === T_FN
                ) {
                    $ignoreTokens += [T_STATIC => T_STATIC];
                }

                $previous = $phpcsFile->findPrevious($ignoreTokens, ($valuePointer - 1), ($arrayStart + 1), true);
                if ($previous === false) {
                    $previous = $stackPtr;
                }

                $previousIsWhitespace = $tokens[($valuePointer - 1)]['code'] === T_WHITESPACE;
                if ($tokens[$previous]['line'] === $tokens[$valuePointer]['line']) {
                    $error = 'Each value in a multi-line array must be on a new line';
                    if ($valuePosition === 0) {
                        $error = 'The first value in a multi-value array must be on a new line';
                    }

                    $fix = $phpcsFile->addFixableError($error, $valuePointer, 'ValueNoNewline');
                    if ($fix === true) {
                        if ($previousIsWhitespace === true) {
                            $phpcsFile->fixer->replaceToken(($valuePointer - 1), $phpcsFile->eolChar);
                        } else {
                            $phpcsFile->fixer->addNewlineBefore($valuePointer);
                        }
                    }
                } else if ($previousIsWhitespace === true) {
                    $expected = $keywordStart;

                    $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $valuePointer, true);
                    $found = ($tokens[$first]['column'] - 1);
                    if ($found !== $expected) {
                        $error = 'Array value not aligned correctly; expected %s spaces but found %s';
                        $data  = [
                            $expected,
                            $found,
                        ];

                        $fix = $phpcsFile->addFixableError($error, $first, 'ValueNotAligned', $data);
                        if ($fix === true) {
                            if ($found === 0) {
                                $phpcsFile->fixer->addContent(($first - 1), str_repeat(' ', $expected));
                            } else {
                                $phpcsFile->fixer->replaceToken(($first - 1), str_repeat(' ', $expected));
                            }
                        }
                    }
                }//end if
            }//end foreach
        }//end if

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

            or

            $a = [
                  'index'  => '2',
                 ];

            In this array, the double arrow is indented too far, but this
            will also cause an error in the value's alignment. If the arrow were
            to be moved back one space however, then both errors would be fixed.
        */

        $indicesStart = ($keywordStart + 1);
        foreach ($indices as $valuePosition => $index) {
            $valuePointer = $index['value'];
            if ($valuePointer === false) {
                // Syntax error or live coding.
                continue;
            }

            if (isset($index['index']) === false) {
                // Array value only.
                continue;
            }

            $indexPointer = $index['index'];
            $indexLine    = $tokens[$indexPointer]['line'];

            $previous = $phpcsFile->findPrevious([T_WHITESPACE, T_COMMA], ($indexPointer - 1), ($arrayStart + 1), true);
            if ($previous === false) {
                $previous = $stackPtr;
            }

            if ($tokens[$previous]['line'] === $indexLine) {
                $error = 'Each index in a multi-line array must be on a new line';
                if ($valuePosition === 0) {
                    $error = 'The first index in a multi-value array must be on a new line';
                }

                $fix = $phpcsFile->addFixableError($error, $indexPointer, 'IndexNoNewline');
                if ($fix === true) {
                    if ($tokens[($indexPointer - 1)]['code'] === T_WHITESPACE) {
                        $phpcsFile->fixer->replaceToken(($indexPointer - 1), $phpcsFile->eolChar);
                    } else {
                        $phpcsFile->fixer->addNewlineBefore($indexPointer);
                    }
                }

                continue;
            }

            if ($tokens[$indexPointer]['column'] !== $indicesStart && ($indexPointer - 1) !== $arrayStart) {
                $expected = ($indicesStart - 1);
                $found    = ($tokens[$indexPointer]['column'] - 1);
                $error    = 'Array key not aligned correctly; expected %s spaces but found %s';
                $data     = [
                    $expected,
                    $found,
                ];

                $fix = $phpcsFile->addFixableError($error, $indexPointer, 'KeyNotAligned', $data);
                if ($fix === true) {
                    if ($found === 0 || $tokens[($indexPointer - 1)]['code'] !== T_WHITESPACE) {
                        $phpcsFile->fixer->addContent(($indexPointer - 1), str_repeat(' ', $expected));
                    } else {
                        $phpcsFile->fixer->replaceToken(($indexPointer - 1), str_repeat(' ', $expected));
                    }
                }
            }

            $arrowStart = ($tokens[$indexPointer]['column'] + $maxLength + 1);
            if ($tokens[$index['arrow']]['column'] !== $arrowStart) {
                $expected = ($arrowStart - ($index['index_length'] + $tokens[$indexPointer]['column']));
                $found    = ($tokens[$index['arrow']]['column'] - ($index['index_length'] + $tokens[$indexPointer]['column']));
                $error    = 'Array double arrow not aligned correctly; expected %s space(s) but found %s';
                $data     = [
                    $expected,
                    $found,
                ];

                $fix = $phpcsFile->addFixableError($error, $index['arrow'], 'DoubleArrowNotAligned', $data);
                if ($fix === true) {
                    if ($found === 0) {
                        $phpcsFile->fixer->addContent(($index['arrow'] - 1), str_repeat(' ', $expected));
                    } else {
                        $phpcsFile->fixer->replaceToken(($index['arrow'] - 1), str_repeat(' ', $expected));
                    }
                }

                continue;
            }

            $valueStart = ($arrowStart + 3);
            if ($tokens[$valuePointer]['column'] !== $valueStart) {
                $expected = ($valueStart - ($tokens[$index['arrow']]['length'] + $tokens[$index['arrow']]['column']));
                $found    = ($tokens[$valuePointer]['column'] - ($tokens[$index['arrow']]['length'] + $tokens[$index['arrow']]['column']));
                if ($found < 0) {
                    $found = 'newline';
                }

                $error = 'Array value not aligned correctly; expected %s space(s) but found %s';
                $data  = [
                    $expected,
                    $found,
                ];

                $fix = $phpcsFile->addFixableError($error, $index['arrow'], 'ValueNotAligned', $data);
                if ($fix === true) {
                    if ($found === 'newline') {
                        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($valuePointer - 1), null, true);
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($prev + 1); $i < $valuePointer; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->replaceToken(($valuePointer - 1), str_repeat(' ', $expected));
                        $phpcsFile->fixer->endChangeset();
                    } else if ($found === 0) {
                        $phpcsFile->fixer->addContent(($valuePointer - 1), str_repeat(' ', $expected));
                    } else {
                        $phpcsFile->fixer->replaceToken(($valuePointer - 1), str_repeat(' ', $expected));
                    }
                }
            }//end if

            // Check each line ends in a comma.
            $valueStart = $valuePointer;
            $nextComma  = false;

            $end = $phpcsFile->findEndOfStatement($valueStart);
            if ($end === false) {
                $valueEnd = $valueStart;
            } else if ($tokens[$end]['code'] === T_COMMA) {
                $valueEnd  = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($end - 1), $valueStart, true);
                $nextComma = $end;
            } else {
                $valueEnd = $end;
                $next     = $phpcsFile->findNext(Tokens::$emptyTokens, ($end + 1), $arrayEnd, true);
                if ($next !== false && $tokens[$next]['code'] === T_COMMA) {
                    $nextComma = $next;
                }
            }

            $valueLine = $tokens[$valueEnd]['line'];
            if ($tokens[$valueEnd]['code'] === T_END_HEREDOC || $tokens[$valueEnd]['code'] === T_END_NOWDOC) {
                $valueLine++;
            }

            if ($nextComma === false || ($tokens[$nextComma]['line'] !== $valueLine)) {
                $error = 'Each line in an array declaration must end in a comma';
                $fix   = $phpcsFile->addFixableError($error, $valuePointer, 'NoComma');

                if ($fix === true) {
                    // Find the end of the line and put a comma there.
                    for ($i = ($valuePointer + 1); $i <= $arrayEnd; $i++) {
                        if ($tokens[$i]['line'] > $valueLine) {
                            break;
                        }
                    }

                    $phpcsFile->fixer->beginChangeset();
                    $phpcsFile->fixer->addContentBefore(($i - 1), ',');
                    if ($nextComma !== false) {
                        $phpcsFile->fixer->replaceToken($nextComma, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }
            }//end if

            // Check that there is no space before the comma.
            if ($nextComma !== false && $tokens[($nextComma - 1)]['code'] === T_WHITESPACE) {
                // Here/nowdoc closing tags must have the comma on the next line.
                $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($nextComma - 1), null, true);
                if ($tokens[$prev]['code'] !== T_END_HEREDOC && $tokens[$prev]['code'] !== T_END_NOWDOC) {
                    $content     = $tokens[($nextComma - 2)]['content'];
                    $spaceLength = $tokens[($nextComma - 1)]['length'];
                    $error       = 'Expected 0 spaces between "%s" and comma; %s found';
                    $data        = [
                        $content,
                        $spaceLength,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $nextComma, 'SpaceBeforeComma', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($nextComma - 1), '');
                    }
                }
            }
        }//end foreach

    }//end processMultiLineArray()


}//end class
