<?php
	require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
	use \Bitrix\Iblock\PropertyTable;
?>
<?php
	CModule::IncludeModule("iblock");
	if (htmlspecialchars($_POST["ACTION"]) == 'add' && !empty($_POST)) {
		$ANKETA_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_IBLOCK", 0);
$ANKETA_ANSWERS_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_ANSWERS_IBLOCK", 0);
$ANKETA_CRITERIA_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_CRITERIA_IBLOCK", 0);


	$arIblockProps = array();

	if(intval($ANKETA_ANSWERS_IBLOCK)>0) {
   $rsProperty = \Bitrix\Iblock\PropertyTable::getList(array(
	    'filter' => array(
	        'IBLOCK_ID'=>$ANKETA_ANSWERS_IBLOCK,
	        'ACTIVE'=>'Y',
	        //'=PROPERTY_TYPE'=>\Bitrix\Iblock\PropertyTable::TYPE_LIST
	    ),
	    'select' => array(
	        'ID',
	        'NAME',
	        'CODE',
	    ),
	));
	// PROPERTY_TYPE:
	// \Bitrix\Iblock\PropertyTable::TYPE_STRING - строка
	// \Bitrix\Iblock\PropertyTable::TYPE_NUMBER - число
	// \Bitrix\Iblock\PropertyTable::TYPE_LIST - список
	// \Bitrix\Iblock\PropertyTable::TYPE_ELEMENT - привязка к элементу
	// \Bitrix\Iblock\PropertyTable::TYPE_SECTION - привязка к разделу
	// \Bitrix\Iblock\PropertyTable::TYPE_FILE - файл

	while($arProperty=$rsProperty->fetch())
	{
			$arIblockProps[$arProperty["CODE"]] = $arProperty["ID"];
	} //while

   } //if(intval($Iblock_id)>0)


		$el = new CIBlockElement;
		$param1_id = intval($_POST["PARAM1_ID"]);
		$param2_id = intval($_POST["PARAM2_ID"]);
		$PROP = array();

		$arAnswers = array();
		foreach ($_POST as $key => $value) {

			if (strpos($key, "QUESTION") !== false) {
				$arKey = explode("_", $key);
				if (is_array($arKey)) {
					$param_id = 0;
					if($param1_id = $arKey[1]){
					  $param_id = $param1_id;
					}
					if($param2_id = $arKey[1]){
					  $param_id = $param2_id;
					}
					$arAnswers[$arKey[2]][$param_id]=$value;
				}

			}
		}
		$num = 0;
		foreach($arAnswers as $answer_id=>$answer_values){
					$PROP[$arIblockProps["ANSWERS"]]["n" . $num] = Array(
						"VALUE" => $answer_id,
						"DESCRIPTION" => serialize($answer_values));
					$num++;
		}
		$PROP[$arIblockProps["ANKETA_ID"]] = $_POST["ANKETA_ID"];
		$PROP[$arIblockProps["FIO"]] = $_POST["FIO"];
		$PROP[$arIblockProps["DOLGNOST"]] = $_POST["DOLGNOST"];
		$PROP[$arIblockProps["PHONE"]] = $_POST["PHONE"];
		$PROP[$arIblockProps["EMAIL"]] = $_POST["EMAIL"];
		$PROP[$arIblockProps["ORGNAME"]] = $_POST["ORGANIZATION"];

		$arLoadProductArray = Array(
			"IBLOCK_SECTION_ID" => false, // элемент лежит в корне раздела
			"IBLOCK_ID" => $ANKETA_ANSWERS_IBLOCK,
			"PROPERTY_VALUES" => $PROP,
			"NAME" => $_POST["SPECNUM"],
			"ACTIVE" => "Y", // активен
			"PREVIEW_TEXT" => $_POST["COMMENTS"],
			//"DETAIL_TEXT"    => "текст для детального просмотра",
		);

		if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
			echo "success";
		} else {
			echo "Error: " . $el->LAST_ERROR;
		}
	}
