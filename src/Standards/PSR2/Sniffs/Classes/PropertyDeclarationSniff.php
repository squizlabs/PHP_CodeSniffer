<?php
/**
 * Verifies that properties are declared correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Tokens;

class PropertyDeclarationSniff extends AbstractVariableSniff
{


    /**
     * Processes the function tokens within the class.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['content'][1] === '_') {
            $error = 'Property name "%s" should not be prefixed with an underscore to indicate visibility';
            $data  = [$tokens[$stackPtr]['content']];
            $phpcsFile->addWarning($error, $stackPtr, 'Underscore', $data);
        }

        // Detect multiple properties defined at the same time. Throw an error
        // for this, but also only process the first property in the list so we don't
        // repeat errors.
        $find   = Tokens::$scopeModifiers;
        $find[] = T_VARIABLE;
        $find[] = T_VAR;
        $find[] = T_READONLY;
        $find[] = T_SEMICOLON;
        $find[] = T_OPEN_CURLY_BRACKET;

        $prev = $phpcsFile->findPrevious($find, ($stackPtr - 1));
        if ($tokens[$prev]['code'] === T_VARIABLE) {
            return;
        }

        if ($tokens[$prev]['code'] === T_VAR) {
            $error = 'The var keyword must not be used to declare a property';
            $phpcsFile->addError($error, $stackPtr, 'VarUsed');
        }

        $next = $phpcsFile->findNext([T_VARIABLE, T_SEMICOLON], ($stackPtr + 1));
        if ($next !== false && $tokens[$next]['code'] === T_VARIABLE) {
            $error = 'There must not be more than one property declared per statement';
            $phpcsFile->addError($error, $stackPtr, 'Multiple');
        }

        try {
            $propertyInfo = $phpcsFile->getMemberProperties($stackPtr);
            if (empty($propertyInfo) === true) {
                return;
            }
        } catch (\Exception $e) {
            // Turns out not to be a property after all.
            return;
        }

        if ($propertyInfo['type'] !== '') {
            $typeToken = $propertyInfo['type_end_token'];
            $error     = 'There must be a space after the property type declaration; %s found';
            if ($tokens[($typeToken + 1)]['code'] !== T_WHITESPACE) {
                $data = ['0'];
                $fix  = $phpcsFile->addFixableError($error, $typeToken, 'SpacingAfterType', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->addContent($typeToken, ' ');
                }
            } else if ($tokens[($typeToken + 1)]['content'] !== ' ') {
                $next = $phpcsFile->findNext(T_WHITESPACE, ($typeToken + 1), null, true);
                if ($tokens[$next]['line'] !== $tokens[$typeToken]['line']) {
                    $found = 'newline';
                } else {
                    // According to PSR-12: "There MUST be a space between type declaration and property name."
                    // This does not mean a single space only is expected, but at least 1. So, if many, ignore sniff.
                    return;
                }

                $data = [$found];

                $nextNonWs = $phpcsFile->findNext(Tokens::$emptyTokens, ($typeToken + 1), null, true);
                if ($nextNonWs !== $next) {
                    $phpcsFile->addError($error, $typeToken, 'SpacingAfterType', $data);
                } else {
                    $fix = $phpcsFile->addFixableError($error, $typeToken, 'SpacingAfterType', $data);
                    if ($fix === true) {
                        $phpcsFile->fixer->beginChangeset();
                        for ($x = ($typeToken + 1); $x < $next; $x++) {
                            $phpcsFile->fixer->replaceToken($x, '');
                        }

                        $phpcsFile->fixer->addContent($typeToken, ' ');
                        $phpcsFile->fixer->endChangeset();
                    }
                }
            }//end if
        }//end if

        if ($propertyInfo['scope_specified'] === false) {
            $error = 'Visibility must be declared on property "%s"';
            $data  = [$tokens[$stackPtr]['content']];
            $phpcsFile->addError($error, $stackPtr, 'ScopeMissing', $data);
        }

        /*
         * Note: per PSR-PER section 4.6, the order should be:
         * - Inheritance modifier: `abstract` or `final`.
         * - Visibility modifier: `public`, `protected`, or `private`.
         * - Scope modifier: `static`.
         * - Mutation modifier: `readonly`.
         * - Type declaration.
         * - Name.
         *
         * Ref: https://www.php-fig.org/per/coding-style/#46-modifier-keywords
         *
         * At this time (PHP 8.2), inheritance modifiers cannot be applied to properties and
         * the `static` and `readonly` modifiers are mutually exclusive and cannot be used together.
         *
         * Based on that, the below modifier keyword order checks are sufficient (for now).
         */

        if ($propertyInfo['scope_specified'] === true && $propertyInfo['is_static'] === true) {
            $scopePtr  = $phpcsFile->findPrevious(Tokens::$scopeModifiers, ($stackPtr - 1));
            $staticPtr = $phpcsFile->findPrevious(T_STATIC, ($stackPtr - 1));
            if ($scopePtr > $staticPtr) {
                $error = 'The static declaration must come after the visibility declaration';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'StaticBeforeVisibility');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();

                    for ($i = ($scopePtr + 1); $scopePtr < $stackPtr; $i++) {
                        if ($tokens[$i]['code'] !== T_WHITESPACE) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->replaceToken($scopePtr, '');
                    $phpcsFile->fixer->addContentBefore($staticPtr, $propertyInfo['scope'].' ');

                    $phpcsFile->fixer->endChangeset();
                }
            }
        }//end if

        if ($propertyInfo['scope_specified'] === true && $propertyInfo['is_readonly'] === true) {
            $scopePtr    = $phpcsFile->findPrevious(Tokens::$scopeModifiers, ($stackPtr - 1));
            $readonlyPtr = $phpcsFile->findPrevious(T_READONLY, ($stackPtr - 1));
            if ($scopePtr > $readonlyPtr) {
                $error = 'The readonly declaration must come after the visibility declaration';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'ReadonlyBeforeVisibility');
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();

                    for ($i = ($scopePtr + 1); $scopePtr < $stackPtr; $i++) {
                        if ($tokens[$i]['code'] !== T_WHITESPACE) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->replaceToken($scopePtr, '');
                    $phpcsFile->fixer->addContentBefore($readonlyPtr, $propertyInfo['scope'].' ');

                    $phpcsFile->fixer->endChangeset();
                }
            }
        }//end if

    }//end processMemberVar()


    /**
     * Processes normal variables.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processVariable(File $phpcsFile, $stackPtr)
    {
        /*
            We don't care about normal variables.
        */

    }//end processVariable()


    /**
     * Processes variables in double quoted strings.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position where the token was found.
     *
     * @return void
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr)
    {
        /*
            We don't care about normal variables.
        */

    }//end processVariableInString()


}//end class
