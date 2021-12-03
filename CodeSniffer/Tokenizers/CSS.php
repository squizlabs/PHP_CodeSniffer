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
     * If TRUE, files that appear to be minified will not be processed.
     *
     * @var boolean
     */
    public $skipMinified = true;


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
            echo "\t*** START CSS TOKENIZING 1ST PASS ***".PHP_EOL;
        }

        // If the content doesn't have an EOL char on the end, add one so
        // the open and close tags we add are parsed correctly.
        $eolAdded = false;
        if (substr($string, (strlen($eolChar) * -1)) !== $eolChar) {
            $string  .= $eolChar;
            $eolAdded = true;
        }

        $string = str_replace('<?php', '^PHPCS_CSS_T_OPEN_TAG^', $string);
        $string = str_replace('?>', '^PHPCS_CSS_T_CLOSE_TAG^', $string);
        $tokens = parent::tokenizeString('<?php '.$string.'?>', $eolChar);

        $finalTokens    = array();
        $finalTokens[0] = array(
                           'code'    => T_OPEN_TAG,
                           'type'    => 'T_OPEN_TAG',
                           'content' => '',
                          );

        $newStackPtr      = 1;
        $numTokens        = count($tokens);
        $multiLineComment = false;
        for ($stackPtr = 1; $stackPtr < $numTokens; $stackPtr++) {
            $token = $tokens[$stackPtr];

            // CSS files don't have lists, breaks etc, so convert these to
            // standard strings early so they can be converted into T_STYLE
            // tokens and joined with other strings if needed.
            if ($token['code'] === T_BREAK
                || $token['code'] === T_LIST
                || $token['code'] === T_DEFAULT
                || $token['code'] === T_SWITCH
                || $token['code'] === T_FOR
                || $token['code'] === T_FOREACH
                || $token['code'] === T_WHILE
                || $token['code'] === T_DEC
            ) {
                $token['type'] = 'T_STRING';
                $token['code'] = T_STRING;
            }

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type    = $token['type'];
                $content = PHP_CodeSniffer::prepareForOutput($token['content']);
                echo "\tProcess token $stackPtr: $type => $content".PHP_EOL;
            }

            if ($token['code'] === T_BITWISE_XOR
                && $tokens[($stackPtr + 1)]['content'] === 'PHPCS_CSS_T_OPEN_TAG'
            ) {
                $content = '<?php';
                for ($stackPtr = ($stackPtr + 3); $stackPtr < $numTokens; $stackPtr++) {
                    if ($tokens[$stackPtr]['code'] === T_BITWISE_XOR
                        && $tokens[($stackPtr + 1)]['content'] === 'PHPCS_CSS_T_CLOSE_TAG'
                    ) {
                        // Add the end tag and ignore the * we put at the end.
                        $content  .= '?>';
                        $stackPtr += 2;
                        break;
                    } else {
                        $content .= $tokens[$stackPtr]['content'];
                    }
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo "\t\t=> Found embedded PHP code: ";
                    $cleanContent = PHP_CodeSniffer::prepareForOutput($content);
                    echo $cleanContent.PHP_EOL;
                }

                $finalTokens[$newStackPtr] = array(
                                              'type'    => 'T_EMBEDDED_PHP',
                                              'code'    => T_EMBEDDED_PHP,
                                              'content' => $content,
                                             );

                $newStackPtr++;
                continue;
            }//end if

            if ($token['code'] === T_GOTO_LABEL) {
                // Convert these back to T_STRING followed by T_COLON so we can
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
                || $token['content'][0] === '#')
            ) {
                $content = ltrim($token['content'], '#/');

                // Guard against PHP7+ syntax errors by stripping
                // leading zeros so the content doesn't look like an invalid int.
                $leadingZero = false;
                if ($content[0] === '0') {
                    $content     = '1'.$content;
                    $leadingZero = true;
                }

                $commentTokens = parent::tokenizeString('<?php '.$content.'?>', $eolChar);

                // The first and last tokens are the open/close tags.
                array_shift($commentTokens);
                array_pop($commentTokens);

                if ($leadingZero === true) {
                    $commentTokens[0]['content'] = substr($commentTokens[0]['content'], 1);
                    $content = substr($content, 1);
                }

                if ($token['content'][0] === '#') {
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

                array_splice($tokens, $stackPtr, 1, $commentTokens);
                $numTokens = count($tokens);
                $stackPtr--;
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

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END CSS TOKENIZING 1ST PASS ***".PHP_EOL;
            echo "\t*** START CSS TOKENIZING 2ND PASS ***".PHP_EOL;
        }

        // A flag to indicate if we are inside a style definition,
        // which is defined using curly braces.
        $inStyleDef = false;

        // A flag to indicate if an At-rule like "@media" is used, which will result
        // in nested curly brackets.
        $asperandStart = false;

        $numTokens = count($finalTokens);
        for ($stackPtr = 0; $stackPtr < $numTokens; $stackPtr++) {
            $token = $finalTokens[$stackPtr];

            if (PHP_CODESNIFFER_VERBOSITY > 1) {
                $type    = $token['type'];
                $content = PHP_CodeSniffer::prepareForOutput($token['content']);
                echo "\tProcess token $stackPtr: $type => $content".PHP_EOL;
            }

            switch ($token['code']) {
            case T_OPEN_CURLY_BRACKET:
                // Opening curly brackets for an At-rule do not start a style
                // definition. We also reset the asperand flag here because the next
                // opening curly bracket could be indeed the start of a style
                // definition.
                if ($asperandStart === true) {
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        if ($inStyleDef === true) {
                            echo "\t\t* style definition closed *".PHP_EOL;
                        }

                        if ($asperandStart === true) {
                            echo "\t\t* at-rule definition closed *".PHP_EOL;
                        }
                    }

                    $inStyleDef    = false;
                    $asperandStart = false;
                } else {
                    $inStyleDef = true;
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* style definition opened *".PHP_EOL;
                    }
                }
                break;
            case T_CLOSE_CURLY_BRACKET:
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    if ($inStyleDef === true) {
                        echo "\t\t* style definition closed *".PHP_EOL;
                    }

                    if ($asperandStart === true) {
                        echo "\t\t* at-rule definition closed *".PHP_EOL;
                    }
                }

                $inStyleDef    = false;
                $asperandStart = false;
                break;
            case T_MINUS:
                // Minus signs are often used instead of spaces inside
                // class names, IDs and styles.
                if ($finalTokens[($stackPtr + 1)]['code'] === T_STRING) {
                    if ($finalTokens[($stackPtr - 1)]['code'] === T_STRING) {
                        $newContent = $finalTokens[($stackPtr - 1)]['content'].'-'.$finalTokens[($stackPtr + 1)]['content'];

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t\t* token is a string joiner; ignoring this and previous token".PHP_EOL;
                            $old = PHP_CodeSniffer::prepareForOutput($finalTokens[($stackPtr + 1)]['content']);
                            $new = PHP_CodeSniffer::prepareForOutput($newContent);
                            echo "\t\t=> token ".($stackPtr + 1)." content changed from \"$old\" to \"$new\"".PHP_EOL;
                        }

                        $finalTokens[($stackPtr + 1)]['content'] = $newContent;
                        unset($finalTokens[$stackPtr]);
                        unset($finalTokens[($stackPtr - 1)]);
                    } else {
                        $newContent = '-'.$finalTokens[($stackPtr + 1)]['content'];

                        $finalTokens[($stackPtr + 1)]['content'] = $newContent;
                        unset($finalTokens[$stackPtr]);
                    }
                } else if ($finalTokens[($stackPtr + 1)]['code'] === T_LNUMBER) {
                    // They can also be used to provide negative numbers.
                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        echo "\t\t* token is part of a negative number; adding content to next token and ignoring *".PHP_EOL;
                        $content = PHP_CodeSniffer::prepareForOutput($finalTokens[($stackPtr + 1)]['content']);
                        echo "\t\t=> token ".($stackPtr + 1)." content changed from \"$content\" to \"-$content\"".PHP_EOL;
                    }

                    $finalTokens[($stackPtr + 1)]['content'] = '-'.$finalTokens[($stackPtr + 1)]['content'];
                    unset($finalTokens[$stackPtr]);
                }//end if

                break;
            case T_COLON:
                // Only interested in colons that are defining styles.
                if ($inStyleDef === false) {
                    break;
                }

                for ($x = ($stackPtr - 1); $x >= 0; $x--) {
                    if (isset(PHP_CodeSniffer_Tokens::$emptyTokens[$finalTokens[$x]['code']]) === false) {
                        break;
                    }
                }

                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    $type = $finalTokens[$x]['type'];
                    echo "\t\t=> token $x changed from $type to T_STYLE".PHP_EOL;
                }

                $finalTokens[$x]['type'] = 'T_STYLE';
                $finalTokens[$x]['code'] = T_STYLE;
                break;
            case T_STRING:
                if (strtolower($token['content']) === 'url') {
                    // Find the next content.
                    for ($x = ($stackPtr + 1); $x < $numTokens; $x++) {
                        if (isset(PHP_CodeSniffer_Tokens::$emptyTokens[$finalTokens[$x]['code']]) === false) {
                            break;
                        }
                    }

                    // Needs to be in the format "url(" for it to be a URL.
                    if ($finalTokens[$x]['code'] !== T_OPEN_PARENTHESIS) {
                        continue 2;
                    }

                    // Make sure the content isn't empty.
                    for ($y = ($x + 1); $y < $numTokens; $y++) {
                        if (isset(PHP_CodeSniffer_Tokens::$emptyTokens[$finalTokens[$y]['code']]) === false) {
                            break;
                        }
                    }

                    if ($finalTokens[$y]['code'] === T_CLOSE_PARENTHESIS) {
                        continue 2;
                    }

                    if (PHP_CODESNIFFER_VERBOSITY > 1) {
                        for ($i = ($stackPtr + 1); $i <= $y; $i++) {
                            $type    = $finalTokens[$i]['type'];
                            $content = PHP_CodeSniffer::prepareForOutput($finalTokens[$i]['content']);
                            echo "\tProcess token $i: $type => $content".PHP_EOL;
                        }

                        echo "\t\t* token starts a URL *".PHP_EOL;
                    }

                    // Join all the content together inside the url() statement.
                    $newContent = '';
                    for ($i = ($x + 2); $i < $numTokens; $i++) {
                        if ($finalTokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                            break;
                        }

                        $newContent .= $finalTokens[$i]['content'];
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $content = PHP_CodeSniffer::prepareForOutput($finalTokens[$i]['content']);
                            echo "\t\t=> token $i added to URL string and ignored: $content".PHP_EOL;
                        }

                        unset($finalTokens[$i]);
                    }

                    $stackPtr = $i;

                    // If the content inside the "url()" is in double quotes
                    // there will only be one token and so we don't have to do
                    // anything except change its type. If it is not empty,
                    // we need to do some token merging.
                    $finalTokens[($x + 1)]['type'] = 'T_URL';
                    $finalTokens[($x + 1)]['code'] = T_URL;

                    if ($newContent !== '') {
                        $finalTokens[($x + 1)]['content'] .= $newContent;
                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            $content = PHP_CodeSniffer::prepareForOutput($finalTokens[($x + 1)]['content']);
                            echo "\t\t=> token content changed to: $content".PHP_EOL;
                        }
                    }
                } else if ($finalTokens[$stackPtr]['content'][0] === '-'
                    && $finalTokens[($stackPtr + 1)]['code'] === T_STRING
                ) {
                    if (isset($finalTokens[($stackPtr - 1)]) === true
                        && $finalTokens[($stackPtr - 1)]['code'] === T_STRING
                    ) {
                        $newContent = $finalTokens[($stackPtr - 1)]['content'].$finalTokens[$stackPtr]['content'].$finalTokens[($stackPtr + 1)]['content'];

                        if (PHP_CODESNIFFER_VERBOSITY > 1) {
                            echo "\t\t* token is a string joiner; ignoring this and previous token".PHP_EOL;
                            $old = PHP_CodeSniffer::prepareForOutput($finalTokens[($stackPtr + 1)]['content']);
                            $new = PHP_CodeSniffer::prepareForOutput($newContent);
                            echo "\t\t=> token ".($stackPtr + 1)." content changed from \"$old\" to \"$new\"".PHP_EOL;
                        }

                        $finalTokens[($stackPtr + 1)]['content'] = $newContent;
                        unset($finalTokens[$stackPtr]);
                        unset($finalTokens[($stackPtr - 1)]);
                    } else {
                        $newContent = $finalTokens[$stackPtr]['content'].$finalTokens[($stackPtr + 1)]['content'];

                        $finalTokens[($stackPtr + 1)]['content'] = $newContent;
                        unset($finalTokens[$stackPtr]);
                    }
                }//end if

                break;
            case T_ASPERAND:
                $asperandStart = true;
                if (PHP_CODESNIFFER_VERBOSITY > 1) {
                    echo "\t\t* at-rule definition opened *".PHP_EOL;
                }
                break;
            default:
                // Nothing special to be done with this token.
                break;
            }//end switch
        }//end for

        // Reset the array keys to avoid gaps.
        $finalTokens = array_values($finalTokens);
        $numTokens   = count($finalTokens);

        // Blank out the content of the end tag.
        $finalTokens[($numTokens - 1)]['content'] = '';

        if ($eolAdded === true) {
            // Strip off the extra EOL char we added for tokenizing.
            $finalTokens[($numTokens - 2)]['content'] = substr(
                $finalTokens[($numTokens - 2)]['content'],
                0,
                (strlen($eolChar) * -1)
            );

            if ($finalTokens[($numTokens - 2)]['content'] === '') {
                unset($finalTokens[($numTokens - 2)]);
                $finalTokens = array_values($finalTokens);
                $numTokens   = count($finalTokens);
            }
        }

        if (PHP_CODESNIFFER_VERBOSITY > 1) {
            echo "\t*** END CSS TOKENIZING 2ND PASS ***".PHP_EOL;
        }

        return $finalTokens;

    }//end tokenizeString()


    /**
     * Performs additional processing after main tokenizing.
     *
     * @param array  $tokens  The array of tokens to process.
     * @param string $eolChar The EOL character to use for splitting strings.
     *
     * @return void
     */
    public function processAdditional(&$tokens, $eolChar)
    {
        /*
            We override this method because we don't want the PHP version to
            run during CSS processing because it is wasted processing time.
        */

    }//end processAdditional()


}//end class
