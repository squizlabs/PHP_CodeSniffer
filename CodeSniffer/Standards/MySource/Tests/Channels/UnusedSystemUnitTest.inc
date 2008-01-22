<?php
Channels::includeSystem('OurSystem');
Channels::includeSystem('Page');
Channels::includeAsset('Page2');
Channels::includeAsset('Folder');

function one() {
    Channels::includeSystem('MySystem');
    $siteid = MySystem2::getCurrentSiteId();
    Page::create();
    PageAssetType::create();
}

function two() {
    Page2AssetType::create();
    Folder::getFolder();
}

class MyClass
{
    function one() {
        Channels::includeSystem('MySystem2');
        Channels::includeSystem('Page2');
        $siteid = MySystem2::getCurrentSiteId();
    }

    function two() {
        $siteid = Page2::getCurrentSiteId();
    }
}
?>