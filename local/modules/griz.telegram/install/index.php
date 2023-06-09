<?php
//подключаем основные классы для работы с модулем
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Griz\Telegram\TelegramTable;
use Griz\Telegram\Internals\Control\EventManager as GrizEventManager;

//в данном модуле создадим адресную книгу, и здесь мы подключаем класс, который создаст нам эту таблицу

Loc::loadMessages(__FILE__);

//в названии класса пишем название директории нашего модуля, только вместо точки ставим нижнее подчеркивание
class griz_telegram extends CModule
{

    private ?string $docRoot;
    public function __construct()
    {
        $arModuleVersion = array();

        $this->docRoot = Application::getDocumentRoot();

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'griz.telegram';
        $this->MODULE_NAME = Loc::getMessage('MYMODULE_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MYMODULE_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('MYMODULE_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'https://griz.it';//адрес вашего сайта
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallFiles();
        $this->installDB();
        $this->InstallEvents();

    }

    //вызываем метод удаления таблицы и удаляем модуль из регистра
    public function doUninstall()
    {
        $this->uninstallDB();
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    //вызываем метод создания таблицы из выше подключенного класса
    public function installDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            TelegramTable::getEntity()->createDbTable();

        }
    }

    //вызываем метод удаления таблицы, если она существует
    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            if (Application::getConnection()->isTableExists(Base::getInstance('\Griz\Telegram\TelegramTable')->getDBTableName())) {
                $connection = Application::getInstance()->getConnection();
                $connection->dropTable(TelegramTable::getTableName());
            }
        }
    }


    function InstallEvents()
    {
        GrizEventManager::addBasicEventHandlers();
    }


    function UnInstallEvents()
    {
        GrizEventManager::removeBasicEventHandlers();
    }

    function InstallFiles()
    {

        CopyDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin', true);
        CopyDirFiles(__DIR__.'/js/', $this->docRoot.'/bitrix/js/'.$this->partnerId."/".$this->moduleNameShort, true, true);
        CopyDirFiles(__DIR__.'/css/', $this->docRoot.'/bitrix/css/'.$this->partnerId."/".$this->moduleNameShort, true, true);


        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles(__DIR__.'/admin/', $this->docRoot.'/bitrix/admin');

        return true;
    }
}