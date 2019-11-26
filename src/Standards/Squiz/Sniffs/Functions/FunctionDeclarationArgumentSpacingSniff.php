<?php
/**
 * Checks that arguments in function declarations are spaced correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class FunctionDeclarationArgumentSpacingSniff implements Sniff
{

    /**
     * How many spaces should surround the equals signs.
     *
     * @var integer
     */
    public $equalsSpacing = 0;

    /**
     * How many spaces should follow the opening bracket.
     *
     * @var integer
     */
    public $requiredSpacesAfterOpen = 0;

    /**
     * How many spaces should precede the closing bracket.
     *
     * @var integer
     */
    public $requiredSpacesBeforeClose = 0;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_FUNCTION,
            T_CLOSURE,
            T_FN,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['parenthesis_opener']) === false
            || isset($tokens[$stackPtr]['parenthesis_closer']) === false
            || $tokens[$stackPtr]['parenthesis_opener'] === null
            || $tokens[$stackPtr]['parenthesis_closer'] === null
        ) {
            return;
        }

        $this->equalsSpacing           = (int) $this->equalsSpacing;
        $this->requiredSpacesAfterOpen = (int) $this->requiredSpacesAfterOpen;
        $this->requiredSpacesBeforeClose = (int) $this->requiredSpacesBeforeClose;

        $this->processBracket($phpcsFile, $tokens[$stackPtr]['parenthesis_opener']);

        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $use = $phpcsFile->findNext(T_USE, ($tokens[$stackPtr]['parenthesis_closer'] + 1), $tokens[$stackPtr]['scope_opener']);
            if ($use !== false) {
                $openBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1), null);
                $this->processBracket($phpcsFile, $openBracket);
            }
        }

    }//end process()


    /**
     * Processes the contents of a single set of brackets.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file being scanned.
     * @param int                         $openBracket The position of the open bracker
     *                                                 in the stack.
     *
     * @return void
     */
    public function processBracket($phpcsFile, $openBracket)
    {
        $tokens       = $phpcsFile->getTokens();
        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        $multiLine    = ($tokens[$openBracket]['line'] !== $tokens[$closeBracket]['line']);

        if (isset($tokens[$openBracket]['parenthesis_owner']) === true) {
            $stackPtr = $tokens[$openBracket]['parenthesis_owner'];
        } else {
            $stackPtr = $phpcsFile->findPrevious(T_USE, ($openBracket - 1));
        }

        $params = $phpcsFile->getMethodParameters($stackPtr);

        if (empty($params) === true) {
            // Check spacing around parenthesis.
            $next = $phpcsFile->findNext(T_WHITESPACE, ($openBracket + 1), $closeBracket, true);
            if ($next === false) {
                if (($closeBracket - $openBracket) !== 1) {
                    $error = 'Expected 0 spaces between parenthesis of function declaration; %s found';
                    $data  = [$tokens[($openBracket + 1)]['length']];
                    $fix   = $phpcsFile->addFixableError($error, $openBracket, 'SpacingBetween', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($openBracket + 1), '');
                    }
                }

                // No params, so we don't check normal spacing rules.
                return;
            }
        }

        foreach ($params as $paramNumber => $param) {
            if ($param['pass_by_reference'] === true) {
                $refToken = $param['reference_token'];

                $gap = 0;
                if ($tokens[($refToken + 1)]['code'] === T_WHITESPACE) {
                    $gap = $tokens[($refToken + 1)]['length'];
                }

                if ($gap !== 0) {
                    $error = 'Expected 0 spaces after reference operator for argument "%s"; %s found';
                    $data  = [
                        $param['name'],
                        $gap,
                    ];
                    $fix   = $phpcsFile->addFixableError($error, $refToken, 'SpacingAfterReference', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($refToken + 1), '');
                    }
                }
            }//end if

            if ($param['variable_length'] === true) {
                $variadicToken = $param['variadic_token'];

                $gap = 0;
                if ($tokens[($variadicToken + 1)]['code'] === T_WHITESPACE) {
                    $gap = $tokens[($variadicToken + 1)]['length'];
                }

                if ($gap !== 0) {
                    $error = 'Expected 0 spaces after variadic operator for argument "%s"; %s found';
                    $data  = [
                        $param['name'],
                        $gap,
                    ];
                    $fix   = $phpcsFile->addFixableError($error, $variadicToken, 'SpacingAfterVariadic', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($variadicToken + 1), '');
                    }
                }
            }//end if

            if (isset($param['default_equal_token']) === true) {
                $equalToken = $param['default_equal_token'];

                $spacesBefore = 0;
                if (($equalToken - $param['token']) > 1) {
                    $spacesBefore = $tokens[($param['token'] + 1)]['length'];
                }

                if ($spacesBefore !== $this->equalsSpacing) {
                    $error = 'Incorrect spacing between argument "%s" and equals sign; expected '.$this->equalsSpacing.' but found %s';
                    $data  = [
                        $param['name'],
                        $spacesBefore,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $equalToken, 'SpaceBeforeEquals', $data);
                    if ($fix === true) {
                        $padding = str_repeat(' ', $this->equalsSpacing);
                        if ($spacesBefore === 0) {
                            $phpcsFile->fixer->addContentBefore($equalToken, $padding);
                        } else {
                            $phpcsFile->fixer->replaceToken(($equalToken - 1), $padding);
                        }
                    }
                }//end if

                $spacesAfter = 0;
                if ($tokens[($equalToken + 1)]['code'] === T_WHITESPACE) {
                    $spacesAfter = $tokens[($equalToken + 1)]['length'];
                }

                if ($spacesAfter !== $this->equalsSpacing) {
                    $error = 'Incorrect spacing between default value and equals sign for argument "%s"; expected '.$this->equalsSpacing.' but found %s';
                    $data  = [
                        $param['name'],
                        $spacesAfter,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $equalToken, 'SpaceAfterEquals', $data);
                    if ($fix === true) {
                        $padding = str_repeat(' ', $this->equalsSpacing);
                        if ($spacesAfter === 0) {
                            $phpcsFile->fixer->addContent($equalToken, $padding);
                        } else {
                            $phpcsFile->fixer->replaceToken(($equalToken + 1), $padding);
                        }
                    }
                }//end if
            }//end if

            if ($param['type_hint_token'] !== false) {
                $typeHintToken = $param['type_hint_end_token'];

                $gap = 0;
                if ($tokens[($typeHintToken + 1)]['code'] === T_WHITESPACE) {
                    $gap = $tokens[($typeHintToken + 1)]['length'];
                }

                if ($gap !== 1) {
                    $error = 'Expected 1 space between type hint and argument "%s"; %s found';
                    $data  = [
                        $param['name'],
                        $gap,
                    ];
                    $fix   = $phpcsFile->addFixableError($error, $typeHintToken, 'SpacingAfterHint', $data);
                    if ($fix === true) {
                        if ($gap === 0) {
                            $phpcsFile->fixer->addContent($typeHintToken, ' ');
                        } else {
                            $phpcsFile->fixer->replaceToken(($typeHintToken + 1), ' ');
                        }
                    }
                }
            }//end if

            $commaToken = false;
            if ($paramNumber > 0 && $params[($paramNumber - 1)]['comma_token'] !== false) {
                $commaToken = $params[($paramNumber - 1)]['comma_token'];
            }

            if ($commaToken !== false) {
                if ($tokens[($commaToken - 1)]['code'] === T_WHITESPACE) {
                    $error = 'Expected 0 spaces between argument "%s" and comma; %s found';
                    $data  = [
                        $params[($paramNumber - 1)]['name'],
                        $tokens[($commaToken - 1)]['length'],
                    ];

                    $fix = $phpcsFile->addFixableError($error, $commaToken, 'SpaceBeforeComma', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->replaceToken(($commaToken - 1), '');
                    }
                }

                // Don't check spacing after the comma if it is the last content on the line.
                $checkComma = true;
                if ($multiLine === true) {
                    $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($commaToken + 1), $closeBracket, true);
                    if ($tokens[$next]['line'] !== $tokens[$commaToken]['line']) {
                        $checkComma = false;
                    }
                }

                if ($checkComma === true) {
                    if ($param['type_hint_token'] === false) {
                        $spacesAfter = 0;
                        if ($tokens[($commaToken + 1)]['code'] === T_WHITESPACE) {
                            $spacesAfter = $tokens[($commaToken + 1)]['length'];
                        }

                        if ($spacesAfter === 0) {
                            $error = 'Expected 1 space between comma and argument "%s"; 0 found';
                            $data  = [$param['name']];
                            $fix   = $phpcsFile->addFixableError($error, $commaToken, 'NoSpaceBeforeArg', $data);
                            if ($fix === true) {
                                $phpcsFile->fixer->addContent($commaToken, ' ');
                            }
                        } else if ($spacesAfter !== 1) {
                            $error = 'Expected 1 space between comma and argument "%s"; %s found';
                            $data  = [
                                $param['name'],
                                $spacesAfter,
                            ];

                            $fix = $phpcsFile->addFixableError($error, $commaToken, 'SpacingBeforeArg', $data);
                            if ($fix === true) {
                                $phpcsFile->fixer->replaceToken(($commaToken + 1), ' ');
                            }
                        }//end if
                    } else {
                        $hint = $phpcsFile->getTokensAsString($param['type_hint_token'], (($param['type_hint_end_token'] - $param['type_hint_token']) + 1));
                        if ($param['nullable_type'] === true) {
                            $hint = '?'.$hint;
                        }

                        if ($tokens[($commaToken + 1)]['code'] !== T_WHITESPACE) {
                            $error = 'Expected 1 space between comma and type hint "%s"; 0 found';
                            $data  = [$hint];
                            $fix   = $phpcsFile->addFixableError($error, $commaToken, 'NoSpaceBeforeHint', $data);
                            if ($fix === true) {
                                $phpcsFile->fixer->addContent($commaToken, ' ');
                            }
                        } else {
                            $gap = $tokens[($commaToken + 1)]['length'];
                            if ($gap !== 1) {
                                $error = 'Expected 1 space between comma and type hint "%s"; %s found';
                                $data  = [
                                    $hint,
                                    $gap,
                                ];
                                $fix   = $phpcsFile->addFixableError($error, $commaToken, 'SpacingBeforeHint', $data);
                                if ($fix === true) {
                                    $phpcsFile->fixer->replaceToken(($commaToken + 1), ' ');
                                }
                            }
                        }//end if
                    }//end if
                }//end if
            }//end if
        }//end foreach

        // Only check spacing around parenthesis for single line definitions.
        if ($multiLine === true) {
            return;
        }

        $gap = 0;
        if ($tokens[($closeBracket - 1)]['code'] === T_WHITESPACE) {
            $gap = $tokens[($closeBracket - 1)]['length'];
        }

        if ($gap !== $this->requiredSpacesBeforeClose) {
            $error = 'Expected %s spaces before closing parenthesis; %s found';
            $data  = [
                $this->requiredSpacesBeforeClose,
                $gap,
            ];
            $fix   = $phpcsFile->addFixableError($error, $closeBracket, 'SpacingBeforeClose', $data);
            if ($fix === true) {
                $padding = str_repeat(' ', $this->requiredSpacesBeforeClose);
                if ($gap === 0) {
                    $phpcsFile->fixer->addContentBefore($closeBracket, $padding);
                } else {
                    $phpcsFile->fixer->replaceToken(($closeBracket - 1), $padding);
                }
            }
        }

        $gap = 0;
        if ($tokens[($openBracket + 1)]['code'] === T_WHITESPACE) {
            $gap = $tokens[($openBracket + 1)]['length'];
        }

        if ($gap !== $this->requiredSpacesAfterOpen) {
            $error = 'Expected %s spaces after opening parenthesis; %s found';
            $data  = [
                $this->requiredSpacesAfterOpen,
                $gap,
            ];
            $fix   = $phpcsFile->addFixableError($error, $openBracket, 'SpacingAfterOpen', $data);
            if ($fix === true) {
                $padding = str_repeat(' ', $this->requiredSpacesAfterOpen);
                if ($gap === 0) {
                    $phpcsFile->fixer->addContent($openBracket, $padding);
                } else {
                    $phpcsFile->fixer->replaceToken(($openBracket + 1), $padding);
                }
            }
        }

    }//end processBracket()


}//end class
