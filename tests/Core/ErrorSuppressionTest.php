<?php
/**
 * Tests for PHP_CodeSniffer error suppression tags.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;
use PHPUnit\Framework\TestCase;

class ErrorSuppressionTest extends TestCase
{


    /**
     * Test suppressing a single error.
     *
     * @param string $before         Annotation to place before the code.
     * @param string $after          Annotation to place after the code.
     * @param int    $expectedErrors Optional. Number of errors expected.
     *                               Defaults to 0.
     *
     * @dataProvider dataSuppressError
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testSuppressError($before, $after, $expectedErrors=0)
    {
        static $config, $ruleset;

        if (isset($config, $ruleset) === false) {
            $config            = new Config();
            $config->standards = ['Generic'];
            $config->sniffs    = ['Generic.PHP.LowerCaseConstant'];

            $ruleset = new Ruleset($config);
        }

        $content = '<?php '.PHP_EOL.$before.'$var = FALSE;'.PHP_EOL.$after;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame($expectedErrors, $file->getErrorCount());
        $this->assertCount($expectedErrors, $file->getErrors());

    }//end testSuppressError()


    /**
     * Data provider.
     *
     * @see testSuppressError()
     *
     * @return array
     */
    public function dataSuppressError()
    {
        return [
            'no suppression'                                                           => [
                'before'         => '',
                'after'          => '',
                'expectedErrors' => 1,
            ],

            // Inline slash comments.
            'disable/enable: slash comment'                                            => [
                'before' => '// phpcs:disable'.PHP_EOL,
                'after'  => '// phpcs:enable',
            ],
            'disable/enable: multi-line slash comment, tab indented'                   => [
                'before' => "\t".'// For reasons'.PHP_EOL."\t".'// phpcs:disable'.PHP_EOL."\t",
                'after'  => "\t".'// phpcs:enable',
            ],
            'disable/enable: slash comment, with @'                                    => [
                'before' => '// @phpcs:disable'.PHP_EOL,
                'after'  => '// @phpcs:enable',
            ],
            'disable/enable: slash comment, mixed case'                                => [
                'before' => '// PHPCS:Disable'.PHP_EOL,
                'after'  => '// pHPcs:enabLE',
            ],

            // Inline hash comments.
            'disable/enable: hash comment'                                             => [
                'before' => '# phpcs:disable'.PHP_EOL,
                'after'  => '# phpcs:enable',
            ],
            'disable/enable: multi-line hash comment, tab indented'                    => [
                'before' => "\t".'# For reasons'.PHP_EOL."\t".'# phpcs:disable'.PHP_EOL."\t",
                'after'  => "\t".'# phpcs:enable',
            ],
            'disable/enable: hash comment, with @'                                     => [
                'before' => '# @phpcs:disable'.PHP_EOL,
                'after'  => '# @phpcs:enable',
            ],
            'disable/enable: hash comment, mixed case'                                 => [
                'before' => '# PHPCS:Disable'.PHP_EOL,
                'after'  => '# pHPcs:enabLE',
            ],

            // Inline star (block) comments.
            'disable/enable: star comment'                                             => [
                'before' => '/* phpcs:disable */'.PHP_EOL,
                'after'  => '/* phpcs:enable */',
            ],
            'disable/enable: multi-line star comment'                                  => [
                'before' => '/*'.PHP_EOL.' phpcs:disable'.PHP_EOL.' */'.PHP_EOL,
                'after'  => '/*'.PHP_EOL.' phpcs:enable'.PHP_EOL.' */',
            ],
            'disable/enable: multi-line star comment, each line starred'               => [
                'before' => '/*'.PHP_EOL.' * phpcs:disable'.PHP_EOL.' */'.PHP_EOL,
                'after'  => '/*'.PHP_EOL.' * phpcs:enable'.PHP_EOL.' */',
            ],
            'disable/enable: multi-line star comment, each line starred, tab indented' => [
                'before' => "\t".'/*'.PHP_EOL."\t".' * phpcs:disable'.PHP_EOL."\t".' */'.PHP_EOL."\t",
                'after'  => "\t".'/*'.PHP_EOL.' * phpcs:enable'.PHP_EOL.' */',
            ],

            // Docblock comments.
            'disable/enable: single line docblock comment'                             => [
                'before' => '/** phpcs:disable */'.PHP_EOL,
                'after'  => '/** phpcs:enable */',
            ],

            // Deprecated syntax.
            'old style: slash comment'                                                 => [
                'before' => '// @codingStandardsIgnoreStart'.PHP_EOL,
                'after'  => '// @codingStandardsIgnoreEnd',
            ],
            'old style: star comment'                                                  => [
                'before' => '/* @codingStandardsIgnoreStart */'.PHP_EOL,
                'after'  => '/* @codingStandardsIgnoreEnd */',
            ],
            'old style: multi-line star comment'                                       => [
                'before' => '/*'.PHP_EOL.' @codingStandardsIgnoreStart'.PHP_EOL.' */'.PHP_EOL,
                'after'  => '/*'.PHP_EOL.' @codingStandardsIgnoreEnd'.PHP_EOL.' */',
            ],
            'old style: single line docblock comment'                                  => [
                'before' => '/** @codingStandardsIgnoreStart */'.PHP_EOL,
                'after'  => '/** @codingStandardsIgnoreEnd */',
            ],
        ];

    }//end dataSuppressError()


    /**
     * Test suppressing 1 out of 2 errors.
     *
     * @param string $before         Annotation to place before the code.
     * @param string $between        Annotation to place between the code.
     * @param int    $expectedErrors Optional. Number of errors expected.
     *                               Defaults to 1.
     *
     * @dataProvider dataSuppressSomeErrors
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testSuppressSomeErrors($before, $between, $expectedErrors=1)
    {
        static $config, $ruleset;

        if (isset($config, $ruleset) === false) {
            $config            = new Config();
            $config->standards = ['Generic'];
            $config->sniffs    = ['Generic.PHP.LowerCaseConstant'];

            $ruleset = new Ruleset($config);
        }

        $content = <<<EOD
<?php
$before
\$var = FALSE;
$between
\$var = TRUE;
EOD;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame($expectedErrors, $file->getErrorCount());
        $this->assertCount($expectedErrors, $file->getErrors());

    }//end testSuppressSomeErrors()


    /**
     * Data provider.
     *
     * @see testSuppressSomeErrors()
     *
     * @return array
     */
    public function dataSuppressSomeErrors()
    {
        return [
            'no suppression'                               => [
                'before'         => '',
                'between'        => '',
                'expectedErrors' => 2,
            ],

            // With suppression.
            'disable/enable: slash comment'                => [
                'before'  => '// phpcs:disable',
                'between' => '// phpcs:enable',
            ],
            'disable/enable: slash comment, with @'        => [
                'before'  => '// @phpcs:disable',
                'between' => '// @phpcs:enable',
            ],
            'disable/enable: hash comment'                 => [
                'before'  => '# phpcs:disable',
                'between' => '# phpcs:enable',
            ],
            'disable/enable: hash comment, with @'         => [
                'before'  => '# @phpcs:disable',
                'between' => '# @phpcs:enable',
            ],
            'disable/enable: single line docblock comment' => [
                'before'  => '/** phpcs:disable */',
                'between' => '/** phpcs:enable */',
            ],

            // Deprecated syntax.
            'old style: slash comment'                     => [
                'before'  => '// @codingStandardsIgnoreStart',
                'between' => '// @codingStandardsIgnoreEnd',
            ],
            'old style: single line docblock comment'      => [
                'before'  => '/** @codingStandardsIgnoreStart */',
                'between' => '/** @codingStandardsIgnoreEnd */',
            ],
        ];

    }//end dataSuppressSomeErrors()


    /**
     * Test suppressing a single warning.
     *
     * @param string $before           Annotation to place before the code.
     * @param string $after            Annotation to place after the code.
     * @param int    $expectedWarnings Optional. Number of warnings expected.
     *                                 Defaults to 0.
     *
     * @dataProvider dataSuppressWarning
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testSuppressWarning($before, $after, $expectedWarnings=0)
    {
        static $config, $ruleset;

        if (isset($config, $ruleset) === false) {
            $config            = new Config();
            $config->standards = ['Generic'];
            $config->sniffs    = ['Generic.Commenting.Todo'];

            $ruleset = new Ruleset($config);
        }

        $content = <<<EOD
<?php
$before
//TODO: write some code.
$after
EOD;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame($expectedWarnings, $file->getWarningCount());
        $this->assertCount($expectedWarnings, $file->getWarnings());

    }//end testSuppressWarning()


    /**
     * Data provider.
     *
     * @see testSuppressWarning()
     *
     * @return array
     */
    public function dataSuppressWarning()
    {
        return [
            'no suppression'                               => [
                'before'           => '',
                'after'            => '',
                'expectedWarnings' => 1,
            ],

            // With suppression.
            'disable/enable: slash comment'                => [
                'before' => '// phpcs:disable',
                'after'  => '// phpcs:enable',
            ],
            'disable/enable: slash comment, with @'        => [
                'before' => '// @phpcs:disable',
                'after'  => '// @phpcs:enable',
            ],
            'disable/enable: single line docblock comment' => [
                'before' => '/** phpcs:disable */',
                'after'  => '/** phpcs:enable */',
            ],

            // Deprecated syntax.
            'old style: slash comment'                     => [
                'before' => '// @codingStandardsIgnoreStart',
                'after'  => '// @codingStandardsIgnoreEnd',
            ],
            'old style: single line docblock comment'      => [
                'before' => '/** @codingStandardsIgnoreStart */',
                'after'  => '/** @codingStandardsIgnoreEnd */',
            ],
        ];

    }//end dataSuppressWarning()


    /**
     * Test suppressing a single error using a single line ignore.
     *
     * @param string $before         Annotation to place before the code.
     * @param string $after          Optional. Annotation to place after the code.
     *                               Defaults to an empty string.
     * @param int    $expectedErrors Optional. Number of errors expected.
     *                               Defaults to 1.
     *
     * @dataProvider dataSuppressLine
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testSuppressLine($before, $after='', $expectedErrors=1)
    {
        static $config, $ruleset;

        if (isset($config, $ruleset) === false) {
            $config            = new Config();
            $config->standards = ['Generic'];
            $config->sniffs    = ['Generic.PHP.LowerCaseConstant'];

            $ruleset = new Ruleset($config);
        }

        $content = <<<EOD
<?php
$before
\$var = FALSE;$after
\$var = FALSE;
EOD;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame($expectedErrors, $file->getErrorCount());
        $this->assertCount($expectedErrors, $file->getErrors());

    }//end testSuppressLine()


    /**
     * Data provider.
     *
     * @see testSuppressLine()
     *
     * @return array
     */
    public function dataSuppressLine()
    {
        return [
            'no suppression'                             => [
                'before'         => '',
                'after'          => '',
                'expectedErrors' => 2,
            ],

            // With suppression on line before.
            'ignore: line before, slash comment'         => ['before' => '// phpcs:ignore'],
            'ignore: line before, slash comment, with @' => ['before' => '// @phpcs:ignore'],
            'ignore: line before, hash comment'          => ['before' => '# phpcs:ignore'],
            'ignore: line before, hash comment, with @'  => ['before' => '# @phpcs:ignore'],
            'ignore: line before, star comment'          => ['before' => '/* phpcs:ignore */'],
            'ignore: line before, star comment, with @'  => ['before' => '/* @phpcs:ignore */'],

            // With suppression as trailing comment on code line.
            'ignore: end of line, slash comment'         => [
                'before' => '',
                'after'  => ' // phpcs:ignore',
            ],
            'ignore: end of line, slash comment, with @' => [
                'before' => '',
                'after'  => ' // @phpcs:ignore',
            ],
            'ignore: end of line, hash comment'          => [
                'before' => '',
                'after'  => ' # phpcs:ignore',
            ],
            'ignore: end of line, hash comment, with @'  => [
                'before' => '',
                'after'  => ' # @phpcs:ignore',
            ],

            // Deprecated syntax.
            'old style: line before, slash comment'      => ['before' => '// @codingStandardsIgnoreLine'],
            'old style: end of line, slash comment'      => [
                'before' => '',
                'after'  => ' // @codingStandardsIgnoreLine',
            ],
        ];

    }//end dataSuppressLine()


    /**
     * Test suppressing a single error using a single line ignore in the middle of a line.
     *
     * @covers PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testSuppressLineMidLine()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.PHP.LowerCaseConstant'];

        $ruleset = new Ruleset($config);

        $content = '<?php '.PHP_EOL.'$var = FALSE; /* @phpcs:ignore */ $var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame(0, $file->getErrorCount());
        $this->assertCount(0, $file->getErrors());

    }//end testSuppressLineMidLine()


    /**
     * Test suppressing a single error using a single line ignore within a docblock.
     *
     * @covers PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testSuppressLineWithinDocblock()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.Files.LineLength'];

        $ruleset = new Ruleset($config);

        // Process with @ suppression on line before inside docblock.
        $comment = str_repeat('a ', 50);
        $content = <<<EOD
<?php
/**
 * Comment here
 * @phpcs:ignore
 * $comment
 */
