<?php

function test()
{
    $a = array();

    $b = Array();

    $c = Array(
            'a' => 1,
         );
}


class TestClass
{
    public $good = array(
                    'width'  => '',
                    'height' => '',
                   );

    private $_bad = Array(
                    'width' => '',
                    'height' => ''
                     );


    public function test()
    {
        $truck = array(
                 'width' => '',
                  'height' => '',
                 );

        $plane = Array(
                'width' => '',
                  'height' => '',
                 );

        $car = array(
                'width' => '',
                'height' => '',
               );

        $bus = array(
                'width'  =>   '',
                'height' => ''
               );

        $train = array(
                   TRUE,
                  FALSE,
                  'aaa',
                 );

        $inline = array('aaa', 'bbb', 'ccc');
        $inline = array('aaa');
        $inline = Array('aaa');

        $bigone = array(
                   'name' => 'bigone',
                   'children'   => Array(
                                    '1a' => 'child',
                                    '11b' => 'child',
                                    '111c'      => 'child',
                                    'children'  => Array(
                                                    'child' => 'aaa',
                                                   ),
                                   ),
                   'short_name' => 'big'
                  );
    }

}//end class

?>