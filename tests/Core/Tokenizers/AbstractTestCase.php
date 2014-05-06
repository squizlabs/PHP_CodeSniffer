<?php
/**
 * TestCase Abstract Helper class.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Benjamin Pearson <bpearson@squiz.com.au>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * TestCase Abstract Helper class.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Benjamin Pearson <bpearson@squiz.com.au>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Core_Tokenizers_AbstractTestCase extends PHPUnit_Framework_TestCase
{


    /**
     * Returns tokens from a string.
     *
     * @param string $code The code to tokenize.
     * @param string $type The tokenizer type.
     *
     * @return string
     */
    protected function tokenizeString($code, $type='PHP')
    {
        $tokens    = array();
        $tokenizer = $this->getTokenizer($type);
        if ($tokenizer !== null) {
            $tokens = $tokenizer->tokenizeString($code, '\n');
        }

        return $tokens;

    }//end tokenizeString()


    /**
     * Return a tokenizer.
     *
     * @param string $type The tokenizer type.
     *
     * @return object
     */
    protected function getTokenizer($type='PHP')
    {
        $tokenizer      = null;
        $tokenizerClass = 'PHP_CodeSniffer_Tokenizers_'.$type;
        if (class_exists($tokenizerClass) === true) {
            $tokenizer = new $tokenizerClass();
        }

        return $tokenizer;

    }//end getTokenizer()


}//end class

?>
