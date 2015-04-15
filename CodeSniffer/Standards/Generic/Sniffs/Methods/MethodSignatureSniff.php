<?php
/**
 * Generic_Sniffs_Methods_MethodSignatureSniff.
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
 * Generic_Sniffs_Methods_MethodSignatureSniff.
 *
 * Ensures that method signatures adhere to the given pattern.
 *
 * **Example**
 *
 *    The pattern 'static abstract visibility final function' will enforce, that the keywords, are in the given order.
 *    It however will not enforce all keywords to be present.
 *
 *    //valid - adhere to the given pattern
 *    static public function
 *    function
 *    abstract final function
 *
 *    //invalid - order of elements does not adhere to the pattern
 *    public static function
 *    final public function
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Florian Grandel <jerico.dev@gmail.com>
 * @copyright 2009-2014 Florian Grandel
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Generic_Sniffs_Methods_MethodSignatureSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    public $pattern = 'abstract final visibility static function';

    protected static $tokenNameMap = array(
                                      'final'      => T_FINAL,
                                      'static'     => T_STATIC,
                                      'abstract'   => T_ABSTRACT,
                                      'function'   => T_FUNCTION,
                                      'public'     => T_PUBLIC,
                                      'protected'  => T_PROTECTED,
                                      'private'    => T_PRIVATE,
                                      ' '          => T_WHITESPACE,
                                      'visibility' => 'visibility',
                                     );

    protected static $tokenRegex = array(
                                    T_FINAL      => '(final)?',
                                    T_STATIC     => '(static)?',
                                    T_ABSTRACT   => '(abstract)?',
                                    T_FUNCTION   => 'function',
                                    T_PUBLIC     => '(public)?',
                                    T_PROTECTED  => '(protected)?',
                                    T_PRIVATE    => '(private)?',
                                    T_WHITESPACE => '\s?',
                                    'visibility' => '(public|protected|private)?',
                                   );


    /**
     * Registers the tokens that this sniff wants to listen for.
     */
    public function __construct()
    {
        parent::__construct(array(T_CLASS, T_INTERFACE, T_TRAIT), array(T_FUNCTION), true);

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
        $tokenPatternSet   = self::preparePattern($this->pattern);
        $signatureTokenSet = self::getSignatureTokens($phpcsFile, $stackPtr);
        $doesMatch         = self::assertSignaturePattern($tokenPatternSet, $signatureTokenSet);

        if ($doesMatch === true) {
            $fix = $phpcsFile->addFixableError(
                'Method signature does not match defined signature pattern.',
                $stackPtr
            );

            if ($fix === false) {
                $correctTokenSet = self::generateCorrectTokenOrder($tokenPatternSet, $signatureTokenSet);
                foreach ($correctTokenSet as $position => $token) {
                    $phpcsFile->fixer->replaceToken($position, $token['content']);
                }
            }
        }

    }//end processTokenWithinScope()


    /**
     * Convert the signature pattern to the correct token codes.
     *
     * @param string $pattern The pattern defining the method signature
     *
     * @return array The list of tiken codes
     */
    protected static function preparePattern( $pattern )
    {
        if (empty($pattern) === true) {
            return array();
        }

        $patternSet = explode(' ', $pattern);
        $tokenName  = array_shift($patternSet);

        if ($tokenName === 'visibility') {
            $token = array(
                      T_PUBLIC,
                      T_PROTECTED,
                      T_PRIVATE,
                     );
        } else if (isset(self::$tokenNameMap[$tokenName]) === true) {
            $token = array(self::$tokenNameMap[$tokenName]);
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    'Illegal pattern "%s" found. Contains unknown token "%s"',
                    $pattern,
                    $tokenName
                )
            );
        }

        $token[] = T_WHITESPACE;

        return self::trim(array_merge($token, self::preparePattern(implode(' ', $patternSet))));

    }//end preparePattern()


    /**
     * Returns the tokens of the functions signature.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file where this token was found.
     * @param int                  $stackPointer The position in the stack where this
     *                                        token was found.
     *
     * @return array
     */
    protected static function getSignatureTokens( PHP_CodeSniffer_File $phpcsFile, $stackPointer )
    {
        $tokenSet = $phpcsFile->getTokens();

        $signatureTokenSet = array();
        $currentPointer    = $stackPointer;
        while (isset($tokenSet[$currentPointer])) {
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

        $signatureTokenSet = self::trim($signatureTokenSet);

        return $signatureTokenSet;

    }//end getSignatureTokens()


    /**
     * Assert that the tokens adhere to the pattern.
     *
     * @param array $patternSet The tokens according to the pattern
     * @param array $tokenSet   The tokens in the file
     *
     * @return bool
     */
    protected static function assertSignaturePattern(array $patternSet, array $tokenSet)
    {
        $signatureString = '';
        foreach ($tokenSet as $token) {
            $signatureString .= $token['content'];
        }

        $signaturePattern = '';
        foreach ($patternSet as $tokenCode) {
            $signaturePattern .= self::$tokenRegex[$tokenCode];
        }

        return preg_match('#^'.$signaturePattern.'$#i', $signatureString) === 1;

    }//end assertSignaturePattern()


    /**
     * Returns tokens in the correct order.
     *
     * @param array $patternSet The tokens according to the pattern
     * @param array $tokenSet   The tokens in the file
     *
     * @return array|bool
     */
    protected static function generateCorrectTokenOrder(array $patternSet, array $tokenSet)
    {
        $simplifiedTokenSet = array();
        foreach ($tokenSet as $position => $token) {
            $simplifiedTokenSet[$token['code']] = $position;
        }

        reset($tokenSet);
        $currentPosition = key($tokenSet);

        $orderedTokenSet = array();
        foreach ($patternSet as $code) {
            if ($code === T_WHITESPACE) {
                continue;
            } else if (isset($simplifiedTokenSet[$code]) === true) {
                $position = $simplifiedTokenSet[$code];
                $orderedTokenSet[$currentPosition] = $tokenSet[$position];
                $currentPosition++;
                $orderedTokenSet[$currentPosition] = array(
                                                      'code'    => T_WHITESPACE,
                                                      'content' => ' ',
                                                     );
                $currentPosition++;
                unset($simplifiedTokenSet[$code]);
            }
        }

        array_pop($orderedTokenSet);

        $doesCountMatch = count($orderedTokenSet) === count($tokenSet);

        if ($doesCountMatch === true) {
            return $orderedTokenSet;
        } else {
            return false;
        }

    }//end generateCorrectTokenOrder()


    /**
     * Remove leading an trailing tokens from the tokenSet.
     *
     * @param array $tokenSet The tokenSet to trim
     * @param array $trimSet  The Tokens to remove
     *
     * @return array
     */
    protected static function trim(array $tokenSet, array $trimSet = array( T_WHITESPACE, T_COMMENT))
    {
        reset($tokenSet);
        $curPointer = key($tokenSet);

        while ((isset($tokenSet[$curPointer]) === true)
            && (in_array($tokenSet[$curPointer]['code'], $trimSet) === true)
        ) {
            unset($tokenSet[$curPointer]);
            $curPointer++;
        }

        end($tokenSet);
        $curPointer = key($tokenSet);

        while ((isset($tokenSet[$curPointer]) === true)
            && (in_array($tokenSet[$curPointer]['code'], $trimSet) === true)
        ) {
            unset( $tokenSet[$curPointer] );
            $curPointer--;
        }

        return $tokenSet;

    }//end trim()


}//end class
