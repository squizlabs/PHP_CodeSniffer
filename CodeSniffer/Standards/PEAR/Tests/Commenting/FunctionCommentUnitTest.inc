<?php
class PHP_CodeSniffer_File
{

    /**
     * Constructs a PHP_CodeSniffer_File.
     *
     * long desc here
     *
     * @param int $stackPtr   The position in the stack of the token that opened
     *                        the scope (eg. an IF token or FOR token).
     * @param int $detph    How many scope levels down we are.
     * @param int    $index    the index
     * @return
     * @throws
     */
    private function _recurseScopeMap($stackPtr, $depth=1, $index)
    {
        $this->_recurseScopeMap($stackPtr, $depth);
        return $stackPtr;

    }//end _recurseScopeMap()

}//end class

?>
