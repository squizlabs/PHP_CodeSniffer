<?php
/**
 * Tokenizes CSS code.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
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
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
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
        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** START CSS TOKENIZING ***".PHP_EOL;
        }

        // If the content doesn't have an EOl char on the end, add one so
        // the open and close tags we add are parsed correctly.
        if (substr($string, 0, (strlen($eolChar) * -1)) !== $eolChar) {
            $string .= $eolChar;
        }

        $tokens      = parent::tokenizeString('<?php '.$string.'?>', $eolChar);
        $finalTokens = array();

        $newStackPtr      = 0;
        $numTokens        = count($tokens);
        $multiLineComment = false;
        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token = $tokens[$stackPtr];

            // CSS files don't have lists or break tags, so convert these to
            // standard strings early so they can be converted into T_STYLE
            // tokens and joined with other strings if needed.
            if ($token['code'] === T_BREAK || $token['code'] === T_LIST) {
                $token['type'] = 'T_STRING';
                $token['code'] = T_STRING;
            }

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type    = $token['type'];
                $content = str_replace($eolChar, '\n', $token['content']);
                echo "\tProcess token $stackPtr: $type => $content".PHP_EOL;
            }

            // Sometimes, there are PHP tags embedded in the code, which causes issues
            // with how PHP tokenizeses the string. After the first closing tag is found,
            // everything outside PHP tags is set as inline HTML tokens (1 for each line).
            // So we need to go through and find these tokens so we can re-tokenize them.
            if ($token['code'] === T_CLOSE_TAG && $stackPtr !== ($numTokens - 1)) {
                $content = '<?php ';
                for ($x = ($stackPtr + 1); $x < $numTokens; $x++) {
                    if ($tokens[$x]['code'] === T_INLINE_HTML) {
                        $content .= $tokens[$x]['content'];
                    } else {
                        $x--;
                        break;
                    }
                }

                if ($x < ($numTokens - 1)) {
                    // This is not the last closing tag in the file, so we
                    // have to add another closing tag here. If it is the last closing
                    // tag, this additional one would have been added during the
                    // original tokenize call.
                    $content .= ' ?>';
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo "\t\t=> Found premature closing tag at $stackPtr".PHP_EOL;
                    $cleanContent = str_replace($eolChar, '\n', $content);
                    echo "\t\tcontent: $cleanContent".PHP_EOL;
                    $oldNumTokens = $numTokens;
                }

                // Tokenize the string and remove the extra PHP tags we don't need.
                $moreTokens = parent::tokenizeString($content, $eolChar);
                array_shift($moreTokens);
                array_pop($moreTokens);
                array_pop($moreTokens);

                // Rebuild the tokens array.
                array_splice($tokens, ($stackPtr + 1), ($x - $stackPtr), $moreTokens);
                $numTokens = count($tokens);
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $count = count($moreTokens);
                    $diff  = ($x - $stackPtr);
                    echo "\t\t* added $count tokens, replaced $diff; size changed from $oldNumTokens to $numTokens *".PHP_EOL;
                }
            }//end if

            if ($token['code'] === T_GOTO_LABEL) {
                // Convert these back to T_STRING folowed by T_COLON so we can
                // more easily process style definitions.
                $finalTokens[$newStackPtr] = array(
                                              'type'    => 'T_STRING',
                                              'code'    => T_STRING,
                                              'content' => substr($token['content'], 0, -1),
                                             );
                $newStackPtr++;
                $finalTokens[$newStackPtr] = array(
                                              'type'    => 'T_COLON',
                                              'code'    => T_COLON,
                                              'content' => ':',
                                             );
                $newStackPtr++;
                continue;
            }

            if ($token['code'] === T_FUNCTION) {
                // There are no functions in CSS, so convert this to a string.
                $finalTokens[$newStackPtr] = array(
                                              'type'    => 'T_STRING',
                                              'code'    => T_STRING,
                                              'content' => $token['content'],
                                             );

                $newStackPtr++;
                continue;
            }

            if ($token['code'] === T_COMMENT
                && substr($token['content'], 0, 2) === '/*'
            ) {
                // Multi-line comment. Record it so we can ignore other
                // comment tags until we get out of this one.
                $multiLineComment = true;
            }

            if ($token['code'] === T_COMMENT
                && $multiLineComment === false
                && (substr($token['content'], 0, 2) === '//'
                || $token['content']{0} === '#')
            ) {
                $content = ltrim($token['content'], '#/');
                $commentTokens
                    = parent::tokenizeString('<?php '.$content.'?>', $eolChar);

                // The first and last tokens are the open/close tags.
                array_shift($commentTokens);
                array_pop($commentTokens);

                if ($token['content']{0} === '#') {
                    // The # character is not a comment in CSS files, so
                    // determine what it means in this context.
                    $firstContent = $commentTokens[0]['content'];

                    // If the first content is just a number, it is probably a
                    // colour like 8FB7DB, which PHP splits into 8 and FB7DB.
                    if (($commentTokens[0]['code'] === T_LNUMBER
                        || $commentTokens[0]['code'] === T_DNUMBER)
                        && $commentTokens[1]['code'] === T_STRING
                    ) {
                        $firstContent .= $commentTokens[1]['content'];
                        array_shift($commentTokens);
                    }

                    // If the first content looks like a colour and not a class
                    // definition, join the tokens together.
                    if (preg_match('/^[ABCDEF0-9]+$/i', $firstContent) === 1
                        && $commentTokens[1]['content'] !== '-'
                    ) {
                        array_shift($commentTokens);
                        // Work out what we trimmed off above and remember to re-add it.
                        $trimmed = substr($token['content'], 0, (strlen($token['content']) - strlen($content)));
                        $finalTokens[$newStackPtr] = array(
                                                      'type'    => 'T_COLOUR',
                                                      'code'    => T_COLOUR,
                                                      'content' => $trimmed.$firstContent,
                                                     );
                    } else {
                        $finalTokens[$newStackPtr] = array(
                                                      'type'    => 'T_HASH',
                                                      'code'    => T_HASH,
                                                      'content' => '#',
                                                     );
                    }
                } else {
                    $finalTokens[$newStackPtr] = array(
                                                  'type'    => 'T_STRING',
                                                  'code'    => T_STRING,
                                                  'content' => '//',
                                                 );
                }//end if

                $newStackPtr++;

                foreach ($commentTokens as $tokenData) {
                    if ($tokenData['code'] === T_COMMENT
                        && (substr($tokenData['content'], 0, 2) === '//'
                        || $tokenData['content']{0} === '#')
                    ) {
                        // This is a comment in a comment, so it needs
                        // to go through the whole process again.
                        $tokens[$stackPtr]['content'] = $tokenData['content'];
                        $stackPtr--;
                        break;
                    }

                    $finalTokens[$newStackPtr] = $tokenData;
                    $newStackPtr++;
                }

                continue;
            }//end if

            if ($token['code'] === T_COMMENT 
                && substr($token['content'], -2) === '*/'
            ) {
                // Multi-line comment is done.
                $multiLineComment = false;
            }

            $finalTokens[$newStackPtr] = $token;
            $newStackPtr++;
        }//end for

        // A flag to indicate if we are inside a style definition,
        // which is defined using curly braces. I'm assuming you can't
        // have nested curly brackets.
        $inStyleDef = false;

        $numTokens = count($finalTokens);
        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token = $finalTokens[$stackPtr];

            switch ($token['code']) {
            case T_OPEN_CURLY_BRACKET:
                $inStyleDef = true;
                break;
            case T_CLOSE_CURLY_BRACKET:
                $inStyleDef = false;
                break;
            case T_MINUS:
                // Minus signs are often used instead of spaces inside
                // class names, IDs and styles.
                if ($finalTokens[($stackPtr + 1)]['code'] === T_STRING) {
                    if ($finalTokens[($stackPtr - 1)]['code'] === T_STRING) {
                        $newContent = $finalTokens[($stackPtr - 1)]['content'].'-'.$finalTokens[($stackPtr + 1)]['content'];

                        $finalTokens[($stackPtr - 1)]['content'] = $newContent;
                        unset($finalTokens[$stackPtr]);
                        unset($finalTokens[($stackPtr + 1)]);
                        $stackPtr -= 2;
                    } else {
                        $newContent = '-'.$finalTokens[($stackPtr + 1)]['content'];

                        $finalTokens[($stackPtr + 1)]['content'] = $newContent;
                        unset($finalTokens[$stackPtr]);
                        $stackPtr--;
                    }

                    $finalTokens = array_values($finalTokens);
                    $numTokens   = count($finalTokens);
                } else if ($finalTokens[($stackPtr + 1)]['code'] === T_LNUMBER) {
                    // They can also be used to provide negative numbers.
                    $finalTokens[($stackPtr + 1)]['content']
                        = '-'.$finalTokens[($stackPtr + 1)]['content'];
                    unset($finalTokens[$stackPtr]);

                    $finalTokens = array_values($finalTokens);
                    $numTokens   = count($finalTokens);
                }

                break;
            case T_COLON:
                // Only interested in colons that are defining styles.
                if ($inStyleDef === false) {
                    break;
                }

                for ($x = ($stackPtr - 1); $x >= 0; $x--) {
                    if (in_array($finalTokens[$x]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false) {
                        break;
                    }
                }

                $finalTokens[$x]['type'] = 'T_STYLE';
                $finalTokens[$x]['code'] = T_STYLE;
                break;
            case T_STRING:
                if (strtolower($token['content']) === 'url') {
                    // Find the next content.
                    for ($x = ($stackPtr + 1); $x < $numTokens; $x++) {
                        if (in_array($finalTokens[$x]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false) {
                            break;
                        }
                    }

                    // Needs to be in the format "url(" for it to be a URL.
                    if ($finalTokens[$x]['code'] !== T_OPEN_PARENTHESIS) {
                        continue;
                    }

                    // Make sure the content isn't empty.
                    for ($y = ($x + 1); $y < $numTokens; $y++) {
                        if (in_array($finalTokens[$y]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === false) {
                            break;
                        }
                    }

                    if ($finalTokens[$y]['code'] === T_CLOSE_PARENTHESIS) {
                        continue;
                    }

                    // Join all the content together inside the url() statement.
                    $newContent = '';
                    for ($i = ($x + 2); $i < $numTokens; $i++) {
                        if ($finalTokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                            break;
                        }

                        $newContent .= $finalTokens[$i]['content'];
                        unset($finalTokens[$i]);
                    }

                    // If the content inside the "url()" is in double quotes
                    // there will only be one token and so we don't have to do
                    // anything except change its type. If it is not empty,
                    // we need to do some token merging.
                    $finalTokens[($x + 1)]['type'] = 'T_URL';
                    $finalTokens[($x + 1)]['code'] = T_URL;

                    if ($newContent !== '') {
                        $finalTokens[($x + 1)]['content'] .= $newContent;

                        $finalTokens = array_values($finalTokens);
                        $numTokens   = count($finalTokens);
                    }
                }//end if

                break;
            default:
                // Nothing special to be done with this token.
                break;
            }//end switch
        }//end for

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END CSS TOKENIZING ***".PHP_EOL;
        }

        return $finalTokens;

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
        // We override this method because we don't want the PHP version to
        // run during CSS processing because it is wasted processing time.

    }//end processAdditional()


}//end class

?>
