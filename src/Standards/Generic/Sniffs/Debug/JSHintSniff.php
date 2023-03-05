<?php
/**
 * Runs jshint.js on the file.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Alexander Wei§ <aweisswa@gmx.de>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Debug;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Common;

class JSHintSniff implements Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['JS'];


    /**
     * Returns the token types that this sniff is interested in.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_OPEN_TAG];

    }//end register()


    /**
     * Processes the tokens that this sniff is interested in.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where the token was found.
     * @param int                         $stackPtr  The position in the stack where
     *                                               the token was found.
     *
     * @return void
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If jshint.js could not be run
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $rhinoPath  = Config::getExecutablePath('rhino');
        $jshintPath = Config::getExecutablePath('jshint');
        if ($jshintPath === null) {
            return;
        }

        $fileName   = $phpcsFile->getFilename();
        $jshintPath = Common::escapeshellcmd($jshintPath);

        if ($rhinoPath !== null) {
            $rhinoPath = Common::escapeshellcmd($rhinoPath);
            $cmd       = "$rhinoPath \"$jshintPath\" ".escapeshellarg($fileName);
            exec($cmd, $output, $retval);

            $regex = '`^(?P<error>.+)\(.+:(?P<line>[0-9]+).*:[0-9]+\)$`';
        } else {
            $cmd = "$jshintPath ".escapeshellarg($fileName);
            exec($cmd, $output, $retval);

            $regex = '`^(.+?): line (?P<line>[0-9]+), col [0-9]+, (?P<error>.+)$`';
        }

        if (is_array($output) === true) {
            foreach ($output as $finding) {
                $matches    = [];
                $numMatches = preg_match($regex, $finding, $matches);
                if ($numMatches === 0) {
                    continue;
                }

                $line    = (int) $matches['line'];
                $message = 'jshint says: '.trim($matches['error']);
                $phpcsFile->addWarningOnLine($message, $line, 'ExternalTool');
            }
        }

        // Ignore the rest of the file.
        return ($phpcsFile->numTokens + 1);

    }//end process()


}//end class
