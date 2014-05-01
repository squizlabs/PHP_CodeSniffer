<?php
/**
 * Tests for the PHP Tokenizer of PHP_CodeSniffer.
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

require_once dirname(__FILE__).'/AbstractTestCase.php';

/**
 * Tests for the PHP Tokenizer of PHP_CodeSniffer.
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
class Core_Tokenizers_PHP extends Core_Tokenizers_AbstractTestCase
{


    /**
     * Test the tokenizer.
     *
     * @return void
     */
    public function testTokenizer()
    {
        $code     = '<?php ';
        $tokens   = $this->tokenizeString($code);
        $expected = array(
                     array(
                      'code'       => 368,
                      'type'       => 'T_OPEN_TAG',
                      'content'    => '<?php ',
                      'line'       => 1,
                      'column'     => 1,
                      'length'     => 6,
                      'level'      => 0,
                      'conditions' => array(),
                     ),
                    );
        $this->assertSame($expected, $tokens);

        $code     = '<?php if ($someVar) { $otherVar = 1; }';
        $tokens   = $this->tokenizeString($code);
        $expected = array(
                     array(
                      'code'       => 368,
                      'type'       => 'T_OPEN_TAG',
                      'content'    => '<?php ',
                      'line'       => 1,
                      'column'     => 1,
                      'length'     => 6,
                      'level'      => 0,
                      'conditions' => array(),
                     ),
                     array(
                      'code'               => 301,
                      'type'               => 'T_IF',
                      'content'            => 'if',
                      'line'               => 1,
                      'parenthesis_opener' => 3,
                      'parenthesis_closer' => 5,
                      'parenthesis_owner'  => 1,
                      'scope_condition'    => 1,
                      'scope_opener'       => 7,
                      'scope_closer'       => 16,
                      'column'             => 7,
                      'length'             => 2,
                      'level'              => 0,
                      'conditions'         => array(),
                     ),
                     array(
                      'code'       => 371,
                      'type'       => 'T_WHITESPACE',
                      'content'    => ' ',
                      'line'       => 1,
                      'column'     => 9,
                      'length'     => 1,
                      'level'      => 0,
                      'conditions' => array(),
                     ),
                     array(
                      'code'               => 1004,
                      'type'               => 'T_OPEN_PARENTHESIS',
                      'content'            => '(',
                      'line'               => 1,
                      'parenthesis_opener' => 3,
                      'parenthesis_closer' => 5,
                      'parenthesis_owner'  => 1,
                      'column'             => 10,
                      'length'             => 1,
                      'level'              => 0,
                      'conditions'         => array(),
                     ),
                     array(
                      'code'               => 309,
                      'type'               => 'T_VARIABLE',
                      'content'            => '$someVar',
                      'line'               => 1,
                      'nested_parenthesis' => array(3 => 5),
                      'column'             => 11,
                      'length'             => 8,
                      'level'              => 0,
                      'conditions'         => array(),
                     ),
                     array(
                      'code'               => 1005,
                      'type'               => 'T_CLOSE_PARENTHESIS',
                      'content'            => ')',
                      'line'               => 1,
                      'parenthesis_opener' => 3,
                      'parenthesis_closer' => 5,
                      'parenthesis_owner'  => 1,
                      'column'             => 19,
                      'length'             => 1,
                      'level'              => 0,
                      'conditions'         => array(),
                     ),
                     array(
                      'code'       => 371,
                      'type'       => 'T_WHITESPACE',
                      'content'    => ' ',
                      'line'       => 1,
                      'column'     => 20,
                      'length'     => 1,
                      'level'      => 0,
                      'conditions' => array(),
                     ),
                     array(
                      'code'            => 1000,
                      'type'            => 'T_OPEN_CURLY_BRACKET',
                      'content'         => '{',
                      'line'            => 1,
                      'bracket_opener'  => 7,
                      'bracket_closer'  => 16,
                      'scope_condition' => 1,
                      'scope_opener'    => 7,
                      'scope_closer'    => 16,
                      'column'          => 21,
                      'length'          => 1,
                      'level'           => 0,
                      'conditions'      => array(),
                     ),
                     array(
                      'code'       => 371,
                      'type'       => 'T_WHITESPACE',
                      'content'    => ' ',
                      'line'       => 1,
                      'column'     => 22,
                      'length'     => 1,
                      'level'      => 1,
                      'conditions' => array(1 => 301),
                     ),
                     array(
                      'code'       => 309,
                      'type'       => 'T_VARIABLE',
                      'content'    => '$otherVar',
                      'line'       => 1,
                      'column'     => 23,
                      'length'     => 9,
                      'level'      => 1,
                      'conditions' => array(1 => 301),
                     ),
                     array(
                      'code'       => 371,
                      'type'       => 'T_WHITESPACE',
                      'content'    => ' ',
                      'line'       => 1,
                      'column'     => 32,
                      'length'     => 1,
                      'level'      => 1,
                      'conditions' => array(1 => 301),
                     ),
                     array(
                      'code'       => 1014,
                      'type'       => 'T_EQUAL',
                      'content'    => '=',
                      'line'       => 1,
                      'column'     => 33,
                      'length'     => 1,
                      'level'      => 1,
                      'conditions' => array(1 => 301),
                     ),
                     array(
                      'code'       => 371,
                      'type'       => 'T_WHITESPACE',
                      'content'    => ' ',
                      'line'       => 1,
                      'column'     => 34,
                      'length'     => 1,
                      'level'      => 1,
                      'conditions' => array(1 => 301),
                     ),
                     array(
                      'code'       => 305,
                      'type'       => 'T_LNUMBER',
                      'content'    => '1',
                      'line'       => 1,
                      'column'     => 35,
                      'length'     => 1,
                      'level'      => 1,
                      'conditions' => array(1 => 301),
                     ),
                     array(
                      'code'       => 1013,
                      'type'       => 'T_SEMICOLON',
                      'content'    => ';',
                      'line'       => 1,
                      'column'     => 36,
                      'length'     => 1,
                      'level'      => 1,
                      'conditions' => array(1 => 301),
                     ),
                     array(
                      'code'       => 371,
                      'type'       => 'T_WHITESPACE',
                      'content'    => ' ',
                      'line'       => 1,
                      'column'     => 37,
                      'length'     => 1,
                      'level'      => 1,
                      'conditions' => array(1 => 301),
                     ),
                     array(
                      'code'            => 1001,
                      'type'            => 'T_CLOSE_CURLY_BRACKET',
                      'content'         => '}',
                      'line'            => 1,
                      'bracket_opener'  => 7,
                      'bracket_closer'  => 16,
                      'scope_condition' => 1,
                      'scope_opener'    => 7,
                      'scope_closer'    => 16,
                      'column'          => 38,
                      'length'          => 1,
                      'level'           => 0,
                      'conditions'      => array(),
                     ),
                    );
        $this->assertEquals($expected, $tokens);

    }//end testTokenizer()


}//end class

?>
