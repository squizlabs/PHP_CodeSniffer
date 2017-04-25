<?php
/**
 * Generic_Sniffs_Functions_SpaceBeforeParenthesisSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Florian Grandel <jerico.dev@gmail.com>
 * @copyright 2009-2014 Florian Grandel
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Functions_SpaceBeforeParenthesisSniff.
 *
 * Ensures that there is a space between the function name and the parenthesis.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Florian Grandel <jerico.dev@gmail.com>
 * @copyright 2009-2014 Florian Grandel
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Functions_SpaceBeforeParenthesisSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Only allow the space character as valid between function name and parenthesis.
     *
     * @var bool
     */
    public $spacesOnly = true;

    /**
     * Ensure there are exactly this many whitespace characters between function name and parenthesis.
     *
     * @var int
     */
    public $numberOfSpaces = 1;


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return void
     */
    public function register()
    {
        return array(
                T_FUNCTION,
                T_CLOSURE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $whitespaceSet = self::getPrecidingWhitespace($phpcsFile, $stackPtr);
        $isLengthExact = self::assertWhitespaceLength($whitespaceSet, $this->numberOfSpaces);
        $onlySpaces    = self::assertTokenContentCharacters($whitespaceSet, ' ', $this->spacesOnly);

        if (($isLengthExact && $onlySpaces) === false) {
            $phpcsFile->addError(
                'The spacing before the functions parenthesis must be correct',
                $stackPtr
            );
        }

    }//end process()


    /**
     * Get the whitespace preciding the position in the stack.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                  $stackPointer The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return array
     */
    protected static function getPrecidingWhitespace(PHP_CodeSniffer_File $phpcsFile, $stackPointer )
    {
        $tokenSet = $phpcsFile->getTokens();
        $token    = $tokenSet[$stackPointer];
        $paranthesisPosition = $token['parenthesis_opener'];
        $pos        = $phpcsFile->findPrevious(T_STRING, $paranthesisPosition);
        $whitespace = array_slice($tokenSet, $pos, ($paranthesisPosition - $pos), true);
        unset( $whitespace[$pos] );

        return $whitespace;

    }//end getPrecidingWhitespace()


    /**
     * Get the whitespace preciding the position in the stack.
     *
     * @param array $tokenSet The tokens to check.
     * @param int   $length   The length the whitespace should have
     *
     * @return boolean
     */
    protected static function assertWhitespaceLength(array $tokenSet, $length)
    {
        if ($length === null) {
            return true;
        }

        $whitespace = '';
        foreach ($tokenSet as $token) {
            $whitespace .= $token['content'];
        }

        return strlen($whitespace) === $length;

    }//end assertWhitespaceLength()


    /**
     * Assert, that the tokens content only contain the characters in the charList.
     *
     * @param array $tokenSet The tokens to check.
     * @param int   $charList The allowed characters
     * @param bool  $exact    Should the match be exact?
     *
     * @return boolean
     */
    protected static function assertTokenContentCharacters(array $tokenSet, $charList, $exact)
    {
        if ($exact === false) {
            return true;
        }

        foreach ($tokenSet as $token) {
            if (trim($token['content'], $charList) !== '') {
                return false;
            }
        }

        return true;

    }//end assertTokenContentCharacters()


}//end class
