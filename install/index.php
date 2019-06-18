<?
/* Модуль предназначен для анкетирования клиентов на предмет удовлетворенности.

    1.Установка/удаление модуля : Marketplace/Установленные решения/ (папка с модулем должна находится в /bitrix/modules/)

    2.Настройка модуля(Настройки продукта/Модули/Экспорт товаров на сайт ok.ru):
        -вкладка "Доступ"
            настройка прав доступа к модулю
        -вкладка "Установки"
            настройка Количества строк, обрабатываемых за один проход (по-умолчанию 50 строк)

    3.Использование : Сервисы/Экспорт на сайт ok.ru/Экспорт.
        1.Первый шаг : выбор каталога и разделов
        2.Второй шаг: Выбор разделителя и название файла
        3.Третий шаг:
           - Шапка таблицы предстваляет собой название полей, которые предоставил ok
           - Вторая строка таблицы  свойства каталога, которые будут экспортироваться по-умолчанию
                |Название раздела - в какую категорию на сайте будет экспортироваться товар
                |Модель выглядит так - Дуб Винстон [40854]
                |id предложения присваивает сам ok
                |Цена берется из товара
                |Валюта товара
           - Третья строка таблицы свойства каталога, которые будут экспортироваться, если свойства по-умолчанию не заполнены
        4. Четвертый шаг: экспорт

*/
IncludeModuleLangFile(__FILE__);

if (class_exists("eremin_anketa"))
    return;

Class eremin_anketa extends CModule
{
    var $MODULE_ID = "eremin.anketa";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";

    function eremin_anketa()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = GetMessage("EANK_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("EANK_MODULE_DESCRIPTION");

        $this->PARTNER_NAME = GetMessage("EANK_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("EANK_PARTNER_URL");
    }

    function DoInstall()
    {
        global $APPLICATION;

        if (!IsModuleInstalled("eremin.anketa")) {
            //$this->InstallEvents();
            $this->InstallFiles();
        }
        return true;
    }

	 	function InstallDB()
		{
		}

	 function UnInstallDB()
		{
		}

    function DoUninstall()
    {
       // $this->UnInstallEvents();
        $this->UnInstallFiles();
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }

    function InstallFiles()
    {
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/components/news.list/show_anketa", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components/bitrix/news.list/show_anketa", true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/components/news.list/show_anketa/ajax", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components/bitrix/news.list/show_anketa/ajax/", true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/components/news.list/show_anketa/css", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components/bitrix/news.list/show_anketa/css/", true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/components/news.list/show_anketa/lang/ru", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components/bitrix/news.list/show_anketa/lang/ru/", true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/components/news.list/show_anketa/lang/en", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components/bitrix/news.list/show_anketa/lang/en/", true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/public/anketa", $_SERVER["DOCUMENT_ROOT"] . "/anketa", true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/panel", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/panel/eremin.anketa/", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/images", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/images", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/include", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/include", true, true);
        RegisterModule("eremin.anketa");
        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/components/bitrix/news.list/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components/bitrix/news.list/show_anketa");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/panel/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/panel/eremin.anketa");
        DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/news.list/show_anketa");
        DeleteDirFilesEx("/anketa");
        DeleteDirFilesEx("/bitrix/include/eremin.anketa");
        DeleteDirFilesEx("/bitrix/images/eremin.anketa");
        UnRegisterModule("eremin.anketa");
        return true;
    }


}

?>