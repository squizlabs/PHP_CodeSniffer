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


    /**
     * Needs at least 1 throws tag, even though we
     * don't know what it is.
     */
    public function notOKVariable()
    {
        try {
            // Do something.
        } catch (PHP_Exception2 $e) {
            logError();
            throw $e;
        }

    }//end notOKVariable()


    /**
     * Needs at least 1 throws tag, even though we
     * don't know what it is.
     *
     * @throws PHP_Exception1
     */
    public function okVariable()
    {
        throw new PHP_Exception1('Error');

        try {
            // Do something.
        } catch (PHP_Exception1 $e) {
            logError();
            throw $e;
        }

    }//end okVariable()


    /**
     * Needs 1 @throws tag.
     *
     * @throws Exception Unknown exception type.
     */
    function okVariable()
    {
        throw $e;

    }//end okVariable()


    /**
     * Needs 1 @throws tag.
     *
     * @throws Exception Unknown exception type.
     */
    function okFunction()
    {
        throw $this->callSomeFunction();

    }//end okFunction()


    /**
     * Comment inside function.
     *
     * @throws Exception
     */
    function okFunction()
    {
        /**
         * @var FooClass
         */
        $foo = FooFactory::factory();
        throw new Exception;

    }//end okFunction


}//end class

class NamespacedException {
    /**
     * @throws \Exception
     */
    public function foo() {
        throw new \Exception();
    }

    /**
     * @throws \Foo\Bar\FooBarException
     */
    public function fooBar2() {
        throw new \Foo\Bar\FooBarException();
    }
    
    /**
     * @throws FooBarException
     */
    public function fooBar2() {
        throw new \Foo\Bar\FooBarException();
    }
}

class Foo {
    /**
     * Comment
     */
    public function foo() {
    }//end foo()

    public function bar() {
        throw new Exception();
    }
}
?>
