<?php


class SelfMemberReferenceUnitTestExample
{


    private $test_count = 0;


    private $test_results = array();


    public function SelfMemberReferenceUnitTestExample()
    {
        $test_results =& $this->test_results;


        // Correct call to self.
        $test_results[] = self::selfMemberReferenceUnitTestFunction();


        // Incorrect case.
        $test_results[] = Self::selfMemberReferenceUnitTestFunction();
        $test_results[] = SELF::selfMemberReferenceUnitTestFunction();


        // Incorrect spacing.
        $test_results[] = self ::selfMemberReferenceUnitTestFunction();
        $test_results[] = self:: selfMemberReferenceUnitTestFunction();
        $test_results[] = self :: selfMemberReferenceUnitTestFunction();

    }


    function selfMemberReferenceUnitTestFunction()
    {
        $this->test_count = $this->test_count + 1;
        return $this->test_count;

    }


}


?>