<?php
	require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
?>
<?php
	CModule::IncludeModule("iblock");
	if (htmlspecialchars($_POST["ACTION"]) == 'add' && !empty($_POST)) {
		$ANKETA_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_IBLOCK", 0);
$ANKETA_ANSWERS_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_ANSWERS_IBLOCK", 0);
$ANKETA_CRITERIA_IBLOCK = COption::GetOptionInt("eremin.anketa", "ANKETA_CRITERIA_IBLOCK", 0);
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
					$PROP[115]["n" . $num] = Array(
						"VALUE" => $answer_id,
						"DESCRIPTION" => serialize($answer_values));
					$num++;
		}
		$PROP[116] = $_POST["ANKETA_ID"];
		$PROP[114] = $_POST["FIO"];
		$PROP[117] = $_POST["DOLGNOST"];
		$PROP[118] = $_POST["PHONE"];
		$PROP[119] = $_POST["EMAIL"];

		$arLoadProductArray = Array(
			"IBLOCK_SECTION_ID" => false, // элемент лежит в корне раздела
			"IBLOCK_ID" => $ANKETA_ANSWERS_IBLOCK,
			"PROPERTY_VALUES" => $PROP,
			"NAME" => $_POST["ORGANIZATION"],
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
