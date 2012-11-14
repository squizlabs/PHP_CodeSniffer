<?php
/**
 * Notify-send report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Christian Weiske <christian.weiske@netresearch.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2012 Christian Weiske
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Notify-send report for PHP_CodeSniffer.
 *
 * Supported configuration parameters:
 * - notifysend_path    - Full path to notify-send cli command
 * - notifysend_timeout - Timeout in milliseconds
 * - notifysend_showok  - Show "ok, all fine" messages (0/1)
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Christian Weiske <christian.weiske@netresearch.de>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2012 Christian Weiske
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Reports_Notifysend implements PHP_CodeSniffer_Report
{

    /**
     * Notification timeout in milliseconds.
     *
     * @var integer
     */
    protected $timeout = 3000;

    /**
     * Path to notify-send command.
     *
     * @var string
     */
    protected $path = 'notify-send';

    /**
     * Show "ok, all fine" messages.
     *
     * @var boolean
     */
    protected $showOk = true;

    /**
     * A record of the last file checked.
     *
     * This is used in case we only checked one file and need to print
     * the name/path of the file. We wont have access to the checked file list
     * after the run has been completed.
     *
     * @var string
     */
    private $_lastCheckedFile = '';


    /**
     * Load configuration data.
     *
     * @return void
     */
    public function __construct()
    {
        $path = PHP_CodeSniffer::getConfigData('notifysend_path');
        if ($path !== null) {
            $this->path = $path;
        }

        $timeout = PHP_CodeSniffer::getConfigData('notifysend_timeout');
        if ($timeout !== null) {
            $this->timeout = (int) $timeout;
        }

        $showOk = PHP_CodeSniffer::getConfigData('notifysend_showok');
        if ($showOk !== null) {
            $this->showOk = (boolean) $showOk;
        }

    }//end __construct()


    /**
     * Prints all violations for processed files, in a Checkstyle format.
     *
     * Violations are grouped by file.
     *
     * @param array   $report      Prepared report.
     * @param boolean $showSources Show sources?
     * @param int     $width       Maximum allowed lne width.
     * @param boolean $toScreen    Is the report being printed to screen?
     *
     * @return string
     */
    public function generateFileReport(
        $report,
        $showSources=false,
        $width=80
    ) {
        // We don't need to print anything, but we want this file counted
        // in the total number of checked files even if it has no errors.
        $this->_lastCheckedFile = $report['filename'];
        return true;
    }


    /**
     * Generates a summary of errors and warnings for each file processed.
     *
     * If verbose output is enabled, results are shown for all files, even if
     * they have no errors or warnings. If verbose output is disabled, we only
     * show files that have at least one warning or error.
     *
     * @param array   $report      Prepared report.
     * @param boolean $showSources Show sources?
     * @param int     $width       Maximum allowed line width.
     * @param boolean $toScreen    Is the report being printed to screen?
     *
     * @return string
     */
    public function generate(
        $cachedData,
        $totalFiles,
        $totalErrors,
        $totalWarnings,
        $showSources=false,
        $width=80,
        $toScreen=true
    ) {
        $msg = $this->generateMessage($totalFiles, $totalErrors, $totalWarnings);
        if ($msg === null) {
            if ($this->showOk) {
                $this->notifyAllFine();
            }
        } else {
            $this->notifyErrors($msg);
        }

    }//end generate()


    /**
     * Generate the error message to show to the user.
     *
     * @param array $report CS report data.
     *
     * @return string Error message or NULL if no error/warning found.
     */
    protected function generateMessage($totalFiles, $totalErrors, $totalWarnings)
    {
        if ($totalErrors === 0 && $totalWarnings === 0) {
            // Nothing to print.
            return null;
        }

        $msg = '';
        if ($totalFiles > 1) {
            $msg .= 'Checked '.$totalFiles.' files'.PHP_EOL;
        } else {
            $msg .= $this->_lastCheckedFile.PHP_EOL;
        }

        if ($totalWarnings > 0) {
            $msg .= $totalWarnings.' warnings'.PHP_EOL;
        }

        if ($totalErrors > 0) {
            $msg .= $totalErrors.' errors'.PHP_EOL;
        }

        return $msg;

    }//end generateMessage()


    /**
     * Tell the user that all is fine and no error/warning has been found.
     *
     * @return void
     */
    protected function notifyAllFine()
    {
        $cmd  = $this->getBasicCommand();
        $cmd .= ' -i info';
        $cmd .= ' "PHP CodeSniffer: Ok"';
        $cmd .= ' "All fine"';

        exec($cmd);

    }//end notifyAllFine()


    /**
     * Tell the user that errors/warnings have been found.
     *
     * @param string $msg Message to display.
     *
     * @return void
     */
    protected function notifyErrors($msg)
    {
        $cmd  = $this->getBasicCommand();
        $cmd .= ' -i error';
        $cmd .= ' "PHP CodeSniffer: Error"';
        $cmd .= ' '.escapeshellarg(trim($msg));

        exec($cmd);

    }//end notifyErrors()


    /**
     * Generate and return the basic notify-send command string to execute.
     *
     * @return string Shell command with common parameters.
     */
    protected function getBasicCommand()
    {
        $cmd = escapeshellcmd($this->path);
        $cmd .= ' --category dev.validate';
        $cmd .= ' -a phpcs';
        $cmd .= ' -t '.(int) $this->timeout;

        return $cmd;

    }//end getBasicCommand()


}//end class

?>
