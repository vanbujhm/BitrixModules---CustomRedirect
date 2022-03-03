<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

Loader::registerAutoLoadClasses('vg.custom_redirect', array(
    // no thanks, bitrix, we better will use psr-4 than your class names convention
    'VGCustomRedirectTable' => 'lib/redirect_table.php',
    'VGCustomRedirect' => 'lib/redirect.php',
));

EventManager::getInstance()->addEventHandler('main', 'OnPageStart', function(){
    // do something when new user added
});