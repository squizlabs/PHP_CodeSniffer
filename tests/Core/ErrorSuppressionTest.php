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
     * @return void
     */
    public function testNestedSuppressLine()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.PHP.LowerCaseConstant'];

        $ruleset = new Ruleset($config);

        // Process with disable/enable suppression and no single line suppression.
        $content = '<?php '.PHP_EOL.'// phpcs:disable'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with disable/enable @ suppression and no single line suppression.
        $content = '<?php '.PHP_EOL.'// @phpcs:disable'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// @phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with disable/enable suppression and no single line suppression (hash comment).
        $content = '<?php '.PHP_EOL.'# phpcs:disable'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'# phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with disable/enable suppression and no single line suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with line suppression nested within disable/enable suppression.
        $content = '<?php '.PHP_EOL.'// phpcs:disable'.PHP_EOL.'// phpcs:ignore'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with line @ suppression nested within disable/enable @ suppression.
        $content = '<?php '.PHP_EOL.'// @phpcs:disable'.PHP_EOL.'// @phpcs:ignore'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// @phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with line @ suppression nested within disable/enable @ suppression (hash comment).
        $content = '<?php '.PHP_EOL.'# @phpcs:disable'.PHP_EOL.'# @phpcs:ignore'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'# @phpcs:enable';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with line suppression nested within disable/enable suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreStart'.PHP_EOL.'// @codingStandardsIgnoreLine'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'$var = TRUE;'.PHP_EOL.'// @codingStandardsIgnoreEnd';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

    }//end testNestedSuppressLine()


    /**
     * Test suppressing a scope opener.
     *
     * @return void
     */
    public function testSuppressScope()
    {
        $config            = new Config();
        $config->standards = ['PEAR'];
        $config->sniffs    = ['PEAR.NamingConventions.ValidVariableName'];

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'function myFunction() {'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'//phpcs:disable'.PHP_EOL.'function myFunction() {'.PHP_EOL.'//phpcs:enable'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with suppression (hash comment).
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'#phpcs:disable'.PHP_EOL.'function myFunction() {'.PHP_EOL.'#phpcs:enable'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'//@phpcs:disable'.PHP_EOL.'function myFunction() {'.PHP_EOL.'//@phpcs:enable'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'//@codingStandardsIgnoreStart'.PHP_EOL.'function myFunction() {'.PHP_EOL.'//@codingStandardsIgnoreEnd'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with a docblock suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'/** phpcs:disable */'.PHP_EOL.'function myFunction() {'.PHP_EOL.'/** phpcs:enable */'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with a docblock @ suppression.
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'/** @phpcs:disable */'.PHP_EOL.'function myFunction() {'.PHP_EOL.'/** @phpcs:enable */'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

        // Process with a docblock suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'class MyClass() {'.PHP_EOL.'/** @codingStandardsIgnoreStart */'.PHP_EOL.'function myFunction() {'.PHP_EOL.'/** @codingStandardsIgnoreEnd */'.PHP_EOL.'$this->foo();'.PHP_EOL.'}'.PHP_EOL.'}';
        $file    = new DummyFile($content, $ruleset, $config);

        $errors    = $file->getErrors();
        $numErrors = $file->getErrorCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);

    }//end testSuppressScope()


    /**
     * Test suppressing a whole file.
     *
     * @return void
     */
    public function testSuppressFile()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = ['Generic.Commenting.Todo'];

        $ruleset = new Ruleset($config);

        // Process without suppression.
        $content = '<?php '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Process with suppression.
        $content = '<?php '.PHP_EOL.'// phpcs:ignoreFile'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with @ suppression.
        $content = '<?php '.PHP_EOL.'// @phpcs:ignoreFile'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with suppression (hash comment).
        $content = '<?php '.PHP_EOL.'# phpcs:ignoreFile'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with @ suppression (hash comment).
        $content = '<?php '.PHP_EOL.'# @phpcs:ignoreFile'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'// @codingStandardsIgnoreFile'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process mixed case.
        $content = '<?php '.PHP_EOL.'// PHPCS:Ignorefile'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process late comment.
        $content = '<?php '.PHP_EOL.'class MyClass {}'.PHP_EOL.'$foo = new MyClass()'.PHP_EOL.'// phpcs:ignoreFile';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process late comment (deprecated syntax).
        $content = '<?php '.PHP_EOL.'class MyClass {}'.PHP_EOL.'$foo = new MyClass()'.PHP_EOL.'// @codingStandardsIgnoreFile';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with a block comment suppression.
        $content = '<?php '.PHP_EOL.'/* phpcs:ignoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with a multi-line block comment suppression.
        $content = '<?php '.PHP_EOL.'/*'.PHP_EOL.' phpcs:ignoreFile'.PHP_EOL.' */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with a block comment suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'/* @codingStandardsIgnoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with a multi-line block comment suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'/*'.PHP_EOL.' @codingStandardsIgnoreFile'.PHP_EOL.' */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with docblock suppression.
        $content = '<?php '.PHP_EOL.'/** phpcs:ignoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Process with docblock suppression (deprecated syntax).
        $content = '<?php '.PHP_EOL.'/** @codingStandardsIgnoreFile */'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

    }//end testSuppressFile()


    /**
     * Test disabling specific sniffs.
     *
     * @return void
     */
    public function testDisableSelected()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = [
            'Generic.PHP.LowerCaseConstant',
            'Generic.Commenting.Todo',
        ];

        $ruleset = new Ruleset($config);

        // Suppress a single sniff.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress a single sniff with reason (hash comment).
        $content = '<?php '.PHP_EOL.'# phpcs:disable Generic.Commenting.Todo -- for reasons'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress multiple sniffs.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress adding sniffs.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'// phpcs:disable Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress a category of sniffs.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress a whole standard.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress using docblocks.
        $content = '<?php '.PHP_EOL.'/**
        '.PHP_EOL.' * phpcs:disable Generic.Commenting.Todo'.PHP_EOL.' */ '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        $content = '<?php '.PHP_EOL.'/**
        '.PHP_EOL.' * @phpcs:disable Generic.Commenting.Todo'.PHP_EOL.' */ '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress wrong category using docblocks.
        $content = '<?php '.PHP_EOL.'/**
        '.PHP_EOL.' * phpcs:disable Generic.Files'.PHP_EOL.' */ '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        $content = '<?php '.PHP_EOL.'/**
        '.PHP_EOL.' * @phpcs:disable Generic.Files'.PHP_EOL.' */ '.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

    }//end testDisableSelected()


    /**
     * Test re-enabling specific sniffs that have been disabled.
     *
     * @return void
     */
    public function testEnableSelected()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = [
            'Generic.PHP.LowerCaseConstant',
            'Generic.Commenting.Todo',
        ];

        $ruleset = new Ruleset($config);

        // Suppress a single sniff and re-enable.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting.Todo'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress multiple sniffs and re-enable.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress multiple sniffs and re-enable one.
        $content = '<?php '.PHP_EOL.'# phpcs:disable Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'# phpcs:enable Generic.Commenting.Todo'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'$var = FALSE;';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a category of sniffs and re-enable.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a whole standard and re-enable.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a whole standard and re-enable a category.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a category and re-enable a whole standard.
        $content = '<?php '.PHP_EOL.'# phpcs:disable Generic.Commenting'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'# phpcs:enable Generic'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a sniff and re-enable a category.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a whole standard and re-enable a sniff.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting.Todo'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a whole standard and re-enable and re-disable a sniff.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting.Todo'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(2, $numWarnings);
        $this->assertCount(2, $warnings);

        // Suppress a whole standard and re-enable 2 specific sniffs independently.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting.Todo'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'// phpcs:enable Generic.PHP.LowerCaseConstant'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'$var = FALSE;'.PHP_EOL;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(2, $numWarnings);
        $this->assertCount(2, $warnings);

    }//end testEnableSelected()


    /**
     * Test ignoring specific sniffs.
     *
     * @return void
     */
    public function testIgnoreSelected()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = [
            'Generic.PHP.LowerCaseConstant',
            'Generic.Commenting.Todo',
        ];

        $ruleset = new Ruleset($config);

        // No suppression.
        $content = '<?php '.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);
        $this->assertEquals(2, $numWarnings);
        $this->assertCount(2, $warnings);

        // Suppress a single sniff.
        $content = '<?php '.PHP_EOL.'// phpcs:ignore Generic.Commenting.Todo'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress multiple sniffs.
        $content = '<?php '.PHP_EOL.'// phpcs:ignore Generic.Commenting.Todo,Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Add to suppression.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'// phpcs:ignore Generic.PHP.LowerCaseConstant'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress a category of sniffs.
        $content = '<?php '.PHP_EOL.'# phpcs:ignore Generic.Commenting'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a whole standard.
        $content = '<?php '.PHP_EOL.'// phpcs:ignore Generic'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

    }//end testIgnoreSelected()


    /**
     * Test ignoring specific sniffs.
     *
     * @return void
     */
    public function testCommenting()
    {
        $config            = new Config();
        $config->standards = ['Generic'];
        $config->sniffs    = [
            'Generic.PHP.LowerCaseConstant',
            'Generic.Commenting.Todo',
        ];

        $ruleset = new Ruleset($config);

        // Suppress a single sniff.
        $content = '<?php '.PHP_EOL.'// phpcs:ignore Generic.Commenting.Todo -- Because reasons'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a single sniff and re-enable.
        $content = '<?php '.PHP_EOL.'// phpcs:disable Generic.Commenting.Todo --Because reasons'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code'.PHP_EOL.'// phpcs:enable Generic.Commenting.Todo   --  Because reasons'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Suppress a single sniff using block comments.
        $content = '<?php '.PHP_EOL.'/*'.PHP_EOL.'    Disable some checks'.PHP_EOL.'    phpcs:disable Generic.Commenting.Todo'.PHP_EOL.'*/'.PHP_EOL.'$var = FALSE;'.PHP_EOL.'//TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(1, $numErrors);
        $this->assertCount(1, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

        // Suppress a single sniff with a multi-line comment.
        $content = '<?php '.PHP_EOL.'// Turn off a check for the next line of code.'.PHP_EOL.'// phpcs:ignore Generic.Commenting.Todo'.PHP_EOL.'$var = FALSE; //TODO: write some code'.PHP_EOL.'$var = FALSE; //TODO: write some code';
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(2, $numErrors);
        $this->assertCount(2, $errors);
        $this->assertEquals(1, $numWarnings);
        $this->assertCount(1, $warnings);

        // Ignore an enable before a disable.
        $content = '<?php '.PHP_EOL.'// phpcs:enable Generic.PHP.NoSilencedErrors -- Because reasons'.PHP_EOL.'$var = @delete( $filename );'.PHP_EOL;
        $file    = new DummyFile($content, $ruleset, $config);
        $file->process();

        $errors      = $file->getErrors();
        $numErrors   = $file->getErrorCount();
        $warnings    = $file->getWarnings();
        $numWarnings = $file->getWarningCount();
        $this->assertEquals(0, $numErrors);
        $this->assertCount(0, $errors);
        $this->assertEquals(0, $numWarnings);
        $this->assertCount(0, $warnings);

    }//end testCommenting()


}//end class
