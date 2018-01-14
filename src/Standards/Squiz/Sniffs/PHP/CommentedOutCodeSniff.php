<?php
/**
 * Warn about commented out code.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Exceptions\TokenizerException;

class CommentedOutCodeSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'CSS',
    ];

    /**
     * If a comment is more than $maxPercentage% code, a warning will be shown.
     *
     * @var integer
     */
    public $maxPercentage = 35;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_COMMENT];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return int|void Integer stack pointer to skip forward or void to continue
     *                  normal file processing.
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore comments at the end of code blocks.
        if (substr($tokens[$stackPtr]['content'], 0, 6) === '//end ') {
            return;
        }

        $content      = '';
        $lastLineSeen = $tokens[$stackPtr]['line'];
        $commentStyle = 'line';
        if (strpos($tokens[$stackPtr]['content'], '/*') === 0) {
            $commentStyle = 'block';
        }

        $lastCommentBlockToken = $stackPtr;
        for ($i = $stackPtr; $i < $phpcsFile->numTokens; $i++) {
            if ($tokens[$i]['code'] === T_WHITESPACE) {
                continue;
            }

            if (isset(Tokens::$phpcsCommentTokens[$tokens[$i]['code']]) === true) {
                $lastLineSeen = $tokens[$i]['line'];
                continue;
            }

            if ($tokens[$stackPtr]['code'] !== $tokens[$i]['code']
                || ($lastLineSeen + 1) < $tokens[$i]['line']
            ) {
                break;
            }

            /*
                Trim as much off the comment as possible so we don't
                have additional whitespace tokens or comment tokens
            */

            $tokenContent = trim($tokens[$i]['content']);

            if ($commentStyle === 'line') {
                if (substr($tokenContent, 0, 2) === '//') {
                    $tokenContent = substr($tokenContent, 2);
                }

                if (substr($tokenContent, 0, 1) === '#') {
                    $tokenContent = substr($tokenContent, 1);
                }
            } else {
                if (substr($tokenContent, 0, 3) === '/**') {
                    $tokenContent = substr($tokenContent, 3);
                }

                if (substr($tokenContent, 0, 2) === '/*') {
                    $tokenContent = substr($tokenContent, 2);
                }

                if (substr($tokenContent, -2) === '*/') {
                    $tokenContent = substr($tokenContent, 0, -2);
                }

                if (substr($tokenContent, 0, 1) === '*') {
                    $tokenContent = substr($tokenContent, 1);
                }
            }//end if

            $content     .= $tokenContent.$phpcsFile->eolChar;
            $lastLineSeen = $tokens[$i]['line'];

            $lastCommentBlockToken = $i;
        }//end for

        // Quite a few comments use multiple dashes, equals signs etc
        // to frame comments and licence headers.
        $content = preg_replace('/[-=#*]{2,}/', '-', $content);

        // Random numbers sitting inside the content can throw parse errors
        // for invalid literals in PHP7+, so strip those.
        $content = preg_replace('/\d+/', '', $content);

        $content = trim($content);

        if ($content === '') {
            return ($lastCommentBlockToken + 1);
        }

        if ($phpcsFile->tokenizerType === 'PHP') {
            $content = '<?php '.$content.' ?>';
        }

        // Because we are not really parsing code, the tokenizer can throw all sorts
        // of errors that don't mean anything, so ignore them.
        $oldErrors = ini_get('error_reporting');
        ini_set('error_reporting', 0);
        try {
            $tokenizerClass = get_class($phpcsFile->tokenizer);
            $tokenizer      = new $tokenizerClass($content, $phpcsFile->config, $phpcsFile->eolChar);
            $stringTokens   = $tokenizer->getTokens();
        } catch (TokenizerException $e) {
            // We couldn't check the comment, so ignore it.
            ini_set('error_reporting', $oldErrors);
            return ($lastCommentBlockToken + 1);
        }

        ini_set('error_reporting', $oldErrors);

        $numTokens = count($stringTokens);

        /*
            We know what the first two and last two tokens should be
            (because we put them there) so ignore this comment if those
            tokens were not parsed correctly. It obviously means this is not
            valid code.
        */

        // First token is always the opening PHP tag.
        if ($stringTokens[0]['code'] !== T_OPEN_TAG) {
            return ($lastCommentBlockToken + 1);
        }

        // Last token is always the closing PHP tag, unless something went wrong.
        if (isset($stringTokens[($numTokens - 1)]) === false
            || $stringTokens[($numTokens - 1)]['code'] !== T_CLOSE_TAG
        ) {
            return ($lastCommentBlockToken + 1);
        }

        // Second last token is always whitespace or a comment, depending
        // on the code inside the comment.
        if ($phpcsFile->tokenizerType === 'PHP'
            && isset(Tokens::$emptyTokens[$stringTokens[($numTokens - 2)]['code']]) === false
        ) {
            return ($lastCommentBlockToken + 1);
        }

        $emptyTokens  = [
            T_WHITESPACE              => true,
            T_STRING                  => true,
            T_STRING_CONCAT           => true,
            T_ENCAPSED_AND_WHITESPACE => true,
            T_NONE                    => true,
            T_COMMENT                 => true,
        ];
        $emptyTokens += Tokens::$phpcsCommentTokens;

        $numComment  = 0;
        $numPossible = 0;
        $numCode     = 0;

        for ($i = 0; $i < $numTokens; $i++) {
            if (isset($emptyTokens[$stringTokens[$i]['code']]) === true) {
                // Looks like comment.
                $numComment++;
            } else if (isset(Tokens::$comparisonTokens[$stringTokens[$i]['code']]) === true
                || isset(Tokens::$arithmeticTokens[$stringTokens[$i]['code']]) === true
                || $stringTokens[$i]['code'] === T_GOTO_LABEL
            ) {
                // Commented out HTML/XML and other docs contain a lot of these
                // characters, so it is best to not use them directly.
                $numPossible++;
            } else {
                // Looks like code.
                $numCode++;
            }
        }

        // We subtract 3 from the token number so we ignore the start/end tokens
        // and their surrounding whitespace. We take 2 off the number of code
        // tokens so we ignore the start/end tokens.
        if ($numTokens > 3) {
            $numTokens -= 3;
        }

        if ($numCode >= 2) {
            $numCode -= 2;
        }

        $percentCode = ceil((($numCode / $numTokens) * 100));
        if ($percentCode > $this->maxPercentage) {
            // Just in case.
            $percentCode = min(100, $percentCode);

            $error = 'This comment is %s%% valid code; is this commented out code?';
            $data  = [$percentCode];
            $phpcsFile->addWarning($error, $stackPtr, 'Found', $data);
        }

        return ($lastCommentBlockToken + 1);

    }//end process()


}//end class
