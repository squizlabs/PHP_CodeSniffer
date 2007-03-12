<?php
class FunctionCommentThrowTagUnitTest
{


    /**
     * Missing throw tag.
     *
     * @since  4.0.0
     * @return void
     */
    public function missingThrowTag()
    {
        throw new PHP_Exception('Error');

    }//end missingThrowTag()


    /**
     * Tag and token number mismatch.
     *
     * @since  4.0.0
     * @return void
     * @throws PHP_Exception1
     */
    public function oneMoreThrowsTagNeeded()
    {
        throw new PHP_Exception1('Error');
        throw new PHP_Exception2('Error');

    }//end oneMoreThrowsTagNeeded()


    /**
     * Tag and token number mismatch.
     *
     * @since  4.0.0
     * @return void
     * @throws PHP_Exception1
     * @throws PHP_Exception2
     */
    public function oneLessThrowsTagNeeded()
    {
        throw new PHP_Exception1('Error');

    }//end oneLessThrowsTagNeeded()


    /**
     * Wrong exception type name.
     *
     * @since  4.0.0
     * @return void
     * @throws PHP_Wrong_Exception
     */
    public function wrongExceptionName()
    {
        throw new PHP_Correct_Exception('Error');

    }//end wrongExceptionName()


    /**
     * Wrong exception type name.
     *
     * @since  4.0.0
     * @return void
     * @throws PHP_Correct_Exception1
     * @throws PHP_Wrong_Exception2
     * @throws PHP_Wrong_Exception3
     */
    public function moreWrongExceptionName()
    {
        throw new PHP_Correct_Exception1('Error');
        throw new PHP_Correct_Exception2('Error');
        throw new PHP_Correct_Exception3('Error');

    }//end moreWrongExceptionName()


}//end class
?>
