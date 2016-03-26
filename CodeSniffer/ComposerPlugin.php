<?php
/**
 * Composer plugin class to register the Composer standards installer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2016 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * Composer plugin class to register the Composer standards installer.
 *
 * This registers the Composer installer that is responsible for
 * installing standards into the correct folder.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Alain Schlesser <alain.schlesser@gmail.com>
 * @copyright 2006-2016 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_ComposerPlugin implements PluginInterface
{


    /**
     * Activate the Composer plugin.
     *
     * @param Composer    $composer Reference to the Composer instance.
     * @param IOInterface $io       Reference to the IO interface.
     *
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $installer = new PHP_CodeSniffer_ComposerInstaller($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);

    }//end activate()


}//end class
