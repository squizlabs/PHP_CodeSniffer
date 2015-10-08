<?php
/**
 * Unit test class for the DisallowShortOpenTag sniff.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Tests\PHP;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class DisallowShortOpenTagUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getErrorList()
    {
        $option = (boolean) ini_get('short_open_tag');
        if ($option === true || defined('HHVM_VERSION') === true) {
            return array(
                    4 => 1,
                    5 => 1,
                   );
        } else if (version_compare(PHP_VERSION, '5.4.0RC1') >= 0) {
            // Shorthand echo is always available from PHP 5.4.0 but needed the
            // short_open_tag ini var to be set for versions before this.
            return array(4 => 1);
        }

        return array();

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array<int, int>
     */
    public function getWarningList()
    {
        return array();

    }//end getWarningList()


}//end class
