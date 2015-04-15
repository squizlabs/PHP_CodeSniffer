<?php
/**
 * Generic_Sniffs_Methods_ForceVisibilitySniff.
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
 * Generic_Sniffs_Methods_ForceVisibilitySniff.
 *
 * Ensures that a methods visibility (public, protected, private) is set.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Florian Grandel <jerico.dev@gmail.com>
 * @copyright 2009-2014 Florian Grandel
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Methods_ForceVisibilitySniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     */
    public function __construct()
    {
        parent::__construct(
            array(
             T_CLASS,
             T_INTERFACE,
             T_TRAIT,
            ),
            array(T_FUNCTION),
            true
        );

    }//end __construct()


    /**
     * Processes a token that is found within the scope that this test is
     * listening to.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int                  $stackPtr  The position in the stack where this
     *                                        token was found.
     * @param int                  $currScope The position in the tokens array that
     *                                        opened the scope that this test is
     *                                        listening for.
     *
     * @return void
     */
    protected function processTokenWithinScope( PHP_CodeSniffer_File $phpcsFile, $stackPtr, $currScope )
    {
        $signatureTokenSet = self::getSignatureTokens($phpcsFile, $stackPtr);
        $doesContain       = self::assertContainsToken($signatureTokenSet, array(T_PUBLIC, T_PROTECTED, T_PRIVATE));
        if ($doesContain === false) {
            $fix = $phpcsFile->addError(
                'Method does not explicitly define its visibility.',
                $stackPtr
            );
        }

    }//end processTokenWithinScope()


    /**
     * Get the tokens of the method's signature.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file where this token was found.
     * @param integer              $stackPointer The position in the stack where this
     *                                        token was found.
     *
     * @return array
     */
    protected static function getSignatureTokens( PHP_CodeSniffer_File $phpcsFile, $stackPointer )
    {
        $tokenSet = $phpcsFile->getTokens();

        $signatureTokenSet = array();
        $currentPointer    = $stackPointer;
        while (isset($tokenSet[$currentPointer]) === true) {
            $currentToken = $tokenSet[$currentPointer];
            switch($currentToken['code']) {
            case T_FUNCTION:
            case T_WHITESPACE:
            case T_STATIC:
            case T_ABSTRACT:
            case T_FINAL:
            case T_PUBLIC:
            case T_PROTECTED:
            case T_PRIVATE:
                array_unshift($signatureTokenSet, $currentToken);
                break;
            default:
                break 2;
            }

            $currentPointer--;
        }

        return $signatureTokenSet;

    }//end getSignatureTokens()


    /**
     * Assert that the token set contains the expected tokens.
     *
     * @param array $tokenSet       The token set to check.
     * @param array $expectedTokens The tokens to expect.
     *
     * @return bool
     */
    protected static function assertContainstoken( $tokenSet, $expectedTokens )
    {
        foreach ($tokenSet as $token) {
            if (in_array($token['code'], $expectedTokens) === true) {
                return true;
            }
        }

        return false;

    }//end assertContainstoken()


}//end class