EOD;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame(0, $file->getErrorCount());
        $this->assertCount(0, $file->getErrors());

    }//end testSuppressLineWithinDocblock()


    /**
     * Test that using a single line ignore does not interfere with other suppressions.
     *
     * @param string $before Annotation to place before the code.
     * @param string $after  Annotation to place after the code.
     *
     * @dataProvider dataNestedSuppressLine
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testNestedSuppressLine($before, $after)
    {
        static $config, $ruleset;

        if (isset($config, $ruleset) === false) {
            $config            = new Config();
            $config->standards = ['Generic'];
            $config->sniffs    = ['Generic.PHP.LowerCaseConstant'];

            $ruleset = new Ruleset($config);
        }

        $content = <<<EOD
<?php
$before
\$var = FALSE;
\$var = TRUE;
$after
EOD;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame(0, $file->getErrorCount());
        $this->assertCount(0, $file->getErrors());

    }//end testNestedSuppressLine()


    /**
     * Data provider.
     *
     * @see testNestedSuppressLine()
     *
     * @return array
     */
    public function dataNestedSuppressLine()
    {
        return [
            // Process with disable/enable suppression and no single line suppression.
            'disable/enable: slash comment, no single line suppression'                       => [
                'before' => '// phpcs:disable',
                'after'  => '// phpcs:enable',
            ],
            'disable/enable: slash comment, with @, no single line suppression'               => [
                'before' => '// @phpcs:disable',
                'after'  => '// @phpcs:enable',
            ],
            'disable/enable: hash comment, no single line suppression'                        => [
                'before' => '# phpcs:disable',
                'after'  => '# phpcs:enable',
            ],
            'old style: slash comment, no single line suppression'                            => [
                'before' => '// @codingStandardsIgnoreStart',
                'after'  => '// @codingStandardsIgnoreEnd',
            ],

            // Process with line suppression nested within disable/enable suppression.
            'disable/enable: slash comment, next line nested single line suppression'         => [
                'before' => '// phpcs:disable'.PHP_EOL.'// phpcs:ignore',
                'after'  => '// phpcs:enable',
            ],
            'disable/enable: slash comment, with @, next line nested single line suppression' => [
                'before' => '// @phpcs:disable'.PHP_EOL.'// @phpcs:ignore',
                'after'  => '// @phpcs:enable',
            ],
            'disable/enable: hash comment, next line nested single line suppression'          => [
                'before' => '# @phpcs:disable'.PHP_EOL.'# @phpcs:ignore',
                'after'  => '# @phpcs:enable',
            ],
            'old style: slash comment, next line nested single line suppression'              => [
                'before' => '// @codingStandardsIgnoreStart'.PHP_EOL.'// @codingStandardsIgnoreLine',
                'after'  => '// @codingStandardsIgnoreEnd',
            ],
        ];

    }//end dataNestedSuppressLine()


    /**
     * Test suppressing a scope opener.
     *
     * @param string $before         Annotation to place before the scope opener.
     * @param string $after          Annotation to place after the scope opener.
     * @param int    $expectedErrors Optional. Number of errors expected.
     *                               Defaults to 0.
     *
     * @dataProvider dataSuppressScope
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testSuppressScope($before, $after, $expectedErrors=0)
    {
        static $config, $ruleset;

        if (isset($config, $ruleset) === false) {
            $config            = new Config();
            $config->standards = ['PEAR'];
            $config->sniffs    = ['PEAR.Functions.FunctionDeclaration'];

            $ruleset = new Ruleset($config);
        }

        $content = '<?php '.PHP_EOL.$before.'$var = FALSE;'.$after.PHP_EOL.'$var = FALSE;';
        $content = <<<EOD
<?php
class MyClass() {
    $before
    function myFunction() {
        $after
        \$this->foo();
    }
}
EOD;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame($expectedErrors, $file->getErrorCount());
        $this->assertCount($expectedErrors, $file->getErrors());

    }//end testSuppressScope()


    /**
     * Data provider.
     *
     * @see testSuppressScope()
     *
     * @return array
     */
    public function dataSuppressScope()
    {
        return [
            'no suppression'                                       => [
                'before'         => '',
                'after'          => '',
                'expectedErrors' => 1,
            ],

            // Process with suppression.
            'disable/enable: slash comment'                        => [
                'before' => '//phpcs:disable',
                'after'  => '//phpcs:enable',
            ],
            'disable/enable: slash comment, with @'                => [
                'before' => '//@phpcs:disable',
                'after'  => '//@phpcs:enable',
            ],
            'disable/enable: hash comment'                         => [
                'before' => '#phpcs:disable',
                'after'  => '#phpcs:enable',
            ],
            'disable/enable: single line docblock comment'         => [
                'before' => '/** phpcs:disable */',
                'after'  => '/** phpcs:enable */',
            ],
            'disable/enable: single line docblock comment, with @' => [
                'before' => '/** @phpcs:disable */',
                'after'  => '/** @phpcs:enable */',
            ],

            // Deprecated syntax.
            'old style: start/end, slash comment'                  => [
                'before' => '//@codingStandardsIgnoreStart',
                'after'  => '//@codingStandardsIgnoreEnd',
            ],
            'old style: start/end, single line docblock comment'   => [
                'before' => '/** @codingStandardsIgnoreStart */',
                'after'  => '/** @codingStandardsIgnoreEnd */',
            ],
        ];

    }//end dataSuppressScope()


    /**
     * Test suppressing a whole file.
     *
     * @param string $before           Annotation to place before the code.
     * @param string $after            Optional. Annotation to place after the code.
     *                                 Defaults to an empty string.
     * @param int    $expectedWarnings Optional. Number of warnings expected.
     *                                 Defaults to 0.
     *
     * @dataProvider dataSuppressFile
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testSuppressFile($before, $after='', $expectedWarnings=0)
    {
        static $config, $ruleset;

        if (isset($config, $ruleset) === false) {
            $config            = new Config();
            $config->standards = ['Generic'];
            $config->sniffs    = ['Generic.Commenting.Todo'];

            $ruleset = new Ruleset($config);
        }

        $content = <<<EOD
<?php
$before
class MyClass {}
\$foo = new MyClass();
//TODO: write some code
$after
EOD;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame($expectedWarnings, $file->getWarningCount());
        $this->assertCount($expectedWarnings, $file->getWarnings());

    }//end testSuppressFile()


    /**
     * Data provider.
     *
     * @see testSuppressFile()
     *
     * @return array
     */
    public function dataSuppressFile()
    {
        return [
            'no suppression'                                          => [
                'before'         => '',
                'after'          => '',
                'expectedErrors' => 1,
            ],

            // Process with suppression.
            'ignoreFile: start of file, slash comment'                => ['before' => '// phpcs:ignoreFile'],
            'ignoreFile: start of file, slash comment, with @'        => ['before' => '// @phpcs:ignoreFile'],
            'ignoreFile: start of file, slash comment, mixed case'    => ['before' => '// PHPCS:Ignorefile'],
            'ignoreFile: start of file, hash comment'                 => ['before' => '# phpcs:ignoreFile'],
            'ignoreFile: start of file, hash comment, with @'         => ['before' => '# @phpcs:ignoreFile'],
            'ignoreFile: start of file, single-line star comment'     => ['before' => '/* phpcs:ignoreFile */'],
            'ignoreFile: start of file, multi-line star comment'      => [
                'before' => '/*'.PHP_EOL.' phpcs:ignoreFile'.PHP_EOL.' */',
            ],
            'ignoreFile: start of file, single-line docblock comment' => ['before' => '/** phpcs:ignoreFile */'],

            // Process late comment.
            'ignoreFile: late comment, slash comment'                 => [
                'before' => '',
                'after'  => '// phpcs:ignoreFile',
            ],

            // Deprecated syntax.
            'old style: start of file, slash comment'                 => ['before' => '// @codingStandardsIgnoreFile'],
            'old style: start of file, single-line star comment'      => ['before' => '/* @codingStandardsIgnoreFile */'],
            'old style: start of file, multi-line star comment'       => [
                'before' => '/*'.PHP_EOL.' @codingStandardsIgnoreFile'.PHP_EOL.' */',
            ],
            'old style: start of file, single-line docblock comment'  => ['before' => '/** @codingStandardsIgnoreFile */'],

            // Deprecated syntax, late comment.
            'old style: late comment, slash comment'                  => [
                'before' => '',
                'after'  => '// @codingStandardsIgnoreFile',
            ],
        ];

    }//end dataSuppressFile()


    /**
     * Test disabling specific sniffs.
     *
     * @param string $before           Annotation to place before the code.
     * @param int    $expectedErrors   Optional. Number of errors expected.
     *                                 Defaults to 0.
     * @param int    $expectedWarnings Optional. Number of warnings expected.
     *                                 Defaults to 0.
     *
     * @dataProvider dataDisableSelected
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testDisableSelected($before, $expectedErrors=0, $expectedWarnings=0)
    {
        static $config, $ruleset;

        if (isset($config, $ruleset) === false) {
            $config            = new Config();
            $config->standards = ['Generic'];
            $config->sniffs    = [
                'Generic.PHP.LowerCaseConstant',
                'Generic.Commenting.Todo',
            ];

            $ruleset = new Ruleset($config);
        }

        $content = <<<EOD
<?php
$before
\$var = FALSE;
//TODO: write some code
EOD;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame($expectedErrors, $file->getErrorCount());
        $this->assertCount($expectedErrors, $file->getErrors());

        $this->assertSame($expectedWarnings, $file->getWarningCount());
        $this->assertCount($expectedWarnings, $file->getWarnings());

    }//end testDisableSelected()


    /**
     * Data provider.
     *
     * @see testDisableSelected()
     *
     * @return array
     */
    public function dataDisableSelected()
    {
        return [
            // Single sniff.
            'disable: single sniff'                        => [
                'before'         => '// phpcs:disable Generic.Commenting.Todo',
                'expectedErrors' => 1,
            ],
            'disable: single sniff with reason'            => [
                'before'         => '# phpcs:disable Generic.Commenting.Todo -- for reasons',
                'expectedErrors' => 1,
            ],
            'disable: single sniff, docblock'              => [
                'before'         => '/**'.PHP_EOL.' * phpcs:disable Generic.Commenting.Todo'.PHP_EOL.' */ ',
                'expectedErrors' => 1,
            ],
            'disable: single sniff, docblock, with @'      => [
                'before'         => '/**'.PHP_EOL.' * @phpcs:disable Generic.Commenting.Todo'.PHP_EOL.' */ ',
                'expectedErrors' => 1,
            ],

            // Multiple sniffs.
            'disable: multiple sniffs in one comment'      => ['before' => '// phpcs:disable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant'],
            'disable: multiple sniff in multiple comments' => [
                'before' => '// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'// phpcs:disable Generic.PHP.LowerCaseConstant',
            ],

            // Selectiveness variations.
            'disable: complete category'                   => [
                'before'         => '// phpcs:disable Generic.Commenting',
                'expectedErrors' => 1,
            ],
            'disable: whole standard'                      => ['before' => '// phpcs:disable Generic'],
            'disable: single errorcode'                    => [
                'before'         => '# @phpcs:disable Generic.Commenting.Todo.TaskFound',
                'expectedErrors' => 1,
            ],
            'disable: single errorcode and a category'     => ['before' => '// phpcs:disable Generic.PHP.LowerCaseConstant.Found,Generic.Commenting'],

            // Wrong category/sniff/code.
            'disable: wrong error code and category'       => [
                'before'           => '/**'.PHP_EOL.' * phpcs:disable Generic.PHP.LowerCaseConstant.Upper,Generic.Comments'.PHP_EOL.' */ ',
                'expectedErrors'   => 1,
                'expectedWarnings' => 1,
            ],
            'disable: wrong category, docblock'            => [
                'before'           => '/**'.PHP_EOL.' * phpcs:disable Generic.Files'.PHP_EOL.' */ ',
                'expectedErrors'   => 1,
                'expectedWarnings' => 1,
            ],
            'disable: wrong category, docblock, with @'    => [
                'before'           => '/**'.PHP_EOL.' * @phpcs:disable Generic.Files'.PHP_EOL.' */ ',
                'expectedErrors'   => 1,
                'expectedWarnings' => 1,
            ],
        ];

    }//end dataDisableSelected()


    /**
     * Test re-enabling specific sniffs that have been disabled.
     *
     * @param string $code             Code pattern to check.
     * @param int    $expectedErrors   Number of errors expected.
     * @param int    $expectedWarnings Number of warnings expected.
     *
     * @dataProvider dataEnableSelected
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testEnableSelected($code, $expectedErrors, $expectedWarnings)
    {
        static $config, $ruleset;

        if (isset($config, $ruleset) === false) {
            $config            = new Config();
            $config->standards = ['Generic'];
            $config->sniffs    = [
                'Generic.PHP.LowerCaseConstant',
                'Generic.Commenting.Todo',
            ];

            $ruleset = new Ruleset($config);
        }

        $content = '<?php '.$code;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame($expectedErrors, $file->getErrorCount());
        $this->assertCount($expectedErrors, $file->getErrors());

        $this->assertSame($expectedWarnings, $file->getWarningCount());
        $this->assertCount($expectedWarnings, $file->getWarnings());

    }//end testEnableSelected()


    /**
     * Data provider.
     *
     * @see testEnableSelected()
     *
     * @return array
     */
    public function dataEnableSelected()
    {
        return [
            'disable/enable: a single sniff'                                                                                => [
                'code'             => '
                    // phpcs:disable Generic.Commenting.Todo
                    $var = FALSE;
                    //TODO: write some code
                    // phpcs:enable Generic.Commenting.Todo
                    //TODO: write some code',
                'expectedErrors'   => 1,
                'expectedWarnings' => 1,
            ],
            'disable/enable: multiple sniffs'                                                                               => [
                'code'             => '
                    // phpcs:disable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant
                    $var = FALSE;
                    //TODO: write some code
                    // phpcs:enable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant
                    //TODO: write some code
                    $var = FALSE;',
                'expectedErrors'   => 1,
                'expectedWarnings' => 1,
            ],
            'disable: multiple sniffs; enable: one'                                                                         => [
                'code'             => '
                    # phpcs:disable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant
                    $var = FALSE;
                    //TODO: write some code
                    # phpcs:enable Generic.Commenting.Todo
                    //TODO: write some code
                    $var = FALSE;',
                'expectedErrors'   => 0,
                'expectedWarnings' => 1,
            ],
            'disable/enable: complete category'                                                                             => [
                'code'             => '
                    // phpcs:disable Generic.Commenting
                    $var = FALSE;
                    //TODO: write some code
                    // phpcs:enable Generic.Commenting
                    //TODO: write some code',
                'expectedErrors'   => 1,
                'expectedWarnings' => 1,
            ],
            'disable/enable: whole standard'                                                                                => [
                'code'             => '
                    // phpcs:disable Generic
                    $var = FALSE;
                    //TODO: write some code
                    // phpcs:enable Generic
                    //TODO: write some code',
                'expectedErrors'   => 0,
                'expectedWarnings' => 1,
            ],
            'disable: whole standard; enable: category from the standard'                                                   => [
                'code'             => '
                    // phpcs:disable Generic
                    $var = FALSE;
                    //TODO: write some code
                    // phpcs:enable Generic.Commenting
                    //TODO: write some code',
                'expectedErrors'   => 0,
                'expectedWarnings' => 1,
            ],
            'disable: a category; enable: the whole standard containing the category'                                       => [
                'code'             => '
                    # phpcs:disable Generic.Commenting
                    $var = FALSE;
                    //TODO: write some code
                    # phpcs:enable Generic
                    //TODO: write some code',
                'expectedErrors'   => 1,
                'expectedWarnings' => 1,
            ],
            'disable: single sniff; enable: the category containing the sniff'                                              => [
                'code'             => '
                    // phpcs:disable Generic.Commenting.Todo
                    $var = FALSE;
                    //TODO: write some code
                    // phpcs:enable Generic.Commenting
                    //TODO: write some code',
                'expectedErrors'   => 1,
                'expectedWarnings' => 1,
            ],
            'disable: whole standard; enable: single sniff from the standard'                                               => [
                'code'             => '
                    // phpcs:disable Generic
                    $var = FALSE;
                    //TODO: write some code
                    // phpcs:enable Generic.Commenting.Todo
                    //TODO: write some code',
                'expectedErrors'   => 0,
                'expectedWarnings' => 1,
            ],
            'disable: whole standard; enable: single sniff from the standard; disable: that same sniff; enable: everything' => [
                'code'             => '
                    // phpcs:disable Generic
                    $var = FALSE;
                    //TODO: write some code
                    // phpcs:enable Generic.Commenting.Todo
                    //TODO: write some code
                    // phpcs:disable Generic.Commenting.Todo
                    //TODO: write some code
                    // phpcs:enable
                    //TODO: write some code',
                'expectedErrors'   => 0,
                'expectedWarnings' => 2,
            ],
            'disable: whole standard; enable: single sniff from the standard; enable: other sniff from the standard'        => [
                'code'             => '
                    // phpcs:disable Generic
                    $var = FALSE;
                    //TODO: write some code
                    // phpcs:enable Generic.Commenting.Todo
                    //TODO: write some code
                    $var = FALSE;
                    // phpcs:enable Generic.PHP.LowerCaseConstant
                    //TODO: write some code
                    $var = FALSE;',
                'expectedErrors'   => 1,
                'expectedWarnings' => 2,
            ],
        ];

    }//end dataEnableSelected()


    /**
     * Test ignoring specific sniffs.
     *
     * @param string $before           Annotation to place before the code.
     * @param int    $expectedErrors   Number of errors expected.
     * @param int    $expectedWarnings Number of warnings expected.
     *
     * @dataProvider dataIgnoreSelected
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testIgnoreSelected($before, $expectedErrors, $expectedWarnings)
    {
        static $config, $ruleset;

        if (isset($config, $ruleset) === false) {
            $config            = new Config();
            $config->standards = ['Generic'];
            $config->sniffs    = [
                'Generic.PHP.LowerCaseConstant',
                'Generic.Commenting.Todo',
            ];

            $ruleset = new Ruleset($config);
        }

        $content = <<<EOD
<?php
$before
\$var = FALSE; //TODO: write some code
\$var = FALSE; //TODO: write some code
EOD;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame($expectedErrors, $file->getErrorCount());
        $this->assertCount($expectedErrors, $file->getErrors());

        $this->assertSame($expectedWarnings, $file->getWarningCount());
        $this->assertCount($expectedWarnings, $file->getWarnings());

    }//end testIgnoreSelected()


    /**
     * Data provider.
     *
     * @see testIgnoreSelected()
     *
     * @return array
     */
    public function dataIgnoreSelected()
    {
        return [
            'no suppression'                              => [
                'before'           => '',
                'expectedErrors'   => 2,
                'expectedWarnings' => 2,
            ],

            // With suppression.
            'ignore: single sniff'                        => [
                'before'           => '// phpcs:ignore Generic.Commenting.Todo',
                'expectedErrors'   => 2,
                'expectedWarnings' => 1,
            ],
            'ignore: multiple sniffs'                     => [
                'before'           => '// phpcs:ignore Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant',
                'expectedErrors'   => 1,
                'expectedWarnings' => 1,
            ],
            'disable: single sniff; ignore: single sniff' => [
                'before'           => '// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'// phpcs:ignore Generic.PHP.LowerCaseConstant',
                'expectedErrors'   => 1,
                'expectedWarnings' => 0,
            ],
            'ignore: category of sniffs'                  => [
                'before'           => '# phpcs:ignore Generic.Commenting',
                'expectedErrors'   => 2,
                'expectedWarnings' => 1,
            ],
            'ignore: whole standard'                      => [
                'before'           => '// phpcs:ignore Generic',
                'expectedErrors'   => 1,
                'expectedWarnings' => 1,
            ],
        ];

    }//end dataIgnoreSelected()


    /**
     * Test ignoring specific sniffs.
     *
     * @param string $code             Code pattern to check.
     * @param int    $expectedErrors   Number of errors expected.
     * @param int    $expectedWarnings Number of warnings expected.
     *
     * @dataProvider dataCommenting
     * @covers       PHP_CodeSniffer\Tokenizers\Tokenizer::createPositionMap
     *
     * @return void
     */
    public function testCommenting($code, $expectedErrors, $expectedWarnings)
    {
        static $config, $ruleset;

        if (isset($config, $ruleset) === false) {
            $config            = new Config();
            $config->standards = ['Generic'];
            $config->sniffs    = [
                'Generic.PHP.LowerCaseConstant',
                'Generic.Commenting.Todo',
            ];

            $ruleset = new Ruleset($config);
        }

        $content = '<?php '.$code;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $this->assertSame($expectedErrors, $file->getErrorCount());
        $this->assertCount($expectedErrors, $file->getErrors());

        $this->assertSame($expectedWarnings, $file->getWarningCount());
        $this->assertCount($expectedWarnings, $file->getWarnings());

    }//end testCommenting()


    /**
     * Data provider.
     *
     * @see testCommenting()
     *
     * @return array
     */
    public function dataCommenting()
    {
        return [
            'ignore: single sniff'                                                                         => [
                'code'             => '
                    // phpcs:ignore Generic.Commenting.Todo -- Because reasons
                    $var = FALSE; //TODO: write some code
                    $var = FALSE; //TODO: write some code',
                'expectedErrors'   => 2,
                'expectedWarnings' => 1,
            ],
            'disable: single sniff; enable: same sniff - test whitespace handling around reason delimiter' => [
                'code'             => '
                    // phpcs:disable Generic.Commenting.Todo --Because reasons
                    $var = FALSE;
                    //TODO: write some code
                    // phpcs:enable Generic.Commenting.Todo   --  Because reasons
                    //TODO: write some code',
                'expectedErrors'   => 1,
                'expectedWarnings' => 1,
            ],
            'disable: single sniff, multi-line comment'                                                    => [
                'code'             => '
                    /*
                        Disable some checks
                        phpcs:disable Generic.Commenting.Todo
                    */
                    $var = FALSE;
                    //TODO: write some code',
                'expectedErrors'   => 1,
                'expectedWarnings' => 0,
            ],
            'ignore: single sniff, multi-line slash comment'                                               => [
                'code'             => '
                    // Turn off a check for the next line of code.
                    // phpcs:ignore Generic.Commenting.Todo
                    $var = FALSE; //TODO: write some code
                    $var = FALSE; //TODO: write some code',
                'expectedErrors'   => 2,
                'expectedWarnings' => 1,
            ],
            'enable before disable, sniff not in standard'                                                 => [
                'code'             => '
                    // phpcs:enable Generic.PHP.NoSilencedErrors -- Because reasons
                    $var = @delete( $filename );
                    ',
                'expectedErrors'   => 0,
                'expectedWarnings' => 0,
            ],
        ];

    }//end dataCommenting()


}//end class
