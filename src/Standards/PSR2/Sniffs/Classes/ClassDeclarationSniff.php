<?php
/**
 * Checks the declaration of the class and its inheritance is correct.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\Classes\ClassDeclarationSniff as PEARClassDeclarationSniff;
use PHP_CodeSniffer\Util\Tokens;

class ClassDeclarationSniff extends PEARClassDeclarationSniff
{

    /**
     * The number of spaces code should be indented.
     *
     * @var integer
     */
    public $indent = 4;


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
        // We want all the errors from the PEAR standard, plus some of our own.
        parent::process($phpcsFile, $stackPtr);

        // Just in case.
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return;
        }

        $this->processOpen($phpcsFile, $stackPtr);
        $this->processClose($phpcsFile, $stackPtr);

    }//end process()


    /**
     * Processes the opening section of a class declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function processOpen(File $phpcsFile, $stackPtr)
    {
        $tokens       = $phpcsFile->getTokens();
        $stackPtrType = strtolower($tokens[$stackPtr]['content']);

        // Check alignment of the keyword and braces.
        if ($tokens[($stackPtr - 1)]['code'] === T_WHITESPACE) {
            $prevContent = $tokens[($stackPtr - 1)]['content'];
            if ($prevContent !== $phpcsFile->eolChar) {
                $blankSpace = substr($prevContent, strpos($prevContent, $phpcsFile->eolChar));
                $spaces     = strlen($blankSpace);

                if (in_array($tokens[($stackPtr - 2)]['code'], [T_ABSTRACT, T_FINAL, T_READONLY], true) === true
                    && $spaces !== 1
                ) {
                    $prevContent = strtolower($tokens[($stackPtr - 2)]['content']);
                    $error       = 'Expected 1 space between %s and %s keywords; %s found';
                    $data        = [
                        $prevContent,
                        $stackPtrType,
                        $spaces,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeKeyword', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                    }
                }
            } else if ($tokens[($stackPtr - 2)]['code'] === T_ABSTRACT
                || $tokens[($stackPtr - 2)]['code'] === T_FINAL
                || $tokens[($stackPtr - 2)]['code'] === T_READONLY
            ) {
                $prevContent = strtolower($tokens[($stackPtr - 2)]['content']);
                $error       = 'Expected 1 space between %s and %s keywords; newline found';
                $data        = [
                    $prevContent,
                    $stackPtrType,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'NewlineBeforeKeyword', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                }
            }//end if
        }//end if

        // We'll need the indent of the class/interface declaration for later.
        $classIndent = 0;
        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if ($tokens[$i]['line'] === $tokens[$stackPtr]['line']) {
                continue;
            }

            // We changed lines.
            if ($tokens[($i + 1)]['code'] === T_WHITESPACE) {
                $classIndent = $tokens[($i + 1)]['length'];
            }

            break;
        }

        $className    = null;
        $checkSpacing = true;

        if ($tokens[$stackPtr]['code'] !== T_ANON_CLASS) {
            $className = $phpcsFile->findNext(T_STRING, $stackPtr);
        } else {
            // Ignore the spacing check if this is a simple anon class.
            $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
            if ($next === $tokens[$stackPtr]['scope_opener']
                && $tokens[$next]['line'] > $tokens[$stackPtr]['line']
            ) {
                $checkSpacing = false;
            }
        }

        if ($checkSpacing === true) {
            // Spacing of the keyword.
            if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
                $gap = 0;
            } else if ($tokens[($stackPtr + 2)]['line'] !== $tokens[$stackPtr]['line']) {
                $gap = 'newline';
            } else {
                $gap = $tokens[($stackPtr + 1)]['length'];
            }

            if ($gap !== 1) {
                $error = 'Expected 1 space after %s keyword; %s found';
                $data  = [
                    $stackPtrType,
                    $gap,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterKeyword', $data);
                if ($fix === true) {
                    if ($gap === 0) {
                        $phpcsFile->fixer->addContent($stackPtr, ' ');
                    } else {
                        $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                    }
                }
            }
        }//end if

        // Check after the class/interface name.
        if ($className !== null
            && $tokens[($className + 2)]['line'] === $tokens[$className]['line']
        ) {
            $gap = $tokens[($className + 1)]['content'];
            if (strlen($gap) !== 1) {
                $found = strlen($gap);
                $error = 'Expected 1 space after %s name; %s found';
                $data  = [
                    $stackPtrType,
                    $found,
                ];

                $fix = $phpcsFile->addFixableError($error, $className, 'SpaceAfterName', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($className + 1), ' ');
                }
            }
        }

        $openingBrace = $tokens[$stackPtr]['scope_opener'];

        // Check positions of the extends and implements keywords.
        $compareToken = $stackPtr;
        $compareType  = 'name';
        if ($tokens[$stackPtr]['code'] === T_ANON_CLASS) {
            if (isset($tokens[$stackPtr]['parenthesis_opener']) === true) {
                $compareToken = $tokens[$stackPtr]['parenthesis_closer'];
                $compareType  = 'closing parenthesis';
            } else {
                $compareType = 'keyword';
            }
        }

        foreach (['extends', 'implements'] as $keywordType) {
            $keyword = $phpcsFile->findNext(constant('T_'.strtoupper($keywordType)), ($compareToken + 1), $openingBrace);
            if ($keyword !== false) {
                if ($tokens[$keyword]['line'] !== $tokens[$compareToken]['line']) {
                    $error = 'The '.$keywordType.' keyword must be on the same line as the %s '.$compareType;
                    $data  = [$stackPtrType];
                    $fix   = $phpcsFile->addFixableError($error, $keyword, ucfirst($keywordType).'Line', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        $comments = [];

                        for ($i = ($compareToken + 1); $i < $keyword; ++$i) {
                            if ($tokens[$i]['code'] === T_COMMENT) {
                                $comments[] = trim($tokens[$i]['content']);
                            }

                            if ($tokens[$i]['code'] === T_WHITESPACE
                                || $tokens[$i]['code'] === T_COMMENT
                            ) {
                                $phpcsFile->fixer->replaceToken($i, ' ');
                            }
                        }

                        $phpcsFile->fixer->addContent($compareToken, ' ');
                        if (empty($comments) === false) {
                            $i = $keyword;
                            while ($tokens[($i + 1)]['line'] === $tokens[$keyword]['line']) {
                                ++$i;
                            }

                            $phpcsFile->fixer->addContentBefore($i, ' '.implode(' ', $comments));
                        }

                        $phpcsFile->fixer->endChangeset();
                    }//end if
                } else {
                    // Check the whitespace before. Whitespace after is checked
                    // later by looking at the whitespace before the first class name
                    // in the list.
                    $gap = $tokens[($keyword - 1)]['length'];
                    if ($gap !== 1) {
                        $error = 'Expected 1 space before '.$keywordType.' keyword; %s found';
                        $data  = [$gap];
                        $fix   = $phpcsFile->addFixableError($error, $keyword, 'SpaceBefore'.ucfirst($keywordType), $data);
                        if ($fix === true) {
                            $phpcsFile->fixer->replaceToken(($keyword - 1), ' ');
                        }
                    }
                }//end if
            }//end if
        }//end foreach

        // Check each of the extends/implements class names. If the extends/implements
        // keyword is the last content on the line, it means we need to check for
        // the multi-line format, so we do not include the class names
        // from the extends/implements list in the following check.
        // Note that classes can only extend one other class, so they can't use a
        // multi-line extends format, whereas an interface can extend multiple
        // other interfaces, and so uses a multi-line extends format.
        if ($tokens[$stackPtr]['code'] === T_INTERFACE) {
            $keywordTokenType = T_EXTENDS;
        } else {
            $keywordTokenType = T_IMPLEMENTS;
        }

        $implements          = $phpcsFile->findNext($keywordTokenType, ($stackPtr + 1), $openingBrace);
        $multiLineImplements = false;
        if ($implements !== false) {
            $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($openingBrace - 1), $implements, true);
            if ($tokens[$prev]['line'] !== $tokens[$implements]['line']) {
                $multiLineImplements = true;
            }
        }

        $find = [
            T_STRING,
            $keywordTokenType,
        ];

        if ($className !== null) {
            $start = $className;
        } else if (isset($tokens[$stackPtr]['parenthesis_closer']) === true) {
            $start = $tokens[$stackPtr]['parenthesis_closer'];
        } else {
            $start = $stackPtr;
        }

        $classNames = [];
        $nextClass  = $phpcsFile->findNext($find, ($start + 2), ($openingBrace - 1));
        while ($nextClass !== false) {
            $classNames[] = $nextClass;
            $nextClass    = $phpcsFile->findNext($find, ($nextClass + 1), ($openingBrace - 1));
        }

        $classCount         = count($classNames);
        $checkingImplements = false;
        $implementsToken    = null;
        foreach ($classNames as $n => $className) {
            if ($tokens[$className]['code'] === $keywordTokenType) {
                $checkingImplements = true;
                $implementsToken    = $className;

                continue;
            }

            if ($checkingImplements === true
                && $multiLineImplements === true
                && ($tokens[($className - 1)]['code'] !== T_NS_SEPARATOR
                || $tokens[($className - 2)]['code'] !== T_STRING)
            ) {
                $prev = $phpcsFile->findPrevious(
                    [
                        T_NS_SEPARATOR,
                        T_WHITESPACE,
                    ],
                    ($className - 1),
                    $implements,
                    true
                );

                if ($prev === $implementsToken && $tokens[$className]['line'] !== ($tokens[$prev]['line'] + 1)) {
                    if ($keywordTokenType === T_EXTENDS) {
                        $error = 'The first item in a multi-line extends list must be on the line following the extends keyword';
                        $fix   = $phpcsFile->addFixableError($error, $className, 'FirstExtendsInterfaceSameLine');
                    } else {
                        $error = 'The first item in a multi-line implements list must be on the line following the implements keyword';
                        $fix   = $phpcsFile->addFixableError($error, $className, 'FirstInterfaceSameLine');
                    }

                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($prev + 1); $i < $className; $i++) {
                            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->addNewline($prev);
                        $phpcsFile->fixer->endChangeset();
                    }
                } else if ($tokens[$prev]['line'] !== ($tokens[$className]['line'] - 1)) {
                    if ($keywordTokenType === T_EXTENDS) {
                        $error = 'Only one interface may be specified per line in a multi-line extends declaration';
                        $fix   = $phpcsFile->addFixableError($error, $className, 'ExtendsInterfaceSameLine');
                    } else {
                        $error = 'Only one interface may be specified per line in a multi-line implements declaration';
                        $fix   = $phpcsFile->addFixableError($error, $className, 'InterfaceSameLine');
                    }

                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($i = ($prev + 1); $i < $className; $i++) {
                            if ($tokens[$i]['code'] !== T_WHITESPACE) {
                                break;
                            }

                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        $phpcsFile->fixer->addNewline($prev);
                        $phpcsFile->fixer->endChangeset();
                    }
                } else {
                    $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($className - 1), $implements);
                    if ($tokens[$prev]['line'] !== $tokens[$className]['line']) {
                        $found = 0;
                    } else {
                        $found = $tokens[$prev]['length'];
                    }

                    $expected = ($classIndent + $this->indent);
                    if ($found !== $expected) {
                        $error = 'Expected %s spaces before interface name; %s found';
                        $data  = [
                            $expected,
                            $found,
                        ];
                        $fix   = $phpcsFile->addFixableError($error, $className, 'InterfaceWrongIndent', $data);
                        if ($fix === true) {
                            $padding = str_repeat(' ', $expected);
                            if ($found === 0) {
                                $phpcsFile->fixer->addContent($prev, $padding);
                            } else {
                                $phpcsFile->fixer->replaceToken($prev, $padding);
                            }
                        }
                    }
                }//end if
            } else if ($tokens[($className - 1)]['code'] !== T_NS_SEPARATOR
                || $tokens[($className - 2)]['code'] !== T_STRING
            ) {
                // Not part of a longer fully qualified class name.
                if ($tokens[($className - 1)]['code'] === T_COMMA
                    || ($tokens[($className - 1)]['code'] === T_NS_SEPARATOR
                    && $tokens[($className - 2)]['code'] === T_COMMA)
                ) {
                    $error = 'Expected 1 space before "%s"; 0 found';
                    $data  = [$tokens[$className]['content']];
                    $fix   = $phpcsFile->addFixableError($error, ($nextComma + 1), 'NoSpaceBeforeName', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->addContentBefore(($nextComma + 1), ' ');
                    }
                } else {
                    if ($tokens[($className - 1)]['code'] === T_NS_SEPARATOR) {
                        $prev = ($className - 2);
                    } else {
                        $prev = ($className - 1);
                    }

                    $last    = $phpcsFile->findPrevious(T_WHITESPACE, $prev, null, true);
                    $content = $phpcsFile->getTokensAsString(($last + 1), ($prev - $last));
                    if ($content !== ' ') {
                        $found = strlen($content);

                        $error = 'Expected 1 space before "%s"; %s found';
                        $data  = [
                            $tokens[$className]['content'],
                            $found,
                        ];

                        $fix = $phpcsFile->addFixableError($error, $className, 'SpaceBeforeName', $data);
                        if ($fix === true) {
                            if ($tokens[$prev]['code'] === T_WHITESPACE) {
                                $phpcsFile->fixer->beginChangeset();
                                $phpcsFile->fixer->replaceToken($prev, ' ');
                                while ($tokens[--$prev]['code'] === T_WHITESPACE) {
                                    $phpcsFile->fixer->replaceToken($prev, ' ');
                                }

                                $phpcsFile->fixer->endChangeset();
                            } else {
                                $phpcsFile->fixer->addContent($prev, ' ');
                            }
                        }
                    }//end if
                }//end if
            }//end if

            if ($checkingImplements === true
                && $tokens[($className + 1)]['code'] !== T_NS_SEPARATOR
                && $tokens[($className + 1)]['code'] !== T_COMMA
            ) {
                if ($n !== ($classCount - 1)) {
                    // This is not the last class name, and the comma
                    // is not where we expect it to be.
                    if ($tokens[($className + 2)]['code'] !== $keywordTokenType) {
                        $error = 'Expected 0 spaces between "%s" and comma; %s found';
                        $data  = [
                            $tokens[$className]['content'],
                            $tokens[($className + 1)]['length'],
                        ];

                        $fix = $phpcsFile->addFixableError($error, $className, 'SpaceBeforeComma', $data);
                        if ($fix === true) {
                            $phpcsFile->fixer->replaceToken(($className + 1), '');
                        }
                    }
                }

                $nextComma = $phpcsFile->findNext(T_COMMA, $className);
            } else {
                $nextComma = ($className + 1);
            }//end if
        }//end foreach

    }//end processOpen()


    /**
     * Processes the closing section of a class declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function processClose(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check that the closing brace comes right after the code body.
        $closeBrace  = $tokens[$stackPtr]['scope_closer'];
        $prevContent = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBrace - 1), null, true);
        if ($prevContent !== $tokens[$stackPtr]['scope_opener']
            && $tokens[$prevContent]['line'] !== ($tokens[$closeBrace]['line'] - 1)
        ) {
            $error = 'The closing brace for the %s must go on the next line after the body';
            $data  = [$tokens[$stackPtr]['content']];
            $fix   = $phpcsFile->addFixableError($error, $closeBrace, 'CloseBraceAfterBody', $data);

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($prevContent + 1); $i < $closeBrace; $i++) {
                    $phpcsFile->fixer->replaceToken($i, '');
                }

                if (strpos($tokens[$prevContent]['content'], $phpcsFile->eolChar) === false) {
                    $phpcsFile->fixer->replaceToken($closeBrace, $phpcsFile->eolChar.$tokens[$closeBrace]['content']);
                }

                $phpcsFile->fixer->endChangeset();
            }
        }//end if

        if ($tokens[$stackPtr]['code'] !== T_ANON_CLASS) {
            // Check the closing brace is on it's own line, but allow
            // for comments like "//end class".
            $ignoreTokens   = Tokens::$phpcsCommentTokens;
            $ignoreTokens[] = T_WHITESPACE;
            $ignoreTokens[] = T_COMMENT;
            $ignoreTokens[] = T_SEMICOLON;
            $ignoreTokens[] = T_COMMA;
            $nextContent    = $phpcsFile->findNext($ignoreTokens, ($closeBrace + 1), null, true);
            if ($tokens[$nextContent]['content'] !== $phpcsFile->eolChar
                && $tokens[$nextContent]['line'] === $tokens[$closeBrace]['line']
            ) {
                $type  = strtolower($tokens[$stackPtr]['content']);
                $error = 'Closing %s brace must be on a line by itself';
                $data  = [$type];
                $phpcsFile->addError($error, $closeBrace, 'CloseBraceSameLine', $data);
            }
        }

    }//end processClose()


}//end class
