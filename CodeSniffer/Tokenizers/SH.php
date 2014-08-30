<?php
/**
 * Tokenizes SH code.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Tokenizers_SH
{
    /**
     * Creates an array of tokens when given some SH code.
     *
     * Uses the PHP tokenizer to do all the tricky work
     *
     * @param string $string  The string to tokenize.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return array
     */
    public function tokenizeString($string, $eolChar = '\n')
    {
        $tokens[] = array(
                     'code'    => T_OPEN_TAG,
                     'type'    => 'T_OPEN_TAG',
                     'content' => '',
                    );

        $string = str_replace($eolChar, "\n", $string);
        $chars    = str_split($string);

        $countValues = array_count_values($chars);
        $numLines = $countValues["\n"];

        for ($i = 0; $i < $numLines; $i++) {
            $tokens[] = array(
                'code'    => T_WHITESPACE,
                'type'    => 'T_WHITESPACE',
                'content' => "\n",
            );
        }

        $tokens[] = array(
                     'code'    => T_CLOSE_TAG,
                     'type'    => 'T_CLOSE_TAG',
                     'content' => '',
                    );

        return $tokens;
    }//end tokenizeString()


    /**
     * Performs additional processing after main tokenizing.
     *
     * @param array  &$tokens The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     */
    public function processAdditional(&$tokens, $eolChar)
    {
    }//end processAdditional()

}//end class

?>
