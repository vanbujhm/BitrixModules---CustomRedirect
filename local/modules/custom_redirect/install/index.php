<?php
defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Custom_redirect\RedirectTable;

Loc::loadMessages(__FILE__);

if (class_exists('custom_redirect')) {
    return;
}

class custom_redirect extends CModule
{
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

    public function __construct()
    {
        $arModuleVersion = array();
        
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_ID = 'custom_redirect';
        $this->MODULE_NAME = Loc::getMessage('custom_redirect_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('custom_redirect_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = "Custom";
        $this->PARTNER_URI = "http://very-good.ru/";
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        RegisterModuleDependences("main", "OnPageStart", "custom_redirect", "Custom_redirect\Redirect", "TrailingSlashUrl");
        RegisterModuleDependences("main", "OnPageStart", "custom_redirect", "Custom_redirect\Redirect", "RedirectUrl");
        $this->installDB();
    }

    public function doUninstall()
    {
        $this->uninstallDB();
        UnRegisterModuleDependences("main", "OnPageStart", "custom_redirect", "Custom_redirect\Redirect", "TrailingSlashUrl");
        UnRegisterModuleDependences("main", "OnPageStart", "custom_redirect", "Custom_redirect\Redirect", "RedirectUrl");
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    public function installDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            RedirectTable::getEntity()->createDbTable();
        }
    }

    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            $connection = Application::getInstance()->getConnection();
            $connection->dropTable(RedirectTable::getTableName());
        }
    }
}