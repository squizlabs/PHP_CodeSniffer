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

    public function getAnonymousClass() {
        return new class() {
            public static function something() {
                $this->doSomething();
            }
        };
    }
}

trait MyTrait {
    public static function myFunc() {
        $this->doSomething();
    }
}

$b = new class()
{
    public static function myFunc() {
        $this->doSomething();
    }

    public static function other() {
        return fn () => $this->name;
    }

    public static function anonClassUseThis() {
        return new class($this) {
            public function __construct($class) {
            }
        };
    }

    public static function anonClassAnotherThis() {
        return new class() {
            public function __construct() {
                $this->id = 1;
            }
        };
    }

    public static function anonClassNestedUseThis() {
        return new class(new class($this) {}) {
        };
    }

    public static function anonClassNestedAnotherThis() {
        return new class(new class() {
            public function __construct() {
                $this->id = 1;
            }
        }) {
        };
    }

    public static function thisMustBeLowercase() {
        $This = 'hey';

        return $This;
    }
}
