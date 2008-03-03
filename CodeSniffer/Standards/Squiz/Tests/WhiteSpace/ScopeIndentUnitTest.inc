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
    }
}
?>
