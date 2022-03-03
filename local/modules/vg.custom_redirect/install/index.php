<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

/*if(class_exists('custom_redirect')){
    return;
}*/

class vg_custom_redirect extends CModule{
    /** @var string */
    public $MODULE_ID;

    /** @var string */
    public $MODULE_VERSION;

    /** @var string */
    public $MODULE_VERSION_DATE;

    /** @var string */
    public $MODULE_NAME;

    /** @var string */
    public $MODULE_DESCRIPTION;

    /** @var string */
    public $MODULE_GROUP_RIGHTS;

    /** @var string */
    public $PARTNER_NAME;

    /** @var string */
    public $PARTNER_URI;

    public function __construct(){
        $arModuleVersion = array();
        
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_ID = 'vg.custom_redirect';
        $this->MODULE_NAME = Loc::getMessage('MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = "Custom";
        $this->PARTNER_URI = "https://very-good.ru/";
    }

    public function doInstall(){
        ModuleManager::registerModule($this->MODULE_ID);
        RegisterModuleDependences("main", "OnPageStart", "vg.custom_redirect", "VGCustomRedirect", "TrailingSlashUrl");
        RegisterModuleDependences("main", "OnPageStart", "vg.custom_redirect", "VGCustomRedirect", "RedirectUrl");
        $this->installDB();
    }

    public function doUninstall(){
        $this->uninstallDB();
        UnRegisterModuleDependences("main", "OnPageStart", "vg.custom_redirect", "VGCustomRedirect", "TrailingSlashUrl");
        UnRegisterModuleDependences("main", "OnPageStart", "vg.custom_redirect", "VGCustomRedirect", "RedirectUrl");
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    public function installDB(){
        if(Loader::includeModule($this->MODULE_ID)){
            VGCustomRedirectTable::getEntity()->createDbTable();
        }
    }

    public function uninstallDB(){
        if(Loader::includeModule($this->MODULE_ID)){
            $connection = Application::getInstance()->getConnection();
            $connection->dropTable(VGCustomRedirectTable::getTableName());
        }
    }
}