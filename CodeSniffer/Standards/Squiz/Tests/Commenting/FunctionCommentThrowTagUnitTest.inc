<?php
class FunctionCommentThrowTagUnitTest
{


    /**
     * Missing throw tag.
     *
     */
    public function missingThrowTag()
    {
        throw new PHP_Exception('Error');

    }//end missingThrowTag()


    /**
     * Tag and token number mismatch.
     *
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
     * @throws PHP_Wrong_Exception
     */
    public function wrongExceptionName()
    {
        throw new PHP_Correct_Exception('Error');

    }//end wrongExceptionName()


    /**
     * Wrong exception type name.
     *
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


    /**
     * Wrong exception type name.
     *
     * @throws PHP_Correct_Exception
     */
    public function sameExceptionName()
    {
        throw new PHP_Correct_Exception('Error');
        throw new PHP_Correct_Exception('Error');

    }//end sameExceptionName()


    /**
     * This is a valid chunk.
     *
     * @throws PHP_Exception1
     * @throws PHP_Exception2
     */
    public function thisShouldWorkOK()
    {
        throw new PHP_Exception2('Error');
        throw new PHP_Exception1('Error');
        throw new PHP_Exception2('Error');
        throw new PHP_Exception1('Error');
        throw new PHP_Exception2('Error');

    }//end thisShouldWorkOK()


    /**
     * This is not OK, missing 2 @throws tag.
     *
     * @throws PHP_Exception1
     * @throws PHP_Exception2
     */
    public function notOK()
    {
        throw new PHP_Missing_Exception1('Error');
        throw new PHP_Exception2('Error');
        throw new PHP_Missing_Exception2('Error');
        throw new PHP_Missing_Exception2('Error');
        throw new PHP_Exception1('Error');
        throw new PHP_Exception2('Error');
        throw new PHP_Missing_Exception1('Error');

    }//end notOK()


}//end class
?>
