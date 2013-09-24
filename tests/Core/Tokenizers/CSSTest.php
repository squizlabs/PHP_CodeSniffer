<?php
/**
 * Tests for the Csv report of PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Alexander Zimmermann <alex@azimmermann.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2013 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

define('PHP_CODESNIFFER_VERBOSITY', 0);

/**
 * Tests for the Csv report of PHP_CodeSniffer.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Alexander Zimmermann <alex@azimmermann.com>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2009 SQLI <www.sqli.com>
 * @copyright 2006-2013 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Core_Tokenizers_CSSTest extends PHPUnit_Framework_TestCase
{

    /**
     * Store path to generated files.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

    }//end setUp()


    /**
     * Tests the additional Tokenizer method when list keyword exists in css file.
     *
     * @return void
     */
    public function testAdditionalTokenizerWithListStyles()
    {
        $tokens    = '';
        include __DIR__ . '/_files/CSS/list-token.php';

        $tokenizer = new PHP_CodeSniffer_Tokenizers_CSS();
        $tokenizer->processAdditional($tokens, PHP_EOL);

        $expected = '';
        include __DIR__ . '/_files/CSS/list-token-expected.php';

        $this->assertSame($expected, $tokens);

    }//end testGenerate()


    /**
     * Tests the additional Tokenizer method when list keyword exists in css file.
     *
     * @return void
     */
    public function testAdditionalTokenizerWithManyListStyles()
    {
        $tokens    = '';
        include __DIR__ . '/_files/CSS/many-list-token.php';

        $tokenizer = new PHP_CodeSniffer_Tokenizers_CSS();
        $tokenizer->processAdditional($tokens, PHP_EOL);

        $expected = '';
        include __DIR__ . '/_files/CSS/many-list-token-expected.php';

        $this->assertSame($expected, $tokens);

    }//end testGenerate()


    /**
     * Tests the additional Tokenizer method when list keyword exists in css file.
     *
     * @return void
     */
    public function testAdditionalTokenizerWithBreakStyles()
    {
        $tokens    = '';
        include __DIR__ . '/_files/CSS/break-token.php';

        $tokenizer = new PHP_CodeSniffer_Tokenizers_CSS();
        $tokenizer->processAdditional($tokens, PHP_EOL);

        $expected = '';
        include __DIR__ . '/_files/CSS/break-token-expected.php';

        $this->assertSame($expected, $tokens);

    }//end testGenerate()


}//end class

?>
