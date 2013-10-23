<?php
/**
 * JUnit report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Oleg Lobach <oleg@lobach.info>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * JUnit report for PHP_CodeSniffer.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Oleg Lobach <oleg@lobach.info>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Reports_Junit implements PHP_CodeSniffer_Report
{


    /**
     * Prints all violations for processed files, in a JUnit format.
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
    public function generate(
        $report,
        $showSources=false,
        $width=80,
        $toScreen=true
    ) {
        $errors = 0;
        $tests  = 0;
        foreach ($report['files'] as $file) {
            if (count($file['messages']) === 0) {
                $tests++;
                continue;
            }

            $errors += ($file['errors'] + $file['warnings']);
            $tests  += ($file['errors'] + $file['warnings']);
        }

        $out = new XMLWriter;
        $out->openMemory();
        $out->setIndent(true);
        $out->startDocument('1.0', 'UTF-8');

        $out->startElement('testsuites');
        $out->writeAttribute('name', 'PHP_CodeSniffer '.PHP_CodeSniffer::VERSION);
        $out->writeAttribute('tests', $tests);
        $out->writeAttribute('failures', $errors);

        $errorsShown = 0;
        foreach ($report['files'] as $filename => $file) {
            $out->startElement('testsuite');
            $out->writeAttribute('name', $filename);

            if (count($file['messages']) === 0) {
                $out->writeAttribute('tests', 1);
                $out->writeAttribute('failures', 0);

                $out->startElement('testcase');
                $out->writeAttribute('name', $filename);
                $out->endElement();

                $out->endElement();
                continue;
            }

            $failures = ($file['errors'] + $file['warnings']);
            $out->writeAttribute('tests', $failures);
            $out->writeAttribute('failures', $failures);

            foreach ($file['messages'] as $line => $lineErrors) {
                foreach ($lineErrors as $column => $colErrors) {
                    foreach ($colErrors as $error) {
                        $out->startElement('testcase');
                        $out->writeAttribute('name', $error['source']." at $filename ($line:$column)");

                        $error['type'] = strtolower($error['type']);
                        if (PHP_CODESNIFFER_ENCODING !== 'utf-8') {
                            $error['message'] = iconv(PHP_CODESNIFFER_ENCODING, 'utf-8', $error['message']);
                        }

                        $out->startElement('failure');
                        $out->writeAttribute('type', $error['type']);
                        $out->writeAttribute('message', $error['message']);
                        $out->endElement();

                        $out->endElement();

                        $errorsShown++;
                    }
                }
            }//end foreach

            $out->endElement();

        }//end foreach

        $out->endElement();
        echo $out->flush();

        return $errorsShown;

    }//end generate()


}//end class

?>
