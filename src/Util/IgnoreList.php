<?php
/**
 * Class to manage a list of sniffs to ignore.
 *
 * @author    Brad Jorsch <brad.jorsch@automattic.com>
 * @copyright 2023 Brad Jorsch
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util;

class IgnoreList
{

    /**
     * Ignore data.
     *
     * Data is a tree, standard → category → sniff → code.
     * Each level may be a boolean indicating that everything underneath the branch is or is not ignored, or
     * may have a `.default' key indicating the default status for any branches not in the tree.
     *
     * @var array|boolean
     */
    private $data = [ '.default' => false ];


    /**
     * Get an instance set to ignore nothing.
     *
     * @return static
     */
    public static function ignoringNone()
    {
        return new static();

    }//end ignoringNone()


    /**
     * Get an instance set to ignore everything.
     *
     * @return static
     */
    public static function ignoringAll()
    {
        $ret = new static();
        $ret->data['.default'] = true;
        return $ret;

    }//end ignoringAll()


    /**
     * Check whether a sniff code is ignored.
     *
     * @param string $code Partial or complete sniff code.
     *
     * @return bool
     */
    public function check($code)
    {
        $data = $this->data;
        $ret  = $data['.default'];
        foreach (explode('.', $code) as $part) {
            if (isset($data[$part]) === false) {
                break;
            }

            $data = $data[$part];
            if (is_bool($data) === true) {
                $ret = $data;
                break;
            }

            if (isset($data['.default']) === true) {
                $ret = $data['.default'];
            }
        }

        return $ret;

    }//end check()


    /**
     * Set the ignore status for a sniff.
     *
     * @param string $code   Partial or complete sniff code.
     * @param bool   $ignore Whether the specified sniff should be ignored.
     *
     * @return this
     */
    public function set($code, $ignore)
    {
        $data  = &$this->data;
        $parts = explode('.', $code);
        while (count($parts) > 1) {
            $part = array_shift($parts);
            if (isset($data[$part]) === false) {
                $data[$part] = [];
            } else if (is_bool($data[$part]) === true) {
                $data[$part] = [ '.default' => $data[$part] ];
            }

            $data = &$data[$part];
        }

        $part        = array_shift($parts);
        $data[$part] = (bool) $ignore;

        return $this;

    }//end set()


    /**
     * Check if the list is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        $arrs = [ $this->data ];
        while ($arrs !== []) {
            $arr = array_pop($arrs);
            foreach ($arr as $v) {
                if ($v === true) {
                    return false;
                }

                if (is_array($v) === true) {
                    $arrs[] = $v;
                }
            }
        }

        return true;

    }//end isEmpty()


    /**
     * Check if the list ignores everything.
     *
     * @return bool
     */
    public function isAll()
    {
        $arrs = [ $this->data ];
        while ($arrs !== []) {
            $arr = array_pop($arrs);
            foreach ($arr as $v) {
                if ($v === false) {
                    return false;
                }

                if (is_array($v) === true) {
                    $arrs[] = $v;
                }
            }
        }

        return true;

    }//end isAll()


}//end class
