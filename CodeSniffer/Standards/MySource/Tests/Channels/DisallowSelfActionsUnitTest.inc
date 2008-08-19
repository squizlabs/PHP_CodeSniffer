<?php
class SomethingActions
{

    private static function _x()
    {
    }


    public static function y()
    {
        self::z();
        SomethingActions::z();
        self::_x();
        self::a();
    }


    public static function z()
    {
    }

    protected static function a()
    {
        self::a(); // recursion, yay!
        self::z();
        self::y();
        self::b();
        echo self::$_myVar;
        echo self::$yourVar;
    }
}
?>
