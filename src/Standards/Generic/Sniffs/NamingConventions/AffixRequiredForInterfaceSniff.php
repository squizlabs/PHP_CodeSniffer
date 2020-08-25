<?php
/**
 * Checks that interfaces are suffixed or prefixed by given affix.
 * By default it checks that interface has 'Interface' suffix - e.g. BarInterface.
 *
 * @author  Anna Borzenko <annnechko@gmail.com>
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class AffixRequiredForInterfaceSniff implements Sniff
{

    /**
     * The affix type - either 'suffix' or 'prefix'.
     * Default to 'suffix'.
     *
     * @var string
     */
    public $affixType = 'suffix';

     /**
      * A prefix/suffix that must be added to interface name.
      * Default to 'Interface'.
      *
      * @var string
      */
    public $affixValue = 'Interface';

    /**
     * Whether to run case sensitive prefix/suffix comparison.
     * Default to false.
     *
     * @var boolean
     */
    public $isCaseSensitive = false;


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_INTERFACE];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $interfaceName = $phpcsFile->getDeclarationName($stackPtr);
        if ($interfaceName === null) {
            return;
        }

        $this->affixValue = trim((string) $this->affixValue);
        $affixLength      = strlen($this->affixValue);
        if ($affixLength === 0) {
            // If affix is empty - then we think all names are valid.
            return;
        }

        $isSuffixRequired = $this->affixType === 'suffix';
        if ($isSuffixRequired === true) {
            $affix = substr($interfaceName, -$affixLength);
        } else {
            $affix = substr($interfaceName, 0, $affixLength);
        }

        if (strlen($interfaceName) < $affixLength || $this->checkAffix($affix) === false) {
            $verb = 'prefixed';
            if ($isSuffixRequired === true) {
                $verb = 'suffixed';
            }

            $affixErrorValue = $this->affixValue;
            if ($this->isCaseSensitive === true) {
                $affixErrorValue .= ' (case sensitive)';
            }

            $nameExample = $this->affixValue.'Bar';
            if ($isSuffixRequired === true) {
                $nameExample = 'Bar'.$this->affixValue;
            }

            $errorData = [
                $verb,
                $affixErrorValue,
                $nameExample,
                $interfaceName,
            ];
            $phpcsFile->addError('Interfaces MUST be %s by %s: e.g. %s. Found: %s', $stackPtr, 'Missing', $errorData);
        }//end if

    }//end process()


    /**
     * Checks if affix from the interface name is right.
     *
     * @param string $affix Affix from the checking interface name.
     *
     * @return bool
     */
    private function checkAffix($affix)
    {
        if ($this->isCaseSensitive === false) {
            return strtolower($affix) === strtolower($this->affixValue);
        }

        return $affix === $this->affixValue;

    }//end checkAffix()


}//end class
