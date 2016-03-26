<?php
/**
 * Composer installer class to handle installation of additional standards.
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

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

/**
 * Composer installer class to handle installation of additional standards.
 *
 * This makes sure that Composer packages of type
 * `phpcs-standard` are installed in the correct subfolder.
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
class PHP_CodeSniffer_ComposerInstaller extends LibraryInstaller
{

    const EXTRA_KEY      = 'standards';
    const STANDARDS_PATH = '../php_codesniffer/CodeSniffer/Standards/';
    const TYPE           = 'phpcs-standard';


    /**
     * Install the package.
     *
     * @param InstalledRepositoryInterface $repo    The repository from where the package was fetched.
     * @param PackageInterface             $package The package to install.
     *
     * @return void
     */
    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
        $installPath = $this->getInstallPath($package);

        if ($this->io->isVerbose() === true) {
            $this->io->write(
                sprintf(
                    _('Symlinking PHPCS standards package %1$s'),
                    $installPath
                ),
                true
            );
        }

        parent::install($repo, $package);

        foreach ($this->getStandards($package) as $standardName => $standardPath) {
            $this->linkStandard(
                $standardName,
                $installPath.DIRECTORY_SEPARATOR.$standardPath
            );
        }

    }//end install()


    /**
     * Whether the installer supports a given package type.
     *
     * @param string $packageType Type of the package to check support for.
     *
     * @return bool Whether the package is supported.
     */
    public function supports($packageType)
    {
        return self::TYPE === $packageType;

    }//end supports()


    /**
     * Get the list of standards to add.
     *
     * @param PackageInterface $package The package for which to retrieve the standards.
     *
     * @return array List of standards.
     */
    protected function getStandards(PackageInterface $package)
    {
        $extraData = $package->getExtra();

        // Bail early if no "extra" key found.
        if (empty($extraData) === true) {
            return array();
        }

        // Bail early if "extra" key does not contain "standards" key.
        if (array_key_exists(self::EXTRA_KEY, $extraData) === false) {
            return array();
        }

        return (array) $extraData[self::EXTRA_KEY];

    }//end getStandards()


    /**
     * Link a standard to the PHPCS installation.
     *
     * @param string $name Name of the standard.
     * @param string $path Relative path of the standard.
     *
     * @return void
     */
    protected function linkStandard($name, $path)
    {
        if ($this->io->isVeryVerbose() === true) {
            $this->io->write(
                sprintf(
                    _('Symlinking standard "%1$s" to path "%2$s"'),
                    $name,
                    $path
                ),
                true
            );
        }

        $linkPath = getcwd().DIRECTORY_SEPARATOR.self::STANDARDS_PATH.$name;

        $this->filesystem->relativeSymlink($path, $linkPath);

    }//end linkStandard()


}//end class
