<?php

function test()
{
    $a = array();

    $b = Array ();

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

        $train = array  (
                   TRUE,
                  FALSE,
                  'aaa'
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

$value = array ( );
$value = array(  );
$value = array('1'=>$one, '2'  =>  $two, '3'=> $three, '4' =>$four);
$value = array('1'=>$one);

if (in_array('1', array('1','2','3')) === TRUE) {
    $value = in_array('1', array('1'  ,   '2',     '3','4'));
}

$value = array(
          '1'=> TRUE,
          FALSE,
          '3' => 'aaa',);

$value = array(
          '1'=> TRUE,
          FALSE,
         );

$value = array(
          TRUE,
          '1' => FALSE,
         );

$value = array(1,
          2 ,
          3 ,
         );

$value = array(1 => $one,
          2 => $two ,
          3 => $three ,
         );

$value = array(
          'tag'   => $tag,
          'space' => $this->_getIndentation($tag, $tagElement),
         );

$expected = array(
             array(
              '1' => 1,
              '1' => 2,
             ),
            );

$expected = array(
             array(
              '1' => 1,
              '1' => 2
             )
            );

// Space in second arg.
$args = array(
         '"'.$this->id.'"',
         (int) $hasSessions,
        );

// No errors.
$paths = array(
          Init::ROOT_DIR.'/Systems'   => 'Systems',
          Init::ROOT_DIR.'/Installer' => 'Systems',
         );

$x = array(
     );

$x = array('test'
     );
$x = array('test',
     );
$x = array('name' => 'test',
     );

$x = array(
      $x,
     );

$func = array(
         $x,
         'get'.$x.'Replacement'
        );

$array = array(
          'input_one' => 'one',
          'inputTwo'  => 'two',
          'input_3'   => 3,
         );

$array = array(
          'input_one',
          'inputTwo',
          'input_3',
         );

// Malformed
$foo = array(1
, 2);

$listItems[$aliasPath] = array('itemContent' => implode('<br/>', $aliases));

$listItems[$aliasPath] = array(
                          'itemContent' => implode('<br/>', $aliases)
                         );

$test = array(
         'test' => TestFunction::blah(
             $value1,
             $value2
         ),
        );

$c = array('a' => 1,);

?>
