<?php
/**
 * Tests for \PHP_CodeSniffer\Config
 *
 * @author    Tom Klingenberg <ktomk@users.github.com>
 * @copyright 2017 Contributors
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core;

use PHP_CodeSniffer\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{


    /**
     * Flag config-file existence
     *
     * @var boolean
     */
    private $configExisted;


    /**
     * Configuration file path
     *
     * @var string
     */
    private $configFile;

    /**
     * Configuration file permissions
     *
     * @var null|int
     */
    private $configPerms;


    /**
     * Test set up
     *
     * @return void
     */
    public function setUp()
    {
        $this->configFile    = $this->getConfigPath();
        $this->configExisted = file_exists($this->configFile);
        if (true === $this->configExisted) {
            $this->configPerms = fileperms($this->configFile);
        }

        parent::setUp();

    }//end setUp()


    /**
     * Test tear down
     *
     * @return void
     */
    public function tearDown()
    {

        $fileExists = file_exists($this->configFile);
        if (true === $fileExists && null !== $this->configPerms) {
            chmod($this->configFile, ($this->configPerms & 0777));
        }

        if (false === $this->configExisted && true === $fileExists) {
            unlink($this->configFile);
        }

        parent::tearDown();

    }//end tearDown()


    /**
     * Test read-only config file throws a RuntimeException
     *
     * @return void
     */
    public function testReadonlyConfigThrowsPhpCodeSnifferRuntimeException()
    {
        $exception = $this->readonlyConfig(
            function () {
                Config::setConfigData('foo', 'bar');
            }
        );

        $this->assertInstanceOf(
            'PHP_CodeSniffer\Exceptions\RuntimeException',
            $exception,
            'Expected exception class'
        );

    }//end testReadonlyConfigThrowsPhpCodeSnifferRuntimeException()


    /**
     * Test read-only config file throws a RuntimeException
     *
     * @return void
     */
    public function testReadonlyConfigSettingValue()
    {
        $exception = $this->readonlyConfig(
            function () {
                $args = array(
                         '--config-set',
                         'foo',
                         'bar',
                        );
                new Config($args);
            }
        );

        $this->assertNotNull(
            $exception,
            'An expected exception was not thrown'
        );

        $this->assertInstanceOf(
            'PHP_CodeSniffer\Exceptions\DeepExitException',
            $exception
        );

    }//end testReadonlyConfigSettingValue()


    /**
     * Make callback operate on a read-only config file, helper function
     * for tests.
     *
     * @param callable $callback to be run with a readonly config
     *
     * @return NULL|\Exception
     */
    private function readonlyConfig($callback)
    {
        if (true === defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->markTestSkipped('Test not valid for windows');
        }

        $file = $this->getConfigPath();

        if (false === file_exists($this->configFile)) {
            touch($file);
        }

        $this->assertFileExists(
            $this->configFile,
            'precondition: config file exists'
        );
        $this->assertTrue(
            is_writable($this->configFile),
            'precondition: config file is writable'
        );

        $result = fileperms($this->configFile);

        $savedPermission = ($result & 0777);

        $this->assertInternalType(
            'int',
            $result,
            'precondition: fileperms available'
        );

        $this->assertTrue(
            chmod($this->configFile, 0444),
            'precondition: read-only config file'
        );

        $exception = null;
        try {
            call_user_func($callback);
        } catch (\Exception $exception) {
            // Fall-through intended.
        }

        chmod($this->configFile, $savedPermission);

        return $exception;

    }//end readonlyConfig()


    /**
     * Get path of the configuration file.
     *
     * @return string
     */
    private function getConfigPath()
    {
        return __DIR__.'/../../CodeSniffer.conf';

    }//end getConfigPath()


}//end class
