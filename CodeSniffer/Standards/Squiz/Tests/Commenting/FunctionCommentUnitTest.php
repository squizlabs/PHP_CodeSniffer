<?php
/**
 * Unit test class for FunctionCommentSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for FunctionCommentSniff.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Squiz_Tests_Commenting_FunctionCommentUnitTest extends AbstractSniffUnitTest
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
        $errors = array(
                   5   => 1,
                   10  => 3,
                   12  => 2,
                   13  => 2,
                   14  => 1,
                   15  => 1,
                   28  => 1,
                   43  => 1,
                   76  => 1,
                   87  => 1,
                   103 => 1,
                   109 => 1,
                   112 => 1,
                   122 => 1,
                   123 => 3,
                   124 => 2,
                   125 => 1,
                   126 => 1,
                   137 => 4,
                   138 => 4,
                   139 => 4,
                   143 => 2,
                   152 => 1,
                   155 => 2,
                   159 => 1,
                   166 => 1,
                   173 => 1,
                   183 => 1,
                   190 => 2,
                   193 => 2,
                   196 => 1,
                   199 => 2,
                   210 => 1,
                   211 => 1,
                   222 => 1,
                   223 => 1,
                   224 => 1,
                   225 => 1,
                   226 => 1,
                   227 => 1,
                   230 => 2,
                   232 => 1,
                   246 => 1,
                   248 => 4,
                   261 => 1,
                   263 => 1,
                   276 => 1,
                   277 => 1,
                   278 => 1,
                   279 => 1,
                   280 => 1,
                   281 => 1,
                   284 => 1,
                   286 => 2,
                   294 => 1,
                   302 => 1,
                   312 => 1,
                   358 => 1,
                   359 => 2,
                   372 => 1,
                   373 => 1,
                   387 => 1,
                   407 => 1,
                   441 => 1,
                   500 => 1,
                   526 => 1,
                   548 => 1,
                   641 => 1,
                   669 => 1,
                  );

        // The yield tests will only work in PHP versions where yield exists and
        // will throw errors in earlier versions.
        if (version_compare(PHP_VERSION, '5.5.0') < 0) {
            $errors[676] = 1;
        } else {
            $errors[688] = 1;
        }

        return $errors;

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
