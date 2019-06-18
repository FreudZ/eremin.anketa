<?
global $DB;
$db_type = strtolower($DB->type);

Bitrix\Main\Loader::registerAutoloadClasses(
	"eremin.anketa",
	array(
		"Eremin\\Anketa\\AnketaTools" => "lib/anketatools.php",
		//"PHPExcel" => "lib/PHPExcel.php",
/*		"Softinform\\Exportok\\CatsyncTable" => "lib/catsync.php",
		"Softinform\\Exportok\\ItemsyncTable" => "lib/itemsync.php",
		"Softinform\\Exportok\\ExportOk" => "lib/exportok.php",
		"Softinform\\Exportok\\ApiOk" => "lib/apiok.php",*/
	)
);
