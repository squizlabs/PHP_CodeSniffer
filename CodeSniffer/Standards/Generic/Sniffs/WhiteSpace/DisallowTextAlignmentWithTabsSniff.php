<?php
/**
 * Generic_Sniffs_WhiteSpace_DisallowTextAlignmentWithTabsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Lars Heber (it-consulting@larsheber.de)
 * @copyright 2014 Lars Heber
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_WhiteSpace_DisallowTextAlignmentWithTabsSniff.
 *
 * Throws errors if tabs are used for any text adjustment.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Lars Heber (it-consulting@larsheber.de)
 * @copyright 2014 Lars Heber
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 */
class Generic_Sniffs_WhiteSpace_DisallowTextAlignmentWithTabsSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
        'PHP',
        'JS',
        'CSS',
    );

    /**
     * The --tab-width CLI value that is being used.
     *
     * @var int
     */
    private $_tabWidth = null;

    /**
     * The --encoding CLI value that is being used.
     *
     * @var string
     */
    private $_encoding = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        if ($this->_tabWidth === null || $this->_encoding === null) {
            $this->initialize($phpcsFile);
        }

        $tokens = $phpcsFile->getTokens();
        $error  = 'Tabs are not allowed for any indentation';

        for ($i = ($stackPtr + 1); $i < $phpcsFile->numTokens; $i++) {
            if (strpos($tokens[$i]['content'], "\t") === false) {
                continue;
            } else {
                $phpcsFile->recordMetric($i, 'Line indent', 'tabs');
            }

            $fix = $phpcsFile->addFixableError($error, $i, 'TabsUsed');
            if ($fix === true) {
                // Calculate REAL start column after all relevant tabs have been converted
                $firstOnLine = $phpcsFile->findFirstOnLine(array(), $i, true);
                $startColumn = 1;
                for ($k = $firstOnLine; $k < $i; $k++) {
                    $tmpContent = $tokens[$k]['content'];
                    if (strpos($tmpContent, "\t") !== false) {
                        $startColumn += $this->convertTabs($tmpContent, $startColumn, false);
                    } else {
                        $startColumn += $tokens[$k]['length'];
                    }
                }

                // Replace tabs with spaces, using SMART tabs.
                // Other sniffs can then correct the indent if they need to.
                $phpcsFile->fixer->replaceToken(
                    $i,
                    $this->convertTabs($tokens[$i]['content'], $startColumn, true)
                );
            }
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()

    /**
     * Convert tabs to spaces
     *
     * @param string $text
     * @param int $startColumn
     * @param bool $replace true: Replace tabs in $text; false: calculate true length after conversion
     * @return int|string
     */
    private function convertTabs($text, $startColumn, $replace)
    {
        $endColumn = $startColumn;
        if ($replace) {
            $result = '';
        }
        for ($i = 0; $i < mb_strlen($text, $this->_encoding); $i++) {
            $c = mb_substr($text, $i, 1);
            if ($c != "\t") {
                $endColumn++;
                if ($replace) {
                    $result .= $c;
                }
            } else {
                $countSpaces = $this->_tabWidth - ($endColumn-1) % $this->_tabWidth;
                $endColumn += $countSpaces;
                if ($replace) {
                    $result .= str_repeat(' ', $countSpaces);
                }
            }
        }
        if ($replace) {
            return $result;
        } else {
            return $endColumn - $startColumn;
        }
    }

    /**
     * Initialize sniffer
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     */
    private function initialize(PHP_CodeSniffer_File $phpcsFile)
    {
        $cliValues = $phpcsFile->phpcs->cli->getCommandLineValues();
        if ($this->_tabWidth === null) {
            if (empty($cliValues['tabWidth'])) {
                // We have no idea how wide tabs are, so assume 4 spaces for fixing.
                // It shouldn't really matter because indent checks elsewhere in the
                // standard should fix things up.
                $this->_tabWidth = 4;
            } else {
                $this->_tabWidth = $cliValues['tabWidth'];
            }
         }
         if ($this->_encoding === null) {
            if (empty($cliValues['encoding'])) {
                $this->_encoding = 'utf-8';
            } else {
                $this->_encoding = $cliValues['encoding'];
            }
        }
    }
}
