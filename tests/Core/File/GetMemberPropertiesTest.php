<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File::getMemberProperties method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;
use PHPUnit\Framework\TestCase;

class GetMemberPropertiesTest extends TestCase
{

    /**
     * The PHP_CodeSniffer_File object containing parsed contents of the test case file.
     *
     * @var \PHP_CodeSniffer\Files\File
     */
    private $phpcsFile;


    /**
     * Initialize & tokenize PHP_CodeSniffer_File with code from the test case file.
     *
     * Methods used for these tests can be found in a test case file in the same
     * directory and with the same name, using the .inc extension.
     *
     * @return void
     */
    public function setUp()
    {
        $config            = new Config();
        $config->standards = ['Generic'];

        $ruleset = new Ruleset($config);

        $pathToTestFile  = dirname(__FILE__).'/'.basename(__FILE__, '.php').'.inc';
        $this->phpcsFile = new DummyFile(file_get_contents($pathToTestFile), $ruleset, $config);
        $this->phpcsFile->process();

    }//end setUp()


    /**
     * Clean up after finished test.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->phpcsFile);

    }//end tearDown()


    /**
     * Test the getMemberProperties() method.
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataGetMemberProperties
     *
     * @return void
     */
    public function testGetMemberProperties($identifier, $expected)
    {
        $start    = ($this->phpcsFile->numTokens - 1);
        $delim    = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            $identifier
        );
        $variable = $this->phpcsFile->findNext(T_VARIABLE, ($delim + 1));

        $result = $this->phpcsFile->getMemberProperties($variable);
        $this->assertSame($expected, $result);

    }//end testGetMemberProperties()


    /**
     * Data provider for the GetMemberProperties test.
     *
     * @see testGetMemberProperties()
     *
     * @return array
     */
    public function dataGetMemberProperties()
    {
        return [
            [
                '/* testVar */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testPublic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testProtected */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testPrivate */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testStaticVar */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testVarStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testPublicStatic */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testProtectedStatic */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testPrivateStatic */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testPublicStaticWithDocblock */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testProtectedStaticWithDocblock */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testPrivateStaticWithDocblock */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testNoPrefix */',
                [
                    'scope'           => 'public',
                    'scope_specified' => false,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testGroupProtectedStatic 1 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testGroupProtectedStatic 2 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testGroupProtectedStatic 3 */',
                [
                    'scope'           => 'protected',
                    'scope_specified' => true,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testGroupPrivate 1 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testGroupPrivate 2 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testGroupPrivate 3 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testGroupPrivate 4 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testGroupPrivate 5 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testGroupPrivate 6 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testGroupPrivate 7 */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testPropertyAfterMethod */',
                [
                    'scope'           => 'private',
                    'scope_specified' => true,
                    'is_static'       => true,
                ],
            ],
            [
                '/* testInterfaceProperty */',
                [],
            ],
            [
                '/* testNestedProperty 1 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
            [
                '/* testNestedProperty 2 */',
                [
                    'scope'           => 'public',
                    'scope_specified' => true,
                    'is_static'       => false,
                ],
            ],
        ];

    }//end dataGetMemberProperties()


    /**
     * Test receiving an expected exception when a non property is passed.
     *
     * @param string $identifier Comment which precedes the test case.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\TokenizerException
     * @expectedExceptionMessage $stackPtr is not a class member var
     *
     * @dataProvider dataNotClassProperty
     *
     * @return void
     */
    public function testNotClassPropertyException($identifier)
    {
        $start    = ($this->phpcsFile->numTokens - 1);
        $delim    = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            $identifier
        );
        $variable = $this->phpcsFile->findNext(T_VARIABLE, ($delim + 1));

        $result = $this->phpcsFile->getMemberProperties($variable);

    }//end testNotClassPropertyException()


    /**
     * Data provider for the NotClassPropertyException test.
     *
     * @see testNotClassPropertyException()
     *
     * @return array
     */
    public function dataNotClassProperty()
    {
        return [
            ['/* testMethodParam */'],
            ['/* testImportedGlobal */'],
            ['/* testLocalVariable */'],
            ['/* testGlobalVariable */'],
            ['/* testNestedMethodParam 1 */'],
            ['/* testNestedMethodParam 2 */'],
        ];

    }//end dataNotClassProperty()


    /**
     * Test receiving an expected exception when a non variable is passed.
     *
     * @expectedException        PHP_CodeSniffer\Exceptions\TokenizerException
     * @expectedExceptionMessage $stackPtr must be of type T_VARIABLE
     *
     * @return void
     */
    public function testNotAVariableException()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $delim = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNotAVariable */'
        );
        $next  = $this->phpcsFile->findNext(T_WHITESPACE, ($delim + 1), null, true);

        $result = $this->phpcsFile->getMemberProperties($next);

    }//end testNotAVariableException()


}//end class
