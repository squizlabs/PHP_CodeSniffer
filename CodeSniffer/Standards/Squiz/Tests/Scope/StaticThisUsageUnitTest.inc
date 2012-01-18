<?php
class MyClass
{
    public static function func1()
    {
        $value = 'hello';
        $newValue = array($this->func2());
        $result = $this->getValue($value);
        return $this->setValue($result);
    }

    public function func1()
    {
        $value = 'hello';
        $newValue = array($this->func2());
        $result = $this->getValue($value);
        return $this->setValue($result);
    }

    function func1()
    {
        $value = 'hello';
        $newValue = array($this->func2());
        $result = $this->getValue($value);
        return $this->setValue($result);
    }
}
?>