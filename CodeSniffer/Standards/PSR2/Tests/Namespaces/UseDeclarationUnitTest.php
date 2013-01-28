<?php
/**
 * Unit test class for the UseDeclaration sniff.
 *
 * PHP version 5
 *
 * @category	PHP
 * @package	 PHP_CodeSniffer
 * @author		Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license	 https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link			http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Unit test class for the UseDeclaration sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category	PHP
 * @package	 PHP_CodeSniffer
 * @author		Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license	 https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version	 Release: @package_version@
 * @link			http://pear.php.net/package/PHP_CodeSniffer
 */
class PSR2_Tests_Namespaces_UseDeclarationUnitTest extends AbstractSniffUnitTest
{


		/**
		 * Returns the lines where errors should occur.
		 *
		 * The key of the array should represent the line number and the value
		 * should represent the number of errors that should occur on that line.
		 *
		 * @return array(int => int)
		 */
		public function getErrorList($testFile='')
		{
				switch ($testFile) {
				case 'UseDeclarationUnitTest.2.inc':
						return array(
										5	=> 1,
										10 => 2,
									 );
						break;
				case 'UseDeclarationUnitTest.3.inc':
						return array(
										5	=> 1,
									 );
						break;
				default:
						return array();
						break;
				}//end switch

		}//end getErrorList()


		/**
		 * Returns the lines where warnings should occur.
		 *
		 * The key of the array should represent the line number and the value
		 * should represent the number of warnings that should occur on that line.
		 *
		 * @return array(int => int)
		 */
		public function getWarningList()
		{
				return array();

		}//end getWarningList()


}//end class

?>
