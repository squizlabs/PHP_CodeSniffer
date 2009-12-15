<?php
Channels::includeSystem('MySystem');
Channels::includeAsset('Page');
require_once 'libs/FileSystem2.inc';

function one() {
    Channels::includeSystem('MySystem2');
    $siteid = MySystem2::getCurrentSiteId();
    Page::create();
    PageAssetType::create();
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
Page::create();
PageAssetType::create();

class AssetListingAssetType extends AssetAssetType
{
    function one() {
        Channels::includeSystem('MySystem2');
        $siteid = MySystem2::getCurrentSiteId();
        Page::create();
        PageAssetType::create();
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

Channels::includeSystem('AssetType');
AssetType::includeAsset('User');
AssetType::includeAsset('FolderAsset');
UserAssetType::create();
FolderAssetType::create();
$query->fetch(PDO::FETCH_NUM)
BaseSystem::getDataDir();
Util::getArrayIndex(array(), '');


Channels::includeSystem('Widget');
Widget::includeWidget('AbstractContainer');
class MyWidget extends AbstractContainerWidgetType {}
class MyOtherWidget extends BookWidgetType {}

$zip = new ZipArchive();
$res = $zip->open($path, ZipArchive::CREATE);

class AssetListingUnitTest extends AbstractMySourceUnitTest
{
    function setUp() {
        parent::setUp();
        Channels::includeSystem('MySystem2');
        include_once 'Libs/FileSystem.inc';
    }

    function two() {
        $siteid = MySystem2::getCurrentSiteId();
        $parserFiles = FileSystem::listDirectory();
    }

    function three() {
        $siteid = MySystem3::getCurrentSiteId();
        $parserFiles = FileSystem::listDirectory();
    }
}

if (Channels::systemExists('Log') === TRUE) {
   Channels::includeSystem('Log');
} else {
   return;
}

Log::addProjectLog('metadata.field.update', $msg);

function two() {
    Widget::includeWidget('CacheAdminScreen');
    $barChart = CacheAdminScreenWidgetType::constructBarchart($data);
}

$adjustDialog->setOrientation(AbstractWidgetWidgetType::CENTER);
?>
