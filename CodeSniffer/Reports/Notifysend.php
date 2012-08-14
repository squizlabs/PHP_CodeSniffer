<?php
/**
 * Summary report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Christian Weiske <christian.weiske@netresearch.de>
 * @copyright 2012 Christian Weiske
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Summary report for PHP_CodeSniffer that can be used with notify-send.
 *
 * Supported configuration parameters:
 * - notifysend_path    - Full path to notify-send cli command
 * - notifysend_timeout - Timeout in milliseconds
 * - notifysend_showok  - Show "ok, all fine" messages (0/1)
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Christian Weiske <christian.weiske@netresearch.de>
 * @copyright 2012 Christian Weiske
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Reports_Notifysend
    implements PHP_CodeSniffer_Report
{
    /**
     * Notification timeout in milliseconds
     *
     * @var integer
     */
    protected $timeout = 3000;

    /**
     * Path to notify-send command
     *
     * @var string
     */
    protected $path = 'notify-send';

    /**
     * Show "ok, all fine" messages
     *
     * @var boolean
     */
    protected $showOk = true;


    /**
     * Load configuration data
     */
    public function __construct()
    {
        $path = PHP_CodeSniffer::getConfigData('notifysend_path');
        if ($path !== null) {
            $this->path = $path;
        }

        $timeout = PHP_CodeSniffer::getConfigData('notifysend_timeout');
        if ($timeout !== null) {
            $this->timeout = (int)$timeout;
        }

        $showOk = PHP_CodeSniffer::getConfigData('notifysend_showok');
        if ($showOk !== null) {
            $this->showOk = (boolean)$showOk;
        }
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
        $report, $showSources = false, $width = 80,$toScreen = true
    ) {
        $msg = $this->generateMessage($report);
        if ($msg === null) {
            if ($this->showOk) {
                $this->notifyAllFine();
            }
            return 0;
        }

        $this->notifyErrors($msg);

        return ($report['totals']['errors'] + $report['totals']['warnings']);
    }

    /**
     * Generate the error message to show to the user
     *
     * @param array $report CS report data
     *
     * @return string Error message or NULL if no error/warning found
     */
    protected function generateMessage($report)
    {
        $allErrors   = $report['totals']['errors'];
        $allWarnings = $report['totals']['warnings'];

        if ($allErrors == 0 && $allWarnings == 0) {
            // Nothing to print.
            return null;
        }

        $msg = '';
        if (count($report['files']) > 1) {
            $msg .= 'Checked ' . count($report['files']) . ' files' . PHP_EOL;
        } else {
            $msg .= key($report['files']) . PHP_EOL;
        }
        if ($allWarnings > 0) {
            $msg .= $allWarnings . ' warnings' . PHP_EOL;
        }
        if ($allErrors > 0) {
            $msg .= $allErrors . ' errors' . PHP_EOL;
        }

        return $msg;
    }

    /**
     * Tell the user that all is fine and no error/warning has been found.
     *
     * @return void
     */
    protected function notifyAllFine()
    {
        exec(
            $this->getBasicCommand()
            . ' -i info'
            . ' "PHP CodeSniffer: Ok"'
            . ' "All fine"'
        );
    }


    /**
     * Tell the user that errors/warnings have been found.
     *
     * @param string $msg Message to display
     *
     * @return void
     */
    protected function notifyErrors($msg)
    {
        exec(
            $this->getBasicCommand()
            . ' -i error'
            . ' "PHP CodeSniffer: Error"'
            . ' ' . escapeshellarg(trim($msg))
        );
    }


    /**
     * Generate and return the basic notify-send command string to execute
     *
     * @return string Shell command with common parameters
     */
    protected function getBasicCommand()
    {
        return escapeshellcmd($this->path)
            . ' --category dev.validate'
            . ' -a phpcs'
            . ' -t ' . (int) $this->timeout;
    }
}

?>
