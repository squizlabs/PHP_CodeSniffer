<?php

/**
 * File docblock.
 *
 * @package Vendor\Package
 */

class Foo {
    /**
     * Function docblock.
     */
    public function bar() {
        do_something();
        ?>
        <p>Demo</p>

        <?php
        /**
         * Arbitrary docblock.
         */
        api_call();
    }
}
