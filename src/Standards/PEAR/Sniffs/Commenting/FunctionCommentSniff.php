<?php
/**
 * Parses and verifies the doc comments for functions.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class FunctionCommentSniff implements Sniff
{


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
        $find   = Tokens::$methodPrefixes;
        $find[] = T_WHITESPACE;

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);
        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            // Inline comments might just be closing comments for
            // control structures or functions instead of function comments
            // using the wrong comment type. If there is other code on the line,
            // assume they relate to that code.
            $prev = $phpcsFile->findPrevious($find, ($commentEnd - 1), null, true);
            if ($prev !== false && $tokens[$prev]['line'] === $tokens[$commentEnd]['line']) {
                $commentEnd = $prev;
            }
        }

        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT
        ) {
            $function = $phpcsFile->getDeclarationName($stackPtr);
            $phpcsFile->addError(
                'Missing doc comment for function %s()',
                $stackPtr,
                'Missing',
                [$function]
            );
            $phpcsFile->recordMetric($stackPtr, 'Function has doc comment', 'no');
            return;
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Function has doc comment', 'yes');
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a function comment', $stackPtr, 'WrongStyle');
            return;
        }

        if ($tokens[$commentEnd]['line'] !== ($tokens[$stackPtr]['line'] - 1)) {
            $error = 'There must be no blank lines after the function comment';
            $phpcsFile->addError($error, $commentEnd, 'SpacingAfter');
        }

        $commentStart = $tokens[$commentEnd]['comment_opener'];
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === '@see') {
                // Make sure the tag isn't empty.
                $string = $phpcsFile->findNext(T_DOC_COMMENT_STRING, $tag, $commentEnd);
                if ($string === false || $tokens[$string]['line'] !== $tokens[$tag]['line']) {
                    $error = 'Content missing for @see tag in function comment';
                    $phpcsFile->addError($error, $tag, 'EmptySees');
                }
            }
        }

        $this->processReturn($phpcsFile, $stackPtr, $commentStart);
        $this->processThrows($phpcsFile, $stackPtr, $commentStart);
        $this->processParams($phpcsFile, $stackPtr, $commentStart);

    }//end process()


    /**
     * Process the return comment of this function comment.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile    The file being scanned.
     * @param int                         $stackPtr     The position of the current token
     *                                                  in the stack passed in $tokens.
     * @param int                         $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processReturn(File $phpcsFile, $stackPtr, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        // Skip constructor and destructor.
        $methodName      = $phpcsFile->getDeclarationName($stackPtr);
        $isSpecialMethod = ($methodName === '__construct' || $methodName === '__destruct');

        $return = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === '@return') {
                if ($return !== null) {
                    $error = 'Only 1 @return tag is allowed in a function comment';
                    $phpcsFile->addError($error, $tag, 'DuplicateReturn');
                    return;
                }

                $return = $tag;
            }
        }

        if ($isSpecialMethod === true) {
            return;
        }

        if ($return !== null) {
            $content = $tokens[($return + 2)]['content'];
            if (empty($content) === true || $tokens[($return + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                $error = 'Return type missing for @return tag in function comment';
                $phpcsFile->addError($error, $return, 'MissingReturnType');
            }
        } else {
            $error = 'Missing @return tag in function comment';
            $phpcsFile->addError($error, $tokens[$commentStart]['comment_closer'], 'MissingReturn');
        }//end if

    }//end processReturn()


    /**
     * Process any throw tags that this function comment has.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile    The file being scanned.
     * @param int                         $stackPtr     The position of the current token
     *                                                  in the stack passed in $tokens.
     * @param int                         $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processThrows(File $phpcsFile, $stackPtr, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] !== '@throws') {
                continue;
            }

            $exception = null;
            if ($tokens[($tag + 2)]['code'] === T_DOC_COMMENT_STRING) {
                $matches = [];
                preg_match('/([^\s]+)(?:\s+(.*))?/', $tokens[($tag + 2)]['content'], $matches);
                $exception = $matches[1];
            }

            if ($exception === null) {
                $error = 'Exception type missing for @throws tag in function comment';
                $phpcsFile->addError($error, $tag, 'InvalidThrows');
            }
        }//end foreach

    }//end processThrows()


    /**
     * Process the function parameter comments.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile    The file being scanned.
     * @param int                         $stackPtr     The position of the current token
     *                                                  in the stack passed in $tokens.
     * @param int                         $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processParams(File $phpcsFile, $stackPtr, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        $params  = [];
        $maxType = 0;
        $maxVar  = 0;
        foreach ($tokens[$commentStart]['comment_tags'] as $pos => $tag) {
            if ($tokens[$tag]['content'] !== '@param') {
                continue;
            }

            $type          = '';
            $typeSpace     = 0;
            $var           = '';
            $varSpace      = 0;
            $comment       = '';
            $commentEnd    = 0;
            $commentTokens = [];

            if ($tokens[($tag + 2)]['code'] === T_DOC_COMMENT_STRING) {
                $matches = [];
                preg_match('/((?:(?![$.]|&(?=\$)).)*)(?:((?:\.\.\.)?(?:\$|&)[^\s]+)(?:(\s+)(.*))?)?/', $tokens[($tag + 2)]['content'], $matches);

                if (empty($matches) === false) {
                    $typeLen   = strlen($matches[1]);
                    $type      = trim($matches[1]);
                    $typeSpace = ($typeLen - strlen($type));
                    $typeLen   = strlen($type);
                    if ($typeLen > $maxType) {
                        $maxType = $typeLen;
                    }
                }

                if (isset($matches[2]) === true) {
                    $var    = $matches[2];
                    $varLen = strlen($var);
                    if ($varLen > $maxVar) {
                        $maxVar = $varLen;
                    }

                    if (isset($matches[4]) === true) {
                        $varSpace = strlen($matches[3]);
                        $comment  = $matches[4];

                        // Any strings until the next tag belong to this comment.
                        if (isset($tokens[$commentStart]['comment_tags'][($pos + 1)]) === true) {
                            $end = $tokens[$commentStart]['comment_tags'][($pos + 1)];
                        } else {
                            $end = $tokens[$commentStart]['comment_closer'];
                        }

                        for ($i = ($tag + 3); $i < $end; $i++) {
                            if ($tokens[$i]['code'] === T_DOC_COMMENT_STRING) {
                                $comment        .= ' '.$tokens[$i]['content'];
                                $commentEnd      = $i;
                                $commentTokens[] = $i;
                            }
                        }
                    } else {
                        $error = 'Missing parameter comment';
                        $phpcsFile->addError($error, $tag, 'MissingParamComment');
                    }//end if
                } else {
                    $error = 'Missing parameter name';
                    $phpcsFile->addError($error, $tag, 'MissingParamName');
                }//end if
            } else {
                $error = 'Missing parameter type';
                $phpcsFile->addError($error, $tag, 'MissingParamType');
            }//end if

            $params[] = [
                'tag'            => $tag,
                'type'           => $type,
                'var'            => $var,
                'comment'        => $comment,
                'comment_end'    => $commentEnd,
                'comment_tokens' => $commentTokens,
                'type_space'     => $typeSpace,
                'var_space'      => $varSpace,
            ];
        }//end foreach

        $realParams  = $phpcsFile->getMethodParameters($stackPtr);
        $foundParams = [];

        // We want to use ... for all variable length arguments, so add
        // this prefix to the variable name so comparisons are easier.
        foreach ($realParams as $pos => $param) {
            if ($param['variable_length'] === true) {
                $realParams[$pos]['name'] = '...'.$realParams[$pos]['name'];
            }
        }

        foreach ($params as $pos => $param) {
            if ($param['var'] === '') {
                continue;
            }

            $foundParams[] = $param['var'];

            if (trim($param['type']) !== '') {
                // Check number of spaces after the type.
                $spaces = ($maxType - strlen($param['type']) + 1);
                if ($param['type_space'] !== $spaces) {
                    $error = 'Expected %s spaces after parameter type; %s found';
                    $data  = [
                        $spaces,
                        $param['type_space'],
                    ];

                    $fix = $phpcsFile->addFixableError($error, $param['tag'], 'SpacingAfterParamType', $data);
                    if ($fix === true) {
                        $commentToken = ($param['tag'] + 2);

                        $content  = $param['type'];
                        $content .= str_repeat(' ', $spaces);
                        $content .= $param['var'];
                        $content .= str_repeat(' ', $param['var_space']);

                        $wrapLength = ($tokens[$commentToken]['length'] - $param['type_space'] - $param['var_space'] - strlen($param['type']) - strlen($param['var']));

                        $star        = $phpcsFile->findPrevious(T_DOC_COMMENT_STAR, $param['tag']);
                        $spaceLength = (strlen($content) + $tokens[($commentToken - 1)]['length'] + $tokens[($commentToken - 2)]['length']);

                        $padding  = str_repeat(' ', ($tokens[$star]['column'] - 1));
                        $padding .= '* ';
                        $padding .= str_repeat(' ', $spaceLength);

                        $content .= wordwrap(
                            $param['comment'],
                            $wrapLength,
                            $phpcsFile->eolChar.$padding
                        );

                        $phpcsFile->fixer->replaceToken($commentToken, $content);
                        for ($i = ($commentToken + 1); $i <= $param['comment_end']; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }
                    }//end if
                }//end if
            }//end if

            // Make sure the param name is correct.
            if (isset($realParams[$pos]) === true) {
                $realName = $realParams[$pos]['name'];
                if ($realName !== $param['var']) {
                    $code = 'ParamNameNoMatch';
                    $data = [
                        $param['var'],
                        $realName,
                    ];

                    $error = 'Doc comment for parameter %s does not match ';
                    if (strtolower($param['var']) === strtolower($realName)) {
                        $error .= 'case of ';
                        $code   = 'ParamNameNoCaseMatch';
                    }

                    $error .= 'actual variable name %s';

                    $phpcsFile->addError($error, $param['tag'], $code, $data);
                }
            } else if (substr($param['var'], -4) !== ',...') {
                // We must have an extra parameter comment.
                $error = 'Superfluous parameter comment';
                $phpcsFile->addError($error, $param['tag'], 'ExtraParamComment');
            }//end if

            if ($param['comment'] === '') {
                continue;
            }

            // Check number of spaces after the param name.
            $spaces = ($maxVar - strlen($param['var']) + 1);
            if ($param['var_space'] !== $spaces) {
                $error = 'Expected %s spaces after parameter name; %s found';
                $data  = [
                    $spaces,
                    $param['var_space'],
                ];

                $fix = $phpcsFile->addFixableError($error, $param['tag'], 'SpacingAfterParamName', $data);
                if ($fix === true) {
                    $commentToken = ($param['tag'] + 2);

                    $content  = $param['type'];
                    $content .= str_repeat(' ', $param['type_space']);
                    $content .= $param['var'];
                    $content .= str_repeat(' ', $spaces);

                    $wrapLength = ($tokens[$commentToken]['length'] - $param['type_space'] - $param['var_space'] - strlen($param['type']) - strlen($param['var']));

                    $star        = $phpcsFile->findPrevious(T_DOC_COMMENT_STAR, $param['tag']);
                    $spaceLength = (strlen($content) + $tokens[($commentToken - 1)]['length'] + $tokens[($commentToken - 2)]['length']);

                    $padding  = str_repeat(' ', ($tokens[$star]['column'] - 1));
                    $padding .= '* ';
                    $padding .= str_repeat(' ', $spaceLength);

                    $content .= wordwrap(
                        $param['comment'],
                        $wrapLength,
                        $phpcsFile->eolChar.$padding
                    );

                    $phpcsFile->fixer->replaceToken($commentToken, $content);
                    for ($i = ($commentToken + 1); $i <= $param['comment_end']; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                }//end if
            }//end if

            // Check the alignment of multi-line param comments.
            if ($param['tag'] !== $param['comment_end']) {
                $wrapLength = ($tokens[($param['tag'] + 2)]['length'] - $param['type_space'] - $param['var_space'] - strlen($param['type']) - strlen($param['var']));

                $startColumn = ($tokens[($param['tag'] + 2)]['column'] + $tokens[($param['tag'] + 2)]['length'] - $wrapLength);

                $star     = $phpcsFile->findPrevious(T_DOC_COMMENT_STAR, $param['tag']);
                $expected = ($startColumn - $tokens[$star]['column'] - 1);

                foreach ($param['comment_tokens'] as $commentToken) {
                    if ($tokens[$commentToken]['column'] === $startColumn) {
                        continue;
                    }

                    $found = 0;
                    if ($tokens[($commentToken - 1)]['code'] === T_DOC_COMMENT_WHITESPACE) {
                        $found = $tokens[($commentToken - 1)]['length'];
                    }

                    $error = 'Parameter comment not aligned correctly; expected %s spaces but found %s';
                    $data  = [
                        $expected,
                        $found,
                    ];

                    if ($found < $expected) {
                        $code = 'ParamCommentAlignment';
                    } else {
                        $code = 'ParamCommentAlignmentExceeded';
                    }

                    $fix = $phpcsFile->addFixableError($error, $commentToken, $code, $data);
                    if ($fix === true) {
                        $padding = str_repeat(' ', $expected);
                        if ($tokens[($commentToken - 1)]['code'] === T_DOC_COMMENT_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken(($commentToken - 1), $padding);
                        } else {
                            $phpcsFile->fixer->addContentBefore($commentToken, $padding);
                        }
                    }
                }//end foreach
            }//end if
        }//end foreach

        $realNames = [];
        foreach ($realParams as $realParam) {
            $realNames[] = $realParam['name'];
        }

        // Report missing comments.
        $diff = array_diff($realNames, $foundParams);
        foreach ($diff as $neededParam) {
            $error = 'Doc comment for parameter "%s" missing';
            $data  = [$neededParam];
            $phpcsFile->addError($error, $commentStart, 'MissingParamTag', $data);
        }

    }//end processParams()


}//end class
