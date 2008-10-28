<?php
/**
 * Tokenizes CSS code.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Tokenizers_PHP', true) === false) {
    throw new Exception('Class PHP_CodeSniffer_Tokenizers_PHP not found');
}

/**
 * Tokenizes CSS code.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Tokenizers_CSS extends PHP_CodeSniffer_Tokenizers_PHP
{


    /**
     * Creates an array of tokens when given some CSS code.
     *
     * Uses the PHP tokenizer to do all the tricky work
     *
     * @param string $string  The string to tokenize.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return array
     */
    public function tokenizeString($string, $eolChar='\n')
    {
        $tokens = parent::tokenizeString('<?php '.$string.' ?>', $eolChar);
        $finalTokens = array();

        $newStackPtr = 0;
        $numTokens   = count($tokens);
        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token = $tokens[$stackPtr];
            if ($token['code'] === T_COMMENT && $token['content']{0} === '#') {
                // The # character is not a comment in CSS files, so determine
                // what it means in this context.
                $content = substr($token['content'], 1);
                $commentTokens = parent::tokenizeString('<?php '.$content.'?>', $eolChar);

                // The first and last tokens are the open/close tags.
                array_shift($commentTokens);
                array_pop($commentTokens);
                $firstContent = $commentTokens[0]['content'];

                // If the first content is just a number, it is probably a
                // colour like 8FB7DB, which PHP splits into 8 and FB7DB.
                if ($commentTokens[0]['code'] === T_LNUMBER && $commentTokens[1]['code'] === T_STRING) {
                    $firstContent .= $commentTokens[1]['content'];
                    array_shift($commentTokens);
                }

                // If the first content looks like a colour and not a class
                // definition, join the tokens together.
                if (preg_match('/^[ABCDEF0-9]+$/i', $firstContent) === 1) {
                    array_shift($commentTokens);
                    $finalTokens[$newStackPtr] = array(
                                                  'type'    => 'T_COLOUR',
                                                  'code'    => T_COLOUR,
                                                  'content' => '#'.$firstContent,
                                                 );
                } else {
                    $finalTokens[$newStackPtr] = array(
                                                  'type'    => 'T_HASH',
                                                  'code'    => T_HASH,
                                                  'content' => '#',
                                                 );
                }

                $newStackPtr++;

                foreach ($commentTokens as $tokenData) {
                    $finalTokens[$newStackPtr] = $tokenData;
                    $newStackPtr++;
                }

                continue;
            }//end if

            $finalTokens[$newStackPtr] = $token;
            $newStackPtr++;
        }//end for


        $numTokens = count($finalTokens);
        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token = $finalTokens[$stackPtr];

            if ($token['code'] === T_MINUS) {
                // Minus signs are often used instead of spaces inside
                // class names, IDs and styles.
                if ($finalTokens[($stackPtr - 1)]['code'] === T_STRING && $finalTokens[($stackPtr + 1)]['code'] === T_STRING) {
                    $newContent = $finalTokens[($stackPtr - 1)]['content'].'-'.$finalTokens[($stackPtr + 1)]['content'];
                    $finalTokens[($stackPtr - 1)]['content'] = $newContent;
                    unset($finalTokens[$stackPtr]);
                    unset($finalTokens[($stackPtr + 1)]);
                    $finalTokens = array_values($finalTokens);
                    $numTokens   = count($finalTokens);
                    $stackPtr--;
                } else if ($finalTokens[($stackPtr + 1)]['code'] === T_LNUMBER) {
                    // They can also be used to provide negative numbers.
                    $finalTokens[($stackPtr + 1)]['content'] = '-'.$finalTokens[($stackPtr + 1)]['content'];
                    unset($finalTokens[$stackPtr]);
                    $finalTokens = array_values($finalTokens);
                    $numTokens   = count($finalTokens);
                }
            }//end if
        }//end for

        return $finalTokens;

    }//end tokenizeString()


}//end class

?>
