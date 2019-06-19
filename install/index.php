<?php

/* ћодуль предназначен дл€ анкетировани€ клиентов на предмет удовлетворенности.
 */
IncludeModuleLangFile(__FILE__);

if (class_exists("eremin_anketa")) {
	return;
}

Class eremin_anketa extends CModule {
	var $MODULE_ID = "eremin.anketa";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";


	function eremin_anketa() {
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include $path . "/version.php";

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = GetMessage("EANK_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("EANK_MODULE_DESCRIPTION");

		$this->PARTNER_NAME = GetMessage("EANK_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("EANK_PARTNER_URL");
	}

	function DoInstall() {
		global $APPLICATION;

		if (!IsModuleInstalled("eremin.anketa")) {
			$this->InstallFiles();
		}
		return true;
	}

	function InstallDB() {
	}

	function UnInstallDB() {
	}

	function DoUninstall() {
		$this->UnInstallFiles();
		return true;
	}

	function UnInstallEvents() {
		return true;
	}

	function InstallFiles() {
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
		CUrlRewriter::Add(array("CONDITION" => "#^/anketa/([a-zA-Z\\-]+)/(.*)#", "RULE" => "SECTION_CODE=\$1", "ID" => "eremin.anketa", "PATH" => "/anketa/index.php"));

		return true;
	}

	function UnInstallFiles() {
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/components/bitrix/news.list/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components/bitrix/news.list/show_anketa");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/eremin.anketa/install/panel/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/panel/eremin.anketa");
		DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/news.list/show_anketa");
		DeleteDirFilesEx("/anketa");
		DeleteDirFilesEx("/bitrix/include/eremin.anketa");
		DeleteDirFilesEx("/bitrix/images/eremin.anketa");
		UnRegisterModule("eremin.anketa");
		CUrlRewriter::Delete(array("PATH" => "/anketa/index.php"));
		return true;
	}

}

?>
