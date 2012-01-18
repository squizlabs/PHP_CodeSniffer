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
