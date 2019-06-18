<? require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

$APPLICATION->IncludeFile('/bitrix/modules/softinform.exportok/lang/ru/admin/ajax_export_ok.php');

$delimiter = $_POST["delimiter"];
$SETUP_FILE_NAME = $_POST["SETUP_FILE_NAME"];
$V = $_POST["V"];
$CHOOSE_PROPERTIES = $_POST["PROPERTIES"];
$PROPERTIES = array();
$IBLOCK_ID = $_POST["IBLOCK_ID"];
$START = $_POST["start"];

foreach ($_POST["DEFAULT_PROP"] as $kpr => $pr) {
    $DEFAULT_PROP[$kpr] = str_replace("PROPERTY_", "", $pr);
}
$SETUP_FILE_NAME = "/bitrix/catalog_export/" . $SETUP_FILE_NAME;

$csvFile = new CCSVData();

if (!isset($fields_type) || ($fields_type != "F" && $fields_type != "R")) {
    $arRunErrors[] = GetMessage("CATI_NO_FORMAT");
}

$csvFile->SetFieldsType($fields_type);

$first_names_r = (isset($first_names_r) && $first_names_r == 'Y' ? true : false);
$csvFile->SetFirstHeader($first_names_r);

foreach ($CHOOSE_PROPERTIES as $k => $v) {
    if ($v == -1) {
        $PROPERTIES[$k] = $DEFAULT_PROP[$k];
    } else {
        $PROPERTIES[$k] = $v;
    }
}

$delimiter_r_char = "";
if (isset($delimiter)) {
    switch ($delimiter) {
        case "TAB":
            $delimiter_r_char = "\t";
            break;
        case "ZPT":
            $delimiter_r_char = ",";
            break;
        case "SPS":
            $delimiter_r_char = " ";
            break;
        case "OTR":
            $delimiter_r_char = (isset($delimiter_other_r) ? substr($delimiter_other_r, 0, 1) : '');
            break;
        case "TZP":
            $delimiter_r_char = ";";
            break;
        default:
            $delimiter_r_char = $delimiter;
    }
}

$csvFile->SetDelimiter($delimiter_r_char);

$SETUP_FILE_NAME = Rel2Abs("/", $SETUP_FILE_NAME);
if (strtolower(substr($SETUP_FILE_NAME, strlen($SETUP_FILE_NAME) - 4)) != ".csv")
    $SETUP_FILE_NAME .= ".csv";
if (0 !== strpos($SETUP_FILE_NAME, $strCatalogDefaultFolder)) {
    $arRunErrors[] = GetMessage('CES_ERROR_PATH_WITHOUT_DEFAUT');
} else {
    CheckDirPath($_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME);

    if (!($fp = fopen($_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME, "wb"))) {
        $arRunErrors[] = GetMessage("CATI_CANNOT_CREATE_FILE");
    }
    @fclose($fp);
}

global $defCatalogAvailGroupFields, $defCatalogAvailProdFields, $defCatalogAvailPriceFields, $defCatalogAvailValueFields, $defCatalogAvailQuantityFields;
global $arCatalogAvailProdFields, $arCatalogAvailGroupFields, $arCatalogAvailPriceFields, $arCatalogAvailValueFields, $arCatalogAvailQuantityFields;

$intCount = 0; // count of all available fields, props, section fields, prices
$arSortFields = array(); // array for order
$selectArray = array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"); // selected element fields


// Prepare arrays for product loading
$strAvailProdFields = COption::GetOptionString("catalog", "allowed_product_fields", $defCatalogAvailProdFields);
$arAvailProdFields = explode(",", $strAvailProdFields);
$arAvailProdFields_names = array();
foreach ($_POST["DEFAULT_PROP"] as $kkpr => $ppr) {
    if (strpos($ppr, "PROPERTY_") !== false) {
        $arSortFields[] = GetMessage($ppr);
    } else {
        $arSortFields[] = $ppr;
    }

}
$arNeedFields = $arSortFields;

//Первая строка в файле
if ($START == 0) {
    $csvFile->SaveFile($_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME, $arNeedFields);
}


$filterSection = array();
if (is_array($V)) {
    foreach ($V as $valV) {
        $filterSection[] = $valV;
    }

} else {
    $filterSection = "";
}

$filter = array("IBLOCK_ID" => $IBLOCK_ID, 'SECTION_ID' => $filterSection);


if ($V == "all") {
    $resID = CIBlockElement::GetList(array('ID' => 'ASC'), array("IBLOCK_ID" => $IBLOCK_ID), false, false, array("ID"));
} else {
    $filter["INCLUDE_SUBSECTIONS"] = "Y";
    $resID = CIBlockElement::GetList(array('ID' => 'ASC'), $filter, false, false, array("ID"));
}

$allIdForExport = array();
while ($objID = $resID->GetNext()) {
    if (!in_array($objID["ID"], $allIdForExport)) {
        $allIdForExport[] = $objID["ID"];
    }
}
$countElements = count($allIdForExport);

$ROWS_COUNT = COption::GetOptionInt("softinform.exportok", "ROWS_COUNT", 50);

$chunkSave = $ROWS_COUNT;//500
$END = $START + $chunkSave;

for ($k = $START; $k < $END; $k++) {
    $res = CIBlockElement::GetByID($allIdForExport[$k]);
    if ($res1 = $res->GetNextElement()) {
        $reOnl = $res1->GetFields();
        $reProp = $res1->GetProperties();
        $resPrice = CPrice::GetBasePrice($reOnl["ID"]);
        $arPrice["PRICE"] = CCurrencyRates::ConvertCurrency(
            $resPrice["PRICE"],
            $resPrice["CURRENCY"],
            CCurrency::GetBaseCurrency());
        $arPrice["CURRENCY"] = CCurrency::GetBaseCurrency();
        foreach ($PROPERTIES as $keyprop => $prop) {
            switch ($keyprop) {
                case "0":
                    $arResFields[$keyprop] = $prop;
                    break;
                case "4"://ID присваивает ok.by
                    $arResFields[4] = "";
                    break;
                case "5"://Цена товара
                    $arResFields[5] = $arPrice["PRICE"];//
                    break;
                case "6"://Валюта товара
                    $arResFields[6] = $arPrice["CURRENCY"];//
                    break;
                default:
                    if ($reProp[$prop]["MULTIPLE"] == "Y") {
                        $arResFields[$keyprop] = $reProp[$prop]["VALUE"][0];
                    } else {
                        $arResFields[$keyprop] = $reProp[$prop]["VALUE"];
                    }
            }
        }
        $csvFile->SaveFile($_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME, $arResFields);
    }
    if ($k == $countElements) {
        break;
    }
}

if ($k >= $countElements) {
    $fileContent = file_get_contents($_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME);
    $fileContent = mb_convert_encoding($fileContent, "CP1251", mb_internal_encoding());
    file_put_contents($_SERVER["DOCUMENT_ROOT"] . $SETUP_FILE_NAME, $fileContent);
    echo json_encode(array("END" => true, "SETUP_FILE_NAME" => $SETUP_FILE_NAME));
} else {
    $post["start"] = $END;
    $post["V"] = $V;
    $post["IBLOCK_ID"] = $IBLOCK_ID;
    $post["SETUP_FILE_NAME"] = $SETUP_FILE_NAME;
    $post["delimiter_r"] = $delimiter_r;
    $post["allcount"] = $countElements;
    $post["currentexport"] = $k;
    echo json_encode($post);
}
