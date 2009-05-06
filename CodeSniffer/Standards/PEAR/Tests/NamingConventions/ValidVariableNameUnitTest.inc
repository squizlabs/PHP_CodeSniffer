<?php
class MyClass
{
    $varName  = 'hello';
    $var_name = 'hello';
    $varname  = 'hello';
    $_varName = 'hello';

    public $varName  = 'hello';
    public $var_name = 'hello';
    public $varname  = 'hello';
    public $_varName = 'hello';

    protected $varName  = 'hello';
    protected $var_name = 'hello';
    protected $varname  = 'hello';
    protected $_varName = 'hello';

    private $_varName  = 'hello';
    private $_var_name = 'hello';
    private $_varname  = 'hello';
    private $varName   = 'hello';
}

class MyClass
{
  function func1()
  {
    function func2()
    {
     return $a;
    }
    return $data;
  }
}

class MyClass
{
    public function prepare() {}
    public function paint() {}
}

if (true) {
    class MyClass
    {
        $varName  = 'hello';
        $var_name = 'hello';
    }
}

class MyClass {
    function myFunction($cc, $cv) {
        $req = "delete from blah
                where not (POP_{$cc}_A =
'{$this->def["POP_{$cc}_A"]}'
                         and POP_{$cc}_B =
'{$this->def["POP_{$cc}_B"]}')";
    }
}

class mpgResponse{
   var $term_id;
   var $currentTag;
   function characterHandler($parser,$data){
       switch($this->currentTag)
       {
           case "term_id": {
               $this->term_id=$data;
               break;
           }
       }
   }//end characterHandler
}//end class mpgResponse
?>
