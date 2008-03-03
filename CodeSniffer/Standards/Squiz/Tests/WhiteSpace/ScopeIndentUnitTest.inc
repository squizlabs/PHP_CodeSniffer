<?php
ob_start();
    foreach ($request->validators as $validator) {
        if ($result === FALSE) {
            $valid = FALSE;
        }
    }
ob_end_clean();

ob_start();
foreach ($request->validators as $validator) {
    if ($result === FALSE) {
        $valid = FALSE;
    }
}
ob_end_clean();

ob_start();
    foreach ($request->validators as $validator) {
        ob_start();
        if ($result === FALSE) {
            $valid = FALSE;
        }
        ob_end_clean();
    }
ob_end_clean();

function myFunction()
{
    ob_start();
        foreach ($request->validators as $validator) {
            if ($result === FALSE) {
                $valid = FALSE;
            }
        }
    ob_end_clean();
    
    ob_start();
    foreach ($request->validators as $validator) {
        if ($result === FALSE) {
            $valid = FALSE;
        }
    }
    ob_end_clean();

    ob_start();
        foreach ($request->validators as $validator) {
            ob_start();
            if ($result === FALSE) {
                $valid = FALSE;
            }
            ob_end_clean();
        }
    ob_end_clean();

}//end myFunction()

foreach ($request->validators as $validator) {
    if ($result === FALSE) {
          $valid = FALSE;
    }//end if
}//end foreach

class MyClass
{
    /**
     * Function comment
     */
    function myFunction()
    {
        /*
            This is a comment
            over multiple lines.
        */

        $x = array(
          '1' => $one,
          '2' => $two,
         );

        switch ($var) {
            case 'one':
                echo 'one';
            break;
            default:
                echo 'two';
            break;
        }//end switch
    }//end myFunction()
}
?>
