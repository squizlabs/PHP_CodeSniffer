<?php
/**
 * A factory to create a baseline collection from a given file
 *
 * @author    Frank Dekker <fdekker@123inkt.nl>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Baseline;

use PHP_CodeSniffer\Exceptions\RuntimeException;

class BaselineSetFactory
{


    /**
     * Read the baseline violations from the given filename path.
     *
     * @param string $fileName the baseline file to import
     *
     * @return BaselineSet|null
     * @throws RuntimeException
     */
    public static function fromFile($fileName)
    {
        if (file_exists($fileName) === false) {
            return null;
        }

        $xml = @simplexml_load_string(file_get_contents($fileName));
        if ($xml === false) {
            throw new RuntimeException('Unable to read xml from: '.$fileName);
        }

        $baselineSet = new BaselineSet();

        foreach ($xml->children() as $node) {
            if ($node->getName() !== 'violation') {
                continue;
            }

            if (isset($node['sniff']) === false) {
                throw new RuntimeException('Missing `sniff` attribute in `violation` in '.$fileName);
            }

            if (isset($node['file']) === false) {
                throw new RuntimeException('Missing `file` attribute in `violation` in '.$fileName);
            }

            if (isset($node['signature']) === false) {
                throw new RuntimeException('Missing `signature` attribute in `violation` in '.$fileName);
            }

            // Normalize filepath (if needed).
            $filePath = '/'.ltrim(str_replace('\\', '/', (string) $node['file']), '/');

            $baselineSet->addEntry(new ViolationBaseline((string) $node['sniff'], $filePath, (string) $node['signature']));
        }//end foreach

        return $baselineSet;

    }//end fromFile()


}//end class
