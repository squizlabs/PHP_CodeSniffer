<?php
class SomethingActions
{

    private static function _x()
    {
    }


    public static function y()
    {
        self::z();
        static::z();
        SomethingActions::z();
        static::_x();
        self::a();
        static::a();
    }


    public static function z()
    {
    }

    protected static function a()
    {
        self::a(); // recursion, yay!
        self::z();
        static::y();
        self::b();
        echo self::$_myVar;
        echo static::$yourVar;
    }
}

abstract class AbstractEditingScreenModeWidgetActions extends AbstractEditingModeWidgetActions {

    public static function getScreens($systemName)
    {

    }//end getScreens()

    public static function setHelpScreenTitle()
    {
        // This is allowed because we are in an abstract class.
        $screens = self::getScreens('');

    }//end setHelpScreenTitle()

}//end class
?>
