<?php
/**
 * Checks for return type declarations if the spacing between a function's closing parenthesis,
 * colon, and return type is correct
 *
 * @author    Arent van Korlaar <avkorlaar@hostnet.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ReturnTypeDeclarationSniff implements Sniff
{

    /**
     * Number of spaces between the function's closing parenthesis and colon.
     *
     * @var integer
     */
    public $numSpacesClosingParenthesisColon = 0;

    /**
     * Number of spaces between the colon and the return type.
     *
     * @var integer
     */
    public $numSpacesColonReturnType = 1;

    /**
     * Ignore new lines when true
     *
     * @var boolean
     */
    public $ignoreNewLines = false;


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_FUNCTION,
            T_CLOSURE,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token
     *                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $closingParenthesisPosition = $this->getClosingParenthesis($phpcsFile, $tokens, $stackPtr);
        $endPosition = $this->getCharacterAfterReturnTypeDeclaration($phpcsFile, $tokens, $stackPtr);

        $tokensToFind = [
            T_COLON,
            T_RETURN_TYPE,
            T_WHITESPACE,
        ];

        $closingParenthesisColonSpacing = $colonReturnTypeSpacing = $acc = '';

        $nextSeparator = $closingParenthesisPosition;
        while (($nextSeparator = $phpcsFile->findNext($tokensToFind, ($nextSeparator + 1), $endPosition)) !== false) {
            if ($tokens[$nextSeparator]['code'] === T_COLON) {
                $closingParenthesisColonSpacing = $acc;
                $acc           = '';
                $colonPosition = $nextSeparator;
            } else if ($tokens[$nextSeparator]['code'] === T_RETURN_TYPE) {
                $colonReturnTypeSpacing = $acc;
                $acc = '';
                $returnTypePosition = $nextSeparator;
            } else {
                $acc = $acc.$tokens[($nextSeparator)]['content'];
            }
        }

        if (isset($colonPosition) === false || isset($returnTypePosition) === false) {
            // No return type declaration found, so nothing to sniff.
            return;
        }

        if ($this->ignoreNewLines === true) {
            $closingParenthesisColonSpacing = preg_replace("/[\n\r]/", "", $closingParenthesisColonSpacing);
            $colonReturnTypeSpacing         = preg_replace("/[\n\r]/", "", $colonReturnTypeSpacing);
        }

        $configuredSpacesClosingParenthesisColon = str_repeat(' ', $this->numSpacesClosingParenthesisColon);
        $configuredSpacesColonReturnType         = str_repeat(' ', $this->numSpacesColonReturnType);

        if ($closingParenthesisColonSpacing === $configuredSpacesClosingParenthesisColon
            && $colonReturnTypeSpacing === $configuredSpacesColonReturnType
        ) {
            // The spacing is ok.
            return;
        }

        $errorMessage = sprintf(
            "Expected \")%s:%sreturntype\";found \")%s:%sreturntype\"",
            $configuredSpacesClosingParenthesisColon,
            $configuredSpacesColonReturnType,
            $closingParenthesisColonSpacing,
            $colonReturnTypeSpacing
        );

        $errorMessage = str_replace("\r\n", '\n', $errorMessage);
        $errorMessage = str_replace("\n", '\n', $errorMessage);
        $errorMessage = str_replace("\r", '\r', $errorMessage);
        $errorMessage = str_replace("\t", '\t', $errorMessage);
        $errorMessage = str_replace('EOL', '\n', $errorMessage);

        if ($phpcsFile->addFixableError($errorMessage, $stackPtr, 'ReturnTypeDeclarationSpacing') === false) {
            return;
        }

        if ($closingParenthesisColonSpacing !== $configuredSpacesClosingParenthesisColon) {
            $this->fixSpacing(
                $phpcsFile,
                $tokens,
                $configuredSpacesClosingParenthesisColon,
                $closingParenthesisPosition,
                $colonPosition
            );
        }

        if ($colonReturnTypeSpacing !== $configuredSpacesColonReturnType) {
            $this->fixSpacing(
                $phpcsFile,
                $tokens,
                $configuredSpacesColonReturnType,
                $colonPosition,
                $returnTypePosition
            );
        }

    }//end process()


    /**
     * Get the position of a function's closing parenthesis within the
     * token stack.
     *
     * @param File  $phpcsFile The file being scanned.
     * @param array $tokens    Token stack for this file
     * @param int   $stackPtr  The position of the current token
     *                         in the stack passed in $tokens.
     *
     * @return int position within the token stack
     */
    private function getClosingParenthesis(File $phpcsFile, array $tokens, $stackPtr)
    {
        $closingParenthesis = $tokens[$stackPtr]['parenthesis_closer'];

        // In case the function is a closure, the closing parenthesis
        // may be positioned after a use language construct.
        if ($tokens[$stackPtr]['code'] === T_CLOSURE) {
            $use = $phpcsFile->findNext(T_USE, ($closingParenthesis + 1), $tokens[$stackPtr]['scope_opener']);
            if ($use !== false) {
                $openBracket        = $phpcsFile->findNext(T_OPEN_PARENTHESIS, ($use + 1));
                $closingParenthesis = $tokens[$openBracket]['parenthesis_closer'];
            }
        }

        return $closingParenthesis;

    }//end getClosingParenthesis()


    /**
     * Get the position of first character after the return type declaration
     * within the token stack.
     * This can be an opening brace, or, in case of an interface,
     * a semicolon.
     *
     * @param File  $phpcsFile The file being scanned.
     * @param array $tokens    Token stack for this file
     * @param int   $stackPtr  The position of the current token
     *                         in the stack passed in $tokens.
     *
     * @return int position within the token stack
     */
    private function getCharacterAfterReturnTypeDeclaration(File $phpcsFile, array $tokens, $stackPtr)
    {
        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            $endPosition = $phpcsFile->findNext(T_SEMICOLON, $stackPtr);
        } else {
            $endPosition = $tokens[$stackPtr]['scope_opener'];
        }

        return $endPosition;

    }//end getCharacterAfterReturnTypeDeclaration()


    /**
     * Fix the spacing to the required spacing between start and end
     *
     * @param File   $phpcsFile       The file being scanned.
     * @param array  $tokens          Token stack for this file
     * @param string $requiredSpacing Required spacing between start and end
     * @param int    $start           Position of the start in the token stack
     * @param int    $end             Position of the end in the token stack
     *
     * @return void
     */
    private function fixSpacing(File $phpcsFile, $tokens, $requiredSpacing, $start, $end)
    {
        // Currently there is no spacing, but spacing should be added.
        if (($start + 1) === $end && empty($requiredSpacing) === false) {
            $phpcsFile->fixer->addContent(
                $start,
                $requiredSpacing
            );

            return;
        }

        // There is a variable amount of spacing. Remove spacing, and insert the required spacing.
        for ($i = ($start + 1); $i < $end; $i++) {
            if ($tokens[$i]['code'] === T_WHITESPACE) {
                if ($this->ignoreNewLines === true
                    && isset($tokens[$i]['content']) === true
                    && preg_match("/[\n\r]/", $tokens[$i]['content']) === 1
                ) {
                    continue;
                }

                if (($i + 1) === $end) {
                    $phpcsFile->fixer->replaceToken($i, $requiredSpacing);
                    break;
                } else {
                    $phpcsFile->fixer->replaceToken($i, '');
                }
            }
        }

    }//end fixSpacing()


}//end class
