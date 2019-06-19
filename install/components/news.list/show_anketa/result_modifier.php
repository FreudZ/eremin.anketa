<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    $arFilter = Array('IBLOCK_ID'=>$arParams["IBLOCK_ID"], 'GLOBAL_ACTIVE'=>'Y', 'INCLUDE_SUBSECTIONS'=>'Y');
    $db_list = CIBlockSection::GetList(Array("depth_level"=>"ASC", "SORT"=>"ASC"), $arFilter, true, array("UF_DESCRIPTION1", "UF_PARAM1", "UF_PARAM2", "UF_SHOW_HEADER", "UF_REDIRECT", "UF_SUCCESS_MESSAGE"));
	 $arAnketa = array();
	 $arHeadings = array();

    while($ar_result = $db_list->GetNext())
    {

    	if($ar_result["CODE"]==$arParams["PARENT_SECTION_CODE"]){
    		$arAnketa = $ar_result;
    	} else{
    		if($ar_result["IBLOCK_SECTION_ID"] == $arAnketa["ID"] && $ar_result["DEPTH_LEVEL"]>1) {
    		  $arHeadings[$ar_result['ID']] = $ar_result;
    		}

    	}
    }


	 $arSelect = Array("ID", "NAME", "IBLOCK_ID","PREVIEW_TEXT");
    $arFilter = Array("ID"=>array($arAnketa["UF_PARAM1"], $arAnketa["UF_PARAM2"]), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nTopCount"=>2), $arSelect);
    while($ob = $res->GetNextElement()){
     $arFields = $ob->GetFields();
	  if($arFields["ID"]==$arAnketa["UF_PARAM1"]){
	  	$arAnketa["TABLE_PARAM_1"] = array("ID"=>$arFields["ID"], "NAME"=>$arFields["NAME"], "PREVIEW_TEXT"=>$arFields["PREVIEW_TEXT"]);
	  }
	  if($arFields["ID"]==$arAnketa["UF_PARAM2"]){
	  	$arAnketa["TABLE_PARAM_2"] = array("ID"=>$arFields["ID"], "NAME"=>$arFields["NAME"], "PREVIEW_TEXT"=>$arFields["PREVIEW_TEXT"]);
	  }
    }
    $arResult["ANKETA"] = $arAnketa;

	 // Функция сортировки по оценке: сортировка по возрастанию.
function array_anketa_sort($x, $y) {
    return ($x['SORT'] > $y['SORT']);

}

	 uasort($arHeadings, 'array_anketa_sort');
    $arResult["HEADINGS"] = $arHeadings;



	 $arTmp = array();
	 foreach($arResult["ITEMS"] as $arItem){
		$arTmp[$arItem["IBLOCK_SECTION_ID"]]["ITEMS"][] = $arItem;
	 }
	 $arResult["ITEMS"] = $arTmp;
	 unset($arTmp);
?>