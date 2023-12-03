<?php
/**
 * Checks that control structures are defined and indented correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ScopeIndentSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];

    /**
     * The number of spaces code should be indented.
     *
     * @var integer
     */
    public $indent = 4;

    /**
     * Does the indent need to be exactly right?
     *
     * If TRUE, indent needs to be exactly $indent spaces. If FALSE,
     * indent needs to be at least $indent spaces (but can be more).
     *
     * @var boolean
     */
    public $exact = false;

    /**
     * Should tabs be used for indenting?
     *
     * If TRUE, fixes will be made using tabs instead of spaces.
     * The size of each tab is important, so it should be specified
     * using the --tab-width CLI argument.
     *
     * @var boolean
     */
    public $tabIndent = false;

    /**
     * The --tab-width CLI value that is being used.
     *
     * @var integer
     */
    private $tabWidth = null;

    /**
     * List of tokens not needing to be checked for indentation.
     *
     * Useful to allow Sniffs based on this to easily ignore/skip some
     * tokens from verification. For example, inline HTML sections
     * or PHP open/close tags can escape from here and have their own
     * rules elsewhere.
     *
     * @var int[]
     */
    public $ignoreIndentationTokens = [];

    /**
     * List of tokens not needing to be checked for indentation.
     *
     * This is a cached copy of the public version of this var, which
     * can be set in a ruleset file, and some core ignored tokens.
     *
     * @var int[]
     */
    private $ignoreIndentation = [];

    /**
     * Any scope openers that should not cause an indent.
     *
     * @var int[]
     */
    protected $nonIndentingScopes = [];

    /**
     * Show debug output for this sniff.
     *
     * @var boolean
     */
    private $debug = false;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        if (defined('PHP_CODESNIFFER_IN_TESTS') === true) {
            $this->debug = false;
        }

        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile All the tokens found in the document.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return int
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $debug = Config::getConfigData('scope_indent_debug');
        if ($debug !== null) {
            $this->debug = (bool) $debug;
        }

        if ($this->tabWidth === null) {
            if (isset($phpcsFile->config->tabWidth) === false || $phpcsFile->config->tabWidth === 0) {
                // We have no idea how wide tabs are, so assume 4 spaces for fixing.
                // It shouldn't really matter because indent checks elsewhere in the
                // standard should fix things up.
                $this->tabWidth = 4;
            } else {
                $this->tabWidth = $phpcsFile->config->tabWidth;
            }
        }

        $lastOpenTag       = $stackPtr;
        $lastCloseTag      = null;
        $openScopes        = [];
        $adjustments       = [];
        $setIndents        = [];
        $disableExactStack = [];
        $disableExactEnd   = 0;

        $tokens  = $phpcsFile->getTokens();
        $first   = $phpcsFile->findFirstOnLine(T_INLINE_HTML, $stackPtr);
        $trimmed = ltrim($tokens[$first]['content']);
        if ($trimmed === '') {
            $currentIndent = ($tokens[$stackPtr]['column'] - 1);
        } else {
            $currentIndent = (strlen($tokens[$first]['content']) - strlen($trimmed));
        }

        if ($this->debug === true) {
            $line = $tokens[$stackPtr]['line'];
            echo "Start with token $stackPtr on line $line with indent $currentIndent".PHP_EOL;
        }

        if (empty($this->ignoreIndentation) === true) {
            $this->ignoreIndentation = [T_INLINE_HTML => true];
            foreach ($this->ignoreIndentationTokens as $token) {
                if (is_int($token) === false) {
                    if (defined($token) === false) {
                        continue;
                    }

                    $token = constant($token);
                }

                $this->ignoreIndentation[$token] = true;
            }
        }//end if

        $this->exact     = (bool) $this->exact;
        $this->tabIndent = (bool) $this->tabIndent;

        $checkAnnotations = $phpcsFile->config->annotations;

        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if ($i === false) {
                // Something has gone very wrong; maybe a parse error.
                break;
            }

            if ($checkAnnotations === true
                && $tokens[$i]['code'] === T_PHPCS_SET
                && isset($tokens[$i]['sniffCode']) === true
                && $tokens[$i]['sniffCode'] === 'Generic.WhiteSpace.ScopeIndent'
                && $tokens[$i]['sniffProperty'] === 'exact'
            ) {
                $value = $tokens[$i]['sniffPropertyValue'];
                if ($value === 'true') {
                    $value = true;
                } else if ($value === 'false') {
                    $value = false;
                } else {
                    $value = (bool) $value;
                }

                $this->exact = $value;

                if ($this->debug === true) {
                    $line = $tokens[$i]['line'];
                    if ($this->exact === true) {
                        $value = 'true';
                    } else {
                        $value = 'false';
                    }

                    echo "* token $i on line $line set exact flag to $value *".PHP_EOL;
                }
            }//end if

            $checkToken  = null;
            $checkIndent = null;

            /*
                Don't check indents exactly between parenthesis or arrays as they
                tend to have custom rules, such as with multi-line function calls
                and control structure conditions.
            */

            $exact = $this->exact;

            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS
                && isset($tokens[$i]['parenthesis_closer']) === true
            ) {
                $disableExactStack[$tokens[$i]['parenthesis_closer']] = $tokens[$i]['parenthesis_closer'];
                $disableExactEnd = max($disableExactEnd, $tokens[$i]['parenthesis_closer']);
                if ($this->debug === true) {
                    $line = $tokens[$i]['line'];
                    $type = $tokens[$disableExactEnd]['type'];
                    echo "Opening parenthesis found on line $line".PHP_EOL;
                    echo "\t=> disabling exact indent checking until $disableExactEnd ($type)".PHP_EOL;
                }
            }

            if ($exact === true && $i < $disableExactEnd) {
                $exact = false;
            }

            // Detect line changes and figure out where the indent is.
            if ($tokens[$i]['column'] === 1) {
                $trimmed = ltrim($tokens[$i]['content']);
                if ($trimmed === '') {
                    if (isset($tokens[($i + 1)]) === true
                        && $tokens[$i]['line'] === $tokens[($i + 1)]['line']
                    ) {
                        $checkToken  = ($i + 1);
                        $tokenIndent = ($tokens[($i + 1)]['column'] - 1);
                    }
                } else {
                    $checkToken  = $i;
                    $tokenIndent = (strlen($tokens[$i]['content']) - strlen($trimmed));
                }
            }

            // Closing parenthesis should just be indented to at least
            // the same level as where they were opened (but can be more).
            if (($checkToken !== null
                && $tokens[$checkToken]['code'] === T_CLOSE_PARENTHESIS
                && isset($tokens[$checkToken]['parenthesis_opener']) === true)
                || ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS
                && isset($tokens[$i]['parenthesis_opener']) === true)
            ) {
                if ($checkToken !== null) {
                    $parenCloser = $checkToken;
                } else {
                    $parenCloser = $i;
                }

                if ($this->debug === true) {
                    $line = $tokens[$i]['line'];
                    echo "Closing parenthesis found on line $line".PHP_EOL;
                }

                $parenOpener = $tokens[$parenCloser]['parenthesis_opener'];
                if ($tokens[$parenCloser]['line'] !== $tokens[$parenOpener]['line']) {
                    $parens = 0;
                    if (isset($tokens[$parenCloser]['nested_parenthesis']) === true
                        && empty($tokens[$parenCloser]['nested_parenthesis']) === false
                    ) {
                        $parens = $tokens[$parenCloser]['nested_parenthesis'];
                        end($parens);
                        $parens = key($parens);
                        if ($this->debug === true) {
                            $line = $tokens[$parens]['line'];
                            echo "\t* token has nested parenthesis $parens on line $line *".PHP_EOL;
                        }
                    }

                    $condition = 0;
                    if (isset($tokens[$parenCloser]['conditions']) === true
                        && empty($tokens[$parenCloser]['conditions']) === false
                        && (isset($tokens[$parenCloser]['parenthesis_owner']) === false
                        || $parens > 0)
                    ) {
                        $condition = $tokens[$parenCloser]['conditions'];
                        end($condition);
                        $condition = key($condition);
                        if ($this->debug === true) {
                            $line = $tokens[$condition]['line'];
                            $type = $tokens[$condition]['type'];
                            echo "\t* token is inside condition $condition ($type) on line $line *".PHP_EOL;
                        }
                    }

                    if ($parens > $condition) {
                        if ($this->debug === true) {
                            echo "\t* using parenthesis *".PHP_EOL;
                        }

                        $parenOpener = $parens;
                        $condition   = 0;
                    } else if ($condition > 0) {
                        if ($this->debug === true) {
                            echo "\t* using condition *".PHP_EOL;
                        }

                        $parenOpener = $condition;
                        $parens      = 0;
                    }

                    $exact = false;

                    $lastOpenTagConditions = array_keys($tokens[$lastOpenTag]['conditions']);
                    $lastOpenTagCondition  = array_pop($lastOpenTagConditions);

                    if ($condition > 0 && $lastOpenTagCondition === $condition) {
                        if ($this->debug === true) {
                            echo "\t* open tag is inside condition; using open tag *".PHP_EOL;
                        }

                        $first = $phpcsFile->findFirstOnLine([T_WHITESPACE, T_INLINE_HTML], $lastOpenTag, true);
                        if ($this->debug === true) {
                            $line = $tokens[$first]['line'];
                            $type = $tokens[$first]['type'];
                            echo "\t* first token on line $line is $first ($type) *".PHP_EOL;
                        }

                        $checkIndent = ($tokens[$first]['column'] - 1);
                        if (isset($adjustments[$condition]) === true) {
                            $checkIndent += $adjustments[$condition];
                        }

                        $currentIndent = $checkIndent;

                        if ($this->debug === true) {
                            $type = $tokens[$lastOpenTag]['type'];
                            echo "\t=> checking indent of $checkIndent; main indent set to $currentIndent by token $lastOpenTag ($type)".PHP_EOL;
                        }
                    } else if ($condition > 0
                        && isset($tokens[$condition]['scope_opener']) === true
                        && isset($setIndents[$tokens[$condition]['scope_opener']]) === true
                    ) {
                        $checkIndent = $setIndents[$tokens[$condition]['scope_opener']];
                        if (isset($adjustments[$condition]) === true) {
                            $checkIndent += $adjustments[$condition];
                        }

                        $currentIndent = $checkIndent;

                        if ($this->debug === true) {
                            $type = $tokens[$condition]['type'];
                            echo "\t=> checking indent of $checkIndent; main indent set to $currentIndent by token $condition ($type)".PHP_EOL;
                        }
                    } else {
                        $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $parenOpener, true);

                        $checkIndent = ($tokens[$first]['column'] - 1);
                        if (isset($adjustments[$first]) === true) {
                            $checkIndent += $adjustments[$first];
                        }

                        if ($this->debug === true) {
                            $line = $tokens[$first]['line'];
                            $type = $tokens[$first]['type'];
                            echo "\t* first token on line $line is $first ($type) *".PHP_EOL;
                        }

                        if ($first === $tokens[$parenCloser]['parenthesis_opener']
                            && $tokens[($first - 1)]['line'] === $tokens[$first]['line']
                        ) {
                            // This is unlikely to be the start of the statement, so look
                            // back further to find it.
                            $first--;
                            if ($this->debug === true) {
                                $line = $tokens[$first]['line'];
                                $type = $tokens[$first]['type'];
                                echo "\t* first token is the parenthesis opener *".PHP_EOL;
                                echo "\t* amended first token is $first ($type) on line $line *".PHP_EOL;
                            }
                        }

                        $prev = $phpcsFile->findStartOfStatement($first, T_COMMA);
                        if ($prev !== $first) {
                            // This is not the start of the statement.
                            if ($this->debug === true) {
                                $line = $tokens[$prev]['line'];
                                $type = $tokens[$prev]['type'];
                                echo "\t* previous is $type on line $line *".PHP_EOL;
                            }

                            $first = $phpcsFile->findFirstOnLine([T_WHITESPACE, T_INLINE_HTML], $prev, true);
                            if ($first !== false) {
                                $prev  = $phpcsFile->findStartOfStatement($first, T_COMMA);
                                $first = $phpcsFile->findFirstOnLine([T_WHITESPACE, T_INLINE_HTML], $prev, true);
                            } else {
                                $first = $prev;
                            }

                            if ($this->debug === true) {
                                $line = $tokens[$first]['line'];
                                $type = $tokens[$first]['type'];
                                echo "\t* amended first token is $first ($type) on line $line *".PHP_EOL;
                            }
                        }//end if

                        if (isset($tokens[$first]['scope_closer']) === true
                            && $tokens[$first]['scope_closer'] === $first
                        ) {
                            if ($this->debug === true) {
                                echo "\t* first token is a scope closer *".PHP_EOL;
                            }

                            if (isset($tokens[$first]['scope_condition']) === true) {
                                $scopeCloser = $first;
                                $first       = $phpcsFile->findFirstOnLine(T_WHITESPACE, $tokens[$scopeCloser]['scope_condition'], true);

                                $currentIndent = ($tokens[$first]['column'] - 1);
                                if (isset($adjustments[$first]) === true) {
                                    $currentIndent += $adjustments[$first];
                                }

                                // Make sure it is divisible by our expected indent.
                                if ($tokens[$tokens[$scopeCloser]['scope_condition']]['code'] !== T_CLOSURE) {
                                    $currentIndent = (int) (ceil($currentIndent / $this->indent) * $this->indent);
                                }

                                $setIndents[$first] = $currentIndent;

                                if ($this->debug === true) {
                                    $type = $tokens[$first]['type'];
                                    echo "\t=> indent set to $currentIndent by token $first ($type)".PHP_EOL;
                                }
                            }//end if
                        } else {
                            // Don't force current indent to be divisible because there could be custom
                            // rules in place between parenthesis, such as with arrays.
                            $currentIndent = ($tokens[$first]['column'] - 1);
                            if (isset($adjustments[$first]) === true) {
                                $currentIndent += $adjustments[$first];
                            }

                            $setIndents[$first] = $currentIndent;

                            if ($this->debug === true) {
                                $type = $tokens[$first]['type'];
                                echo "\t=> checking indent of $checkIndent; main indent set to $currentIndent by token $first ($type)".PHP_EOL;
                            }
                        }//end if
                    }//end if
                } else if ($this->debug === true) {
                    echo "\t * ignoring single-line definition *".PHP_EOL;
                }//end if
            }//end if

            // Closing short array bracket should just be indented to at least
            // the same level as where it was opened (but can be more).
            if ($tokens[$i]['code'] === T_CLOSE_SHORT_ARRAY
                || ($checkToken !== null
                && $tokens[$checkToken]['code'] === T_CLOSE_SHORT_ARRAY)
            ) {
                if ($checkToken !== null) {
                    $arrayCloser = $checkToken;
                } else {
                    $arrayCloser = $i;
                }

                if ($this->debug === true) {
                    $line = $tokens[$arrayCloser]['line'];
                    echo "Closing short array bracket found on line $line".PHP_EOL;
                }

                $arrayOpener = $tokens[$arrayCloser]['bracket_opener'];
                if ($tokens[$arrayCloser]['line'] !== $tokens[$arrayOpener]['line']) {
                    $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $arrayOpener, true);
                    $exact = false;

                    if ($this->debug === true) {
                        $line = $tokens[$first]['line'];
                        $type = $tokens[$first]['type'];
                        echo "\t* first token on line $line is $first ($type) *".PHP_EOL;
                    }

                    if ($first === $tokens[$arrayCloser]['bracket_opener']) {
                        // This is unlikely to be the start of the statement, so look
                        // back further to find it.
                        $first--;
                    }

                    $prev = $phpcsFile->findStartOfStatement($first, [T_COMMA, T_DOUBLE_ARROW]);
                    if ($prev !== $first) {
                        // This is not the start of the statement.
                        if ($this->debug === true) {
                            $line = $tokens[$prev]['line'];
                            $type = $tokens[$prev]['type'];
                            echo "\t* previous is $type on line $line *".PHP_EOL;
                        }

                        $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $prev, true);
                        $prev  = $phpcsFile->findStartOfStatement($first, [T_COMMA, T_DOUBLE_ARROW]);
                        $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $prev, true);
                        if ($this->debug === true) {
                            $line = $tokens[$first]['line'];
                            $type = $tokens[$first]['type'];
                            echo "\t* amended first token is $first ($type) on line $line *".PHP_EOL;
                        }
                    } else if ($tokens[$first]['code'] === T_WHITESPACE) {
                        $first = $phpcsFile->findNext(T_WHITESPACE, ($first + 1), null, true);
                    }

                    $checkIndent = ($tokens[$first]['column'] - 1);
                    if (isset($adjustments[$first]) === true) {
                        $checkIndent += $adjustments[$first];
                    }

                    if (isset($tokens[$first]['scope_closer']) === true
                        && $tokens[$first]['scope_closer'] === $first
                    ) {
                        // The first token is a scope closer and would have already
                        // been processed and set the indent level correctly, so
                        // don't adjust it again.
                        if ($this->debug === true) {
                            echo "\t* first token is a scope closer; ignoring closing short array bracket *".PHP_EOL;
                        }

                        if (isset($setIndents[$first]) === true) {
                            $currentIndent = $setIndents[$first];
                            if ($this->debug === true) {
                                echo "\t=> indent reset to $currentIndent".PHP_EOL;
                            }
                        }
                    } else {
                        // Don't force current indent to be divisible because there could be custom
                        // rules in place for arrays.
                        $currentIndent = ($tokens[$first]['column'] - 1);
                        if (isset($adjustments[$first]) === true) {
                            $currentIndent += $adjustments[$first];
                        }

                        $setIndents[$first] = $currentIndent;

                        if ($this->debug === true) {
                            $type = $tokens[$first]['type'];
                            echo "\t=> checking indent of $checkIndent; main indent set to $currentIndent by token $first ($type)".PHP_EOL;
                        }
                    }//end if
                } else if ($this->debug === true) {
                    echo "\t * ignoring single-line definition *".PHP_EOL;
                }//end if
            }//end if

            // Adjust lines within scopes while auto-fixing.
            if ($checkToken !== null
                && $exact === false
                && (empty($tokens[$checkToken]['conditions']) === false
                || (isset($tokens[$checkToken]['scope_opener']) === true
                && $tokens[$checkToken]['scope_opener'] === $checkToken))
            ) {
                if (empty($tokens[$checkToken]['conditions']) === false) {
                    $condition = $tokens[$checkToken]['conditions'];
                    end($condition);
                    $condition = key($condition);
                } else {
                    $condition = $tokens[$checkToken]['scope_condition'];
                }

                $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $condition, true);

                if (isset($adjustments[$first]) === true
                    && (($adjustments[$first] < 0 && $tokenIndent > $currentIndent)
                    || ($adjustments[$first] > 0 && $tokenIndent < $currentIndent))
                ) {
                    $length = ($tokenIndent + $adjustments[$first]);

                    // When fixing, we're going to adjust the indent of this line
                    // here automatically, so use this new padding value when
                    // comparing the expected padding to the actual padding.
                    if ($phpcsFile->fixer->enabled === true) {
                        $tokenIndent = $length;
                        $this->adjustIndent($phpcsFile, $checkToken, $length, $adjustments[$first]);
                    }

                    if ($this->debug === true) {
                        $line = $tokens[$checkToken]['line'];
                        $type = $tokens[$checkToken]['type'];
                        echo "Indent adjusted to $length for $type on line $line".PHP_EOL;
                    }

                    $adjustments[$checkToken] = $adjustments[$first];

                    if ($this->debug === true) {
                        $line = $tokens[$checkToken]['line'];
                        $type = $tokens[$checkToken]['type'];
                        echo "\t=> add adjustment of ".$adjustments[$checkToken]." for token $checkToken ($type) on line $line".PHP_EOL;
                    }
                }//end if
            }//end if

            // Scope closers reset the required indent to the same level as the opening condition.
            if (($checkToken !== null
                && (isset($openScopes[$checkToken]) === true
                || (isset($tokens[$checkToken]['scope_condition']) === true
                && isset($tokens[$checkToken]['scope_closer']) === true
                && $tokens[$checkToken]['scope_closer'] === $checkToken
                && $tokens[$checkToken]['line'] !== $tokens[$tokens[$checkToken]['scope_opener']]['line'])))
                || ($checkToken === null
                && isset($openScopes[$i]) === true)
            ) {
                if ($this->debug === true) {
                    if ($checkToken === null) {
                        $type = $tokens[$tokens[$i]['scope_condition']]['type'];
                        $line = $tokens[$i]['line'];
                    } else {
                        $type = $tokens[$tokens[$checkToken]['scope_condition']]['type'];
                        $line = $tokens[$checkToken]['line'];
                    }

                    echo "Close scope ($type) on line $line".PHP_EOL;
                }

                $scopeCloser = $checkToken;
                if ($scopeCloser === null) {
                    $scopeCloser = $i;
                }

                $conditionToken = array_pop($openScopes);
                if ($this->debug === true) {
                    $line = $tokens[$conditionToken]['line'];
                    $type = $tokens[$conditionToken]['type'];
                    echo "\t=> removed open scope $conditionToken ($type) on line $line".PHP_EOL;
                }

                if (isset($tokens[$scopeCloser]['scope_condition']) === true) {
                    $first = $phpcsFile->findFirstOnLine([T_WHITESPACE, T_INLINE_HTML], $tokens[$scopeCloser]['scope_condition'], true);
                    if ($this->debug === true) {
                        $line = $tokens[$first]['line'];
                        $type = $tokens[$first]['type'];
                        echo "\t* first token is $first ($type) on line $line *".PHP_EOL;
                    }

                    while ($tokens[$first]['code'] === T_CONSTANT_ENCAPSED_STRING
                        && $tokens[($first - 1)]['code'] === T_CONSTANT_ENCAPSED_STRING
                    ) {
                        $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, ($first - 1), true);
                        if ($this->debug === true) {
                            $line = $tokens[$first]['line'];
                            $type = $tokens[$first]['type'];
                            echo "\t* found multi-line string; amended first token is $first ($type) on line $line *".PHP_EOL;
                        }
                    }

                    $currentIndent = ($tokens[$first]['column'] - 1);
                    if (isset($adjustments[$first]) === true) {
                        $currentIndent += $adjustments[$first];
                    }

                    $setIndents[$scopeCloser] = $currentIndent;

                    if ($this->debug === true) {
                        $type = $tokens[$scopeCloser]['type'];
                        echo "\t=> indent set to $currentIndent by token $scopeCloser ($type)".PHP_EOL;
                    }

                    // We only check the indent of scope closers if they are
                    // curly braces because other constructs tend to have different rules.
                    if ($tokens[$scopeCloser]['code'] === T_CLOSE_CURLY_BRACKET) {
                        $exact = true;
                    } else {
                        $checkToken = null;
                    }
                }//end if
            }//end if

            // Handle scope for JS object notation.
            if ($phpcsFile->tokenizerType === 'JS'
                && (($checkToken !== null
                && $tokens[$checkToken]['code'] === T_CLOSE_OBJECT
                && $tokens[$checkToken]['line'] !== $tokens[$tokens[$checkToken]['bracket_opener']]['line'])
                || ($checkToken === null
                && $tokens[$i]['code'] === T_CLOSE_OBJECT
                && $tokens[$i]['line'] !== $tokens[$tokens[$i]['bracket_opener']]['line']))
            ) {
                if ($this->debug === true) {
                    $line = $tokens[$i]['line'];
                    echo "Close JS object on line $line".PHP_EOL;
                }

                $scopeCloser = $checkToken;
                if ($scopeCloser === null) {
                    $scopeCloser = $i;
                } else {
                    $conditionToken = array_pop($openScopes);
                    if ($this->debug === true) {
                        $line = $tokens[$conditionToken]['line'];
                        $type = $tokens[$conditionToken]['type'];
                        echo "\t=> removed open scope $conditionToken ($type) on line $line".PHP_EOL;
                    }
                }

                $parens = 0;
                if (isset($tokens[$scopeCloser]['nested_parenthesis']) === true
                    && empty($tokens[$scopeCloser]['nested_parenthesis']) === false
                ) {
                    $parens = $tokens[$scopeCloser]['nested_parenthesis'];
                    end($parens);
                    $parens = key($parens);
                    if ($this->debug === true) {
                        $line = $tokens[$parens]['line'];
                        echo "\t* token has nested parenthesis $parens on line $line *".PHP_EOL;
                    }
                }

                $condition = 0;
                if (isset($tokens[$scopeCloser]['conditions']) === true
                    && empty($tokens[$scopeCloser]['conditions']) === false
                ) {
                    $condition = $tokens[$scopeCloser]['conditions'];
                    end($condition);
                    $condition = key($condition);
                    if ($this->debug === true) {
                        $line = $tokens[$condition]['line'];
                        $type = $tokens[$condition]['type'];
                        echo "\t* token is inside condition $condition ($type) on line $line *".PHP_EOL;
                    }
                }

                if ($parens > $condition) {
                    if ($this->debug === true) {
                        echo "\t* using parenthesis *".PHP_EOL;
                    }

                    $first     = $phpcsFile->findFirstOnLine(T_WHITESPACE, $parens, true);
                    $condition = 0;
                } else if ($condition > 0) {
                    if ($this->debug === true) {
                        echo "\t* using condition *".PHP_EOL;
                    }

                    $first  = $phpcsFile->findFirstOnLine(T_WHITESPACE, $condition, true);
                    $parens = 0;
                } else {
                    if ($this->debug === true) {
                        $line = $tokens[$tokens[$scopeCloser]['bracket_opener']]['line'];
                        echo "\t* token is not in parenthesis or condition; using opener on line $line *".PHP_EOL;
                    }

                    $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $tokens[$scopeCloser]['bracket_opener'], true);
                }//end if

                $currentIndent = ($tokens[$first]['column'] - 1);
                if (isset($adjustments[$first]) === true) {
                    $currentIndent += $adjustments[$first];
                }

                if ($parens > 0 || $condition > 0) {
                    $checkIndent = ($tokens[$first]['column'] - 1);
                    if (isset($adjustments[$first]) === true) {
                        $checkIndent += $adjustments[$first];
                    }

                    if ($condition > 0) {
                        $checkIndent   += $this->indent;
                        $currentIndent += $this->indent;
                        $exact          = true;
                    }
                } else {
                    $checkIndent = $currentIndent;
                }

                // Make sure it is divisible by our expected indent.
                $currentIndent      = (int) (ceil($currentIndent / $this->indent) * $this->indent);
                $checkIndent        = (int) (ceil($checkIndent / $this->indent) * $this->indent);
                $setIndents[$first] = $currentIndent;

                if ($this->debug === true) {
                    $type = $tokens[$first]['type'];
                    echo "\t=> checking indent of $checkIndent; main indent set to $currentIndent by token $first ($type)".PHP_EOL;
                }
            }//end if

            if ($checkToken !== null
                && isset(Tokens::$scopeOpeners[$tokens[$checkToken]['code']]) === true
                && in_array($tokens[$checkToken]['code'], $this->nonIndentingScopes, true) === false
                && isset($tokens[$checkToken]['scope_opener']) === true
            ) {
                $exact = true;

                if ($disableExactEnd > $checkToken) {
                    foreach ($disableExactStack as $disableExactStackEnd) {
                        if ($disableExactStackEnd < $checkToken) {
                            continue;
                        }

                        if ($tokens[$checkToken]['conditions'] === $tokens[$disableExactStackEnd]['conditions']) {
                            $exact = false;
                            break;
                        }
                    }
                }

                $lastOpener = null;
                if (empty($openScopes) === false) {
                    end($openScopes);
                    $lastOpener = current($openScopes);
                }

                // A scope opener that shares a closer with another token (like multiple
                // CASEs using the same BREAK) needs to reduce the indent level so its
                // indent is checked correctly. It will then increase the indent again
                // (as all openers do) after being checked.
                if ($lastOpener !== null
                    && isset($tokens[$lastOpener]['scope_closer']) === true
                    && $tokens[$lastOpener]['level'] === $tokens[$checkToken]['level']
                    && $tokens[$lastOpener]['scope_closer'] === $tokens[$checkToken]['scope_closer']
                ) {
                    $currentIndent          -= $this->indent;
                    $setIndents[$lastOpener] = $currentIndent;
                    if ($this->debug === true) {
                        $line = $tokens[$i]['line'];
                        $type = $tokens[$lastOpener]['type'];
                        echo "Shared closer found on line $line".PHP_EOL;
                        echo "\t=> indent set to $currentIndent by token $lastOpener ($type)".PHP_EOL;
                    }
                }

                if ($tokens[$checkToken]['code'] === T_CLOSURE
                    && $tokenIndent > $currentIndent
                ) {
                    // The opener is indented more than needed, which is fine.
                    // But just check that it is divisible by our expected indent.
                    $checkIndent = (int) (ceil($tokenIndent / $this->indent) * $this->indent);
                    $exact       = false;

                    if ($this->debug === true) {
                        $line = $tokens[$i]['line'];
                        echo "Closure found on line $line".PHP_EOL;
                        echo "\t=> checking indent of $checkIndent; main indent remains at $currentIndent".PHP_EOL;
                    }
                }
            }//end if

            // Method prefix indentation has to be exact or else it will break
            // the rest of the function declaration, and potentially future ones.
            if ($checkToken !== null
                && isset(Tokens::$methodPrefixes[$tokens[$checkToken]['code']]) === true
                && $tokens[($checkToken + 1)]['code'] !== T_DOUBLE_COLON
            ) {
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($checkToken + 1), null, true);
                if ($next === false
                    || ($tokens[$next]['code'] !== T_CLOSURE
                    && $tokens[$next]['code'] !== T_VARIABLE
                    && $tokens[$next]['code'] !== T_FN)
                ) {
                    $isMethodPrefix = true;
                    if (isset($tokens[$checkToken]['nested_parenthesis']) === true) {
                        $parenthesis = array_keys($tokens[$checkToken]['nested_parenthesis']);
                        $deepestOpen = array_pop($parenthesis);
                        if (isset($tokens[$deepestOpen]['parenthesis_owner']) === true
                            && $tokens[$tokens[$deepestOpen]['parenthesis_owner']]['code'] === T_FUNCTION
                        ) {
                            // This is constructor property promotion and not a method prefix.
                            $isMethodPrefix = false;
                        }
                    }

                    if ($isMethodPrefix === true) {
                        if ($this->debug === true) {
                            $line = $tokens[$checkToken]['line'];
                            $type = $tokens[$checkToken]['type'];
                            echo "\t* method prefix ($type) found on line $line; indent set to exact *".PHP_EOL;
                        }

                        $exact = true;
                    }
                }//end if
            }//end if

            // JS property indentation has to be exact or else if will break
            // things like function and object indentation.
            if ($checkToken !== null && $tokens[$checkToken]['code'] === T_PROPERTY) {
                $exact = true;
            }

            // Open PHP tags needs to be indented to exact column positions
            // so they don't cause problems with indent checks for the code
            // within them, but they don't need to line up with the current indent
            // in most cases.
            if ($checkToken !== null
                && ($tokens[$checkToken]['code'] === T_OPEN_TAG
                || $tokens[$checkToken]['code'] === T_OPEN_TAG_WITH_ECHO)
            ) {
                $checkIndent = ($tokens[$checkToken]['column'] - 1);

                // If we are re-opening a block that was closed in the same
                // scope as us, then reset the indent back to what the scope opener
                // set instead of using whatever indent this open tag has set.
                if (empty($tokens[$checkToken]['conditions']) === false) {
                    $close = $phpcsFile->findPrevious(T_CLOSE_TAG, ($checkToken - 1));
                    if ($close !== false
                        && $tokens[$checkToken]['conditions'] === $tokens[$close]['conditions']
                    ) {
                        $conditions    = array_keys($tokens[$checkToken]['conditions']);
                        $lastCondition = array_pop($conditions);
                        $lastOpener    = $tokens[$lastCondition]['scope_opener'];
                        $lastCloser    = $tokens[$lastCondition]['scope_closer'];
                        if ($tokens[$lastCloser]['line'] !== $tokens[$checkToken]['line']
                            && isset($setIndents[$lastOpener]) === true
                        ) {
                            $checkIndent = $setIndents[$lastOpener];
                        }
                    }
                }
            }//end if

            // Close tags needs to be indented to exact column positions.
            if ($checkToken !== null && $tokens[$checkToken]['code'] === T_CLOSE_TAG) {
                $exact       = true;
                $checkIndent = $currentIndent;
                $checkIndent = (int) (ceil($checkIndent / $this->indent) * $this->indent);
            }

            // Special case for ELSE statements that are not on the same
            // line as the previous IF statements closing brace. They still need
            // to have the same indent or it will break code after the block.
            if ($checkToken !== null && $tokens[$checkToken]['code'] === T_ELSE) {
                $exact = true;
            }

            // Don't perform strict checking on chained method calls since they
            // are often covered by custom rules.
            if ($checkToken !== null
                && ($tokens[$checkToken]['code'] === T_OBJECT_OPERATOR
                || $tokens[$checkToken]['code'] === T_NULLSAFE_OBJECT_OPERATOR)
                && $exact === true
            ) {
                $exact = false;
            }

            if ($checkIndent === null) {
                $checkIndent = $currentIndent;
            }

            /*
                The indent of the line is checked by the following IF block.

                Up until now, we've just been figuring out what the indent
                of this line should be.

                After this IF block, we adjust the indent again for
                the checking of future lines
            */

            if ($checkToken !== null
                && isset($this->ignoreIndentation[$tokens[$checkToken]['code']]) === false
                && (($tokenIndent !== $checkIndent && $exact === true)
                || ($tokenIndent < $checkIndent && $exact === false))
            ) {
                $type  = 'IncorrectExact';
                $error = 'Line indented incorrectly; expected ';
                if ($exact === false) {
                    $error .= 'at least ';
                    $type   = 'Incorrect';
                }

                if ($this->tabIndent === true) {
                    $expectedTabs = floor($checkIndent / $this->tabWidth);
                    $foundTabs    = floor($tokenIndent / $this->tabWidth);
                    $foundSpaces  = ($tokenIndent - ($foundTabs * $this->tabWidth));
                    if ($foundSpaces > 0) {
                        if ($foundTabs > 0) {
                            $error .= '%s tabs, found %s tabs and %s spaces';
                            $data   = [
                                $expectedTabs,
                                $foundTabs,
                                $foundSpaces,
                            ];
                        } else {
                            $error .= '%s tabs, found %s spaces';
                            $data   = [
                                $expectedTabs,
                                $foundSpaces,
                            ];
                        }
                    } else {
                        $error .= '%s tabs, found %s';
                        $data   = [
                            $expectedTabs,
                            $foundTabs,
                        ];
                    }//end if
                } else {
                    $error .= '%s spaces, found %s';
                    $data   = [
                        $checkIndent,
                        $tokenIndent,
                    ];
                }//end if

                if ($this->debug === true) {
                    $line    = $tokens[$checkToken]['line'];
                    $message = vsprintf($error, $data);
                    echo "[Line $line] $message".PHP_EOL;
                }

                // Assume the change would be applied and continue
                // checking indents under this assumption. This gives more
                // technically accurate error messages.
                $adjustments[$checkToken] = ($checkIndent - $tokenIndent);

                $fix = $phpcsFile->addFixableError($error, $checkToken, $type, $data);
                if ($fix === true || $this->debug === true) {
                    $accepted = $this->adjustIndent($phpcsFile, $checkToken, $checkIndent, ($checkIndent - $tokenIndent));

                    if ($accepted === true && $this->debug === true) {
                        $line = $tokens[$checkToken]['line'];
                        $type = $tokens[$checkToken]['type'];
                        echo "\t=> add adjustment of ".$adjustments[$checkToken]." for token $checkToken ($type) on line $line".PHP_EOL;
                    }
                }
            }//end if

            if ($checkToken !== null) {
                $i = $checkToken;
            }

            // Don't check indents exactly between arrays as they tend to have custom rules.
            if ($tokens[$i]['code'] === T_OPEN_SHORT_ARRAY) {
                $disableExactStack[$tokens[$i]['bracket_closer']] = $tokens[$i]['bracket_closer'];
                $disableExactEnd = max($disableExactEnd, $tokens[$i]['bracket_closer']);
                if ($this->debug === true) {
                    $line    = $tokens[$i]['line'];
                    $type    = $tokens[$disableExactEnd]['type'];
                    $endLine = $tokens[$disableExactEnd]['line'];
                    echo "Opening short array bracket found on line $line".PHP_EOL;
                    if ($disableExactEnd === $tokens[$i]['bracket_closer']) {
                        echo "\t=> disabling exact indent checking until $disableExactEnd ($type) on line $endLine".PHP_EOL;
                    } else {
                        echo "\t=> continuing to disable exact indent checking until $disableExactEnd ($type) on line $endLine".PHP_EOL;
                    }
                }
            }

            // Completely skip here/now docs as the indent is a part of the
            // content itself.
            if ($tokens[$i]['code'] === T_START_HEREDOC
                || $tokens[$i]['code'] === T_START_NOWDOC
            ) {
                if ($this->debug === true) {
                    $line = $tokens[$i]['line'];
                    echo "Here/nowdoc found on line $line".PHP_EOL;
                }

                $i    = $phpcsFile->findNext([T_END_HEREDOC, T_END_NOWDOC], ($i + 1));
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), null, true);
                if ($tokens[$next]['code'] === T_COMMA) {
                    $i = $next;
                }

                if ($this->debug === true) {
                    $line = $tokens[$i]['line'];
                    $type = $tokens[$i]['type'];
                    echo "\t* skipping to token $i ($type) on line $line *".PHP_EOL;
                }

                continue;
            }//end if

            // Completely skip multi-line strings as the indent is a part of the
            // content itself.
            if ($tokens[$i]['code'] === T_CONSTANT_ENCAPSED_STRING
                || $tokens[$i]['code'] === T_DOUBLE_QUOTED_STRING
            ) {
                $i = $phpcsFile->findNext($tokens[$i]['code'], ($i + 1), null, true);
                $i--;
                continue;
            }

            // Completely skip doc comments as they tend to have complex
            // indentation rules.
            if ($tokens[$i]['code'] === T_DOC_COMMENT_OPEN_TAG) {
                $i = $tokens[$i]['comment_closer'];
                continue;
            }

            // Open tags reset the indent level.
            if ($tokens[$i]['code'] === T_OPEN_TAG
                || $tokens[$i]['code'] === T_OPEN_TAG_WITH_ECHO
            ) {
                if ($this->debug === true) {
                    $line = $tokens[$i]['line'];
                    echo "Open PHP tag found on line $line".PHP_EOL;
                }

                if ($checkToken === null) {
                    $first         = $phpcsFile->findFirstOnLine(T_WHITESPACE, $i, true);
                    $currentIndent = (strlen($tokens[$first]['content']) - strlen(ltrim($tokens[$first]['content'])));
                } else {
                    $currentIndent = ($tokens[$i]['column'] - 1);
                }

                $lastOpenTag = $i;

                if (isset($adjustments[$i]) === true) {
                    $currentIndent += $adjustments[$i];
                }

                // Make sure it is divisible by our expected indent.
                $currentIndent  = (int) (ceil($currentIndent / $this->indent) * $this->indent);
                $setIndents[$i] = $currentIndent;

                if ($this->debug === true) {
                    $type = $tokens[$i]['type'];
                    echo "\t=> indent set to $currentIndent by token $i ($type)".PHP_EOL;
                }

                continue;
            }//end if

            // Close tags reset the indent level, unless they are closing a tag
            // opened on the same line.
            if ($tokens[$i]['code'] === T_CLOSE_TAG) {
                if ($this->debug === true) {
                    $line = $tokens[$i]['line'];
                    echo "Close PHP tag found on line $line".PHP_EOL;
                }

                if ($tokens[$lastOpenTag]['line'] !== $tokens[$i]['line']) {
                    $currentIndent = ($tokens[$i]['column'] - 1);
                    $lastCloseTag  = $i;
                } else {
                    if ($lastCloseTag === null) {
                        $currentIndent = 0;
                    } else {
                        $currentIndent = ($tokens[$lastCloseTag]['column'] - 1);
                    }
                }

                if (isset($adjustments[$i]) === true) {
                    $currentIndent += $adjustments[$i];
                }

                // Make sure it is divisible by our expected indent.
                $currentIndent  = (int) (ceil($currentIndent / $this->indent) * $this->indent);
                $setIndents[$i] = $currentIndent;

                if ($this->debug === true) {
                    $type = $tokens[$i]['type'];
                    echo "\t=> indent set to $currentIndent by token $i ($type)".PHP_EOL;
                }

                continue;
            }//end if

            // Anon classes and functions set the indent based on their own indent level.
            if ($tokens[$i]['code'] === T_CLOSURE || $tokens[$i]['code'] === T_ANON_CLASS) {
                $closer = $tokens[$i]['scope_closer'];
                if ($tokens[$i]['line'] === $tokens[$closer]['line']) {
                    if ($this->debug === true) {
                        $type = str_replace('_', ' ', strtolower(substr($tokens[$i]['type'], 2)));
                        $line = $tokens[$i]['line'];
                        echo "* ignoring single-line $type on line $line *".PHP_EOL;
                    }

                    $i = $closer;
                    continue;
                }

                if ($this->debug === true) {
                    $type = str_replace('_', ' ', strtolower(substr($tokens[$i]['type'], 2)));
                    $line = $tokens[$i]['line'];
                    echo "Open $type on line $line".PHP_EOL;
                }

                $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $i, true);
                if ($this->debug === true) {
                    $line = $tokens[$first]['line'];
                    $type = $tokens[$first]['type'];
                    echo "\t* first token is $first ($type) on line $line *".PHP_EOL;
                }

                while ($tokens[$first]['code'] === T_CONSTANT_ENCAPSED_STRING
                    && $tokens[($first - 1)]['code'] === T_CONSTANT_ENCAPSED_STRING
                ) {
                    $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, ($first - 1), true);
                    if ($this->debug === true) {
                        $line = $tokens[$first]['line'];
                        $type = $tokens[$first]['type'];
                        echo "\t* found multi-line string; amended first token is $first ($type) on line $line *".PHP_EOL;
                    }
                }

                $currentIndent = (($tokens[$first]['column'] - 1) + $this->indent);
                $openScopes[$tokens[$i]['scope_closer']] = $tokens[$i]['scope_condition'];
                if ($this->debug === true) {
                    $closerToken    = $tokens[$i]['scope_closer'];
                    $closerLine     = $tokens[$closerToken]['line'];
                    $closerType     = $tokens[$closerToken]['type'];
                    $conditionToken = $tokens[$i]['scope_condition'];
                    $conditionLine  = $tokens[$conditionToken]['line'];
                    $conditionType  = $tokens[$conditionToken]['type'];
                    echo "\t=> added open scope $closerToken ($closerType) on line $closerLine, pointing to condition $conditionToken ($conditionType) on line $conditionLine".PHP_EOL;
                }

                if (isset($adjustments[$first]) === true) {
                    $currentIndent += $adjustments[$first];
                }

                // Make sure it is divisible by our expected indent.
                $currentIndent = (int) (floor($currentIndent / $this->indent) * $this->indent);
                $i = $tokens[$i]['scope_opener'];
                $setIndents[$i] = $currentIndent;

                if ($this->debug === true) {
                    $type = $tokens[$i]['type'];
                    echo "\t=> indent set to $currentIndent by token $i ($type)".PHP_EOL;
                }

                continue;
            }//end if

            // Scope openers increase the indent level.
            if (isset($tokens[$i]['scope_condition']) === true
                && isset($tokens[$i]['scope_opener']) === true
                && $tokens[$i]['scope_opener'] === $i
            ) {
                $closer = $tokens[$i]['scope_closer'];
                if ($tokens[$i]['line'] === $tokens[$closer]['line']) {
                    if ($this->debug === true) {
                        $line = $tokens[$i]['line'];
                        $type = $tokens[$i]['type'];
                        echo "* ignoring single-line $type on line $line *".PHP_EOL;
                    }

                    $i = $closer;
                    continue;
                }

                $condition = $tokens[$tokens[$i]['scope_condition']]['code'];
                if ($condition === T_FN) {
                    if ($this->debug === true) {
                        $line = $tokens[$tokens[$i]['scope_condition']]['line'];
                        echo "* ignoring arrow function on line $line *".PHP_EOL;
                    }

                    $i = $closer;
                    continue;
                }

                if (isset(Tokens::$scopeOpeners[$condition]) === true
                    && in_array($condition, $this->nonIndentingScopes, true) === false
                ) {
                    if ($this->debug === true) {
                        $line = $tokens[$i]['line'];
                        $type = $tokens[$tokens[$i]['scope_condition']]['type'];
                        echo "Open scope ($type) on line $line".PHP_EOL;
                    }

                    $currentIndent += $this->indent;
                    $setIndents[$i] = $currentIndent;
                    $openScopes[$tokens[$i]['scope_closer']] = $tokens[$i]['scope_condition'];
                    if ($this->debug === true) {
                        $closerToken    = $tokens[$i]['scope_closer'];
                        $closerLine     = $tokens[$closerToken]['line'];
                        $closerType     = $tokens[$closerToken]['type'];
                        $conditionToken = $tokens[$i]['scope_condition'];
                        $conditionLine  = $tokens[$conditionToken]['line'];
                        $conditionType  = $tokens[$conditionToken]['type'];
                        echo "\t=> added open scope $closerToken ($closerType) on line $closerLine, pointing to condition $conditionToken ($conditionType) on line $conditionLine".PHP_EOL;
                    }

                    if ($this->debug === true) {
                        $type = $tokens[$i]['type'];
                        echo "\t=> indent set to $currentIndent by token $i ($type)".PHP_EOL;
                    }

                    continue;
                }//end if
            }//end if

            // JS objects set the indent level.
            if ($phpcsFile->tokenizerType === 'JS'
                && $tokens[$i]['code'] === T_OBJECT
            ) {
                $closer = $tokens[$i]['bracket_closer'];
                if ($tokens[$i]['line'] === $tokens[$closer]['line']) {
                    if ($this->debug === true) {
                        $line = $tokens[$i]['line'];
                        echo "* ignoring single-line JS object on line $line *".PHP_EOL;
                    }

                    $i = $closer;
                    continue;
                }

                if ($this->debug === true) {
                    $line = $tokens[$i]['line'];
                    echo "Open JS object on line $line".PHP_EOL;
                }

                $first         = $phpcsFile->findFirstOnLine(T_WHITESPACE, $i, true);
                $currentIndent = (($tokens[$first]['column'] - 1) + $this->indent);
                if (isset($adjustments[$first]) === true) {
                    $currentIndent += $adjustments[$first];
                }

                // Make sure it is divisible by our expected indent.
                $currentIndent      = (int) (ceil($currentIndent / $this->indent) * $this->indent);
                $setIndents[$first] = $currentIndent;

                if ($this->debug === true) {
                    $type = $tokens[$first]['type'];
                    echo "\t=> indent set to $currentIndent by token $first ($type)".PHP_EOL;
                }

                continue;
            }//end if

            // Closing an anon class, closure, or match.
            // Each may be returned, which can confuse control structures that
            // use return as a closer, like CASE statements.
            if (isset($tokens[$i]['scope_condition']) === true
                && $tokens[$i]['scope_closer'] === $i
                && ($tokens[$tokens[$i]['scope_condition']]['code'] === T_CLOSURE
                || $tokens[$tokens[$i]['scope_condition']]['code'] === T_ANON_CLASS
                || $tokens[$tokens[$i]['scope_condition']]['code'] === T_MATCH)
            ) {
                if ($this->debug === true) {
                    $type = str_replace('_', ' ', strtolower(substr($tokens[$tokens[$i]['scope_condition']]['type'], 2)));
                    $line = $tokens[$i]['line'];
                    echo "Close $type on line $line".PHP_EOL;
                }

                $prev = false;

                $object = 0;
                if ($phpcsFile->tokenizerType === 'JS') {
                    $conditions = $tokens[$i]['conditions'];
                    krsort($conditions, SORT_NUMERIC);
                    foreach ($conditions as $token => $condition) {
                        if ($condition === T_OBJECT) {
                            $object = $token;
                            break;
                        }
                    }

                    if ($this->debug === true && $object !== 0) {
                        $line = $tokens[$object]['line'];
                        echo "\t* token is inside JS object $object on line $line *".PHP_EOL;
                    }
                }

                $parens = 0;
                if (isset($tokens[$i]['nested_parenthesis']) === true
                    && empty($tokens[$i]['nested_parenthesis']) === false
                ) {
                    $parens = $tokens[$i]['nested_parenthesis'];
                    end($parens);
                    $parens = key($parens);
                    if ($this->debug === true) {
                        $line = $tokens[$parens]['line'];
                        echo "\t* token has nested parenthesis $parens on line $line *".PHP_EOL;
                    }
                }

                $condition = 0;
                if (isset($tokens[$i]['conditions']) === true
                    && empty($tokens[$i]['conditions']) === false
                ) {
                    $condition = $tokens[$i]['conditions'];
                    end($condition);
                    $condition = key($condition);
                    if ($this->debug === true) {
                        $line = $tokens[$condition]['line'];
                        $type = $tokens[$condition]['type'];
                        echo "\t* token is inside condition $condition ($type) on line $line *".PHP_EOL;
                    }
                }

                if ($parens > $object && $parens > $condition) {
                    if ($this->debug === true) {
                        echo "\t* using parenthesis *".PHP_EOL;
                    }

                    $prev      = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($parens - 1), null, true);
                    $object    = 0;
                    $condition = 0;
                } else if ($object > 0 && $object >= $condition) {
                    if ($this->debug === true) {
                        echo "\t* using object *".PHP_EOL;
                    }

                    $prev      = $object;
                    $parens    = 0;
                    $condition = 0;
                } else if ($condition > 0) {
                    if ($this->debug === true) {
                        echo "\t* using condition *".PHP_EOL;
                    }

                    $prev   = $condition;
                    $object = 0;
                    $parens = 0;
                }//end if

                if ($prev === false) {
                    $prev = $phpcsFile->findPrevious([T_EQUAL, T_RETURN], ($tokens[$i]['scope_condition'] - 1), null, false, null, true);
                    if ($prev === false) {
                        $prev = $i;
                        if ($this->debug === true) {
                            echo "\t* could not find a previous T_EQUAL or T_RETURN token; will use current token *".PHP_EOL;
                        }
                    }
                }

                if ($this->debug === true) {
                    $line = $tokens[$prev]['line'];
                    $type = $tokens[$prev]['type'];
                    echo "\t* previous token is $type on line $line *".PHP_EOL;
                }

                $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $prev, true);
                if ($this->debug === true) {
                    $line = $tokens[$first]['line'];
                    $type = $tokens[$first]['type'];
                    echo "\t* first token on line $line is $first ($type) *".PHP_EOL;
                }

                $prev = $phpcsFile->findStartOfStatement($first);
                if ($prev !== $first) {
                    // This is not the start of the statement.
                    if ($this->debug === true) {
                        $line = $tokens[$prev]['line'];
                        $type = $tokens[$prev]['type'];
                        echo "\t* amended previous is $type on line $line *".PHP_EOL;
                    }

                    $first = $phpcsFile->findFirstOnLine(T_WHITESPACE, $prev, true);
                    if ($this->debug === true) {
                        $line = $tokens[$first]['line'];
                        $type = $tokens[$first]['type'];
                        echo "\t* amended first token is $first ($type) on line $line *".PHP_EOL;
                    }
                }

                $currentIndent = ($tokens[$first]['column'] - 1);
                if ($object > 0 || $condition > 0) {
                    $currentIndent += $this->indent;
                }

                if (isset($tokens[$first]['scope_closer']) === true
                    && $tokens[$first]['scope_closer'] === $first
                ) {
                    if ($this->debug === true) {
                        echo "\t* first token is a scope closer *".PHP_EOL;
                    }

                    if ($condition === 0 || $tokens[$condition]['scope_opener'] < $first) {
                        $currentIndent = $setIndents[$first];
                    } else if ($this->debug === true) {
                        echo "\t* ignoring scope closer *".PHP_EOL;
                    }
                }

                // Make sure it is divisible by our expected indent.
                $currentIndent      = (int) (ceil($currentIndent / $this->indent) * $this->indent);
                $setIndents[$first] = $currentIndent;

                if ($this->debug === true) {
                    $type = $tokens[$first]['type'];
                    echo "\t=> indent set to $currentIndent by token $first ($type)".PHP_EOL;
                }
            }//end if
        }//end for

        // Don't process the rest of the file.
        return $phpcsFile->numTokens;

    }//end process()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile All the tokens found in the document.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     * @param int                         $length    The length of the new indent.
     * @param int                         $change    The difference in length between
     *                                               the old and new indent.
     *
     * @return bool
     */
    protected function adjustIndent(File $phpcsFile, $stackPtr, $length, $change)
    {
        $tokens = $phpcsFile->getTokens();

        // We don't adjust indents outside of PHP.
        if ($tokens[$stackPtr]['code'] === T_INLINE_HTML) {
            return false;
        }

        $padding = '';
        if ($length > 0) {
            if ($this->tabIndent === true) {
                $numTabs = floor($length / $this->tabWidth);
                if ($numTabs > 0) {
                    $numSpaces = ($length - ($numTabs * $this->tabWidth));
                    $padding   = str_repeat("\t", $numTabs).str_repeat(' ', $numSpaces);
                }
            } else {
                $padding = str_repeat(' ', $length);
            }
        }

        if ($tokens[$stackPtr]['column'] === 1) {
            $trimmed  = ltrim($tokens[$stackPtr]['content']);
            $accepted = $phpcsFile->fixer->replaceToken($stackPtr, $padding.$trimmed);
        } else {
            // Easier to just replace the entire indent.
            $accepted = $phpcsFile->fixer->replaceToken(($stackPtr - 1), $padding);
        }

        if ($accepted === false) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT_OPEN_TAG) {
            // We adjusted the start of a comment, so adjust the rest of it
            // as well so the alignment remains correct.
            for ($x = ($stackPtr + 1); $x < $tokens[$stackPtr]['comment_closer']; $x++) {
                if ($tokens[$x]['column'] !== 1) {
                    continue;
                }

                $length = 0;
                if ($tokens[$x]['code'] === T_DOC_COMMENT_WHITESPACE) {
                    $length = $tokens[$x]['length'];
                }

                $padding = ($length + $change);
                if ($padding > 0) {
                    if ($this->tabIndent === true) {
                        $numTabs   = floor($padding / $this->tabWidth);
                        $numSpaces = ($padding - ($numTabs * $this->tabWidth));
                        $padding   = str_repeat("\t", $numTabs).str_repeat(' ', $numSpaces);
                    } else {
                        $padding = str_repeat(' ', $padding);
                    }
                } else {
                    $padding = '';
                }

                $phpcsFile->fixer->replaceToken($x, $padding);
                if ($this->debug === true) {
                    $length = strlen($padding);
                    $line   = $tokens[$x]['line'];
                    $type   = $tokens[$x]['type'];
                    echo "\t=> Indent adjusted to $length for $type on line $line".PHP_EOL;
                }
            }//end for
        }//end if

        return true;

    }//end adjustIndent()


}//end class
