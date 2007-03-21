<?php
Channels::includeSystem('MySystem');
require_once 'libs/FileSystem2.inc';

function one() {
    Channels::includeSystem('MySystem2');
    $siteid = MySystem2::getCurrentSiteId();
}

function two() {
    $siteid = MySystem2::getCurrentSiteId();
    $parserFiles = FileSystem2::listDirectory();
}

function three() {
    include 'libs/FileSystem.inc';
    $siteid = MySystem::getCurrentSiteId();
    $parserFiles = FileSystem::listDirectory();
}

$siteid = MySystem2::getCurrentSiteId();
$parserFiles = FileSystem2::listDirectory();
$siteid = MySystem::getCurrentSiteId();
$parserFiles = FileSystem::listDirectory();

class MyClass
{
    function one() {
        Channels::includeSystem('MySystem2');
        $siteid = MySystem2::getCurrentSiteId();
    }

    function two() {
        $siteid = MySystem2::getCurrentSiteId();
        $parserFiles = FileSystem2::listDirectory();
        return parent::two();
    }

    function three() {
        include 'libs/FileSystem.inc';
        $siteid = MySystem::getCurrentSiteId();
        $parserFiles = FileSystem::listDirectory();
        echo self::two();
    }
}

echo Init::ROOT_DIR;
?>