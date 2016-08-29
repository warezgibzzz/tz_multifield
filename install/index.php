<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class Gibz_Tz_Multifield extends CModule
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
        
        $this->MODULE_ID = 'gibz.tz_multifield';
        $this->MODULE_NAME = Loc::getMessage('GIBZ_TZ_MULTIFIELD_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('GIBZ_TZ_MULTIFIELD_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('GIBZ_TZ_MULTIFIELD_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'http://bitrix.expert';
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

        }
    }

    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID))
        {

        }
    }
}
