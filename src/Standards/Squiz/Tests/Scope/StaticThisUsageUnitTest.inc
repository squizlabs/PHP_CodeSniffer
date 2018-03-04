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

    public static function /* */ func1()
    {
        return $this->setValue($result);
    }

    public static function
    func1()
    {
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

    public static function func1() {
        return function() {
            echo $this->name;
        };
    }

    private static function func1(array $data)
    {
        return new class()
        {
            private $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }
        };
    }
}
