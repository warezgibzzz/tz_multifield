<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use CIBlockPropertyWarezgibzzzTzMultiField;
use OnSaleOrderUpdate;

Loc::loadMessages(__FILE__);

class Warezgibzzz_Tz_Multifield extends CModule
{
    public function __construct()
    {
        $arModuleVersion = array();
        
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        
        $this->MODULE_ID = 'warezgibzzz.tz_multifield';
        $this->MODULE_NAME = Loc::getMessage('WAREZGIBZZZ_TZ_MULTIFIELD_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('WAREZGIBZZZ_TZ_MULTIFIELD_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('WAREZGIBZZZ_TZ_MULTIFIELD_MODULE_PARTNER_NAME');
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installDB();
    }

    public function doUninstall()
    {
        $this->uninstallDB();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installDB()
    {
        if (Loader::includeModule($this->MODULE_ID))
        {
            RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'CIBlockPropertyWarezgibzzzTzMultiField', 'GetUserTypeDescription');
            RegisterModuleDependences('sale', 'OnOrderUpdate', $this->MODULE_ID, 'OnSaleOrderUpdate', 'checkOrderStatus');
        }
    }

    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID))
        {
            UnRegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, 'CIBlockPropertyWarezgibzzzTzMultiField', 'GetUserTypeDescription');
            UnRegisterModuleDependences('sale', 'OnOrderUpdate', $this->MODULE_ID, 'OnSaleOrderUpdate', 'checkOrderStatus');
        }
    }
}
