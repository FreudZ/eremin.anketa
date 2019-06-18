<? // подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/softinform.exportok/include.php"); // инициализация модуля

switch (htmlspecialchars($_REQUEST["type"])) {
    case 'get_proplist_html':
		  $HTML_List = \Softinform\Exportok\ExportOk::getIblockPropsList(intval($_REQUEST["iblock_id"]), 'HTML');
	     echo $HTML_List;
        break;
    case 'clear_prof_catlist':
	 	  //Clear categories list for profile when infoblock reselected
		  $res = true;
		  if (intval($_REQUEST["profile_id"])>0) {
		  $res = \Softinform\Exportok\ExportOk::clearCatInProfile(intval($_REQUEST["profile_id"]));
		  }
		  if($res==true) {
		  	echo 'ok';
		  } else {
		  	echo $res;
		  }
        break;
    default:
        echo '';
        break;
}
